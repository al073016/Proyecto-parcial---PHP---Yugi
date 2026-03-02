<?php

use Illuminate\Support\Facades\Route;

// --- Documentación Swagger ---
Route::get('/docs', function () {
    return file_get_contents(public_path('docs/index.html'));
});

Route::get('/docs/swagger.json', function () {
    return response()->file(public_path('docs/swagger.json'), [
        'Content-Type' => 'application/json',
    ]);
});

// --- Vistas de Autenticación ---
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::get('/register', function () {
    return view('register'); // Asegúrate de que el archivo se llame register.blade.php
})->name('register');

// --- Vistas de la Aplicación ---
Route::get('/catalogo', function () {
    return view('catalogo');
});

Route::view('/admin/dashboard', 'admin.dashboard');