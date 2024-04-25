<?php

namespace App\Models\Lots;

use App\Models\Lots\Constants\LOT_TYPE;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Database\Query\Builder as QueryBuilder;

/**
 * A LargeLot is StandardLot that has StandardLots immediately before and after it in its row
 *
 * @property StandardLots|int $prev
 * @property StandardLots|int $next
 */
class LargeLots extends StandardLots
{
    protected static function booted(): void
    {
        parent::booted();

        static::addGlobalScope('size', function(Builder $builder) {
            //add a global scope for LargeLots so that we include the prev (lag) and next (lead) columns in the
            $builder->from(function(QueryBuilder $subBuilder) {
                $subBuilder->select('*')
                    ->selectRaw(
                        'lag(lots.column) over(partition by row) as prev, lead(lots.column) over(partition by row) as next')
                    ->from('lots')
                    ->orderBy('row')
                    ->orderBy('column');

                return $subBuilder;
            }, 'subTable')
                ->whereRaw('"subTable"."prev" + 1 = "subTable"."column"')
                ->whereRaw('"subTable"."next" - 1 = "subTable"."column"')
                ->where('type', LOT_TYPE::STANDARD);
        });
    }

    /**
     * @return StandardLots
     */
    public function getPrev(): StandardLots
    {
        if (is_int($this->prev)) {
            $this->prev = StandardLots::withoutGlobalScopes()
                ->where('row', $this->row)
                ->where('column', $this->prev)
                ->sole();
        }
        return $this->prev;
    }

    /**
     * @return StandardLots
     */
    public function getNext(): StandardLots
    {
        if (is_int($this->next)) {
            $this->next = StandardLots::withoutGlobalScopes()
                ->where('row', $this->row)
                ->where('column', $this->next)
                ->sole();
        }
        return $this->next;
    }

    /**
     * @return bool
     */
    public function checkAvailability(): bool
    {
        return $this->is_available && $this->getPrev()->is_available && $this->getNext()->is_available;
    }

    /**
     * @param Collection $lots
     * @return Collection<LargeLots>
     */
    public static function removeOverlaps(Collection $lots): Collection
    {
        return $lots->groupBy('rows')
            ->map(function(Collection $row) {
                $hasSeenColumns = [];
                $row->sortBy('column')
                    ->filter(function(LargeLots $lot) use (&$hasSeenColumns) {
                        $hasSeenCurrent = in_array($lot->column, $hasSeenColumns);
                        $hasSeenPrev = in_array($lot->getPrev()->column, $hasSeenColumns);
                        $hasSeenNext = in_array($lot->getnext()->column, $hasSeenColumns);
                        if (!$hasSeenCurrent && !$hasSeenPrev && !$hasSeenNext) {
                            $hasSeenColumns = [
                                ...$hasSeenColumns,
                                $lot->column,
                                $lot->getPrev()->column,
                                $lot->getnext()->column,
                            ];
                            return true;
                        };
                        return false;
                    });
            })->collect();
    }

    /**
     * @param Collection $lots
     * @return int
     */
    public static function getTotalCapacity(Collection $lots): int
    {
        return self::removeOverlaps($lots)->count();
    }

    public static function getAvailableCapacity(Collection $lots): int
    {
        return self::removeOverlaps(
            $lots->filter(function(Lots $lot) {
                return $lot->checkAvailability();
            })
        )->count();
    }
}
