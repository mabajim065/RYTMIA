<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware: role
 *
 * Uso: Route::middleware('role:administrador')
 *      Route::middleware('role:administrador,entrenadora')
 *
 * Devuelve 403 JSON si el usuario autenticado no tiene uno de los roles permitidos.
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->rol, $roles)) {
            return response()->json([
                'message' => 'No tienes permiso para acceder a este recurso.',
                'rol_requerido' => $roles,
                'rol_actual'    => $user?->rol,
            ], 403);
        }

        return $next($request);
    }
}
