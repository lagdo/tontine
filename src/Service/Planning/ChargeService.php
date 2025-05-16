<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Bill;
use Siak\Tontine\Model\ChargeDef;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Service\DataSyncService;
use Siak\Tontine\Service\TenantService;
use Exception;

class ChargeService
{
    /**
     * @param TenantService $tenantService
     * @param DataSyncService $dataSyncService
     */
    public function __construct(private TenantService $tenantService,
        private DataSyncService $dataSyncService)
    {}

    /**
     * @param Round $round
     * @param bool $filter|null
     *
     * @return Relation
     */
    private function getQuery(Round $round, ?bool $filter): Relation
    {
        $chargeCallback = fn($q) => $q->where('round_id', $round->id);
        return $round->guild->charges()
            ->with(['charges' => $chargeCallback])
            ->when($filter === true, fn(Builder $query) => $query
                ->whereHas('charges', $chargeCallback))
            ->when($filter === false, fn(Builder $query) => $query
                ->whereDoesntHave('charges', $chargeCallback));
    }

    /**
     * Get a paginated list of charges.
     *
     * @param Round $round
     * @param int $page
     *
     * @return Collection
     */
    public function getChargeDefs(Round $round, ?bool $filter, int $page = 0): Collection
    {
        return $this->getQuery($round, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('type', 'asc')
            ->orderBy('period', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the number of charges.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getChargeDefCount(Round $round, ?bool $filter): int
    {
        return $this->getQuery($round, $filter)->count();
    }

    /**
     * Get a single charge.
     *
     * @param Round $round
     * @param int $chargeId
     *
     * @return ChargeDef|null
     */
    public function getChargeDef(Round $round, int $chargeId): ?ChargeDef
    {
        return $this->getQuery($round, null)->find($chargeId);
    }

    /**
     * Add new charge.
     *
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function enableCharge(Round $round, int $defId): void
    {
        $def = $this->getChargeDef($round, $defId);
        if(!$def || $def->charges->count() > 0)
        {
            return;
        }

        DB::transaction(function() use($round, $def) {
            $charge = $def->charges()->create(['round_id' => $round->id]);
            // Create charges bills
            $this->dataSyncService->chargeCreated($round->guild, $charge);
        });
    }

    /**
     * Delete a charge.
     *
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function disableCharge(Round $round, int $defId): void
    {
        $def = $this->getChargeDef($round, $defId);
        if(!$def || $def->charges->count() === 0)
        {
            return;
        }

        $charge = $def->charges->first();
        // Will fail if a settlement exists for any of those bills.
        try
        {
            DB::transaction(function() use($charge) {
                $billIds = Bill::ofCharge($charge, true)->pluck('id');
                $charge->oneoff_bills()->delete();
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
}
