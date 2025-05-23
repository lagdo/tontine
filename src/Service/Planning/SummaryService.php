<?php

namespace Siak\Tontine\Service\Planning;

use Siak\Tontine\Model\Pool;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function compact;

class SummaryService
{
    use ReportTrait;

    /**
     * @param PoolService $poolService
     */
    public function __construct(PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    /**
     * Get the receivables of a given pool.
     *
     * Will return basic data on subscriptions.
     *
     * @param Pool $pool
     *
     * @return array
     */
    public function getReceivables(Pool $pool): array
    {
        $sessions = $this->poolService->getActiveSessions($pool);
        $subscriptions = $pool->subscriptions()->with(['member'])->get();
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($pool, $sessions);

        return compact('pool', 'sessions', 'subscriptions', 'figures');
    }

    /**
     * Get the payables of a given pool.
     *
     * @param Pool $pool
     *
     * @return array
     */
    public function getPayables(Pool $pool): array
    {
        $sessions = $this->getActiveSessions($pool, [
            'payables.remitment',
            'payables.subscription',
        ]);
        $subscriptions = $pool->subscriptions()
            ->whereHas('payable') // Always true, normally.
            ->with(['payable', 'payable.session', 'member'])
            ->get();

        $figures = new stdClass();
        // Expected figures only for pools with fixed deposit amount
        if($pool->remit_planned)
        {
            $figures->expected = $this->getExpectedFigures($pool, $sessions);
        }

        return compact('pool', 'sessions', 'subscriptions', 'figures');
    }
}
