<?php

namespace Siak\Tontine\Service\Traits;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use stdClass;

use function floor;
use function gmp_gcd;

trait ReportTrait
{
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
     * @param int $sessionPosition
     *
     * @return int
     */
    public function getRemitmentCount(int $sessionCount, int $subscriptionCount, int $sessionPosition): int
    {
        if($sessionCount === 0 || $subscriptionCount === 0)
        {
            return 0;
        }

        // Greatest common divisor
        $gcd = (int)gmp_gcd($sessionCount, $subscriptionCount);
        $sessionsInLoop = (int)($sessionCount / $gcd);
        $positionInLoop = $sessionPosition % $sessionsInLoop;
        $subscriptionsInLoop = (int)($subscriptionCount / $gcd);
        $extraSubscriptionsInLoop = $subscriptionsInLoop % $sessionsInLoop;

        // There's is an extra remitment when the modulo decreases compared to the previous session.
        $prevModulo = ($positionInLoop * $extraSubscriptionsInLoop) % $sessionsInLoop;
        if($prevModulo > ($prevModulo + $extraSubscriptionsInLoop) % $sessionsInLoop)
        {
            return (int)floor($subscriptionCount / $sessionCount) + 1;
        }
        return (int)floor($subscriptionCount / $sessionCount);
    }

    /**
     * Get the payables of a given pool.
     *
     * @param Pool $pool
     * @param array $with
     *
     * @return Collection
     */
    private function _getSessions(Pool $pool, array $with = []): Collection
    {
        $with['payables'] = function($query) use($pool) {
            // Keep only the subscriptions of the current pool.
            $query->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
                ->where('subscriptions.pool_id', $pool->id);
        };
        return $this->tenantService->getSessions()->load($with);
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function getExpectedFigures(Pool $pool, Collection $sessions, Collection $subscriptions): array
    {
        $sessionCount = $sessions->filter(function($session) use($pool) {
            return $session->enabled($pool);
        })->count();
        $subscriptionCount = $depositCount = $subscriptions->count();
        $depositAmount = $pool->amount * $depositCount;
        $remitmentAmount = $pool->amount * $sessionCount;

        $position = 0;
        $cashier = 0;
        $expectedFigures = [];
        foreach($sessions as $session)
        {
            if($session->disabled($pool))
            {
                $expectedFigures[$session->id] = $this->makeFigures('');
                continue;
            }

            $figures = $this->makeFigures(0);
            $figures->cashier->start = $cashier;
            $figures->cashier->recv = $cashier + $depositAmount;
            $figures->deposit->count = $depositCount;
            $figures->deposit->amount = $depositAmount;
            $figures->remitment->count = !$pool->deposit_fixed ? 1 :
                $this->getRemitmentCount($sessionCount, $subscriptionCount, $position++);
            $figures->remitment->amount = $remitmentAmount * $figures->remitment->count;
            $figures->cashier->end = $cashier + $depositAmount - $figures->remitment->amount;
            $cashier = $figures->cashier->end;

            $expectedFigures[$session->id] = $figures;
        }

        return $expectedFigures;
    }
}
