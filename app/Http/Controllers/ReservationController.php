<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'date' => 'required|date',
            'time' => 'required',
        ]);

        $reservation = Reservation::create([
            'user_id' => Auth::id(),
            'business_id' => $request->business_id,
            'date' => $request->date,
            'time' => $request->time,
            'status' => 'pending', // Puedes cambiar esto según sea necesario
        ]);

        dd($reservation);

        return response()->json($reservation, 201);
    }
    //Obtener todas las reservas
    public function index()
    {
        $reservations = Reservation::where('user_id', Auth::id())->get();
        return response()->json($reservations);
    }
    //Obtener una reserva especifica
    public function show($id)
    {
        $reservation = Reservation::findOrFail($id);
        return response()->json($reservation);
    }

    //Actualizar una reserva
    public function update(Request $request, $id)
    {
        $request->validate([
            'business_id' => 'sometimes|required|exists:businesses,id',
            'date' => 'sometimes|required|date',
            'time' => 'sometimes|required',
            'status' => 'sometimes|required|string',
        ]);

        $reservation = Reservation::findOrFail($id);
        $reservation->update($request->all());

        return response()->json($reservation);
    }

    //Eliminar una reserva
    public function destroy($id)
    {
        $reservation = Reservation::findOrFail($id);
        $reservation->delete();

        return response()->json(['message' => 'Reservation deleted successfully.']);
    }


    public function reservationsByBusiness($businessId)
    {
        $reservations = Reservation::where('business_id', $businessId)->get();
        return response()->json($reservations);
    }



}
