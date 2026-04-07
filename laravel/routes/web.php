<?php

use Illuminate\Support\Facades\Route;

// Ruta raíz → login
Route::get('/', function () {
    return view('welcome');
});

// Dashboard admin → vista Blade con el equipo técnico
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