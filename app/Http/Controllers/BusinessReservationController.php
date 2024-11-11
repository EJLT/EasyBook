<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Business;
use Illuminate\Support\Facades\Auth;

class BusinessReservationController extends Controller
{
    public function index($businessId)
    {
        $business = Business::findOrFail($businessId);

        if ($business->owner_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $reservations = $business->reservations;
        return response()->json($reservations);
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
}
