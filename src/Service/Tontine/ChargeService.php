<?php

namespace Siak\Tontine\Service\Tontine;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\Charge;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\EventTrait;

class ChargeService
{
    use EventTrait;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of charges.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getCharges(int $page = 0): Collection
    {
        return $this->tenantService->tontine()->charges()
            // ->withCount(['tontine_bills', 'round_bills', 'session_bills', 'libre_bills'])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('type', 'asc')
            ->orderBy('period', 'desc')
            ->orderBy('name', 'asc')
            ->get();
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
        return $this->tenantService->tontine()->charges()
            // ->withCount(['tontine_bills', 'round_bills', 'session_bills', 'libre_bills'])
            ->find($chargeId);
    }

    /**
     * Add new charge.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createCharge(array $values): bool
    {
        DB::transaction(function() use($values) {
            $tontine = $this->tenantService->tontine();
            $charge = $tontine->charges()->create($values);
            // Create charges bills
            $this->chargeCreated($tontine, $charge);
        });
        return true;
    }

    /**
     * Update a charge.
     *
     * @param Charge $charge
     * @param array $values
     *
     * @return bool
     */
    public function updateCharge(Charge $charge, array $values): bool
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
        // Delete the charge and the related bills.
        // Will fail if a settlement exists for any of those bills.
        try
        {
            DB::transaction(function() use($charge) {
                $billIds = Bill::ofCharge($charge, true)->pluck('id');
                $charge->tontine_bills()->delete();
                $charge->round_bills()->delete();
                $charge->session_bills()->delete();
                $charge->libre_bills()->delete();
                Bill::whereIn('id', $billIds)->delete();
                $charge->delete();
            });
        }
        catch(Exception)
        {
            throw new MessageException(trans('tontine.charge.errors.cannot_delete'));
        }
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
