<?php

namespace Siak\Tontine\Service\Meeting\Credit;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Saving;
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
     * @param Saving $saving
     *
     * @return int
     */
    private function getSavingDuration(Session $currentSession, Saving $saving): int
    {
        // Count the number of sessions before the current one.
        return $this->tenantService->round()->sessions
            ->filter(function($session) use($currentSession, $saving) {
                return $session->start_at > $saving->session->start_at &&
                    $session->start_at <= $currentSession->start_at;
            })
            ->count();
    }

    /**
     * Get the profit distribution for savings.
     *
     * @param Session $session
     * @param Collection $savings
     * @param int $profitAmount
     *
     * @return Collection
     */
    private function setDistributions(Session $session, Collection $savings, int $profitAmount): Collection
    {
        // Set savings durations and distributions
        foreach($savings as $saving)
        {
            $saving->duration = $this->getSavingDuration($session, $saving);
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
     * @param int $profitAmount
     * @param int $fundId
     *
     * @return Collection
     */
    public function getDistributions(Session $session, int $profitAmount, int $fundId): Collection
    {
        // Get the savings to be rewarded
        $query = Saving::select('savings.*')
            ->join('members', 'members.id', '=', 'savings.member_id')
            ->join('sessions', 'sessions.id', '=', 'savings.session_id')
            ->where('sessions.round_id', $this->tenantService->round()->id)
            ->where('sessions.start_at', '<=', $session->start_at)
            ->orderBy('members.name', 'asc')
            ->orderBy('sessions.start_at', 'asc')
            ->with(['session', 'member']);
        $savings = $fundId > 0 ?
            $query->where('savings.fund_id', $fundId)->get() :
            $query->whereNull('savings.fund_id')->get();
        if($savings->count() === 0)
        {
            return $savings;
        }

        return $this->setDistributions($session, $savings, $profitAmount);
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
     * Get the sum of savings amounts.
     *
     * @param Collection $sessionId
     * @param int $fundId
     *
     * @return int
     */
    private function getSavingAmount(Collection $sessionIds, int $fundId): int
    {
        // Saving: the sum of savings amounts.
        $query = DB::table('savings')
            ->select(DB::raw("sum(amount) as total"))
            ->whereIn('session_id', $sessionIds);
        $saving = $fundId > 0 ?
            $query->where('savings.fund_id', $fundId)->first() :
            $query->whereNull('savings.fund_id')->first();
        return $saving->total ?? 0;
    }

    /**
     * Get the sum of refunded interests.
     *
     * @param Collection $sessionId
     * @param int $fundId
     *
     * @return int
     */
    private function getRefundAmount(Collection $sessionIds, int $fundId): int
    {
        $query = DB::table('refunds')
            ->join('debts', 'refunds.debt_id', '=', 'debts.id')
            ->join('loans', 'debts.loan_id', '=', 'loans.id')
            ->select(DB::raw("sum(debts.amount) as total"))
            ->where('debts.type', Debt::TYPE_INTEREST)
            ->whereIn('refunds.session_id', $sessionIds);
        $profit = $fundId > 0 ?
            $query->where('loans.fund_id', $fundId)->first() :
            $query->whereNull('loans.fund_id')->first();
        return $profit->total ?? 0;
    }

    /**
     * Get the total saving and profit amounts.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return array<int>
     */
    public function getSavingAmounts(Session $session, int $fundId): array
    {
        // Get the ids of all the sessions until the current one.
        $sessionIds = $this->tenantService->round()->sessions()
            ->where('start_at', '<=', $session->start_at)
            ->pluck('id');
        return [
            'saving' => $this->getSavingAmount($sessionIds, $fundId),
            'refund' => $this->getRefundAmount($sessionIds, $fundId),
        ];
    }

    /**
     * Save the profit amount on this session.
     *
     * @param Session $session
     * @param int $profitAmount
     * @param int $fundId
     *
     * @return void
     */
    public function saveProfitAmount(Session $session, int $profitAmount, int $fundId)
    {
        $round = $this->tenantService->round();
        $properties = $round->properties;
        $properties['profit'][$session->id][$fundId] = $profitAmount;
        $round->saveProperties($properties);
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
        $round = $this->tenantService->round();
        return $round->properties['profit'][$session->id][$fundId] ?? 0;
    }
}
