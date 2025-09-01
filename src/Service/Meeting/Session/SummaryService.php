<?php

namespace Siak\Tontine\Service\Meeting\Session;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Payment\BalanceCalculator;
use Siak\Tontine\Service\Planning\PoolService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\ReportTrait;
use stdClass;

use function collect;
use function compact;

class SummaryService
{
    use ReportTrait;

    /**
     * @var Collection
     */
    private $deposits;

    /**
     * @var Collection
     */
    private $remitments;

    /**
     * @var Collection
     */
    private $auctions;

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

        $depositCount = $deposit?->count ?? 0;
        $depositAmount = $deposit?->amount ?? 0;

        $figures->deposit->amount += $depositAmount;
        $figures->cashier->recv += $depositAmount;
        $figures->deposit->count = $depositCount;

        $sessionCount = $pool->sessions->count();
        $remitmentCount = $remitment?->count ?? 0;
        $remitmentAmount = $pool->deposit_fixed ?
            $pool->def->amount * $sessionCount * $remitmentCount :
            ($remitmentCount > 0 ? $depositAmount : 0);

        $figures->cashier->end = $figures->cashier->recv;
        $figures->remitment->amount += $remitmentAmount;
        $figures->cashier->end -= $remitmentAmount;
        $figures->remitment->count += $remitmentCount;

