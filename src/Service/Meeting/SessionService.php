<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
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
        $this->tenantService->round()->sessions()->opened()
            ->get()
            ->each(fn($session) => $this->syncSession($session));
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
