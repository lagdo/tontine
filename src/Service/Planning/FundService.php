<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Service\TenantService;

class FundService
{
    use SessionTrait;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * Get a paginated list of funds.
     *
     * @param Round $round
     * @param int $page
     *
     * @return Collection
     */
    public function getFundDefs(Round $round, int $page = 0): Collection
    {
        return $this->tenantService->guild()
            ->funds()
            ->user()
            ->with(['funds' => fn($query) => $query->ofRound($round)])
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of funds.
     *
     * @return int
     */
    public function getFundDefCount(): int
    {
        return $this->tenantService->guild()->funds()->user()->count();
    }

    /**
     * Get a fund.
     *
     * @param Round $round
     * @param int $fundId
     *
     * @return Fund|null
     */
    public function getFund(Round $round, int $fundId): ?Fund
    {
        return Fund::ofRound($round)->with(['sessions'])->find($fundId);
    }

    /**
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function enableFund(Round $round, int $defId): void
    {
        $def = $this->tenantService->guild()
            ->funds()
            ->user()
            ->withCount(['funds' => fn($query) => $query->ofRound($round)])
            ->find($defId);
        if(!$def || $def->funds_count > 0)
        {
            return;
        }

        // Create the fund
        $def->funds()->create([
            'round_id' => $round->id,
            'start_sid' => $round->start->id,
            'end_sid' => $round->end->id,
            'interest_sid' => $round->end->id,
        ]);
    }

    /**
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function disableFund(Round $round, int $defId): void
    {
        $def = $this->tenantService->guild()
            ->funds()
            ->user()
            ->withCount(['funds' => fn($query) => $query->ofRound($round)])
            ->find($defId);
        if(!$def || $def->funds_count === 0)
        {
            return;
        }

        // Delete the fund
        $def->funds()->ofRound($round)->delete();
    }

    /**
     * Save the fund sessions.
     *
     * @param Fund $fund
     * @param array $values
     *
     * @return void
     */
    public function saveSessions(Fund $fund, array $values)
    {
        $fund->update($values);
    }
}
