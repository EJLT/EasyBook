<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function getScheduleForBusiness($businessId, $date)
    {
        // Recuperamos los horarios para el negocio y la fecha indicada
        $schedules = Schedule::where('business_id', $businessId)
            ->whereDate('date', $date)
            ->get();

        // Devolvemos los horarios como respuesta JSON
        return response()->json($schedules);
    }
}
