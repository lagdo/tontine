<?php

namespace Siak\Tontine\Service;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Payable;
use Siak\Tontine\Model\Receivable;

class FundService
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
     * Get a paginated list of funds in the selected round.
     *
     * @param int $page
     *
     * @return array
     */
    public function getFunds(int $page = 0)
    {
        $funds = $this->tenantService->round()->funds();
        if($page > 0 )
        {
            $funds->take($this->tenantService->getLimit());
            $funds->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $funds->get();
    }

    /**
     * Get the number of funds in the selected round.
     *
     * @return int
     */
    public function getFundCount(): int
    {
        return $this->tenantService->round()->funds()->count();
    }

    /**
     * Get a single fund.
     *
     * @param int $fundId    The fund id
     *
     * @return Fund|null
     */
    public function getFund(int $fundId): ?Fund
    {
        return $this->tenantService->getFund($fundId);
    }

    /**
     * Add a new fund.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createFunds(array $values): bool
    {
        DB::transaction(function() use($values) {
            $this->tenantService->round()->funds()->createMany($values);
        });

        return true;
    }

    /**
     * Update a fund.
     *
     * @param Fund $fund
     * @param array $values
     *
     * @return int
     */
    public function updateFund(Fund $fund, array $values): int
    {
        return $fund->update($values);
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
        // Todo: soft delete this model.
        DB::transaction(function() use($fund) {
            // Delete the payables
            Payable::join('subscriptions', 'subscriptions.id', '=', 'payables.subscription_id')
                ->where('subscriptions.fund_id', $fund->id)
                ->delete();
            // Delete the receivables
            Receivable::join('subscriptions', 'subscriptions.id', '=', 'payables.subscription_id')
                ->where('subscriptions.fund_id', $fund->id)
                ->delete();
            // Delete the fund
            $fund->delete();
        });
    }

    /**
     * @param int $count
     *
     * @return Collection
     */
    public function getFakeFunds(int $count): Collection
    {
        return Fund::factory()->count($count)->make([
            'round_id' => $this->tenantService->round(),
        ]);
    }
}
