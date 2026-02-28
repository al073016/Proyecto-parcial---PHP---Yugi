<?php

use Illuminate\Support\Facades\Route;

// Documentación Swagger de la API
// Accesible en: http://127.0.0.1:8000/docs
Route::get('/docs', function () {
    return file_get_contents(public_path('docs/index.html'));
});

Route::get('/docs/swagger.json', function () {
    return response()->file(public_path('docs/swagger.json'), [
        'Content-Type' => 'application/json',
    ]);
});
