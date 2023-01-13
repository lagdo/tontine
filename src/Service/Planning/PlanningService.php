<?php

namespace Siak\Tontine\Service\Planning;

use Siak\Tontine\Service\Events\EventTrait;
use Siak\Tontine\Service\Tontine\TenantService;

class PlanningService
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
}
