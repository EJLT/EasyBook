<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        // Obtener la hora actual en Europa/Madrid
        $now = Carbon::now('Europe/Madrid');

        // Crear un objeto Carbon con la fecha y hora proporcionadas por el usuario
        $reservationDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time, 'Europe/Madrid');

        // Verificar si la fecha y hora de la reserva son en el pasado
        if ($reservationDateTime->isBefore($now)) {
            return response()->json(['message' => 'You cannot reserve a time in the past.'], 400);
        }

        // Crear la nueva reserva
        $reservation = new Reservation([
            'user_id' => Auth::id(),
            'business_id' => $request->business_id,
            'date' => $request->date,
            'time' => $request->time . ':00',
            'status' => 'pending',
        ]);

        // Guardar la reserva en la base de datos
        $reservation->save();

        // Retornar la reserva creada con un estado 201
        return response()->json([
            'id' => $reservation->id,
            'status' => $reservation->status,
            'business_name' => $reservation->business->name,
            'user_name' => $reservation->user->name,
            'date' => $reservation->date,
            'time' => $reservation->time
        ], 201);
    }

    // Obtener todas las reservas
    public function index()
    {
        $reservations = Reservation::with('business') // Eager load de los negocios
        ->where('user_id', Auth::id())
            ->get();


        $reservations = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'status' => $reservation->status,
                'business_name' => $reservation->business->name,
                'user_name' => $reservation->user->name,
                'date' => $reservation->date,
                'time' => $reservation->time,
            ];
        });

        return response()->json($reservations);
    }

    // Obtener una reserva específica
    public function show($id)
    {
        // Obtener la reserva especificada por el ID
        $reservation = Reservation::with('business') // Eager load del negocio relacionado
        ->where('user_id', Auth::id()) // Asegurarse de que la reserva pertenece al usuario autenticado
        ->findOrFail($id);

        // Devolver la información relevante de la reserva junto con el negocio
        return response()->json([
            'id' => $reservation->id,
            'status' => $reservation->status,
            'business_name' => $reservation->business->name, // Nombre del negocio relacionado
            'reservation_date' => $reservation->date, // Fecha de la reserva
            'reservation_time' => $reservation->time, // Hora de la reserva
        ]);
    }


    // Actualizar una reserva
    public function update(Request $request, $id)
    {
        $request->validate([
            'business_id' => 'sometimes|required|exists:businesses,id',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required|date_format:H:i',
        ]);

        // Obtener la reserva
        $reservation = Reservation::findOrFail($id);

        // Obtener la hora actual en Europa/Madrid
        $now = Carbon::now('Europe/Madrid');

        // Verificar si se está intentando actualizar la fecha o la hora
        if ($request->has('date') || $request->has('time')) {
            $newDate = $request->date ?? $reservation->date;
            $newTime = $request->time ?? $reservation->time;

            // Crear un objeto Carbon con la nueva fecha y hora
            $newDateTime = Carbon::createFromFormat('Y-m-d H:i', $newDate . ' ' . $newTime, 'Europe/Madrid');

            // Verificar si la nueva fecha y hora están en el pasado
            if ($newDateTime->isBefore($now)) {
                return response()->json(['message' => 'You cannot update a reservation to a past time.'], 400);
            }

            // Actualizar la fecha y hora
            $reservation->date = $newDate;
            $reservation->time = $newTime . ':00';
        }
        // Actualizar otros campos si se proporcionan
        if ($request->has('business_id')) {
            $reservation->business_id = $request->business_id;
        }

        $reservation->save();

        // Retornar la reserva actualizada
        return response()->json($reservation);
    }


    public function reservationsByBusiness($businessId)
    {
        $reservations = Reservation::where('business_id', $businessId)->get();
        return response()->json($reservations);
    }

}
