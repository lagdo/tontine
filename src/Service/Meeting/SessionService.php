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
    public function openRound(Round $round)
    {
        // Don't open a round if there are pools with no subscription.
        // $this->checkPoolsSubscriptions();

        DB::transaction(function() use($round) {
            $round->update(['status' => Round::STATUS_OPENED]);
            $this->roundOpened($this->tenantService->tontine(), $round);
        });
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
     * Open a session.
     *
     * @param Session $session
     *
     * @return void
     */
    public function openSession(Session $session)
    {
        if($session->opened)
        {
            return;
        }

        // Make sure the round is also opened.
        $this->openRound($session->round);

        DB::transaction(function() use($session) {
            // Open the session
            $session->update(['status' => Session::STATUS_OPENED]);
            $this->sessionOpened($this->tenantService->tontine(), $session);
        });
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
}
