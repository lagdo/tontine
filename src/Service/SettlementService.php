<?php

namespace Siak\Tontine\Service;

use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;

use Illuminate\Support\Collection;

abstract class SettlementService
{
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
     * @param int $chargeId
     *
     * @return Charge|null
     */
    public function getCharge(int $chargeId): ?Charge
    {
        return $this->tenantService->tontine()->charges()->find($chargeId);
    }

    /**
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     * @param int $page
     *
     * @return Collection
     */
    abstract public function getMembers(Charge $charge, Session $session, ?bool $onlyPaid = null, int $page = 0): Collection;

    /**
     * Get the number of members in the selected round.
     *
     * @param Charge $charge
     * @param Session $session
     * @param bool $onlyPaid|null
     *
     * @return int
     */
    abstract public function getMemberCount(Charge $charge, Session $session, ?bool $onlyPaid = null): int;

    /**
     * Create a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    abstract public function createSettlement(Charge $charge, Session $session, int $Id): void;

    /**
     * Delete a settlement
     *
     * @param Charge $charge
     * @param Session $session
     * @param int $Id
     *
     * @return void
     */
    abstract public function deleteSettlement(Charge $charge, Session $session, int $Id): void;
}
