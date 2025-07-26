<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lagdo\Facades\Logger;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\ChargeDef;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Service\TenantService;
use Exception;

use function trans;

class ChargeService
{
    /**
     * @param TenantService $tenantService
     * @param BillSyncService $billSyncService
     */
    public function __construct(private TenantService $tenantService,
        private BillSyncService $billSyncService)
    {}

    /**
     * @param Round $round
     * @param bool $filter|null
     *
     * @return Relation
     */
    private function getQuery(Round $round, ?bool $filter): Relation
    {
        $onRoundFilter = fn(Builder $q) => $q->where('round_id', $round->id);
        return $round->guild->charges()
            ->when($filter === true, fn(Builder $query) => $query
                ->whereHas('charges', $onRoundFilter))
            ->when($filter === false, fn(Builder $query) => $query
                ->whereDoesntHave('charges', $onRoundFilter));
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
            ->withCount([
                'charges' => fn(Builder $q) => $q->where('round_id', $round->id),
            ])
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
        return $this->getQuery($round, null)
            // It's important to fetch the relations and filter on the round here.
            ->with([
                'charges' => fn(Relation $q) => $q->where('round_id', $round->id),
            ])
            ->find($chargeId);
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

            $this->billSyncService->chargeEnabled($round, $charge);
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
        try
        {
            DB::transaction(function() use($round, $charge) {
                $this->billSyncService->chargeRemoved($round, $charge);

                $charge->delete();
            });
        }
        catch(Exception $e)
        {
            Logger::error('Error while removing a charge.', [
                'message' => $e->getMessage(),
            ]);
            throw new MessageException(trans('tontine.charge.errors.cannot_remove'));
        }
    }

    /**
     * Get the number of active charges in the round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getChargeCount(Round $round): int
    {
        return $round->charges()->count();
    }
}
