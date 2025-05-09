<?php

namespace Siak\Tontine\Service\Planning;

use Siak\Tontine\Model\Pool;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function collect;
use function compact;

class SummaryService
{
    use ReportTrait;

    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(protected LocaleService $localeService,
        protected TenantService $tenantService, PoolService $poolService)
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
        $sessions = $this->getActiveSessions($pool, ['payables.subscription']);
        $subscriptions = $pool->subscriptions()
            ->with(['payable', 'payable.session', 'member'])
            ->get();

        $figures = new stdClass();
        // Expected figures only for pools with fixed deposit amount
        if($pool->remit_planned /*$pool->deposit_fixed*/)
        {
            $figures->expected = $this->getExpectedFigures($pool, $sessions);
        }

        // Set the subscriptions that will be pay at each session.
        // Pad with 0's when the beneficiaries are not yet set.
        $sessions->each(function($session) use($figures, $pool) {
            // Pick the subscriptions ids, and fill with 0's to the max available.
            $remitmentCount = !$pool->deposit_fixed ? 1 :
                $figures->expected[$session->id]->remitment->count;
            $session->beneficiaries = $session->payables
                ->map(fn($payable) => $payable->subscription_id)
                ->pad($remitmentCount, 0);
        });

        // Separate subscriptions that already have a beneficiary assigned from the others.
        $poolSessionIds = $pool->sessions->pluck('id', 'id');
        [$beneficiaries, $subscriptions] = $subscriptions
            ->partition(function($subscription) use($poolSessionIds) {
                $session = $subscription->payable?->session ?? null;
                return $session !== null && $poolSessionIds->has($session->id);
            });
        $beneficiaries = $beneficiaries->pluck('member.name', 'id');
        // Do not show the list of subscriptions for pools with auctions
        if($pool->remit_auction)
        {
            $subscriptions = collect([]);
        }
        else
        {
            $subscriptions = $subscriptions->pluck('member.name', 'id')->sort();
            $subscriptions->prepend('', 0);
        }

        return compact('pool', 'sessions', 'subscriptions', 'beneficiaries', 'figures');
    }
}
