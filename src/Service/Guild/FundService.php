<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\FundDef;
use Siak\Tontine\Service\TenantService;

use function trans;

class FundService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get the default fund.
     *
     * @return FundDef
     */
    public function getDefaultFund(): FundDef
    {
        $defaultFund = $this->tenantService->guild()->default_fund;
        $defaultFund->title = trans('tontine.fund.labels.default');
        return $defaultFund;
    }

    /**
     * Get all active funds, included the default one.
     *
     * @return Collection
     */
    public function getActiveFunds(): Collection
    {
        return $this->tenantService->guild()->funds()->active()
            ->get()
            ->prepend($this->getDefaultFund());
    }

    /**
     * Get a list of funds for the dropdown select component.
     *
     * @return Collection
     */
    public function getFundList(): Collection
    {
        return $this->getActiveFunds()->pluck('title', 'id');
    }

    /**
     * Get a paginated list of funds.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getFunds(int $page = 0): Collection
    {
        return $this->tenantService->guild()->funds()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of funds.
     *
     * @return int
     */
    public function getFundCount(): int
    {
        return $this->tenantService->guild()->funds()->count();
    }

    /**
     * Get a single fund.
     *
     * @param int $fundId    The fund id
     * @param bool $onlyActive
     * @param bool $withDefault
     *
     * @return FundDef|null
     */
    public function getFund(int $fundId, bool $onlyActive = false, bool $withDefault = false): ?FundDef
    {
        if($withDefault && ($fundId === 0 ||
            $fundId === $this->tenantService->guild()->default_fund->id))
        {
            return $this->getDefaultFund();
        }

        $fund = $this->tenantService->guild()->funds()
            ->when($onlyActive, fn(Builder $query) => $query->active())
            ->withCount('funds')
            ->find($fundId);
        return $fund ?? ($withDefault ? $this->getDefaultFund() : null);
    }

    /**
     * @param FundDef $fund
     *
     * @return string
     */
    public function getFundTitle(FundDef $fund): string
    {
        return $fund->id === $this->tenantService->guild()->default_fund->id ?
            $this->getDefaultFund()->title : $fund->title;
    }

    /**
     * Add new fund.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createFund(array $values): bool
    {
        $this->tenantService->guild()->funds()->create($values);

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
     * @param FundDef $fund
     *
     * @return void
     */
    public function deleteFund(FundDef $fund)
    {
        if($fund->id === $this->tenantService->guild()->default_fund->id)
        {
            // Cannot delete the default fund.
            return;
        }

        $fund->delete();
    }
}
