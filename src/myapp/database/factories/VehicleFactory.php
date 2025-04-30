<?php

namespace Database\Factories;

use App\Enums\VehicleType;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

class VehicleFactory extends Factory
{
    protected $model = Vehicle::class;

    public function definition(): array
    {
        return [
            'plate' => strtoupper($this->faker->bothify('???####')),
            'type' => VehicleType::CAR,
        ];
    }

    public function van()
    {
        return $this->state(fn () => ['type' => 'VAN']);
    }

    public function car()
    {
        return $this->state(fn () => ['type' => 'CAR']);
    }
}
