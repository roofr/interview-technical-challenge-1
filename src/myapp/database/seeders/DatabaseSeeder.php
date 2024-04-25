<?php

namespace Database\Seeders;

use App\Models\Lots\SmallLots;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $column = 1;
        do {
            SmallLots::factory()->create([
                'row'           => 1,
                'column'        => $column++
            ]);
        } while ($column <= 50);

        do {
            SmallLots::factory()->create([
                'row'           => 1,
                'column'        => $column++
            ]);
        } while ($column <= 100);
    }
}
