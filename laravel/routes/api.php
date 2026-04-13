<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConjuntoController;
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

        // CRUD conjuntos
        Route::post  ('conjuntos',            [ConjuntoController::class, 'store']);
        Route::put   ('conjuntos/{conjunto}', [ConjuntoController::class, 'update']);
        Route::patch ('conjuntos/{conjunto}', [ConjuntoController::class, 'update']);
        Route::delete('conjuntos/{conjunto}', [ConjuntoController::class, 'destroy']);

        // Gestión de entrenadoras en un conjunto
        Route::post  ('conjuntos/{conjunto}/entrenadores',                    [ConjuntoController::class, 'asignarEntrenadora']);
        Route::delete('conjuntos/{conjunto}/entrenadores/{entrenadorId}',     [ConjuntoController::class, 'desasignarEntrenadora']);
        Route::put   ('conjuntos/{conjunto}/entrenadores/sync',               [ConjuntoController::class, 'sincronizarEntrenadores']);
    });

    /*
    |----------------------------------------------------------------------
    | Consultas y asignaciones — administrador y entrenadora
    |----------------------------------------------------------------------
    */
    Route::middleware('role:administrador,entrenadora')->group(function () {

        // Consulta usuarios por rol
        Route::get('usuarios-por-rol/{rol}', [UserController::class, 'porRol'])
             ->name('usuarios.por-rol');

        // Competiciones
        Route::get('competiciones', [\App\Http\Controllers\Api\CompeticionController::class, 'index']);

        // Categorías
        Route::get('categorias', function () {
            return response()->json(\App\Models\Categoria::orderBy('nombre')->get());
        });

        // Consulta de conjuntos (lectura)
        Route::get('conjuntos',                    [ConjuntoController::class, 'index']);
        Route::get('conjuntos/por-club/{clubId}',  [ConjuntoController::class, 'porClub']);
        Route::get('conjuntos/{conjunto}',         [ConjuntoController::class, 'show']);

        // Asignación de gimnastas a conjuntos
        Route::post  ('conjuntos/{conjunto}/gimnastas',              [ConjuntoController::class, 'asignarGimnasta']);
        Route::delete('conjuntos/{conjunto}/gimnastas/{gimnastaId}', [ConjuntoController::class, 'desasignarGimnasta']);
        Route::put   ('conjuntos/{conjunto}/gimnastas/sync',         [ConjuntoController::class, 'sincronizarGimnastas']);
    });
});