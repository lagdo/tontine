<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Funding;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function gmp_gcd;

class ProfitService
{
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
     * @param Session $currentSession
     * @param Funding $funding
     *
     * @return int
     */
    private function getFundingDuration(Session $currentSession, Funding $funding): int
    {
        // Count the number of sessions before the current one.
        return $this->tenantService->round()->sessions
            ->filter(function($session) use($currentSession, $funding) {
                return $session->start_at > $funding->session->start_at &&
                    $session->start_at <= $currentSession->start_at;
            })
            ->count();
    }

    /**
     * Get the profit distribution for fundings.
     *
     * @param Session $session
     * @param Collection $fundings
     *
     * @return Collection
     */
    private function setDistributions(Session $session, Collection $fundings): Collection
    {
        // Set fundings durations and distributions
        foreach($fundings as $funding)
        {
            $funding->duration = $this->getFundingDuration($session, $funding);
            $funding->distribution = $funding->amount * $funding->duration;
        }
        // Reduce the distributions
        $distributionGcd = (int)$fundings->reduce(function($gcd, $funding) {
            if($gcd === 0)
            {
                return $funding->distribution;
            }
            if($funding->duration === 0)
            {
                return $gcd;
            }
            return gmp_gcd($gcd, $funding->distribution);
        }, $fundings->first()->distribution);
        if($distributionGcd > 0)
        {
            foreach($fundings as $funding)
            {
                $funding->distribution /= $distributionGcd;
            }
        }

        return $fundings;
    }

    /**
     * Get the profit distribution for fundings.
     *
     * @param Session $session
     *
     * @return Collection
     */
    public function getDistributions(Session $session): Collection
    {
        // Get the fundings to be rewarded
        $fundings = Funding::select('fundings.*')
            ->join('members', 'members.id', '=', 'fundings.member_id')
            ->join('sessions', 'sessions.id', '=', 'fundings.session_id')
            ->where('sessions.round_id', $this->tenantService->round()->id)
            ->where('sessions.start_at', '<=', $session->start_at)
            ->orderBy('members.name', 'asc')
            ->orderBy('sessions.start_at', 'asc')
            ->with(['session', 'member'])->get();
        if($fundings->count() === 0)
        {
            return $fundings;
        }

        return $this->setDistributions($session, $fundings);
    }

    /**
     * Get the total profit amount.
     *
     * @param Session $session
     *
     * @return int
     */
    public function getTotalProfit(Session $session): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)
            ->pluck('id');
        // Sum the interest refunds.
        $refund = DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->join('loans', 'loans.id', '=', 'debts.loan_id')
            ->select(DB::raw("sum(debts.amount) as interest"))
            ->where('debts.type', Debt::TYPE_INTEREST)
            ->whereIn('refunds.session_id', $sessionIds)
            ->whereNull('loans.remitment_id')
            ->first();

        return $refund->interest ?? 0;
    }

    /**
     * Save the round profits on this session.
     *
     * @param Session $session
     * @param int $profitAmount
     *
     * @return void
     */
    public function saveProfit(Session $session, int $profitAmount)
    {
        $round = $this->tenantService->round();
        $content = [
            'profit' => [
                'session' => $session->id,
                'amount' => $profitAmount,
            ],
        ];
        if($round->property !== null)
        {
            $round->property->content = $content;
            $round->property->save();
            return;
        }

        $round->property()->create(['content' => $content]);
    }
}
