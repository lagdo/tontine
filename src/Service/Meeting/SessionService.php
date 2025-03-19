<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\EventTrait;
use Siak\Tontine\Service\Traits\SessionTrait;

use function trans;

class SessionService
{
    use EventTrait;
    use SessionTrait;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService)
    {}

    /**
     * Check if some pools still have no subscriptions.
     *
     * @return void
     */
    public function checkPoolsSubscriptions()
    {
        if($this->tenantService->round()->pools()->whereDoesntHave('subscriptions')->count() > 0)
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.pool.errors.no_subscription'));
        }
    }

    /**
     * Open a round.
     *
     * @param Round $round
     *
     * @return void
     */
    private function openRound(Round $round)
    {
        $round->update(['status' => Round::STATUS_OPENED]);
    }

    /**
     * Close a round.
     *
     * @param Round $round
     *
     * @return void
     */
    public function closeRound(Round $round)
    {
        $round->update(['status' => Round::STATUS_CLOSED]);
    }

    /**
     * Sync a session with new members or new subscriptions.
     *
     * @param Session $session
     *
     * @return void
     */
    private function syncSession(Session $session)
    {
        DB::transaction(function() use($session) {
            // Don't sync a round if there are pools with no subscription.
            // $this->checkPoolsSubscriptions();

            // Sync the round.
            $session->round->update(['status' => Round::STATUS_OPENED]);
            $this->roundSynced($this->tenantService->tontine(), $session->round);

            // Sync the session
            $session->update(['status' => Session::STATUS_OPENED]);
            $this->sessionSynced($this->tenantService->tontine(), $session);
        });
    }

    /**
     * Open a session.
     *
     * @param Session $session
     *
     * @return void
     */
    public function openSession(Session $session)
    {
        if($session->pending)
        {
            // If the session is getting opened for the first time, then
            // its also needs to get synced with charges and subscriptions.
            $this->syncSession($session);
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
     * @return void
     */
    public function resyncSessions()
    {
        DB::transaction(function() {
            // Don't sync a round if there are pools with no subscription.
            // $this->checkPoolsSubscriptions();

            // Sync the round.
            $round = $this->tenantService->round();
            $round->update(['status' => Round::STATUS_OPENED]);
            $this->roundSynced($this->tenantService->tontine(), $round);

            // Sync the sessions.
            $round->sessions()->opened()->get()
                ->each(function($session) {
                    $this->sessionSynced($this->tenantService->tontine(), $session);
                });

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
     * @param Session $session
     *
     * @return Session|null
     */
    public function getPrevSession(Session $session): ?Session
    {
        return $this->tenantService->round()->sessions()->active()
            ->where('start_at', '<', $session->start_at)
            ->orderBy('start_at', 'desc')
            ->first();
    }

    /**
     * Find the next session.
     *
     * @param Session $session
     *
     * @return Session|null
     */
    public function getNextSession(Session $session): ?Session
    {
        return $this->tenantService->round()->sessions()->active()
            ->where('start_at', '>', $session->start_at)
            ->orderBy('start_at', 'asc')
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

    /**
     * Find the unique receivable for a pool and a session.
     *
     * @param Pool $pool The pool
     * @param Session $session The session
     * @param int $receivableId
     * @param string $notes
     *
     * @return int
     */
    public function saveReceivableNotes(Pool $pool, Session $session, int $receivableId, string $notes): int
    {
        return $session->receivables()
            ->where('id', $receivableId)
            ->whereIn('subscription_id', $pool->subscriptions()->pluck('id'))
            ->update(['notes' => $notes]);
    }
}
