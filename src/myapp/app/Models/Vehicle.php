<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'plate', 'type'
    ];

    public function parkingSpot(): HasOne
    {
        return $this->hasOne(ParkingSpot::class);
    }

    public function isCar(): bool
    {
        return $this->type === VehicleType::CAR->value;
    }

    public function isMotorcycle(): bool
    {
        return $this->type === VehicleType::MOTORCYCLE->value;
    }

    public function isVan(): bool
    {
        return $this->type === VehicleType::VAN->value;
    }
}
