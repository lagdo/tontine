<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\ProfitTransfer as Transfer;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Meeting\Saving\FundService;
use Siak\Tontine\Service\Payment\BalanceCalculator;
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
     * @param Transfer $transfer
     *
     * @return int
     */
    private function getTransferDuration(Collection $sessions, Transfer $transfer): int
    {
        // Count the number of sessions before the current one.
        return $sessions
            ->filter(fn($session) => $session->day_date > $transfer->session->day_date)
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
        $transfers = $distribution->transfers;
        // Set transfers durations and distributions
        foreach($transfers as $transfer)
        {
            $transfer->duration = $this->getTransferDuration($sessions, $transfer);
            // The number of parts is determined by the transfer amount and duration.
            $transfer->parts = $transfer->amount * $transfer->duration;
            $transfer->profit = 0;
            $transfer->percent = 0;
        }

        $distribution->rewarded = $transfers->filter(fn($transfer) => $transfer->duration > 0);
        // The value of the unit part is the gcd of the transfer amounts.
        // The distribution values is optimized by using the transfer parts value instead
        // of the amount, but the resulting part amount might be confusing when displayed
        // to the users since it can be greater than some transfer amounts in certain cases.
        $partsGcd = $this->gcd($distribution->rewarded->pluck('parts'));
        if($partsGcd === 0)
        {
            return $distribution;
        }

        $amountGcd = $this->gcd($distribution->rewarded->pluck('amount'));
        $distribution->partAmount = $amountGcd;

        $transfers->each(function($transfer) use($partsGcd, $amountGcd) {
            $transfer->profit = $transfer->parts * $transfer->coef / $partsGcd;
            $transfer->parts /= $amountGcd * $transfer->coef;
            $transfer->amount *= $transfer->coef;
        });
        return $distribution;
    }

    /**
     * Get the profit distribution for transfers.
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
        // Get the transfers until the given session.
        $transfers = Transfer::query()
            ->select('v_profit_transfers.*')
            ->join('members', 'members.id', '=', 'v_profit_transfers.member_id')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->join('sessions', 'sessions.id', '=', 'v_profit_transfers.session_id')
            ->whereFund($fund)
            ->whereIn('sessions.id', $sessions->pluck('id'))
            ->orderBy('member_defs.name', 'asc')
            ->orderBy('sessions.day_date', 'asc')
            ->with(['session', 'member'])
            ->get();

        $distribution = new Distribution($sessions, $transfers, $profitAmount);
        return $transfers->count() === 0 ?  $distribution:
            $this->setDistributions($distribution);
    }

    /**
     * Get the refunds amount.
     *
     * @param Session $session
     * @param Fund $fund
     *
     * @return int
     */
    public function getRefundsAmount(Session $session, Fund $fund): int
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->fundService->getFundSessionIds($fund, $session);
        return $this->balanceCalculator->getRefundsAmount($sessionIds, $fund) +
            $this->balanceCalculator->getPartialRefundsAmount($sessionIds, $fund);
    }
}
