<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

// ── Rutas públicas ───────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ── Rutas protegidas (requieren token Sanctum) ───────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── Solo Administrador ───────────────────────────────────────────────────
    Route::middleware('role:administrador')->group(function () {
        // Aquí irán los endpoints de gestión (usuarios, clubs, etc.)
        // Ejemplo: Route::apiResource('/usuarios', UsuarioController::class);
    });

    // ── Administrador + Entrenadora ──────────────────────────────────────────
    Route::middleware('role:administrador,entrenadora')->group(function () {
        // Ejemplo: Route::apiResource('/conjuntos', ConjuntoController::class);
    });

});
