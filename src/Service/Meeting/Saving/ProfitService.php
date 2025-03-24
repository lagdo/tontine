<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function gmp_gcd;

class ProfitService
{
    /**
     * @param BalanceCalculator $balanceCalculator
     * @param TenantService $tenantService
     * @param FundService $fundService
     */
    public function __construct(private BalanceCalculator $balanceCalculator,
        private TenantService $tenantService, private FundService $fundService)
    {}

    /**
     * @param Collection $sessions
     * @param Saving $saving
     *
     * @return int
     */
    private function getSavingDuration(Collection $sessions, Saving $saving): int
    {
        // Count the number of sessions before the current one.
        return $sessions->filter(function($session) use($saving) {
            return $session->start_at > $saving->session->start_at;
        })->count();
    }

    /**
     * Get the amount corresponding to one part for a given distribution
     *
     * @param Collection $savings
     *
     * @return int
     */
    private function getPartValue(Collection $savings): int
    {
        // The value of the unit part is the gcd of the saving amounts.
        // The distribution values might be optimized by using the saving distribution
        // value instead of the amount, but the resulting part value might be confusing
        // since it can be greater than some saving amounts in certain cases.
        return (int)$savings->reduce(function($gcd, $saving) {
            if($gcd === 0)
            {
                return $saving->amount;
            }
            if($saving->duration === 0)
            {
                return $gcd;
            }
            return gmp_gcd($gcd, $saving->amount);
        }, $savings->first()->amount);
    }

    /**
     * Get the profit distribution for savings.
     *
     * @param Collection $sessions
     * @param Collection $savings
     * @param int $profitAmount
     *
     * @return Distribution
     */
    private function distribution(Collection $sessions, Collection $savings,
        int $profitAmount): Distribution
    {
        if($savings->count() === 0)
        {
            return new Distribution($savings);
        }

        // Set savings durations and distributions
        foreach($savings as $saving)
        {
            $saving->duration = $this->getSavingDuration($sessions, $saving);
            $saving->distribution = $saving->amount * $saving->duration;
            $saving->profit = 0;
        }

        // Reduce the distributions by the value of the unit part.
        $partValue = $this->getPartValue($savings);
        if($partValue > 0)
        {
            $sum = (int)($savings->sum('distribution') / $partValue);
            foreach($savings as $saving)
            {
                $saving->distribution /= $partValue;
                $saving->profit = (int)($profitAmount * $saving->distribution / $sum);
            }
        }
        return new Distribution($savings, $partValue);
    }

    /**
     * Get the profit distribution for savings.
     *
     * @param Session $session
     * @param Fund $fund
     * @param int $profitAmount
     *
     * @return Distribution
     */
    public function getDistribution(Session $session, Fund $fund, int $profitAmount): Distribution
    {
        $sessions = $this->fundService->getFundSessions($session, $fund);
        // Get the savings to be rewarded
        $savings = $fund->savings()
            ->select('savings.*')
            ->join('members', 'members.id', '=', 'savings.member_id')
            ->join('sessions', 'sessions.id', '=', 'savings.session_id')
            ->whereIn('sessions.id', $sessions->pluck('id'))
            ->orderBy('members.name', 'asc')
            ->orderBy('sessions.start_at', 'asc')
            ->with(['session', 'member'])
            ->get();
        return $this->distribution($sessions, $savings, $profitAmount);
    }

    /**
     * Get the total saving and profit amounts.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return array<int>
     */
    public function getSavingAmounts(Session $session, Fund $fund): array
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->fundService->getFundSessionIds($session, $fund);
        return [
            'saving' => $this->balanceCalculator->getSavingsAmount($sessionIds, $fund),
            'refund' => $this->balanceCalculator->getRefundsAmount($sessionIds, $fund) +
                $this->balanceCalculator->getPartialRefundsAmount($sessionIds, $fund),
        ];
    }
}
