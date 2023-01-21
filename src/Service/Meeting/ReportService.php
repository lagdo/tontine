<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Currency;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Tontine\TenantService;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function compact;

class ReportService
{
    use ReportTrait;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     * @param Collection $subscriptions
     *
     * @return array
     */
    private function getCollectedFigures(Pool $pool, Collection $sessions, Collection $subscriptions): array
    {
        $cashier = 0;
        $remitmentAmount = $pool->amount * $sessions->filter(function($session) use($pool) {
            return $session->enabled($pool);
        })->count();

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
            foreach($subscriptions as $subscription)
            {
                if(($subscription->receivables[$session->id]->deposit))
                {
                    $figures->deposit->count++;
                    $figures->deposit->amount += $pool->amount;
                    $figures->cashier->recv += $pool->amount;
                }
            }
            $figures->cashier->end = $figures->cashier->recv;
            foreach($session->payables as $payable)
            {
                if(($payable->remitment))
                {
                    $figures->remitment->count++;
                    $figures->remitment->amount += $remitmentAmount;
                    $figures->cashier->end -= $remitmentAmount;
                }
            }

            $cashier = $figures->cashier->end;
            $collectedFigures[$session->id] = $this->formatCurrencies($figures);
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
        $subscriptions = $pool->subscriptions()->with(['member', 'receivables.deposit'])
            ->get()->each(function($subscription) {
                $subscription->setRelation('receivables', $subscription->receivables->keyBy('session_id'));
            });
        $sessions = $this->_getSessions($this->tenantService->round(), $pool, ['payables.remitment']);
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($pool, $sessions, $subscriptions);
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
        $sessions = $this->_getSessions($this->tenantService->round(), $pool, ['payables.subscription.member']);
        $sessionCount = $sessions->filter(function($session) use($pool) {
            return $session->enabled($pool);
        })->count();
        $subscriptionCount = $pool->subscriptions()->count();
        $remitmentAmount = $pool->amount * $sessionCount;
        $formattedAmount = Currency::format($remitmentAmount);

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
                $figures[$session->id]->amount = $formattedAmount;
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
        $sessions = $this->tenantService->round()->sessions->filter(function($_session) use($pool) {
            return $_session->enabled($pool);
        });
        $position = $sessions->filter(function($_session) use($session) {
            return $_session->start_at->lt($session->start_at);
        })->count();

        return $this->getRemitmentCount($sessions->count(), $pool->subscriptions()->count(), $position);
    }
}
