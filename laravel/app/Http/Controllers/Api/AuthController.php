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
        $user->loadMissing(['entrenador.club', 'gimnasta.club', 'gimnasta.categoria', 'gimnasta.conjunto']);

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
}
