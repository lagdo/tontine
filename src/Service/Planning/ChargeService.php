<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\Events\EventTrait;
use Siak\Tontine\Service\TenantService;

class ChargeService
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
     * Get the current tontine.
     *
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
     * Get a paginated list of charges.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getCharges(int $page = 0): Collection
    {
        $charges = $this->tenantService->tontine()->charges();
        if($page > 0 )
        {
            $charges->take($this->tenantService->getLimit());
            $charges->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $charges->get();
    }

    /**
     * Get the number of charges.
     *
     * @return int
     */
    public function getChargeCount(): int
    {
        return $this->tenantService->tontine()->charges()->count();
    }

    /**
     * Get a single charge.
     *
     * @param int $chargeId    The charge id
     *
     * @return Charge|null
     */
    public function getCharge(int $chargeId): ?Charge
    {
        return $this->tenantService->tontine()->charges()->find($chargeId);
    }

    /**
     * Add a new charge.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createCharges(array $values): bool
    {
        DB::transaction(function() use($values) {
            $tontine = $this->tenantService->tontine();
            $charges = $tontine->charges()->createMany($values);
            // Create charges bills
            foreach($charges as $charge)
            {
                $this->chargeCreated($tontine, $charge);
            }
        });
        return true;
    }

    /**
     * Update a charge.
     *
     * @param Charge $charge
     * @param array $values
     *
     * @return int
     */
    public function updateCharge(Charge $charge, array $values): int
    {
        return $charge->update($values);
    }

    /**
     * Toggle a charge.
     *
     * @param Charge $charge
     *
     * @return void
     */
    public function toggleCharge(Charge $charge)
    {
        $charge->update(['active' => !$charge->active]);
    }

    /**
     * Delete a charge.
     *
     * @param Charge $charge
     *
     * @return void
     */
    public function deleteCharge(Charge $charge)
    {
        $charge->update(['active' => false]);
    }

    /**
     * @param int $count
     *
     * @return Collection
     */
    public function getFakeCharges(int $count): Collection
    {
        return Charge::factory()->count($count)->make([
            'tontine_id' => $this->tenantService->tontine(),
        ]);
    }
}
