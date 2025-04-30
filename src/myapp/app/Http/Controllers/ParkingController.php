<?php

namespace App\Http\Controllers;

use App\Enums\VehicleType;
use app\Exceptions\ApiException;
use App\Http\Requests\ParkinkSpotRequest;
use app\Services\ParkingService;
use Illuminate\Http\JsonResponse;

class ParkingController extends Controller
{
    protected ParkingService $parkingService;

    public function __construct(ParkingService $parkingService)
    {
        $this->parkingService = $parkingService;
    }

    public function park(ParkinkSpotRequest $request, $id)
    {
        try {
            $this->parkingService->parkVehicle(
                VehicleType::from($request->input('vehicleType')),
                $request->input('vehiclePlate'),
                $id
            );

            return response()->json(['message' => 'Vehicle parked successfully']);
        } catch (ApiException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function unpark($id): JsonResponse
    {
        try {
            $this->parkingService->unpark($id);

            return response()->json(['message' => 'Vehicle unparked successfully']);
        } catch (ApiException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }

    public function parkingLot(): JsonResponse
    {
        try {
            $data = $this->parkingService->getParkingLot();

            return response()->json([
                'totalCapacity' => $data->count(),
                'availableSpots' => $data->where('occupied', false)->count(),
                'spots' => $data
            ]);
        } catch (ApiException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getCode());
        }
    }
}
