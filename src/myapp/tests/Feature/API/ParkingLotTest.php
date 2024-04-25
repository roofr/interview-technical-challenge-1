<?php


namespace Tests\Controller\API;

use App\Models\Lots\SmallLots;
use App\Models\Lots\StandardLots;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class ParkingLotTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $column = 1;
        do {
            SmallLots::factory()->create([
                'row'           => 1,
                'column'        => $column++,
                'is_available'  => true,
            ]);
        } while ($column <= 4);


        SmallLots::factory()->create([
            'row'           => 1,
            'column'        => 5,
            'is_available'  => false,
        ]);

        $column = 1;
        do {
            StandardLots::factory()->create([
                'row'           => 2,
                'column'        => $column++,
                'is_available'  => true,
            ]);
        } while ($column <= 4);

        StandardLots::factory()->create([
            'row'           => 2,
            'column'        => 5,
            'is_available'  => false,
        ]);
    }

    /**
     * @return void
     */
    public function testParkingLotRequest(): void
    {
        $response = $this->getJson('api/parkingLot');
        $response->assertOk()
            ->assertJson([
                'motorcycle' => [
                    'count' => [
                        'available'     => 8,
                        'totalCapacity' => 10,
                    ],
                ],
                'car'       => [
                    'count' => [
                        'available'     => 4,
                        'totalCapacity' => 5,
                    ],
                ],
                'van'       => [
                    'count' => [
                        'available'     => 1,
                        'totalCapacity' => 1,
                    ],
                ],
            ]);
    }

    /**
     * @return void
     */
    public function testParkingLotMotorcycleRequest(): void
    {
        $response = $this->getJson('api/parkingLot/motorcycle');
        $response->assertOk()
            ->assertJson([
                'count' => [
                    'available'     => 8,
                    'totalCapacity' => 10,
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testParkingLotCarRequest(): void
    {
        $response = $this->getJson('api/parkingLot/car');
        $response->assertOk()
            ->assertJson([
                'count' => [
                    'available'     => 4,
                    'totalCapacity' => 5,
                ]
            ]);
    }

    /**
     * @return void
     */
    public function testParkingLotVanRequest(): void
    {
        $response = $this->getJson('api/parkingLot/van');
        $response->assertOk()
            ->assertJson([
                'count' => [
                    'available'     => 1,
                    'totalCapacity' => 1,
                ]
            ]);
    }
}
