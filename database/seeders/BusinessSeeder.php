<?php

namespace Database\Seeders;

use App\Models\Business;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessSeeder extends Seeder
{
    public function run()
    {
        Business::create([
            'name' => 'Negocio de Ejemplo',
            'address' => 'Calle Falsa 123',
            'owner_id' => 1,
            'phone' => '123456789',
        ]);
    }
}
