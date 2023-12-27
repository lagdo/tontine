<?php

namespace Siak\Tontine\Service\Traits;

use Carbon\Carbon;
use Closure;
use Illuminate\Database\Eloquent\Builder;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;

trait PoolTrait
{
    /**
     * @param Round $round
     * @param Carbon|null $startDate
     * @param Carbon|null $endDate
     * @param Closure|null $poolClosure
     *
     * @return Builder
     */
    private function getPoolsQuery(Round $round, ?Carbon $startDate, ?Carbon $endDate,
        ?Closure $poolClosure = null): Builder
    {
        $roundIds = $round->tontine->rounds()->pluck('id');
        $dates = [
            'start' => $startDate->format('Y-m-d'),
            'end' => $endDate->format('Y-m-d'),
        ];
        // This closure returns pools that have been assigned start and end
        // dates different from those of the round they are attached to.
        // They need to be displayed for each round they have at least one session in.
        $shiftedClosure = function(Builder $query) use($roundIds, $poolClosure, $dates) {
            $query->whereIn('round_id', $roundIds)
                ->when($poolClosure != null, $poolClosure)
                ->whereHas('pool_round', function(Builder $query) use($dates) {
                    $query->whereHas('end_session', function(Builder $query) use($dates) {
                        $query->where('start_at', '>=', $dates['start']);
                    })
                    ->whereHas('start_session', function(Builder $query) use($dates) {
                        $query->where('start_at', '<=', $dates['end']);
                    });
                });
        };

        return Pool::where(function(Builder $query) use($round, $poolClosure) {
            $query->where('round_id', $round->id)
                ->whereDoesntHave('pool_round')
                ->when($poolClosure != null, $poolClosure);
        })
        ->when($startDate && $endDate, function(Builder $query) use($shiftedClosure) {
            $query->orWhere($shiftedClosure);
        });
    }
}
