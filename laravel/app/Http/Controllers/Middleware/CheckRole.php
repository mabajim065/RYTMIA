<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware de control de roles para Rytmia.
 *
 * Uso en rutas:
 *   Route::middleware('role:administrador')
 *   Route::middleware('role:administrador,entrenadora')
 */
class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->rol, $roles, true)) {
            return response()->json([
                'message' => 'No tienes permisos para acceder a este recurso.',
            ], 403);
        }

        return $next($request);
    }
}
