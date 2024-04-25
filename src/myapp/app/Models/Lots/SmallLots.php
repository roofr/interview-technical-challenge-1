<?php

namespace App\Models\Lots;

use App\Models\Lots\Constants\LOT_TYPE;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SmallLots extends Lots
{
    use HasFactory;

    protected $table = 'lots';

    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope('size', function(Builder $builder) {
            $builder->where('type', LOT_TYPE::SMALL);
        });

        static::creating(function(Lots $lot) {
            $lot->type = LOT_TYPE::SMALL;
        });
    }

    public function checkAvailability(): bool
    {
        return $this->is_available;
    }
}
