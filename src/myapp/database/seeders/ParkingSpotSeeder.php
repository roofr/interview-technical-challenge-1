<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParkingSpot;

class ParkingSpotSeeder extends Seeder
{
    public function run(): void
    {
        for ($i = 1; $i <= 20; $i++) {
            ParkingSpot::create([
                'identifier' => $i,
                'type' => 'motorcycle',
            ]);
        }

        for ($i = 21; $i <= 100; $i++) {
            ParkingSpot::create([
                'identifier' => $i,
                'type' => 'regular',
            ]);
        }
    }
}
