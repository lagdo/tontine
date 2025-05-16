<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\ChargeDef;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Service\TenantService;
use Exception;

class ChargeService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService)
    {}

    /**
     * Get a paginated list of charges.
     *
     * @param Guild $guild
     * @param bool|null $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getCharges(Guild $guild, bool $filter = null, int $page = 0): Collection
    {
        return $guild->charges()
            ->when($filter !== null, fn(Builder $query) => $query->active($filter))
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('type', 'asc')
            ->orderBy('period', 'desc')
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the number of charges.
     *
     * @param Guild $guild
     * @param bool|null $filter
     *
     * @return int
     */
    public function getChargeCount(Guild $guild, bool $filter = null): int
    {
        return $guild->charges()
            ->when($filter !== null, fn(Builder $query) => $query->active($filter))
            ->count();
    }

    /**
     * Get a single charge.
     *
     * @param Guild $guild
     * @param int $chargeId    The charge id
     *
     * @return ChargeDef|null
     */
    public function getCharge(Guild $guild, int $chargeId): ?ChargeDef
    {
        return $guild->charges()->find($chargeId);
    }

    /**
     * Add new charge.
     *
     * @param Guild $guild
     * @param array $values
     *
     * @return bool
     */
    public function createCharge(Guild $guild, array $values): bool
    {
        $guild->charges()->create($values);
        return true;
    }

    /**
     * Update a charge.
     *
     * @param ChargeDef $charge
     * @param array $values
     *
     * @return bool
     */
    public function updateCharge(ChargeDef $charge, array $values): bool
    {
        return $charge->update($values);
    }

    /**
     * Toggle a charge.
     *
     * @param ChargeDef $charge
     *
     * @return void
     */
    public function toggleCharge(ChargeDef $charge)
    {
        $charge->update(['active' => !$charge->active]);
    }

    /**
     * Delete a charge.
     *
     * @param ChargeDef $charge
     *
     * @return void
     */
    public function deleteCharge(ChargeDef $charge)
    {
        try
        {
            $charge->delete();
        }
        catch(Exception)
        {
            throw new MessageException(trans('tontine.charge.errors.cannot_delete'));
        }
    }
}
