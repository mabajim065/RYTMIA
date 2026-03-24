<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        // ── Alias de middleware personalizados ───────────────────
        $middleware->alias([
            'role' => \App\Http\Middleware\CheckRole::class,
        ]);

        // ── Excluir rutas API de la verificación CSRF ────────────
        $middleware->validateCsrfTokens(except: [
            'api/*',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {

        // ── Respuestas JSON para errores en rutas API ────────────

        // 401 Unauthenticated → JSON en vez de redirect al login
        $exceptions->render(function (
            \Illuminate\Auth\AuthenticationException $e,
            Request $request
        ) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'No autenticado. Por favor inicia sesión.',
                ], 401);
            }
        });

        // 403 Forbidden → JSON
        $exceptions->render(function (
            \Illuminate\Auth\Access\AuthorizationException $e,
            Request $request
        ) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'No tienes permisos para realizar esta acción.',
                ], 403);
            }
        });

        // 404 Not Found → JSON
        $exceptions->render(function (
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e,
            Request $request
        ) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'message' => 'Recurso no encontrado.',
                ], 404);
            }
        });

        // 422 Validation → ya lo gestiona Laravel automáticamente en JSON
        // 500 genérico → JSON
        $exceptions->render(function (
            \Throwable $e,
            Request $request
        ) {
            if ($request->expectsJson() || $request->is('api/*')) {
                $status = method_exists($e, 'getStatusCode')
                    ? $e->getStatusCode()
                    : 500;

                return response()->json([
                    'message' => $status === 500
                        ? 'Error interno del servidor.'
                        : $e->getMessage(),
                ], $status);
            }
        });

    })->create();