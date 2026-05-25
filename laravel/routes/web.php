<?php

use Illuminate\Support\Facades\Route;

// Ruta raíz → login
Route::get('/', function () {
    return view('welcome');
});

// Dashboard admin
Route::get('/dashboard/admin', function () {
    return view('dashboard.admin');
});

// Dashboard entrenadora
Route::get('/dashboard/entrenadora', function () {
    return view('dashboard.entrenadora');
});

// Dashboard gimnasta
Route::get('/dashboard/gimnasta', function () {
    return view('dashboard.gimnasta');
});

// Recuperar contraseña
Route::get('/recuperar-password', function (Illuminate\Http\Request $request) {
    return view('recuperar-password', [
        'token' => $request->query('token'),
        'email' => $request->query('email')
    ]);
});