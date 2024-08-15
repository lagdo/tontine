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
     * @param SavingService $savingService
     */
    public function __construct(private BalanceCalculator $balanceCalculator,
        private TenantService $tenantService, private FundService $fundService,
        private SavingService $savingService)
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
     * Get the profit distribution for savings.
     *
     * @param Collection $sessions
     * @param Collection $savings
     * @param int $profitAmount
     *
     * @return Collection
     */
    private function setDistributions(Collection $sessions, Collection $savings,
        int $profitAmount): Collection
    {
        // Set savings durations and distributions
        foreach($savings as $saving)
        {
            $saving->duration = $this->getSavingDuration($sessions, $saving);
            $saving->distribution = $saving->amount * $saving->duration;
            $saving->profit = 0;
        }
        // Reduce the distributions
        $distributionGcd = (int)$savings->reduce(function($gcd, $saving) {
            if($gcd === 0)
            {
                return $saving->distribution;
            }
            if($saving->duration === 0)
            {
                return $gcd;
            }
            return gmp_gcd($gcd, $saving->distribution);
        }, $savings->first()->distribution);
        if($distributionGcd > 0)
        {
            $sum = (int)($savings->sum('distribution') / $distributionGcd);
            foreach($savings as $saving)
            {
                $saving->distribution /= $distributionGcd;
                $saving->profit = (int)($profitAmount * $saving->distribution / $sum);
            }
        }

        return $savings;
    }

    /**
     * Get the profit distribution for savings.
     *
     * @param Session $session
     * @param Fund $fund
     * @param int $profitAmount
     *
     * @return Collection
     */
    public function getDistributions(Session $session, Fund $fund, int $profitAmount): Collection
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
        if($savings->count() === 0)
        {
            return $savings;
        }

        return $this->setDistributions($sessions, $savings, $profitAmount);
    }

    /**
     * Get the amount corresponding to one part for a given distribution
     *
     * @param Collection $savings
     *
     * @return int
     */
    public function getPartUnitValue(Collection $savings): int
    {
        // The part value makes sense only iwhen there is more than 2 savings
        // with distribution greater than 0.
        $savings = $savings->filter(fn($saving) => $saving->distribution > 0);
        if($savings->count() < 2)
        {
            return 0;
        }

        $saving = $savings->first();
        return (int)($saving->amount * $saving->duration / $saving->distribution);
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

    /**
     * Get the profit amount saved on this session.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return int
     */
    public function getProfitAmount(Session $session, int $fundId): int
    {
        return $this->savingService->getProfitAmount($session, $fundId);
    }
}
