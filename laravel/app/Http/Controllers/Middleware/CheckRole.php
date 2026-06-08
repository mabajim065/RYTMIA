<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{

    // MIDDLEWARE DE ROLES (RYTMIA)
    // Bloquea o permite el acceso a las rutas según el rol del usuario
    // Ejemplo de uso en rutas: Route::middleware('role:administrador,entrenadora')

    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // 1. Verificación de acceso
        // Comprueba si el usuario está autenticado y si su rol está en la lista permitida
        if (! $user || ! in_array($user->rol, $roles, true)) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a este recurso.',
            ], 403);
        }

        // 2. Autorización
        // Si pasa la validación, permite que la petición continúe su curso
        return $next($request);
    }
}