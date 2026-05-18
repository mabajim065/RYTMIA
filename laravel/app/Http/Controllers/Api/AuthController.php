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
            'dni'      => ['required', 'string', 'size:9'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('dni', strtoupper($request->dni))
                    ->where('activo', true)
                    ->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'dni' => ['Las credenciales no son correctas.'],
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
