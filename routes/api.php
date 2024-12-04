<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\BusinessReservationController;
use App\Http\Controllers\NotificationController;

// Rutas públicas
Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);



Route::post('/login', [LoginController::class, 'login']);
Route::post('/register', [RegisterController::class, 'register']);

// Rutas protegidas para usuarios
Route::middleware(['auth:api', 'role:user'])->prefix('user')->group(function () {
    Route::get('/businesses', [BusinessController::class, 'index']); // Ver negocios
    Route::get('/user/businesses/{id}', [BusinessController::class, 'show']);
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
    Route::post('/businesses/{businessId}/reservations/confirm-all', [BusinessReservationController::class, 'confirmAll']);
    Route::get('/reservations/stats/{businessId}', [BusinessReservationController::class, 'stats']); // Estadísticas de reservas
});
// Notificaciones
Route::middleware(['auth:api'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::put('/notifications/{id}/read', [NotificationController::class, 'read']);
});


// Rutas protegidas para administradores

//FALTA POR IMPLEMENTAR EL ROL DE ADMINISTRADOR

// Mostrar todas las categorías
    Route::get('/categories', [CategoryController::class, 'index']);
// Crear una nueva categoría
    Route::post('/categories', [CategoryController::class, 'store']);
// Mostrar una categoría específica
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
// Actualizar una categoría existente
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
// Eliminar una categoría
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);


