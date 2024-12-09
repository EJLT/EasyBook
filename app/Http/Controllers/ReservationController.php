<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\ReservationNotification;

class ReservationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'business_id' => 'required|exists:businesses,id',
            'date' => 'required|date',
            'time' => 'required|date_format:H:i',
        ]);

        $now = Carbon::now('Europe/Madrid');
        $reservationDateTime = Carbon::createFromFormat('Y-m-d H:i', $request->date . ' ' . $request->time, 'Europe/Madrid');

        if ($reservationDateTime->isBefore($now)) {
            return response()->json(['message' => 'You cannot reserve a time in the past.'], 400);
        }

        $reservation = new Reservation([
            'user_id' => Auth::id(),
            'business_id' => $request->business_id,
            'date' => $request->date,
            'time' => $request->time . ':00',
            'status' => 'pending',
        ]);
        $reservation->save();

        // Enviar correo al usuario
        Mail::to(Auth::user()->email)->send(new ReservationNotification($reservation, 'created'));

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
        // Obtener la hora actual en la zona horaria de Europa/Madrid
        $now = Carbon::now('Europe/Madrid');

        // Filtrar reservas futuras o pendientes del usuario autenticado
        $reservations = Reservation::with('business') // Eager load de los negocios
        ->where('user_id', Auth::id())
            ->where(function ($query) use ($now) {
                $query->where('date', '>', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->where('date', '=', $now->toDateString())
                            ->where('time', '>', $now->toTimeString());
                    });
            })
            ->get();

        // Formatear las reservas
        $formattedReservations = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'status' => $reservation->status,
                'business_name' => $reservation->business->name,
                'user_name' => $reservation->user->name,
                'date' => $reservation->date,
                'time' => $reservation->time,
            ];
        });

        return response()->json($formattedReservations);
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

    public function destroy($id)
    {
        // Buscar la reserva por su ID
        $reservation = Reservation::where('user_id', Auth::id())->findOrFail($id);

        // Eliminar la reserva
        $reservation->delete();

        // Retornar una respuesta de éxito
        return response()->json(['message' => 'Reservation deleted successfully.'], 200);
    }


    public function reservationsByBusiness($businessId)
    {
        $reservations = Reservation::where('business_id', $businessId)->get();
        return response()->json($reservations);
    }

    public function sendTestEmail()
    {
        Mail::raw('This is a test email.', function ($message) {
            $message->to('eduardikolaborda@gmail.com')  // Cambia a tu correo electrónico
            ->subject('Test Email');
        });

        return response()->json(['message' => 'Test email sent']);
    }

    public function history()
    {
        // Obtener todas las reservas del usuario autenticado
        $reservations = Reservation::with('business') // Eager load del negocio
        ->where('user_id', Auth::id()) // Filtrar por el usuario autenticado
        ->get();

        // Formatear las reservas
        $formattedReservations = $reservations->map(function ($reservation) {
            return [
                'id' => $reservation->id,
                'status' => $reservation->status,
                'business_name' => $reservation->business->name,
                'user_name' => $reservation->user->name,
                'date' => $reservation->date,
                'time' => $reservation->time,
            ];
        });

        return response()->json($formattedReservations);

    }
}
