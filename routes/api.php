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

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rutas públicas para registro e inicio de sesión
Route::post('/login', [LoginController::class, 'login'])->name('login');
Route::post('/register', [RegisterController::class, 'register'])->name('register');



Route::middleware('auth:api')->group(function () {
    // Rutas para usuarios
    Route::prefix('user')->group(function () {
        Route::get('/businesses', [BusinessController::class, 'index']); // Ver negocios
        Route::post('/reservations', [ReservationController::class, 'store']); // Crear reserva
        Route::get('/reservations', [ReservationController::class, 'index']); // Listar reservas del usuario
        Route::get('/reservations/{id}', [ReservationController::class, 'show']); // Ver una reserva
        Route::put('/reservations/{id}', [ReservationController::class, 'update']); // Actualizar una reserva
        Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']); // Eliminar una reserva
    });

    Route::middleware('auth:business_owner')->group(function () {
        Route::prefix('business-owner')->group(function () {
            Route::post('/businesses', [BusinessController::class, 'store']); // Crear negocio
            Route::get('/businesses', [BusinessController::class, 'index']); // Ver todos los negocios
            Route::get('/businesses/{id}', [BusinessController::class, 'show']); // Ver un negocio específico
            Route::put('/businesses/{id}', [BusinessController::class, 'update']); // Actualizar negocio
            Route::delete('/businesses/{id}', [BusinessController::class, 'destroy']); // Eliminar negocio

            // Gestión de reservas del negocio
            Route::get('/businesses/{businessId}/reservations', [BusinessReservationController::class, 'index']); // Listar reservas de un negocio
            Route::post('/reservations/{id}/confirm', [BusinessReservationController::class, 'confirm']); // Confirmar reserva
            Route::post('/reservations/{id}/cancel', [BusinessReservationController::class, 'cancel']); // Cancelar reserva
            Route::get('/reservations/stats', [BusinessReservationController::class, 'stats']); // Obtener estadísticas de reservas
        });
    });

    // Rutas para propietarios
    Route::prefix('owner')->middleware('auth:api')->group(function () {
        Route::get('/owners', [OwnerController::class, 'index']);   // Listar todos los propietarios
        Route::post('/owners', [OwnerController::class, 'store']);  // Crear un nuevo propietario
        Route::put('/owners/{id}', [OwnerController::class, 'update']);  // Actualizar un propietario
        Route::delete('/owners/{id}', [OwnerController::class, 'destroy']);  // Eliminar un propietario
    });


    //Rutas de las notificaciones
    Route::middleware('auth:api')->group(function () {
        // Obtener todas las notificaciones
        Route::get('/user/notifications', [NotificationController::class, 'index']);
        // Marcar una notificación como leída
        Route::put('/user/notifications/{id}/read', [NotificationController::class, 'read']);
    });

    Route::middleware('auth:api')->group(function () {
        // Ruta para obtener la información del usuario autenticado
        Route::get('user', [UserController::class, 'show']);
        // Ruta para actualizar la información del usuario autenticado
        Route::put('user', [UserController::class, 'update']);
    });
});
