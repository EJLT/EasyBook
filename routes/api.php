<?php

use App\Http\Controllers\Auth\TokenController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessReservationController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\NotificationController;

// Rutas públicas
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);



Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Rutas protegidas para usuarios
Route::middleware(['auth:api', 'role:user'])->prefix('user')->group(function () {
    Route::get('/businesses', [BusinessController::class, 'index']); // Ver negocios
    Route::post('/reservations', [ReservationController::class, 'store']); // Crear reserva
    Route::get('/reservations', [ReservationController::class, 'index']); // Listar reservas
    Route::get('/reservations/{id}', [ReservationController::class, 'show']); // Ver detalles
    Route::put('/reservations/{id}', [ReservationController::class, 'update']); // Actualizar reserva
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']); // Borrar reserva
});

// Rutas protegidas para propietarios
Route::middleware(['auth:api', 'role:owner'])->prefix('owner')->group(function () {
    Route::post('/businesses', [BusinessController::class, 'store']); // Crear negocio
    Route::get('/businesses', [BusinessController::class, 'index']); // Ver negocios
    Route::get('/businesses/{id}', [BusinessController::class, 'show']); // Ver negocio específico
    Route::put('/businesses/{id}', [BusinessController::class, 'update']); // Actualizar negocio
    Route::delete('/businesses/{id}', [BusinessController::class, 'destroy']); // Borrar negocio
    Route::get('/businesses/{businessId}/reservations', [BusinessReservationController::class, 'index']); // Ver reservas del negocio
    Route::post('/reservations/{id}/confirm', [BusinessReservationController::class, 'confirm']); // Confirmar reserva
    Route::post('/reservations/{id}/cancel', [BusinessReservationController::class, 'cancel']); // Cancelar reserva
    Route::get('/reservations/stats', [BusinessReservationController::class, 'stats']); // Estadísticas de reservas
});
// Notificaciones
Route::middleware(['auth:api'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'read']);
});
