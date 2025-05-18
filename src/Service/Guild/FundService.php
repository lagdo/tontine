<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\FundDef;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Service\TenantService;

class FundService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of funds.
     *
     * @param Guild $guild
     * @param int $page
     *
     * @return Collection
     */
    public function getFunds(Guild $guild, int $page = 0): Collection
    {
        return $guild->funds()->user()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of funds.
     *
     * @param Guild $guild
     *
     * @return int
     */
    public function getFundCount(Guild $guild): int
    {
        return $guild->funds()->user()->count();
    }

    /**
     * Get a single fund.
     *
     * @param Guild $guild
     * @param int $fundId    The fund id
     * @param bool $onlyActive
     * @param bool $withDefault
     *
     * @return FundDef|null
     */
    public function getFund(Guild $guild, int $fundId,
        bool $onlyActive = false, bool $withDefault = false): ?FundDef
    {
        if($withDefault && $fundId === 0)
        {
            return $guild->default_fund;
        }

        $fund = $guild->funds()
            ->when($onlyActive, fn(Builder $query) => $query->active())
            ->withCount('funds')
            ->find($fundId);
        return $fund ?? ($withDefault ? $guild->default_fund : null);
    }

    /**
     * Add new fund.
     *
     * @param Guild $guild
     * @param array $values
     *
     * @return bool
     */
    public function createFund(Guild $guild, array $values): bool
    {
        $values['type'] = FundDef::TYPE_USER;
        $guild->funds()->create($values);

        return true;
    }

    /**
     * Update a fund.
     *
     * @param FundDef $fund
     * @param array $values
     *
     * @return bool
     */
    public function updateFund(FundDef $fund, array $values): bool
    {
        $values['type'] = FundDef::TYPE_USER;

        return $fund->update($values);
    }

    /**
     * Toggle a fund.
     *
     * @param FundDef $fund
     *
     * @return void
     */
    public function toggleFund(FundDef $fund)
    {
        $fund->update(['active' => !$fund->active]);
    }

    /**
     * Delete a fund.
     *
     * @param Guild $guild
     * @param FundDef $fund
     *
     * @return void
     */
    public function deleteFund(Guild $guild, FundDef $fund)
    {
        if($fund->id === $guild->default_fund->id)
        {
            // Cannot delete the default fund.
            return;
        }

        $fund->delete();
    }
}
