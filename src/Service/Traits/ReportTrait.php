<?php

namespace Siak\Tontine\Service\Traits;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Service\Planning\PoolService;
use stdClass;

use function floor;
use function gmp_gcd;

trait ReportTrait
{
    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @param int $defaultValue
     *
     * @return stdClass
     */
    private function makeFigures(int $defaultValue = 0): stdClass
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
    private function getActiveSessions(Pool $pool, array $with = []): Collection
    {
        // Keep only the subscriptions of the current pool.
        $with['payables'] = fn($qp) => $qp
            ->whereHas('subscription', fn($qs) => $qs->where('pool_id', $pool->id));
        return $this->poolService->getActiveSessions($pool)->load($with);
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     *
     * @return array
     */
    private function getExpectedFigures(Pool $pool, Collection $sessions): array
    {
        $depositCount = $pool->subscriptions()->count();
        $sessionCount = $sessions->count();
        $subscriptionCount = $depositCount;
        $depositAmount = $pool->amount * $depositCount;
        $remitmentAmount = $pool->amount * $sessionCount;

        $position = 0;
        $cashier = 0;
        $expectedFigures = [];
        foreach($sessions as $session)
        {
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
