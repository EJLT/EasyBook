<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    // Método para obtener las notificaciones de un usuario
    public function index()
    {
        $user = auth()->user(); // Obtiene el usuario autenticado
        $notifications = $user->notifications; // Obtiene todas las notificaciones del usuario

        return response()->json($notifications); // Retorna las notificaciones como una respuesta JSON
    }

    // Método para leer una notificación específica (puedes marcarla como leída, por ejemplo)
    public function read($id)
    {
        $user = auth()->user();
        $notification = $user->notifications()->findOrFail($id); // Encuentra la notificación por ID

        $notification->markAsRead(); // Marca la notificación como leída

        return response()->json($notification); // Retorna la notificación leída
    }
}
