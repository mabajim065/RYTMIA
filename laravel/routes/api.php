<?php
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConjuntoController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\MensajeController;
use Illuminate\Support\Facades\Route;

// Rutas públicas
Route::post('/login', [AuthController::class, 'login']);
// Recuperación de contraseña
Route::post('/password/forgot', [AuthController::class, 'sendResetLinkEmail']);
Route::post('/password/reset',  [AuthController::class, 'resetPassword']);

// Rutas protegidas q necesitan token Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Cerrar sesión
    Route::post('/logout', [AuthController::class, 'logout']);

    // Obtener datos del usuario logueado
    Route::get('/me', [AuthController::class, 'me']);
    
    // Ver competiciones
    // Todos los roles pueden entrar, pero luego el controlador filtra
    Route::get('competiciones', [\App\Http\Controllers\Api\CompeticionController::class, 'index']);

    // Mensajería interna
    Route::get('mensajes', [MensajeController::class, 'index']);
    Route::post('mensajes', [MensajeController::class, 'store']);
    Route::patch('mensajes/{mensaje}/marcar-leido', [MensajeController::class, 'marcarLeido']);

    // Buscar usuarios por rol
    Route::get('usuarios-por-rol/{rol}', [UserController::class, 'porRol'])
         ->name('usuarios.por-rol');

    // Gestion de usuarios para solo lectura
    // Solo administrador y entrenadora pueden ver usuarios

    Route::middleware('role:administrador,entrenadora')->group(function () {
        // Ver lista de usuarios
        Route::get('usuarios', [UserController::class, 'index']);

        // Ver detalle de un usuario
        Route::get('usuarios/{usuario}', [UserController::class, 'show']);
    });

    // Gestión de usuarios de escritura
    // Solo administrador puede crear, editar o borrar usuarios

    Route::middleware('role:administrador')->group(function () {

        // Crear usuario
        Route::post('usuarios', [UserController::class, 'store']);

        // Editar usuario
        Route::put('usuarios/{usuario}', [UserController::class, 'update']);
        Route::patch('usuarios/{usuario}', [UserController::class, 'update']);

        // Eliminar usuario
        Route::delete('usuarios/{usuario}', [UserController::class, 'destroy']);
        
        // Activar o desactivar usuario
        Route::patch('usuarios/{usuario}/toggle-activo', [UserController::class, 'toggleActivo'])
             ->name('usuarios.toggle-activo');

        // Crear competición
        Route::post('competiciones', [\App\Http\Controllers\Api\CompeticionController::class, 'store']);

        // Crear conjunto
        Route::post('conjuntos', [ConjuntoController::class, 'store']);

        // Editar conjunto
        Route::put('conjuntos/{conjunto}', [ConjuntoController::class, 'update']);
        Route::patch('conjuntos/{conjunto}', [ConjuntoController::class, 'update']);

        // Eliminar conjunto
        Route::delete('conjuntos/{conjunto}', [ConjuntoController::class, 'destroy']);

        // Asignar entrenadora a un conjunto
        Route::post('conjuntos/{conjunto}/entrenadores', [ConjuntoController::class, 'asignarEntrenadora']);

        // Quitar entrenadora de un conjunto
        Route::delete('conjuntos/{conjunto}/entrenadores/{entrenadorId}', [ConjuntoController::class, 'desasignarEntrenadora']);

        // Reemplazar todas las entrenadoras del conjunto
        Route::put('conjuntos/{conjunto}/entrenadores/sync', [ConjuntoController::class, 'sincronizarEntrenadores']);
    });

    // Consultar y asignar gimnastas
    // Administrador y entrenadora pueden gestionar conjuntos y gimnastas

    Route::middleware('role:administrador,entrenadora')->group(function () {

        //Ver categorías ordenadas
        Route::get('categorias', function () {
            return response()->json(\App\Models\Categoria::orderBy('nombre')->get());
        });

        //Ver conjuntos
        Route::get('conjuntos', [ConjuntoController::class, 'index']);

        //Ver conjuntos de un club concreto
        Route::get('conjuntos/por-club/{clubId}', [ConjuntoController::class, 'porClub']);

        //Ver detalle de un conjunto
        Route::get('conjuntos/{conjunto}', [ConjuntoController::class, 'show']);

        //Asignar gimnasta a un conjunto
        Route::post('conjuntos/{conjunto}/gimnastas', [ConjuntoController::class, 'asignarGimnasta']);

        //Quitar gimnasta de un conjunto
        Route::delete('conjuntos/{conjunto}/gimnastas/{gimnastaId}', [ConjuntoController::class, 'desasignarGimnasta']);

        //Reemplazar todas las gimnastas del conjunto
        Route::put('conjuntos/{conjunto}/gimnastas/sync', [ConjuntoController::class, 'sincronizarGimnastas']);
    });
});