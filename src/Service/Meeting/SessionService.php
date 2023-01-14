<?php

namespace Siak\Tontine\Service\Meeting;

use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\Events\EventTrait;
use Siak\Tontine\Service\Tontine\TenantService;

class SessionService
{
    use EventTrait;

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
     * @return Tontine|null
     */
    public function getTontine(): ?Tontine
    {
        return $this->tenantService->tontine();
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
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
        if($round->is_opened)
        {
            return;
        }
        DB::transaction(function() use($round) {
            $round->update(['status' => RoundModel::STATUS_OPENED]);
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
        $round->update(['status' => RoundModel::STATUS_CLOSED]);
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
        // Make sure the round is also opened.
        $this->openRound($session->round);

        if($session->is_opened)
        {
            return;
        }
        DB::transaction(function() use($session) {
            // Open the session
            $session->update(['status' => SessionModel::STATUS_OPENED]);
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
        $session->update(['status' => SessionModel::STATUS_CLOSED]);
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
