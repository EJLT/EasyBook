<?php

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

// Rutas públicas para registro e inicio de sesión
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'register'])->name('register');

// Rutas para usuarios (accesibles solo si están autenticados)
Route::middleware('auth:api')->prefix('user')->group(function () {
    Route::get('/businesses', [BusinessController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::put('/reservations/{id}', [ReservationController::class, 'update']);
    Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);
});

// Rutas para propietarios de negocios (con rol 'owner', accesibles solo si son propietarios)
Route::middleware(['auth:api', 'role:owner'])->prefix('owner')->group(function () {
    Route::post('/businesses', [BusinessController::class, 'store']);
    Route::get('/businesses', [BusinessController::class, 'index']);
    Route::get('/businesses/{id}', [BusinessController::class, 'show']);
    Route::put('/businesses/{id}', [BusinessController::class, 'update']);
    Route::delete('/businesses/{id}', [BusinessController::class, 'destroy']);
    Route::get('/businesses/{businessId}/reservations', [BusinessReservationController::class, 'index']);
    Route::post('/reservations/{id}/confirm', [BusinessReservationController::class, 'confirm']);
    Route::post('/reservations/{id}/cancel', [BusinessReservationController::class, 'cancel']);
    Route::get('/reservations/stats', [BusinessReservationController::class, 'stats']);
});

// Rutas para gestionar propietarios (solo accesibles por admin u otro rol autorizado)
Route::middleware(['auth:api', 'role:owner'])->prefix('owner')->group(function () {
    Route::get('/owners', [OwnerController::class, 'index']);
    Route::post('/owners', [OwnerController::class, 'store']);
    Route::put('/owners/{id}', [OwnerController::class, 'update']);
    Route::delete('/owners/{id}', [OwnerController::class, 'destroy']);
});

// Rutas de las notificaciones (accesibles solo si el usuario está autenticado)
Route::middleware('auth:api')->group(function () {
    Route::get('/user/notifications', [NotificationController::class, 'index']);
    Route::put('/user/notifications/{id}/read', [NotificationController::class, 'read']);
});

// Rutas para la información del usuario (accesibles solo si el usuario está autenticado)
Route::middleware('auth:api')->group(function () {
    Route::get('user', [UserController::class, 'show']);
    Route::put('user', [UserController::class, 'update']);
});
