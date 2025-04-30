<?php

namespace app\Services;

use App\Enums\ParkSpotType;
use App\Enums\VehicleType;
use app\Exceptions\ApiException;
use App\Models\ParkingSpot;
use App\Models\Vehicle;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ParkingService
{
    public function getParkingLot(): Collection
    {
        $spots = ParkingSpot::with('vehicle')->orderBy('identifier')->get();

        return $spots->map(function ($spot) {
            return [
                'id' => $spot->id,
                'identifier' => $spot->identifier,
                'type' => $spot->type,
                'occupied' => !is_null($spot->vehicle_id),
                'vehicle' => $spot->vehicle ? [
                    'id' => $spot->vehicle->id,
                    'plate' => $spot->vehicle->plate,
                    'type' => $spot->vehicle->type,
                ] : null
            ];
        });
    }

    public function unpark(int $spotId): void
    {
        $spot = ParkingSpot::find($spotId);

        if (!$spot) {
            throw new ApiException('Parking spot not found', Response::HTTP_NOT_FOUND);
        }

        if (!$spot->vehicle) {
            throw new ApiException('Parking spot is already empty');
        }

        if ($spot->vehicle->isVan()) {
            ParkingSpot::query()->where('vehicle_id', $spot->vehicle->id)->update(['vehicle_id' => null]);
        } else {
            $spot->vehicle_id = null;
            $spot->save();
        }
    }

    public function parkVehicle(VehicleType $vehicleType, string $vehiclePlate, int $spotId): void
    {
        DB::transaction(function () use ($vehicleType, $vehiclePlate, $spotId) {
            $vehicle = $this->createOrGetVehicle($vehiclePlate, $vehicleType);
            $this->ensureVehicleNotAlreadyParked($vehicle);

            $spot = ParkingSpot::where('id', $spotId)->lockForUpdate()->first();
            if (!$spot) {
                throw new ApiException('Parking spot not found', Response::HTTP_NOT_FOUND);
            }

            if ($spot->isOccupied()) {
                throw new ApiException('Parking spot is already taken');
            }

            if (($vehicle->isCar() || $vehicle->isVan()) && $spot->type !== ParkSpotType::REGULAR->value) {
                throw new ApiException('This spot is not for cars/vans');
            }

            if ($vehicle->isVan()) {
                $this->parkVan($vehicle, $spot);
                return;
            }

            $spot->vehicle_id = $vehicle->id;
            $spot->save();
            sleep(30);
        });
    }

    private function createOrGetVehicle(string $plate, VehicleType $type): Vehicle
    {
        return Vehicle::firstOrCreate(['plate' => $plate], ['type' => $type->value]);
    }

    private function ensureVehicleNotAlreadyParked(Vehicle $vehicle): void
    {
        $existing = ParkingSpot::where('vehicle_id', $vehicle->id)->first();
        if ($existing) {
            throw new ApiException("This vehicle is already parked on {$existing->identifier}");
        }
    }

    private function parkVan(Vehicle $vehicle, ParkingSpot $initialSpot): void
    {
        $spots = $this->getAvailableVanSpots($initialSpot);

        if ($spots->count() < 3) {
            throw new ApiException('Not enough space for van');
        }

        ParkingSpot::whereIn('id', $spots->pluck('id'))->update(['vehicle_id' => $vehicle->id]);
    }

    private function getAvailableVanSpots(ParkingSpot $spot): Collection
    {
        $spots = ParkingSpot::query()
            ->whereIn('type', [ParkSpotType::REGULAR])
            ->whereNull('vehicle_id')
            ->where('identifier', '<>', $spot->identifier)
            ->orderBy('identifier')
            ->limit(2)
            ->lockForUpdate()
            ->get();

        if (count($spots) < 2) {
            return collect();
        }

        $spots->push($spot);

        return $spots;
    }

    private function getAvailableVanSpotsConsecutive(ParkingSpot $spot): Collection
    {
        $spots = ParkingSpot::query()
            ->whereIn('type', [ParkSpotType::REGULAR])
            ->whereNull('vehicle_id')
            ->where('identifier', '>', $spot->identifier)
            ->orderBy('identifier')
            ->get();

        if (count($spots) < 3) {
            return collect();
        }

        $firstSpot = $spot;
        $secondSpot = null;
        $thirdSpot = null;

        foreach ($spots as $spot) {
            if ($firstSpot === null) {
                $firstSpot = $spot;
            } elseif ($secondSpot === null && $spot->identifier === $firstSpot->identifier + 1) {
                $secondSpot = $spot;
            } elseif ($thirdSpot === null && $spot->identifier === $secondSpot->identifier + 1) {
                $thirdSpot = $spot;
                return collect([$firstSpot, $secondSpot, $thirdSpot]);
            } elseif ($secondSpot !== null && $spot->identifier !== $secondSpot->identifier + 1) {
                $firstSpot = $spot;
                $secondSpot = null;
                $thirdSpot = null;
            }
        }

        return collect();
    }
}

