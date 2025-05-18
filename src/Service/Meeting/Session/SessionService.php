<?php

namespace Siak\Tontine\Service\Meeting\Session;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\DataSyncService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\WithTrait;

use function tap;
use function trans;

class SessionService
{
    use WithTrait;

    /**
     * @var bool
     */
    private bool $filterActive = false;

    /**
     * @param TenantService $tenantService
     * @param DataSyncService $dataSyncService
     */
    public function __construct(private TenantService $tenantService,
        private DataSyncService $dataSyncService)
    {}

    /**
     * Get the session statuses
     *
     * @return array
     */
    public function getSessionStatuses(): array
    {
        return [
            Session::STATUS_PENDING => trans('tontine.session.status.pending'),
            Session::STATUS_OPENED => trans('tontine.session.status.opened'),
            Session::STATUS_CLOSED => trans('tontine.session.status.closed'),
        ];;
    }

    /**
     * @param bool $filter
     *
     * @return self
     */
    public function active(bool $filter = true): self
    {
        $this->filterActive = $filter;
        return $this;
    }

    /**
     * @param Round $round
     *
     * @return Relation
     */
    private function getSessionsQuery(Round $round): Relation
    {
        return $round->sessions()->when($this->filterActive,
            fn(Builder $query) => $query->active());
    }

    /**
     * Find a session.
     *
     * @param Round $round
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(Round $round, int $sessionId): ?Session
    {
        return tap($this->getSessionsQuery($round), fn($query) => $this->addWith($query))
            ->find($sessionId);
    }

    /**
     * Get the number of sessions in the selected round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getSessionCount(Round $round): int
    {
        return $this->getSessionsQuery($round)->count();
    }

    /**
     * Get a paginated list of sessions in the selected round.
     *
     * @param Round $round
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getSessions(Round $round, int $page = 0, bool $orderAsc = true): Collection
    {
        return tap($this->getSessionsQuery($round), fn($query) => $this->addWith($query))
            ->orderBy('day_date', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Find a session.
     *
     * @param Guild $guild
     * @param int $sessionId
     *
     * @return Session|null
     */
    public function getGuildSession(Guild $guild, int $sessionId): ?Session
    {
        return $guild->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->find($sessionId);
    }

    /**
     * @param Round $round
     * @param Session $session
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return Collection
     */
    public function getSessionIds(Round $round, Session $session, bool $withCurr = true): Collection
    {
        return $this->getSessionsQuery($round)
            ->where('day_date', $withCurr ? '<=' : '<', $session->day_date)
            ->pluck('sessions.id');
    }

    /**
     * Check if some pools still have no subscriptions.
     *
     * @param Round $round
     *
     * @return void
     */
    public function checkPoolsSubscriptions(Round $round)
    {
        if($round->pools()->whereDoesntHave('subscriptions')->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.no_subscription'));
        }
    }

    /**
     * Sync a session with new members or new subscriptions.
     *
     * @param Round $round
     * @param Session $session
     *
     * @return void
     */
    private function syncSession(Round $round, Session $session)
    {
        DB::transaction(function() use($round, $session) {
            // Don't sync a round if there are pools with no subscription.
            // $this->checkPoolsSubscriptions($round);

            // Sync the round.
            $round->update(['status' => Round::STATUS_OPENED]);
            $this->dataSyncService->roundSynced($round->guild, $session->round);

            // Sync the session
            $session->update(['status' => Session::STATUS_OPENED]);
            $this->dataSyncService->sessionSynced($round->guild, $session);
        });
    }

    /**
     * Open a session.
     *
     * @param Round $round
     * @param Session $session
     *
     * @return void
     */
    public function openSession(Round $round, Session $session)
    {
        if($session->pending)
        {
            // If the session is getting opened for the first time, then
            // its also needs to get synced with charges and subscriptions.
            $this->syncSession($round, $session);
            return;
        }
        if(!$session->opened)
        {
            // Open the session
            $session->update(['status' => Session::STATUS_OPENED]);
        }
    }

    /**
     * Close a session.
     *
     * @param Session $session
     *
     * @return void
     */
    public function closeSession(Session $session)
    {
        $session->update(['status' => Session::STATUS_CLOSED]);
    }

