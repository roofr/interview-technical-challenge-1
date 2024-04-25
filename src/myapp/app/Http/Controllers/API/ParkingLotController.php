<?php

namespace App\Http\Controllers\API;

use App\Models\Lots\LargeLots;
use App\Models\Lots\SmallLots;
use App\Models\Lots\StandardLots;
use Illuminate\Http\JsonResponse;

class ParkingLotController
{
    /**
     * @return JsonResponse
     */
    public function get()
    {
        $motorcycleCount = $this->getByMotorcycle();
        $carCount = $this->getByCar();
        $vanCount = $this->getByVan();

        return response()->json([
            'motorcycle'    => $motorcycleCount->getData(),
            'car'           => $carCount->getData(),
            'van'           => $vanCount->getData(),
        ]);
    }

    /**
     * @return JsonResponse
     */
    public function getByMotorcycle()
    {
        $small = SmallLots::all();
        $availableCapacity = SmallLots::getAvailableCapacity($small);
        $totalCapacity = SmallLots::getTotalCapacity($small);

        $standard = StandardLots::all();
        $availableCapacity += StandardLots::getAvailableCapacity($standard);
        $totalCapacity += StandardLots::getTotalCapacity($standard);

        return $this->createCountResponse($availableCapacity, $totalCapacity);
    }

    /**
     * @return JsonResponse
     */
    public function getByCar()
    {
        $lots = StandardLots::all();
        $availableCapacity = StandardLots::getAvailableCapacity($lots);
        $totalCapacity = StandardLots::getTotalCapacity($lots);

        return $this->createCountResponse($availableCapacity, $totalCapacity);
    }

    /**
     * @return JsonResponse
     */
    public function getByVan()
    {
        $lots = LargeLots::all();
        $availableCapacity = LargeLots::getAvailableCapacity($lots);
        $totalCapacity = LargeLots::getTotalCapacity($lots);

        return $this->createCountResponse($availableCapacity, $totalCapacity);
    }

    /**
     * @param int $availableCapacity
     * @param int $totalCapacity
     * @return JsonResponse
     */
    private function createCountResponse(int $availableCapacity, int $totalCapacity)
    {
        $s = ['count' => [
            'available'     => $availableCapacity,
            'totalCapacity' => $totalCapacity,
        ]];

        return response()->json($s);
    }
}
