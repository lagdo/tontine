<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\Planning\SessionService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function compact;

class SummaryService
{
    use ReportTrait;

    /**
     * @var LocaleService
     */
    protected LocaleService $localeService;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @var SessionService
     */
    public SessionService $sessionService;

    /**
     * @var PoolService
     */
    protected PoolService $poolService;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SessionService $sessionService
     * @param PoolService $poolService
     */
    public function __construct(LocaleService $localeService, TenantService $tenantService,
        SessionService $sessionService, PoolService $poolService)
    {
        $this->localeService = $localeService;
        $this->tenantService = $tenantService;
        $this->sessionService = $sessionService;
        $this->poolService = $poolService;
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
        $remitmentAmount = $this->tenantService->tontine()->is_libre ? 0 :
            $pool->amount * $this->sessionService->enabledSessionCount($pool);

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
                if(($deposit = $subscription->receivables[$session->id]->deposit))
                {
                    $amount = $this->tenantService->tontine()->is_libre ? $deposit->amount : $pool->amount;
                    $figures->deposit->count++;
                    $figures->deposit->amount += $amount;
                    $figures->cashier->recv += $amount;
                }
            }

            if($this->tenantService->tontine()->is_libre)
            {
                $remitmentAmount = $this->poolService->getLibrePoolAmount($pool, $session);
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
        if(!$this->tenantService->tontine()->is_libre)
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
        $sessions = $this->_getSessions($this->tenantService->round(), $pool, ['payables.subscription.member']);
        $sessionCount = $this->sessionService->enabledSessionCount($pool);
        $subscriptionCount = $pool->subscriptions()->count();
        $remitmentAmount = $pool->amount * $sessionCount;
        $formattedAmount = $this->localeService->formatMoney($remitmentAmount);

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
        if($this->tenantService->tontine()->is_libre)
        {
            return 1;
        }

        $sessions = $this->sessionService->enabledSessions($pool);
        $position = $sessions->filter(function($_session) use($session) {
            return $_session->start_at->lt($session->start_at);
        })->count();

        return $this->getRemitmentCount($sessions->count(), $pool->subscriptions()->count(), $position);
    }
}
