<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AppointmentController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [AppointmentController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rutas de citas
    Route::resource('citas', AppointmentController::class);
    Route::patch('citas/{cita}/cancel', [AppointmentController::class, 'cancel'])->name('citas.cancel');
    Route::get('api/available-hours', [AppointmentController::class, 'availableHours'])->name('citas.available-hours');
});

require __DIR__.'/auth.php';
