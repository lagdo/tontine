<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\FundDef;
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
        $onRoundFilter = fn(Builder|Relation $q) => $q->ofRound($round);
        return $round->guild->funds()
            ->when($filter === true, fn(Builder|Relation $query) => $query
                ->whereHas('funds', $onRoundFilter))
            ->when($filter === false, fn(Builder|Relation $query) => $query
                ->whereDoesntHave('funds', $onRoundFilter))
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
            ->with([
                'funds' => fn(Builder|Relation $q) => $q->where('round_id', $round->id),
            ])
            ->withCount([
                'funds as funds_in_round_count' => fn(Builder|Relation $q) => $q->ofRound($round),
            ])
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
     * @return FundDef|null
     */
    public function getFundDef(Round $round, int $defId): ?FundDef
    {
        return $round->guild
            ->funds()
            ->user()
            ->withCount([
                'funds' => fn(Builder|Relation $q) => $q->where('round_id', $round->id),
            ])
            ->find($defId);
    }

    /**
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function enableFund(Round $round, int $defId): void
    {
        $def = $this->getFundDef($round, $defId);
        if(!$def || $def->funds_count > 0)
        {
            return; // Todo: throw an exception
        }

        $itemQuery = Fund::withoutGlobalScopes()->where('def_id', $defId);
        $startSession = $this->getStartSession($round, $itemQuery);
        $endSession = $this->getEndSession($round, $itemQuery);
        if($endSession->day_date <= $startSession->day_date)
        {
            return; // Todo: throw an exception
        }

        // Create the fund
        $def->funds()->create([
            'round_id' => $round->id,
            'start_sid' => $startSession->id,
            'end_sid' => $endSession->id,
            'interest_sid' => $endSession->id,
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
        $def = $this->getFundDef($round, $defId);
        if(!$def || $def->funds_count === 0)
        {
            return;
        }

        // Delete the fund
        $def->funds()->where('round_id', $round->id)->delete();
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

    /**
     * Get the number of active funds in the round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getFundCount(Round $round): int
    {
        return $round->funds()->real()->user()->count();
    }
}
