<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function compact;

class SummaryService
{
    use ReportTrait;

    /**
     * @param BalanceCalculator $balanceCalculator
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(protected BalanceCalculator $balanceCalculator,
        protected TenantService $tenantService, PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function getCollectedFigures(Pool $pool, Collection $sessions,
        Collection $subscriptions): array
    {
        $cashier = 0;
        $collectedFigures = [];
        foreach($sessions as $session)
        {
            if($session->disabled($pool) || $session->pending)
            {
                $collectedFigures[$session->id] = $this->makeFigures('&nbsp;');
                continue;
            }

            $figures = $this->makeFigures(0);
            $figures->cashier->start = $cashier;
            $figures->cashier->recv = $cashier;

            $depositAmount = $this->balanceCalculator->getPoolDepositAmount($pool, $session);
            $figures->deposit->amount += $depositAmount;
            $figures->cashier->recv += $depositAmount;
            $figures->deposit->count = $subscriptions->filter(function($subscription) use($session) {
                return isset($subscription->receivables[$session->id]) ?
                    $subscription->receivables[$session->id]->deposit !== null : false;
            })->count();

            $remitmentAmount = $this->balanceCalculator->getPoolRemitmentAmount($pool, $session);
            $figures->cashier->end = $figures->cashier->recv;
            $figures->remitment->amount += $remitmentAmount;
            $figures->cashier->end -= $remitmentAmount;
            $figures->remitment->count += $session->payables->filter(function($payable) {
                return $payable->remitment !== null;
            })->count();

            $cashier = $figures->cashier->end;
            $collectedFigures[$session->id] = $figures;
        }

        return $collectedFigures;
    }

    /**
     * Get the receivables of a given pool.
     *
     * Will return extended data on subscriptions.
     *
     * @param Pool $pool
     *
     * @return array
     */
    public function getFigures(Pool $pool): array
    {
        $subscriptions = $pool->subscriptions()
            ->with(['member', 'receivables.deposit'])
            ->get()
            ->each(function($subscription) {
                $subscription->setRelation('receivables',
                    $subscription->receivables->keyBy('session_id'));
            });
        $sessions = $this->getEnabledSessions($pool, ['payables.remitment']);
        $figures = new stdClass();
        if($pool->remit_planned)
        {
            $figures->expected = $this->getExpectedFigures($pool, $sessions, $subscriptions);
        }
        $figures->collected = $this->getCollectedFigures($pool, $sessions, $subscriptions);

        return compact('pool', 'sessions', 'subscriptions', 'figures');
    }

    /**
     * @param Pool $pool
     * @param int $sessionId
     *
     * @return array|stdClass
     */
    public function getRemitmentFigures(Pool $pool, int $sessionId = 0)
    {
        $sessions = $this->getEnabledSessions($pool, ['payables.subscription.member']);
        $sessionCount = $sessions->count();
        $subscriptionCount = $pool->subscriptions()->count();
        $remitmentAmount = $pool->amount * $sessionCount;

        $figures = [];
        $position = 0;
        foreach($sessions as $session)
        {
            $figures[$session->id] = new stdClass();
            $figures[$session->id]->payables = $session->payables;
            $figures[$session->id]->count = 0;
            $figures[$session->id]->amount = '';
            if($session->enabled($pool))
            {
                $figures[$session->id]->count =
                    $this->getRemitmentCount($sessionCount, $subscriptionCount, $position++);
                $figures[$session->id]->amount = $remitmentAmount;
            }
        }

        return $sessionId > 0 ? $figures[$sessionId] : $figures;
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getSessionRemitmentCount(Pool $pool, Session $session): int
    {
        if(!$pool->deposit_fixed)
        {
            return 1;
        }

        $sessions = $this->poolService->getEnabledSessions($pool);
        $position = $sessions->filter(function($_session) use($session) {
            return $_session->start_at->lt($session->start_at);
        })->count();

        return $this->getRemitmentCount($sessions->count(), $pool->subscriptions()->count(), $position);
    }
}
