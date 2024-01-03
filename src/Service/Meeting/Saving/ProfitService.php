<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Siak\Tontine\Model\Debt;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function array_keys;
use function count;
use function gmp_gcd;

class ProfitService
{
    /**
     * @param TenantService $tenantService
     * @param SavingService $savingService
     */
    public function __construct(protected TenantService $tenantService,
        protected SavingService $savingService)
    {}

    /**
     * @param Session $currentSession
     * @param int $fundId
     *
     * @return Builder|Relation
     */
    private function getFundSessionsQuery(Session $currentSession, int $fundId): Builder|Relation
    {
        $lastSessionDate = $currentSession->start_at->format('Y-m-d');
        // The closing sessions ids
        $closingSessionIds = array_keys($this->savingService->getFundClosings($fundId));
        if(count($closingSessionIds) === 0)
        {
            // No closing session yet
            return $this->tenantService->tontine()->sessions()
                ->whereDate('sessions.start_at', '<=', $lastSessionDate);
        }
        // The previous closing sessions
        $closingSessions = $this->tenantService->tontine()->sessions()
            ->where('sessions.id', '!=', $currentSession->id)
            ->whereIn('sessions.id', $closingSessionIds)
            ->whereDate('sessions.start_at', '<', $lastSessionDate)
            ->orderByDesc('sessions.start_at')
            ->get();
        if($closingSessions->count() === 0)
        {
            // All the closing sessions are after the current session.
            return $this->tenantService->tontine()->sessions()
                ->whereDate('sessions.start_at', '<=', $lastSessionDate);
        }

        // The most recent previous closing session
        $firstSessionDate = $closingSessions->last()->start_at->format('Y-m-d');
        // Return all the sessions after the most recent previous closing session
        return $this->tenantService->tontine()->sessions()
            ->whereDate('sessions.start_at', '<=', $lastSessionDate)
            ->whereDate('sessions.start_at', '>', $firstSessionDate);
    }

    /**
     * Get the sessions to be used for profit calculation.
     *
     * @param Session $currentSession
     * @param int $fundId
     *
     * @return Collection
     */
    public function getFundSessions(Session $currentSession, int $fundId): Collection
    {
        return $this->getFundSessionsQuery($currentSession, $fundId)->get();
    }

    /**
     * Get the id of sessions to be used for profit calculation.
     *
     * @param Session $currentSession
     * @param int $fundId
     *
     * @return Collection
     */
    public function getFundSessionIds(Session $currentSession, int $fundId): Collection
    {
        return $this->getFundSessionsQuery($currentSession, $fundId)->pluck('sessions.id');
    }

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
     * @param int $fundId
     * @param int $profitAmount
     *
     * @return Collection
     */
    public function getDistributions(Session $session, int $fundId, int $profitAmount): Collection
    {
        $sessions = $this->getFundSessions($session, $fundId);
        // Get the savings to be rewarded
        $query = Saving::select('savings.*')
            ->join('members', 'members.id', '=', 'savings.member_id')
            ->join('sessions', 'sessions.id', '=', 'savings.session_id')
            ->whereIn('sessions.id', $sessions->pluck('id'))
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
     * @param int $fundId
     *
     * @return Fund|null
     */
    public function getFund(int $fundId): ?Fund
    {
        return $this->tenantService->tontine()->funds()->find($fundId);
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
        $refund = $fundId > 0 ?
            $query->where('loans.fund_id', $fundId)->first() :
            $query->whereNull('loans.fund_id')->first();
        return $refund->total ?? 0;
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
        $sessionIds = $this->getFundSessions($session, $fundId)->pluck('id');
        return [
            'saving' => $this->getSavingAmount($sessionIds, $fundId),
            'refund' => $this->getRefundAmount($sessionIds, $fundId),
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

    /**
     * Check if the given session is closing the fund.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return bool
     */
    public function hasFundClosing(Session $session, int $fundId): bool
    {
        return $this->savingService->hasFundClosing($session, $fundId);
    }
}
