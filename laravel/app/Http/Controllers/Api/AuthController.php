<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /*Autentica al usuario y genera un token de acceso.*/
    public function login(Request $request): JsonResponse
    {
        /* Validación de los datos de entrada */
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        /* Intento de autenticación */
        $user = User::where('username', $request->username)
                    ->where('activo', true)
                    ->first();

        /* Verificación de credenciales */
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['Las credenciales no son correctas.'],
            ]);
        }

        // Control de sesión única
        $user->tokens()->delete();
        /* Generación del token de acceso con el rol del usuario como permiso */
        $token = $user->createToken('rytmia-token', [$user->rol])->plainTextToken;
        /* Respuesta con el token nuevo  y  información del usuario */
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

    /*sirve para cerrar la sesión del usuario autenticado y quitar su token de acceso.*/
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada correctamente.']);
    }

    // devuelve los datos del usuario autenticicado
    public function me(Request $request): JsonResponse
    {
        //craga los datos relacionados
        $user = $request->user();
        //carga las relaciones
        $user->loadMissing(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.conjunto.entrenadores.user']);

        //devuelve los datos del usuario
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

    /* Genera y envia por email el enlace para recuperar la contraseña.*/
    public function sendResetLinkEmail(Request $request): JsonResponse
    {
        //se comprueban los dstos de entrada
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'El correo electrónico no está registrado en Rytmia.',
        ]);

        //obtengo el usuario por su correo electronico
        $user = User::where('email', $request->email)->firstOrFail();

        // creacion  de token
        $token = \Illuminate\Support\Str::random(60);

        // Guardar token en la base de datos
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => now()
            ]
        );

        // se envia la notificacion
        $resetUrl = url('/recuperar-password?token=' . $token . '&email=' . urlencode($user->email));
        \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\RecuperarPasswordMail($resetUrl, $user));

        return response()->json([
            'message' => 'Te hemos enviado por correo el enlace para restablecer tu contraseña.'
        ]);
    }

    /* Valida el token y actualiza la contraseña en la base de datos. */
    public function resetPassword(Request $request): JsonResponse
    {
        // comprueba de los datos de entrada
        $request->validate([
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // comprueba  token
        $record = \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();
    
        if (! $record || ! Hash::check($request->token, $record->token)) {
            return response()->json(['message' => 'El enlace de recuperación es inválido o no existe.'], 422);
        }

        // comprueba  q no halla caducado
        $createdAt = \Carbon\Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            \Illuminate\Support\Facades\DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            return response()->json(['message' => 'El enlace de recuperación ha expirado.'], 422);
        }

        // Actualizacion de credenciales
        $user = User::where('email', $request->email)->firstOrFail();
        // actualiza la contraseña y la guarda en la base de datos
        $user->password = Hash::make($request->password);
        // guarda la contraseña temporal para poder notificar al usuario
        $user->password_temporal = $request->password;
        
        $user->save();

        // elimino el token de recuperacion
        \Illuminate\Support\Facades\DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return response()->json([
            'message' => 'Tu contraseña ha sido restablecida correctamente.'
        ]);
    }
}