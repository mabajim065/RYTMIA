<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Login con DNI + contraseña.
     * Devuelve un token Sanctum y los datos del usuario.
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->username)
                    ->where('activo', true)
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Las credenciales no son correctas.'],
            ]);
        }

        // Eliminar tokens anteriores (sesión única)
        $user->tokens()->delete();

        $token = $user->createToken('rytmia-token', [$user->rol])->plainTextToken;

        return response()->json([
            'token' => $token,
            'user'  => [
                'id'       => $user->id,
                'nombre'   => $user->nombre,
                'apellidos'=> $user->apellidos,
                'username' => $user->username,
                'dni'      => $user->dni,
                'email'    => $user->email,
                'rol'      => $user->rol,
                'telefono' => $user->telefono,
            ],
        ]);
    }

    /**
     * Logout: revoca el token actual.
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->loadMissing(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.conjunto.entrenadores.user']);

        return response()->json([
            'id'        => $user->id,
            'nombre'    => $user->nombre,
            'apellidos' => $user->apellidos,
            'dni'       => $user->dni,
            'email'     => $user->email,
            'rol'       => $user->rol,
            'telefono'  => $user->telefono,
            'activo'    => $user->activo,
            'entrenador'=> $user->entrenador,
            'gimnasta'  => $user->gimnasta,
        ]);
    }

    /**
     * Enviar enlace de restablecimiento de contraseña.
     */
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'El correo electrónico no está registrado en Rytmia.',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();

        // Generar un token único
        $token = \Illuminate\Support\Str::random(60);

        // Guardar en la tabla password_reset_tokens
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // Construir la URL de restablecimiento
        $resetUrl = url('/recuperar-password?token=' . $token . '&email=' . urlencode($user->email));

        // Enviar el correo
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RecuperarPasswordMail($resetUrl, $user));

        return response()->json([
            'message' => 'Te hemos enviado por correo el enlace para restablecer tu contraseña.'
        ]);
    }

    /**
     * Restablecer la contraseña usando el token.
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (! $record || ! Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'El enlace de recuperación es inválido o no existe.'], 422);
        }

        // Validar expiración (ej. 60 minutos)
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            return response()->json(['message' => 'El enlace de recuperación ha expirado.'], 422);
        }

        // Actualizar contraseña del usuario
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->password_temporal = $request->password;
        $user->save();

        // Borrar el token de restablecimiento
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Tu contraseña ha sido restablecida correctamente.'
        ]);
    }

    /**
     * Redirige a Google para autenticación.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->scopes(['https://www.googleapis.com/auth/calendar', 'https://www.googleapis.com/auth/userinfo.email'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    /**
     * Maneja el callback de Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
            
            // Buscar usuario por email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Registrar automáticamente al usuario si no existe
                $user = User::create([
                    'nombre' => $googleUser->user['given_name'] ?? $googleUser->getName(),
                    'apellidos' => $googleUser->user['family_name'] ?? 'Google',
                    'email' => $googleUser->getEmail(),
                    'dni' => rand(10000000, 99999999) . chr(rand(65, 90)), // Generar DNI aleatorio para cumplir validación
                    'password' => Hash::make(\Illuminate\Support\Str::random(24)),
                    'rol' => 'gimnasta', // Rol por defecto
                    'activo' => true,
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
            } else {
                // Actualizar datos de Google
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'google_token' => $googleUser->token,
                    'google_refresh_token' => $googleUser->refreshToken,
                ]);
            }

            $token = $user->createToken('rytmia-token', [$user->rol])->plainTextToken;

            // En un entorno real con frontend separado, redirigirías al frontend con el token
            // return redirect()->away('http://localhost:5173/login?token=' . $token);
            
            return response()->json([
                'token' => $token,
                'user'  => $user
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al autenticar con Google: ' . $e->getMessage()], 500);
        }
    }
}
