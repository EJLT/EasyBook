<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Reservation;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BusinessReservationController extends Controller
{
    public function index($businessId)
    {
        $business = Business::findOrFail($businessId);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // Obtener la hora actual en la zona horaria de Europa/Madrid
        $now = Carbon::now('Europe/Madrid');

        // Filtrar reservas futuras o actuales correctamente
        $reservations = Reservation::where('business_id', $businessId)
            ->where(function ($query) use ($now) {
                $query->where('date', '>', $now->toDateString())
                    ->orWhere(function ($subQuery) use ($now) {
                        $subQuery->where('date', '=', $now->toDateString())
                            ->where('time', '>', $now->toTimeString());
                    });
            })
            ->get();

        // Mapear las reservas y extraer el nombre del usuario
        $reservationsWithUserName = $reservations->map(function ($reservation) {
            $reservation->user_name = $reservation->user->name;
            unset($reservation->user);
            return $reservation;
        });

        return response()->json($reservationsWithUserName);
    }


    public function confirm($id)
    {
        $reservation = Reservation::findOrFail($id);
        $business = $reservation->business;

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $reservation->status = 'confirmed';
        $reservation->save();

        return response()->json(['message' => 'Reservation confirmed successfully.']);
    }

    public function cancel($id)
    {
        $reservation = Reservation::findOrFail($id);
        $business = $reservation->business;

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $reservation->status = 'cancelled';
        $reservation->save();

        return response()->json(['message' => 'Reservation cancelled successfully.']);
    }

    public function stats($businessId)
    {
        $business = Business::findOrFail($businessId);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $confirmedCount = $business->reservations()->where('status', 'confirmed')->count();
        $cancelledCount = $business->reservations()->where('status', 'cancelled')->count();
        $pendingCount = $business->reservations()->where('status', 'pending')->count();

        $stats = [
            'confirmed' => $confirmedCount,
            'cancelled' => $cancelledCount,
            'pending' => $pendingCount,
            'total' => $confirmedCount + $cancelledCount + $pendingCount,
        ];

        return response()->json($stats);
    }

    public function confirmAll($businessId)
    {
        $business = Business::findOrFail($businessId);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        DB::transaction(function () use ($business) {
            $reservations = $business->reservations()->where('status', 'pending')->get();

            foreach ($reservations as $reservation) {
                $reservation->status = 'confirmed';
                $reservation->save();
            }
        });

        return response()->json(['message' => 'Todas las reservas han sido confirmadas.']);
    }
}
