<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Service\Tontine\TenantService;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function collect;
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
    private function getExpectedFigures(Pool $pool, Collection $sessions, Collection $subscriptions): array
    {
        $sessionCount = $sessions->filter(function($session) use($pool) {
            return $session->enabled($pool);
        })->count();
        $subscriptionCount = $pool->subscriptions()->count();
        $depositCount = $subscriptions->count();

        $remitmentAmount = $pool->amount * $sessionCount;
        $depositAmount = $pool->amount * $subscriptions->count();

        $rank = 0;
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
            $figures->remitment->count =
                $this->getRemitmentCount($sessionCount, $subscriptionCount, $rank++);
            $figures->remitment->amount = $remitmentAmount * $figures->remitment->count;
            $figures->cashier->end = $cashier + $depositAmount - $figures->remitment->amount;
            $cashier = $figures->cashier->end;

            $expectedFigures[$session->id] = $this->formatCurrencies($figures);
        }

        return $expectedFigures;
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
        $sessions = $this->tenantService->round()->sessions()->get();
        $subscriptions = $pool->subscriptions()->with(['member'])->get();
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($pool, $sessions, $subscriptions);

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
        $sessions = $this->_getSessions($this->tenantService->round(), $pool, ['payables.subscription']);
        $subscriptions = $pool->subscriptions()->with(['payable', 'member'])->get();
        $figures = new stdClass();
        $figures->expected = $this->getExpectedFigures($pool, $sessions, $subscriptions);

        // Set the subscriptions that will be pay at each session.
        // Pad with 0's when the beneficiaries are not yet set.
        $sessions->each(function($session) use($figures, $pool) {
            if($session->disabled($pool))
            {
                return;
            }
            // Pick the subscriptions ids, and fill with 0's to the max available.
            $session->beneficiaries = $session->payables->map(function($payable) {
                return $payable->subscription_id;
            })->pad($figures->expected[$session->id]->remitment->count, 0);
        });

        // Separate subscriptions that already have a beneficiary assigned from the others.
        [$subscriptions, $beneficiaries] = $subscriptions->partition(function($subscription) {
            return !$subscription->payable->session_id;
        });
        $beneficiaries = $beneficiaries->pluck('member.name', 'id');
        // Show the list of subscriptions only for mutual tontines
        if($this->tenantService->tontine()->is_mutual)
        {
            $subscriptions = $subscriptions->pluck('member.name', 'id');
            $subscriptions->prepend('', 0);
        }
        else // if($this->tenantService->tontine()->is_financial)
        {
            $subscriptions = collect([]);
        }

        return compact('pool', 'sessions', 'subscriptions', 'beneficiaries', 'figures');
    }
}
