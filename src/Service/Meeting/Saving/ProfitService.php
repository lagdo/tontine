<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\TenantService;

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
        return $sessions
            ->filter(fn($session) => $session->day_date > $saving->session->day_date)
            ->count();
    }

    /**
     * @param Collection $values
     *
     * @return int
     */
    private function gcd(Collection $values): int
    {
        return (int)$values->reduce(
            fn($gcd, $value) => $gcd === 0 || $value === 0 ? 0 : gmp_gcd($gcd, $value),
            $values->first()
        );
    }

    /**
     * Get the amount corresponding to one part for a given distribution
     *
     * @param Distribution $distribution
     *
     * @return Distribution
     */
    private function setDistributions(Distribution $distribution): Distribution
    {
        $sessions = $distribution->sessions;
        $savings = $distribution->savings;
        // Set savings durations and distributions
        foreach($savings as $saving)
        {
            $saving->duration = $this->getSavingDuration($sessions, $saving);
            // The number of parts is determined by the saving amount and duration.
            $saving->parts = $saving->amount * $saving->duration;
            $saving->profit = 0;
            $saving->percent = 0;
        }

        $distribution->rewarded = $savings->filter(fn($saving) => $saving->duration > 0);
        // The value of the unit part is the gcd of the saving amounts.
        // The distribution values is optimized by using the saving parts value instead
        // of the amount, but the resulting part amount might be confusing when displayed
        // to the users since it can be greater than some saving amounts in certain cases.
        $partsGcd = $this->gcd($distribution->rewarded->pluck('parts'));
        if($partsGcd > 0)
        {
            $amountGcd = $this->gcd($distribution->rewarded->pluck('amount'));
            $distribution->partAmount = $amountGcd;

            $savings->each(function($saving) use($partsGcd, $amountGcd) {
                $saving->profit = $saving->parts / $partsGcd;
                $saving->parts /= $amountGcd;
            });

            $profitSum = $savings->sum('profit');
            $profitAmount = $distribution->profitAmount;

            $savings->each(function($saving) use($profitSum, $profitAmount) {
                $percent = $saving->profit / $profitSum;
                $saving->percent = $percent * 100;
                $saving->profit = (int)($profitAmount * $percent);
            });
        }
        return $distribution;
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
        $sessions = $this->fundService->getFundSessions($fund, $session);
        // Get the savings until the given session.
        $savings = $fund->savings()
            ->select('savings.*')
            ->join('members', 'members.id', '=', 'savings.member_id')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->join('sessions', 'sessions.id', '=', 'savings.session_id')
            ->whereIn('sessions.id', $sessions->pluck('id'))
            ->orderBy('member_defs.name', 'asc')
            ->orderBy('sessions.day_date', 'asc')
            ->with(['session', 'member'])
            ->get();

        $distribution = new Distribution($sessions, $savings, $profitAmount);
        return $savings->count() === 0 ?  $distribution:
            $this->setDistributions($distribution);
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
        $sessionIds = $this->fundService->getFundSessionIds($fund, $session);
        return [
            'saving' => $this->balanceCalculator->getSavingsAmount($sessionIds, $fund),
            'refund' => $this->balanceCalculator->getRefundsAmount($sessionIds, $fund) +
                $this->balanceCalculator->getPartialRefundsAmount($sessionIds, $fund),
        ];
    }
}
