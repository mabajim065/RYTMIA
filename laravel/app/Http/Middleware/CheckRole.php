<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{

    
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        // Comprueba si el usuario existe y si su rol coincide con los requeridos por la ruta
        if (! $user || ! in_array($user->rol, $roles)) {
            return response()->json([
                'message'       => 'No tienes permiso para acceder a este recurso.',
                'rol_requerido' => $roles,
                'rol_actual'    => $user?->rol,
            ], 403);
        }

        // Permite que la peticion continue su flujo normal hacia el controlador
        return $next($request);
    }
}