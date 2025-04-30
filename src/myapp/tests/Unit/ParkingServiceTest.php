<?php

namespace Tests\Unit\Services;

use App\Enums\ParkSpotType;
use App\Enums\VehicleType;
use App\Exceptions\ApiException;
use App\Models\ParkingSpot;
use App\Models\Vehicle;
use App\Services\ParkingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ParkingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ParkingService();
    }

    public function test_get_parking_lot_returns_mapped_data()
    {
        ParkingSpot::factory()->count(3)->create();

        $lot = $this->service->getParkingLot();

        $this->assertCount(3, $lot);
        $this->assertArrayHasKey('identifier', $lot->first());
    }

    public function test_unpark_nonexistent_spot_throws_exception()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Parking spot not found');

        $this->service->unpark(999);
    }

    public function test_unpark_empty_spot_throws_exception()
    {
        $spot = ParkingSpot::factory()->create();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Parking spot is already empty');

        $this->service->unpark($spot->id);
    }

    public function test_unpark_regular_vehicle_clears_spot()
    {
        $vehicle = Vehicle::factory()->create();
        $spot = ParkingSpot::factory()->create(['vehicle_id' => $vehicle->id]);

        $this->service->unpark($spot->id);

        $this->assertNull($spot->fresh()->vehicle_id);
    }

    public function test_unpark_van_clears_all_related_spots()
    {
        $van = Vehicle::factory()->create(['type' => VehicleType::VAN]);
        $spots = ParkingSpot::factory()->count(3)->create(['vehicle_id' => $van->id]);

        $this->service->unpark($spots->first()->id);

        foreach ($spots as $spot) {
            $this->assertNull($spot->fresh()->vehicle_id);
        }
    }

    public function test_park_vehicle_to_nonexistent_spot_throws()
    {
        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Parking spot not found');

        $this->service->parkVehicle(VehicleType::CAR, 'ABC1234', 999);
    }

    public function test_park_vehicle_to_taken_spot_throws()
    {
        $spot = ParkingSpot::factory()->create();
        $vehicle = Vehicle::factory()->create();
        $spot->update(['vehicle_id' => $vehicle->id]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Parking spot is already taken');

        $this->service->parkVehicle(VehicleType::CAR, 'XYZ9999', $spot->id);
    }

    public function test_park_car_in_invalid_spot_type_throws()
    {
        $spot = ParkingSpot::factory()->create(['type' => ParkSpotType::MOTORCYCLE]);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('This spot is not for cars/vans');

        $this->service->parkVehicle(VehicleType::CAR, 'XYZ1234', $spot->id);
    }

    public function test_park_vehicle_already_parked_throws()
    {
        $vehicle = Vehicle::factory()->create();
        ParkingSpot::factory()->create(['vehicle_id' => $vehicle->id]);
        $spot = ParkingSpot::factory()->create();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage("This vehicle is already parked on");

        $this->service->parkVehicle($vehicle->type, $vehicle->plate, $spot->id);
    }

    public function test_park_van_with_insufficient_space_throws()
    {
        $spot = ParkingSpot::factory()->create();

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Not enough space for van');

        $this->service->parkVehicle(VehicleType::VAN, 'VAN1234', $spot->id);
    }

    public function test_successful_parking_for_car()
    {
        $spot = ParkingSpot::factory()->create(['type' => ParkSpotType::REGULAR]);

        $this->service->parkVehicle(VehicleType::CAR, 'CAR1234', $spot->id);

        $this->assertDatabaseHas('parking_spots', [
            'id' => $spot->id,
            'vehicle_id' => Vehicle::where('plate', 'CAR1234')->first()->id
        ]);
    }

    public function test_successful_parking_for_van()
    {
        $spots = ParkingSpot::factory()->count(3)->create(['type' => ParkSpotType::REGULAR]);
        $primarySpot = $spots->first();

        $this->service->parkVehicle(VehicleType::VAN, 'VAN9999', $primarySpot->id);

        $vehicleId = Vehicle::where('plate', 'VAN9999')->first()->id;

        foreach ($spots as $spot) {
            $this->assertEquals($vehicleId, $spot->fresh()->vehicle_id);
        }
    }
}