        return $figures;
    }

    /**
     * @param Pool $pool
     * @param Collection $sessions
     *
     * @return array
     */
    private function getCollectedFigures(Pool $pool, Collection $sessions): array
    {
        $cashier = 0;
        $collectedFigures = [];
        foreach($sessions as $session)
        {
            $deposit = $this->deposits[$pool->id][$session->id] ?? null;
            $remitment = $this->remitments[$pool->id][$session->id] ?? null;
            $figures = $this->getSessionFigures($pool, $cashier, $deposit, $remitment);
            if($pool->remit_auction)
            {
                // Add the auctions amount to the cash amount.
                $figures->cashier->end += $this->auctions[$pool->id][$session->id]?->amount ?? 0;
            }
            $cashier = $figures->cashier->end;
            $collectedFigures[$session->id] = $figures;
        }

        return $collectedFigures;
    }

    /**
     * @param Round $round
     * @param Collection $poolIds
     *
     * @return void
     */
    private function getDeposits(Round $round, Collection $poolIds): void
    {
        $this->deposits = DB::table('v_deposits')
            ->select('subscriptions.pool_id', 'v_deposits.session_id',
                DB::raw('count(*) as count'), DB::raw('sum(v_deposits.amount) as amount'))
            ->join('sessions', 'v_deposits.session_id', '=', 'sessions.id')
            ->join('receivables', 'receivables.id', '=', 'v_deposits.receivable_id')
            ->join('subscriptions', 'receivables.subscription_id', '=', 'subscriptions.id')
            ->whereIn('subscriptions.pool_id', $poolIds)
            ->where('sessions.round_id', $round->id)
            ->groupBy(['subscriptions.pool_id', 'v_deposits.session_id'])
            ->get()
            // Group the data by pool id and session id.
            ->groupBy('pool_id')
            ->map(fn($poolDeposits) => $poolDeposits->groupBy('session_id')
                ->map(fn($array) => $array->first()));
    }

    /**
     * @param Round $round
     * @param Collection $poolIds
     *
     * @return void
     */
    private function getRemitments(Round $round, Collection $poolIds): void
    {
        $this->remitments = DB::table('remitments')
            ->select('subscriptions.pool_id', 'payables.session_id', DB::raw('count(*) as count'))
            ->join('payables', 'payables.id', '=', 'remitments.payable_id')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->join('sessions', 'payables.session_id', '=', 'sessions.id')
            ->whereIn('subscriptions.pool_id', $poolIds)
            ->where('sessions.round_id', $round->id)
            ->groupBy(['subscriptions.pool_id', 'payables.session_id'])
            ->get()
            // Group the data by pool id and session id.
            ->groupBy('pool_id')
            ->map(fn($poolRemitments) => $poolRemitments->groupBy('session_id')
                ->map(fn($array) => $array->first()));
    }

    /**
     * @param Round $round
     * @param Collection $poolIds
     *
     * @return void
     */
    private function getAuctions(Round $round, Collection $poolIds): void
    {
        $this->auctions = DB::table('auctions')
            ->select('subscriptions.pool_id', 'auctions.session_id',
                DB::raw('count(*) as count'), DB::raw('sum(amount) as amount'))
            ->join('remitments', 'auctions.remitment_id', '=', 'remitments.id')
            ->join('payables', 'remitments.payable_id', '=', 'payables.id')
            ->join('subscriptions', 'payables.subscription_id', '=', 'subscriptions.id')
            ->join('sessions', 'payables.session_id', '=', 'sessions.id')
            ->where('paid', true)
            ->whereIn('subscriptions.pool_id', $poolIds)
            ->where('sessions.round_id', $round->id)
            ->groupBy(['subscriptions.pool_id', 'auctions.session_id'])
            ->get()
            // Group the data by pool id and session id.
            ->groupBy('pool_id')
            ->map(fn($poolAuctions) => $poolAuctions->groupBy('session_id')
                ->map(fn($array) => $array->first()));
    }

    /**
     * @param Pool $pool
     *
     * @return array
     */
    private function getPoolFigures(Pool $pool): array
    {
        $sessions = $this->poolService->getActiveSessions($pool);
        $figures = new stdClass();
        if($pool->remit_planned)
        {
            $figures->expected = $this->getExpectedFigures($pool, $sessions);
        }
        $figures->collected = $this->getCollectedFigures($pool, $sessions);
        if($pool->remit_auction)
        {
            $figures->auctions = $this->auctions[$pool->id] ?? collect();
        }

        return compact('pool', 'figures', 'sessions');
    }

    /**
     * Get the receivables of a given pool.
     *
     * @param Round $round
     * @param int $poolId
     *
     * @return Collection
     */
    public function getFigures(Round $round, int $poolId = 0): Collection
    {
        $pools = $round->pools()
            ->with(['round.guild', 'sessions'])
            ->whereHas('subscriptions')
            ->when($poolId > 0, fn($query) => $query->where('pools.id', $poolId))
            ->get();
        if($pools->count() === 0)
        {
            return collect();
        }

        $poolIds = $pools->pluck('id');
        $this->getDeposits($round, $poolIds);
        $this->getRemitments($round, $poolIds);
        $this->getAuctions($round, $poolIds);

        return $pools->map(fn(Pool $pool) => $this->getPoolFigures($pool));
    }

    /**
     * @param Collection $figures
     *
     * @return array
     */
    public function getPoolsBalance(Collection $figures): array
    {
        $pools = [];
        foreach($figures as $poolData)
        {
            foreach($poolData['figures']->collected as $sessionId => $collected)
            {
                $pools[$sessionId] = ($pools[$sessionId] ?? 0) + $collected->cashier->end;
            }
        }
        return $pools;
    }

    /**
     * Get the funds.
     *
     * @param Round $round
     *
     * @return Collection
     */
    public function getFunds(Round $round): Collection
    {
        return Fund::ofRound($round)->get()
            ->filter(fn($fund) => $fund->start_amount > 0 ||
                $fund->end_amount > 0 || $fund->profit_amount > 0);
    }

    /**
     * @param Pool $pool
     * @param Session $session
     *
     * @return int
     */
    public function getSessionRemitmentCount(Pool $pool, Session $session): int
    {
        if(!$pool->remit_planned)
        {
            return -1;
        }
        if(!$pool->deposit_fixed)
        {
            return 1;
        }

        $sessions = $this->poolService->getActiveSessions($pool);
        $position = $sessions->filter(
            fn($_session) => $_session->day_date->lt($session->day_date)
        )->count();

        return $this->getRemitmentCount($sessions->count(),
            $pool->subscriptions()->count(), $position);
    }
}
