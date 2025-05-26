<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
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
     * @param Round $round
     * @param bool $filter|null
     *
     * @return Relation
     */
    private function getQuery(Round $round, ?bool $filter): Relation
    {
        return $round->guild->funds()
            ->when($filter === true, fn(Builder $query) => $query
                ->whereHas('funds', fn($q) => $q->ofRound($round)))
            ->when($filter === false, fn(Builder $query) => $query
                ->whereDoesntHave('funds', fn($q) => $q->ofRound($round)))
            ->when(!$round->add_default_fund, fn($query) => $query->user());
    }

    /**
     * Get a paginated list of funds.
     *
     * @param Round $round
     * @param bool $filter|null
     * @param int $page
     *
     * @return Collection
     */
    public function getFundDefs(Round $round, ?bool $filter, int $page = 0): Collection
    {
        return $this->getQuery($round, $filter)
            ->with(['funds' => fn($query) => $query->ofRound($round)])
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of funds.
     *
     * @param Round $round
     * @param bool $filter|null
     *
     * @return int
     */
    public function getFundDefCount(Round $round, ?bool $filter): int
    {
        return $this->getQuery($round, $filter)->count();
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
        $def = $round->guild
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
        $def = $round->guild
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
