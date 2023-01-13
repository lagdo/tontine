<?php

namespace Siak\Tontine\Service\Traits;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use stdClass;

use function floor;
use function gmp_gcd;

trait ReportTrait
{
    /**
     * @param stdClass $figures
     *
     * @return stdClass
     */
    private function formatCurrencies(stdClass $figures): stdClass
    {
        $figures->cashier->start = Currency::format($figures->cashier->start, true);
        $figures->cashier->recv = Currency::format($figures->cashier->recv, true);
        $figures->cashier->end = Currency::format($figures->cashier->end, true);
        $figures->deposit->amount = Currency::format($figures->deposit->amount, true);
        $figures->remitment->amount = Currency::format($figures->remitment->amount, true);

        return $figures;
    }

    /**
     * @param mixed $defaultValue
     *
     * @return stdClass
     */
    private function makeFigures($defaultValue): stdClass
    {
        $figures = new stdClass();

        $figures->cashier = new stdClass();
        $figures->cashier->start = $defaultValue;
        $figures->cashier->recv = $defaultValue;
        $figures->cashier->end = $defaultValue;

        $figures->deposit = new stdClass();
        $figures->deposit->count = $defaultValue;
        $figures->deposit->amount = $defaultValue;

        $figures->remitment = new stdClass();
        $figures->remitment->count = $defaultValue;
        $figures->remitment->amount = $defaultValue;

        return $figures;
    }

    /**
     * Get the number of subscribers to remit a pool to at a given session
     *
     * @param int $sessionCount
     * @param int $subscriptionCount
     * @param int $sessionRank
     *
     * @return int
     */
    public function getRemitmentCount(int $sessionCount, int $subscriptionCount, int $sessionRank): int
    {
        if($sessionCount === 0 || $subscriptionCount === 0)
        {
            return 0;
        }

        // Greatest common divisor
        $gcd = (int)gmp_gcd($sessionCount, $subscriptionCount);
        $sessionsInLoop = (int)($sessionCount / $gcd);
        $subscriptionsInLoop = (int)($subscriptionCount / $gcd);

        // The session rank in a loop, ranging from 0 to $sessionInLoop - 1.
        $sessionRankInLoop = $sessionRank % $sessionsInLoop;
        $extraSubscriptionsInLoop = $subscriptionsInLoop % $sessionsInLoop;
        return (int)floor($subscriptionCount / $sessionCount) +
            ($sessionRankInLoop < $sessionsInLoop - $extraSubscriptionsInLoop ? 0 : 1);
    }

    /**
     * Get the payables of a given pool.
     *
     * @param Pool $pool
     * @param Round $round
     * @param array $with
     *
     * @return Collection
     */
    private function _getSessions(Round $round, Pool $pool, array $with = []): Collection
    {
        $with['payables'] = function($query) use($pool) {
            // Keep only the subscriptions of the current pool.
            $query->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
                ->where('subscriptions.pool_id', $pool->id);
        };
        return $round->sessions()->with($with)->get();
    }
}
