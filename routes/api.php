<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LoanController;

/*
|--------------------------------------------------------------------------
| API Routes - Sistema de Préstamos
|--------------------------------------------------------------------------
|
| Fase 3: Autenticación con Sanctum
| Fase 4: Roles (admin / alumno)
| Fase 5: Relaciones avanzadas y lógica de bloqueo
|
*/

// ──────────────────────────────────────────────
// RUTAS PÚBLICAS (sin autenticación)
// ──────────────────────────────────────────────

// Autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// Catálogo público de objetos (cualquiera puede ver qué hay disponible)
Route::get('/items',      [ItemController::class, 'index']);
Route::get('/items/{id}', [ItemController::class, 'show']);


// ──────────────────────────────────────────────
// RUTAS PROTEGIDAS (requieren token de Sanctum)
// ──────────────────────────────────────────────

Route::middleware('auth:sanctum')->group(function () {

    // Sesión
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── PRÉSTAMOS (alumno y admin) ──
    // Cualquier usuario autenticado puede solicitar préstamos y ver su historial
    Route::get('/loans',         [LoanController::class, 'index']);
    Route::post('/loans',        [LoanController::class, 'store']);
    Route::put('/loans/{id}',    [LoanController::class, 'update']); // Devolver objeto

    // ── RUTAS EXCLUSIVAS DEL ADMINISTRADOR ──
    Route::middleware('es_admin')->group(function () {

        // Gestión de inventario (solo admin puede agregar/editar/eliminar objetos)
        Route::post('/items',                                 [ItemController::class, 'store']);
        Route::put('/items/{id}',                             [ItemController::class, 'update']);
        Route::delete('/items/{id}',                          [ItemController::class, 'destroy']);
        Route::post('/items/{id}/completar-mantenimiento',    [ItemController::class, 'completarMantenimiento']);

        // Dashboard de riesgo (resumen general del sistema)
        Route::get('/dashboard', [LoanController::class, 'dashboard']);
    });
});
