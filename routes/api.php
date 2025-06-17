<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;

// Rutas de autenticación
Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});

// Rutas de citas
Route::prefix('citas')->middleware('auth:api')->group(function () {
    Route::get('/', [AppointmentController::class, 'index']);
    Route::post('/', [AppointmentController::class, 'store']);
    Route::get('/{id}', [AppointmentController::class, 'show']);
    Route::put('/{id}', [AppointmentController::class, 'update']);
    Route::delete('/{id}', [AppointmentController::class, 'destroy']);
    Route::patch('/{id}/cancel', [AppointmentController::class, 'cancel']);
    Route::get('/available-hours', [AppointmentController::class, 'availableHours']);
    Route::get('/admin/statistics', [AppointmentController::class, 'statistics']);
});
