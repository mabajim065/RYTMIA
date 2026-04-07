<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas públicas (sin autenticación)
|--------------------------------------------------------------------------
*/

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren token Sanctum)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    /*
    |----------------------------------------------------------------------
    | Gestión de usuarios — solo administrador
    |----------------------------------------------------------------------
    */
    Route::middleware('role:administrador')->group(function () {

        Route::apiResource('usuarios', UserController::class);

        // Acciones extra
        Route::patch('usuarios/{usuario}/toggle-activo', [UserController::class, 'toggleActivo'])
             ->name('usuarios.toggle-activo');
    });

    /*
    |----------------------------------------------------------------------
    | Consultas por rol — administrador y entrenadora pueden ver
    |----------------------------------------------------------------------
    */
    Route::middleware('role:administrador,entrenadora')->group(function () {
        Route::get('usuarios-por-rol/{rol}', [UserController::class, 'porRol'])
             ->name('usuarios.por-rol');
    });
});