<?php

namespace Database\Factories;

use app\Enums\ParkSpotType;
use App\Models\ParkingSpot;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParkingSpotFactory extends Factory
{
    protected $model = ParkingSpot::class;

    public function definition(): array
    {
        return [
            'identifier' => $this->faker->unique()->numberBetween(1, 100),
            'type' => ParkSpotType::REGULAR->value,
            'vehicle_id' => null,
        ];
    }
}
