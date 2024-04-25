<?php

namespace App\Models\Lots;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * @package App\Models
 *
 * @property int $id
 * @property int $row
 * @property int $column
 * @property string $type
 * @property bool $is_available
 *
 * @mixin Builder
 */
abstract class Lots extends Model
{
    protected $attributes = [
        'is_available' => false,
    ];

    /**
     * @return bool
     */
    public abstract function checkAvailability(): bool;

    /**
     * @param Collection<Lots> $lots
     * @return int
     */
    public static function getTotalCapacity(Collection $lots): int {
        return $lots->count();
    }

    /**
     * @param Collection<Lots> $lots
     * @return int
     */
    public static function getAvailableCapacity(Collection $lots): int {
        return $lots->filter(function(Lots $lot) {
            return $lot->checkAvailability();
        })->count();
    }
}
