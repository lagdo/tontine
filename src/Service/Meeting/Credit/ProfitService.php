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
     * @param int $profitAmount
     *
     * @return Collection
     */
    private function setDistributions(Session $session, Collection $fundings, int $profitAmount): Collection
    {
        // Set fundings durations and distributions
        foreach($fundings as $funding)
        {
            $funding->duration = $this->getFundingDuration($session, $funding);
            $funding->distribution = $funding->amount * $funding->duration;
            $funding->profit = 0;
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
            $sum = (int)($fundings->sum('distribution') / $distributionGcd);
            foreach($fundings as $funding)
            {
                $funding->distribution /= $distributionGcd;
                $funding->profit = (int)($profitAmount * $funding->distribution / $sum);
            }
        }

        return $fundings;
    }

    /**
     * Get the profit distribution for fundings.
     *
     * @param Session $session
     * @param int $profitAmount
     *
     * @return Collection
     */
    public function getDistributions(Session $session, int $profitAmount): Collection
    {
        // Get the fundings to be rewarded
        $fundings = Funding::select('fundings.*')
            ->join('members', 'members.id', '=', 'fundings.member_id')
            ->join('sessions', 'sessions.id', '=', 'fundings.session_id')
            ->where('sessions.round_id', $this->tenantService->round()->id)
            ->where('sessions.start_at', '<=', $session->start_at)
            ->orderBy('members.name', 'asc')
            ->orderBy('sessions.start_at', 'asc')
            ->with(['session', 'member'])
            ->get();
        if($fundings->count() === 0)
        {
            return $fundings;
        }

        return $this->setDistributions($session, $fundings, $profitAmount);
    }

    /**
     * Get the amount corresponding to one part for a given distribution
     *
     * @param Collection $fundings
     *
     * @return int
     */
    public function getPartUnitValue(Collection $fundings): int
    {
        // The part value makes sense only iwhen there is more than 2 fundings
        // with distribution greater than 0.
        $fundings = $fundings->filter(fn($funding) => $funding->distribution > 0);
        if($fundings->count() < 2)
        {
            return 0;
        }

        $funding = $fundings->first();
        return (int)($funding->amount * $funding->duration / $funding->distribution);
    }

    /**
     * Get the total saving and profit amounts.
     *
     * @param Session $session
     *
     * @return array<int>
     */
    public function getAmounts(Session $session): array
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)
            ->pluck('id');
        // Saving: the sum of fundings amounts.
        $saving = DB::table('fundings')
            ->select(DB::raw("sum(amount) as total"))
            ->whereIn('session_id', $sessionIds)
            ->first();
        // Profit: the sum of interest refunds.
        $profit = DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->select(DB::raw("sum(debts.amount) as total"))
            ->where('debts.type', Debt::TYPE_INTEREST)
            ->whereIn('refunds.session_id', $sessionIds)
            ->first();

        return ['saving' => $saving->total ?? 0, 'profit' => $profit->total ?? 0];
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
        $properties = $round->properties;
        $properties['profit'] = [
            'session' => $session->id,
            'amount' => $profitAmount,
        ];
        $round->saveProperties($properties);
    }
}
