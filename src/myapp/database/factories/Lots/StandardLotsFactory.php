<?php

namespace Database\Factories\Lots;

use App\Models\Lots\Constants\LOT_TYPE;
use Illuminate\Database\Eloquent\Factories\Factory;

class StandardLotsFactory extends Factory
{
    public function definition(): array
    {
        return [
            'row'           => fake()->numberBetween(0, 10),
            'column'        => fake()->numberBetween(0, 10),
            'type'          => LOT_TYPE::STANDARD,
            'is_available'  => fake()->boolean(),
            'created_at'    => fake()->dateTime(),
            'updated_at'    => fake()->dateTime(),
        ];
    }
}
