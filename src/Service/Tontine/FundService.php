<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
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
     * @return Fund
     */
    public function getDefaultFund(): Fund
    {
        $defaultFund = new Fund();
        $defaultFund->id = 0;
        $defaultFund->title = trans('tontine.fund.labels.default');
        $defaultFund->active = true;
        return $defaultFund;
    }

    /**
     * Get all active funds, included the default one.
     *
     * @return Collection
     */
    public function getActiveFunds(): Collection
    {
        return $this->tenantService->tontine()->funds()->active()
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
        return $this->tenantService->tontine()->funds()
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
        return $this->tenantService->tontine()->funds()->count();
    }

    /**
     * Get a single fund.
     *
     * @param int $fundId    The fund id
     * @param bool $onlyActive
     *
     * @return Fund|null
     */
    public function getFund(int $fundId, bool $onlyActive = false): ?Fund
    {
        return $this->tenantService->tontine()->funds()
            ->when($onlyActive, fn(Builder $query) => $query->active())
            ->find($fundId);
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
        $this->tenantService->tontine()->funds()->create($values);
        return true;
    }

    /**
     * Update a fund.
     *
     * @param Fund $fund
     * @param array $values
     *
     * @return bool
     */
    public function updateFund(Fund $fund, array $values): bool
    {
        return $fund->update($values);
    }

    /**
     * Toggle a fund.
     *
     * @param Fund $fund
     *
     * @return void
     */
    public function toggleFund(Fund $fund)
    {
        $fund->update(['active' => !$fund->active]);
    }

    /**
     * Delete a fund.
     *
     * @param Fund $fund
     *
     * @return void
     */
    public function deleteFund(Fund $fund)
    {
        $fund->delete();
    }
}
