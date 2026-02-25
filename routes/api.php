<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\LoanController;


Route::post('/loans', [LoanController::class, 'store']);
Route::put('/loans/{id}', [LoanController::class, 'update']);

// Ruta para obtener los 50 objetos
Route::get('/items', [ItemController::class, 'index']);

// Ruta para ver el detalle de uno solo
Route::get('/items/{id}', [ItemController::class, 'show']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
