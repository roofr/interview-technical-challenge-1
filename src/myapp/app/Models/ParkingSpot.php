<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ParkingSpot extends Model
{
    use HasFactory;

    protected $fillable = [
        'identifier', 'type', 'vehicle_id'
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function isOccupied(): bool
    {
        return !empty($this->vehicle_id);
    }
}

