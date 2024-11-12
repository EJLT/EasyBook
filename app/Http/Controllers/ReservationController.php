<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Notifications\ReservationStatusUpdated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i:s',
        ]);

        // Crear la nueva reserva
        $reservation = new Reservation([
            'user_id' => Auth::id(),
            'business_id' => $request->business_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending',
        ]);

        // Guardar la reserva en la base de datos
        $reservation->save();

        // Enviar la notificación de creación de reserva
        $reservation->user->notify(new ReservationStatusUpdated($reservation));

        // Retornar la reserva creada con un estado 201
        return response()->json($reservation, 201);
    }

    // Obtener todas las reservas
    public function index()
    {
        $reservations = Reservation::where('user_id', Auth::id())->get();
        return response()->json($reservations);
    }

    // Obtener una reserva específica
    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    // Actualizar una reserva
    public function update(Request $request, $id)
    {
        $request->validate([
            'business_id' => 'sometimes|required|exists:businesses,id',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required',
        ]);

        // Obtener la reserva
        $reservation = Reservation::findOrFail($id);

        // Actualizar la reserva con los datos proporcionados
        $reservation->update($request->all());

        // Enviar la notificación de actualización de reserva
        $reservation->user->notify(new ReservationStatusUpdated($reservation));

        // Retornar la reserva actualizada
        return response()->json($reservation);
    }

    // Eliminar una reserva
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        // Enviar la notificación de eliminación de reserva
        $reservation->user->notify(new ReservationStatusUpdated($reservation));

        return response()->json(['message' => 'Reservation deleted successfully.']);
    }

    public function reservationsByBusiness($businessId)
    {
        $reservations = Reservation::where('business_id', $businessId)->get();
        return response()->json($reservations);
    }
}
