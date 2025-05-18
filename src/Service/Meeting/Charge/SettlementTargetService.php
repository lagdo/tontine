<?php

namespace Siak\Tontine\Service\Meeting\Charge;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\SettlementTarget;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\SearchSanitizer;

class SettlementTargetService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService,
        private SearchSanitizer $searchSanitizer)
    {}

    /**
     * @param Session $session
     * @param SettlementTarget $target
     *
     * @return bool
     */
    private function filterTarget(Session $session, SettlementTarget $target): bool
    {
        return $session->day_date >= $target->session->day_date &&
            $session->day_date <= $target->deadline->day_date;
    }

    /**
     * @param Charge $charge
     * @param Session $session
     *
     * @return SettlementTarget|null
     */
    public function getTarget(Charge $charge, Session $session): ?SettlementTarget
    {
        return $charge->targets()
            ->with(['session', 'deadline'])
            ->get()
            ->filter(fn($target) => $this->filterTarget($session, $target))
            ->first();
    }

    /**
     * @param Round $round
     * @param string $search
     *
     * @return Builder|Relation
     */
    private function getMembersQuery(Round $round, string $search = ''): Builder|Relation
    {
        return $round->members()
            ->search($this->searchSanitizer->sanitize($search));
    }

    /**
     * @param Collection $members
     * @param Charge $charge
     * @param SettlementTarget $target
     *
     * @return Collection
     */
    public function getMembersSettlements(Collection $members, Charge $charge, SettlementTarget $target): Collection
    {
        return DB::table('settlements')
            ->join('sessions', 'sessions.id', '=', 'settlements.session_id')
            ->join('bills', 'settlements.bill_id', '=', 'bills.id')
            ->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
            ->where('sessions.day_date', '>=', $target->session->day_date)
            ->where('sessions.day_date', '<=', $target->deadline->day_date)
            ->whereIn('libre_bills.member_id', $members->pluck('id'))
            ->where('libre_bills.charge_id', $charge->id)
            ->select('libre_bills.member_id', DB::raw('sum(bills.amount) as amount'))
            ->groupBy('libre_bills.member_id')
            ->get()
            ->pluck('amount', 'member_id');
    }

    /**
     * @param Round $round
     * @param Charge $charge
     * @param SettlementTarget $target
     * @param string $search
     * @param int $page
     *
     * @return Collection
     */
    public function getMembersWithSettlements(Round $round, Charge $charge,
        SettlementTarget $target, string $search = '', int $page = 0): Collection
    {
        return $this->getMembersQuery($round, $search)
            ->addSelect([
                'paid' => DB::table('settlements')
                    ->join('sessions', 'sessions.id', '=', 'settlements.session_id')
                    ->join('bills', 'bills.id', '=', 'settlements.bill_id')
                    ->join('libre_bills', 'libre_bills.bill_id', '=', 'bills.id')
                    ->whereColumn('libre_bills.member_id', 'members.id')
                    ->where('sessions.day_date', '>=', $target->session->day_date)
                    ->where('sessions.day_date', '<=', $target->deadline->day_date)
                    ->where('libre_bills.charge_id', $charge->id)
                    ->select(DB::raw('sum(bills.amount)'))
            ])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * @param Round $round
     * @param string $search
     *
     * @return int
     */
    public function getMemberCount(Round $round, string $search = ''): int
    {
        return $this->getMembersQuery($round, $search)->count();
    }

    /**
     * @param Session $currentSession
     *
     * @return Collection
     */
    public function getDeadlineSessions(Session $currentSession): Collection
    {
        return $currentSession->round->sessions()
            ->where('day_date', '>', $currentSession->day_date)
            ->orderByDesc('day_date')
            ->pluck('title', 'id');
    }

    /**
     * @param Session $currentSession
     * @param int $sessionId
     *
     * @return Session|null
     */
    private function getDeadlineSession(Session $currentSession, int $sessionId): ?Session
    {
        return $currentSession->round->sessions()
            ->where('day_date', '>', $currentSession->day_date)
            ->find($sessionId);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param array $values
     *
     * @return void
     */
    public function createTarget(Charge $charge, Session $session, array $values): void
    {
        $deadline = $this->getDeadlineSession($session, $values['deadline']);
        if(!$deadline)
        {
            // Todo: throw an exception.
            return;
        }

        $target = new SettlementTarget();
        $target->amount = $values['amount'];
        $target->global = $values['global'];
        $target->charge()->associate($charge);
        $target->session()->associate($session);
        $target->deadline()->associate($deadline);
        $target->save();
    }

    /**
     * @param SettlementTarget $target
     * @param Session $session
     * @param array $values
     *
     * @return void
     */
    public function updateTarget(SettlementTarget $target, Session $session, array $values): void
    {
        $deadline = $this->getDeadlineSession($session, $values['deadline']);
        if(!$deadline)
        {
            // Todo: throw an exception.
            return;
        }

        $target->amount = $values['amount'];
        $target->global = $values['global'];
        $target->session()->associate($session);
        $target->deadline()->associate($deadline);
        $target->save();
    }

    /**
     * @param SettlementTarget $target
     *
     * @return void
     */
    public function deleteTarget(SettlementTarget $target): void
    {
        $target->delete();
    }
}
