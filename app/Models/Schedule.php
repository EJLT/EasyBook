<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'date', 'time', 'is_booked'];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }


    public function getSchedule($businessId)
    {
        $schedules = Schedule::where('business_id', $businessId)
            ->orderBy('date')
            ->orderBy('time')
            ->get()
            ->groupBy('date')
            ->map(function ($daySchedules) {
                return $daySchedules->map(function ($schedule) {
                    return $schedule->is_booked ? "Ocupado" : $schedule->time;
                });
            });

        return response()->json($schedules);
    }
}
