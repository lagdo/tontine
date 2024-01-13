<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\BalanceCalculator;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function compact;

class SummaryService
{
    use ReportTrait;

    /**
     * @param BalanceCalculator $balanceCalculator
     * @param TenantService $tenantService
     * @param PoolService $poolService
     */
    public function __construct(protected BalanceCalculator $balanceCalculator,
        protected TenantService $tenantService, PoolService $poolService)
    {
        $this->poolService = $poolService;
    }

    /**
     * @param Pool $pool
     * @param int $cashier
     * @param stdClass|null $deposit
     * @param stdClass|null $remitment
     *
     * @return stdClass
     */
    private function getSessionFigures(Pool $pool, int $cashier,
        ?stdClass $deposit, ?stdClass $remitment): stdClass
    {
        $figures = $this->makeFigures(0);
        $figures->cashier->start = $cashier;
        $figures->cashier->recv = $cashier;

        $depositCount = !$deposit ? 0 : $deposit->total;
        $depositAmount = $pool->deposit_fixed ? $pool->amount * $depositCount :
            (!$deposit ? 0 : $deposit->amount);

        $figures->deposit->amount += $depositAmount;
        $figures->cashier->recv += $depositAmount;
        $figures->deposit->count = $depositCount;

        $sessionCount = $pool->counter->sessions - $pool->counter->disabled_sessions;
        $remitmentCount = !$remitment ? 0 : $remitment->total;
        $remitmentAmount = !$pool->deposit_fixed ? $depositAmount :
            $pool->amount * $sessionCount * $remitmentCount;

        $figures->cashier->end = $figures->cashier->recv;
        $figures->remitment->amount += $remitmentAmount;
        $figures->cashier->end -= $remitmentAmount;
        $figures->remitment->count += $remitmentCount;

        return $figures;
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     * @param Collection $deposits
     * @param Collection $remitments
     * @param Collection|null $disabledSessions
     *
     * @return array
     */
    private function getCollectedFigures(Pool $pool, Collection $sessions,
        Collection $deposits, Collection $remitments, ?Collection $disabledSessions): array
    {
        $cashier = 0;
        $collectedFigures = [];
        foreach($sessions as $session)
        {
            if(($disabledSessions && $disabledSessions->has($session->id)) || $session->pending)
            {
                $collectedFigures[$session->id] = $this->makeFigures('&nbsp;');
                continue;
            }

            $deposit = $deposits[$pool->id][$session->id][0] ?? null;
            $remitment = $remitments[$pool->id][$session->id][0] ?? null;
            $figures = $this->getSessionFigures($pool, $cashier, $deposit, $remitment);

            $cashier = $figures->cashier->end;
            $collectedFigures[$session->id] = $figures;
        }

        return $collectedFigures;
    }

    /**
     * Get the receivables of a given pool.
     *
     * @param Round $round
     *
     * @return Collection
     */
    public function getFigures(Round $round): Collection
    {
        $pools = $round->pools()
            ->with(['round.tontine', 'counter'])
            ->whereHas('subscriptions')
            ->get();
        $poolIds = $pools->pluck('id');
        $deposits = DB::table('deposits')
            ->select('subscriptions.pool_id', 'receivables.session_id',
                DB::raw('count(*) as total'), DB::raw('sum(deposits.amount) as amount'))
            ->join('receivables', 'receivables.id', '=', 'deposits.receivable_id')
            ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
            ->join('sessions', 'receivables.session_id', '=', 'sessions.id')
            ->whereIn('subscriptions.pool_id', $poolIds)
            ->where('sessions.round_id', $round->id)
            ->groupBy(['subscriptions.pool_id', 'receivables.session_id'])
            ->get()
            // Group the data by pool id and session id.
            ->groupBy('pool_id')
            ->map(fn($poolDeposits) => $poolDeposits->groupBy('session_id'));
        $remitments = DB::table('remitments')
            ->select('subscriptions.pool_id', 'payables.session_id', DB::raw('count(*) as total'))
            ->join('payables', 'payables.id', '=', 'remitments.payable_id')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->join('sessions', 'payables.session_id', '=', 'sessions.id')
            ->whereIn('subscriptions.pool_id', $poolIds)
            ->where('sessions.round_id', $round->id)
            ->groupBy(['subscriptions.pool_id', 'payables.session_id'])
            ->get()
            // Group the data by pool id and session id.
            ->groupBy('pool_id')
            ->map(fn($poolRemitments) => $poolRemitments->groupBy('session_id'));
        $disabledSessions = DB::table('pool_session_disabled')
            ->whereIn('pool_id', $poolIds)
            ->get()
            // Group the data (boolean true) by pool id and session id.
            ->groupBy('pool_id')
            ->map(fn($sessions) => $sessions->groupBy('session_id')->map(fn() => true));
        $allSessions = $round->sessions()->orderBy('start_at', 'asc')->get();

        return $pools->map(function($pool) use($allSessions, $deposits, $remitments, $disabledSessions) {
            $disabledSessions = $disabledSessions[$pool->id] ?? null;
            // Enabled sessions
            $sessions = !$disabledSessions ? $allSessions :
                $allSessions->filter(fn($session) => !$disabledSessions->has($session->id));

            $figures = new stdClass();
            if($pool->remit_planned)
            {
                $depositCount = $pool->subscriptions()->count();
                $figures->expected = $this->getExpectedFigures($pool, $sessions, $depositCount);
            }
            $figures->collected = $this->getCollectedFigures($pool, $sessions, $deposits,
                $remitments, $disabledSessions);
            return compact('pool', 'figures', 'sessions');
        });
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getSessionRemitmentCount(Pool $pool, Session $session): int
    {
        if(!$pool->deposit_fixed)
        {
            return 1;
        }

        $sessions = $this->poolService->getEnabledSessions($pool);
        $position = $sessions->filter(function($_session) use($session) {
            return $_session->start_at->lt($session->start_at);
        })->count();

        return $this->getRemitmentCount($sessions->count(), $pool->subscriptions()->count(), $position);
    }
}
