<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Schedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ScheduleSeeder extends Seeder
{
    public function run()
    {
        $businesses = Business::all();
        foreach ($businesses as $business) {
            for ($i = 0; $i < 30; $i++) { // 30 días
                for ($hour = 9; $hour <= 17; $hour++) { // De 9 AM a 5 PM
                    Schedule::create([
                        'business_id' => $business->id,
                        'date' => now()->addDays($i)->format('Y-m-d'),
                        'time' => "{$hour}:00:00",
                        'is_booked' => false, // Puede ser true si está reservado
                    ]);
                }
            }
        }
    }
}