    /**
     * Resync all the already opened sessions.
     *
     * @param Round $round
     *
     * @return void
     */
    public function resyncSessions(Round $round): void
    {
        DB::transaction(function() use($round) {
            // Don't sync a round if there are pools with no subscription.
            // $this->checkPoolsSubscriptions($round);

            // Sync the round.
            $round->update(['status' => Round::STATUS_OPENED]);
            $this->dataSyncService->roundSynced($round->guild, $round);

            // Sync the sessions.
            $round->sessions()->opened()->get()
                ->each(fn($session) => $this->dataSyncService
                    ->sessionSynced($round->guild, $session));

            // Update the bills amounts.
            /*
            *** Strange Database error. ***
ERROR:  bind message supplies 6 parameters, but prepared statement "pdo_stmt_00000053" requires 12
(SQL: update "bills" set "amount" = (select "amount" from "charges"
inner join "v_bills" on "v_bills"."charge_id" = "charges"."id" where "v_bills"."bill_type" != 0
and "v_bills"."session_id" in (51, 53, 52, 50, 49) and "bills"."id" = "v_bills"."bill_id")
where "ctid" in (select "bills"."ctid" from "bills" inner join "v_bills" on "v_bills"."bill_id" = "bills"."id"
inner join "charges" on "v_bills"."charge_id" = "charges"."id" where "v_bills"."bill_type" != ?
and "v_bills"."session_id" in (?, ?, ?, ?, ?)))
             */
            // $amountQuery = DB::table('charges')
            //     ->join('v_bills', 'v_bills.charge_id', '=', 'charges.id')
            //     ->where('v_bills.bill_type', '!=', Bill::TYPE_LIBRE)
            //     ->whereIn('v_bills.session_id', $round->sessions()->opened()->pluck('id'))
            //     ->whereColumn('bills.id', 'v_bills.bill_id')
            //     ->select('amount');
            // DB::table('bills')
            //     ->join('v_bills', 'v_bills.bill_id', '=', 'bills.id')
            //     ->join('charges', 'v_bills.charge_id', '=', 'charges.id')
            //     ->where('v_bills.bill_type', '!=', Bill::TYPE_LIBRE)
            //     ->whereIn('v_bills.session_id', $round->sessions()->opened()->pluck('id'))
            //     ->update([
            //         'amount' => DB::raw('(' . $amountQuery->toSql() . ')'),
            //     ]);

            // TODO: Fix the SQL update query.
            // Temporary fix: issue an update query for each bill amount to be changed.
            DB::table('bills')
                ->select('bills.id', 'charges.amount')
                ->join('v_bills', 'v_bills.bill_id', '=', 'bills.id')
                ->join('charges', 'v_bills.charge_id', '=', 'charges.id')
                ->where('v_bills.bill_type', '!=', Bill::TYPE_LIBRE)
                ->whereIn('v_bills.session_id', $round->sessions()->opened()->pluck('id'))
                ->get()
                ->each(fn($row) => DB::table('bills')->where('id', $row->id)
                    ->update(['amount' => $row->amount]));
        });
    }

    /**
     * Find the prev session.
     *
     * @param Round $round
     * @param Session $session
     *
     * @return Session|null
     */
    public function getPrevSession(Round $round, Session $session): ?Session
    {
        return $round->sessions()->active()
            ->where('day_date', '<', $session->day_date)
            ->orderBy('day_date', 'desc')
            ->first();
    }

    /**
     * Find the next session.
     *
     * @param Round $round
     * @param Session $session
     *
     * @return Session|null
     */
    public function getNextSession(Round $round, Session $session): ?Session
    {
        return $round->sessions()->active()
            ->where('day_date', '>', $session->day_date)
            ->orderBy('day_date', 'asc')
            ->first();
    }

    /**
     * Update a session agenda.
     *
     * @param Session $session
     * @param string $agenda
     *
     * @return void
     */
    public function saveAgenda(Session $session, string $agenda): void
    {
        $session->update(['agenda' => $agenda]);
    }

    /**
     * Update a session report.
     *
     * @param Session $session
     * @param string $report
     *
     * @return void
     */
    public function saveReport(Session $session, string $report): void
    {
        $session->update(['report' => $report]);
    }
}
