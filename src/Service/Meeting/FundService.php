<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

class FundService
{
    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * @param Session $session
     * @param bool $onlyReal
     *
     * @return Relation
     */
    public function getSessionQuery(Session $session, bool $onlyReal): Relation
    {
        return $session->funds()
            ->join('fund_defs', 'fund_defs.id', '=', 'funds.def_id')
            ->when($onlyReal, fn(Builder $query) => $query->real())
            ->with(['pool'])
            ->orderBy('fund_defs.type') // The default fund is first in the list.
            ->orderBy('funds.id');
    }

    /**
     * Get the active funds of a session.
     *
     * @param Session $session
     * @param bool $onlyReal
     *
     * @return Collection
     */
    public function getSessionFunds(Session $session, bool $onlyReal = true): Collection
    {
        return $this->getSessionQuery($session, $onlyReal)->get();
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getStartingFunds(Session $session): Collection
    {
        return $this->getSessionFunds($session, true)
            ->filter(fn($fund) => $fund->start_sid === $session->id && $fund->start_amount > 0);
    }

    /**
     * @param Session $session
     *
     * @return Collection
     */
    public function getEndingFunds(Session $session): Collection
    {
        return $this->getSessionFunds($session, true)
            ->filter(fn($fund) => $fund->end_sid === $session->id && $fund->end_amount > 0);
    }

    /**
     * Get a list of funds for the dropdown select component.
     *
     * @param Session $session
     * @param bool $onlyReal
     *
     * @return Collection
     */
    public function getSessionFundList(Session $session, bool $onlyReal = true): Collection
    {
        return $this->getSessionQuery($session, $onlyReal)
            ->select(['funds.id'])
            // Since the "title" field is defined in the model and not in the database,
            // we need to get the data before we call the pluck() method.
            ->get()
            ->pluck('title', 'id');
    }

    /**
     * Get the default fund.
     *
     * @param Round $round
     *
     * @return Fund|null
     */
    public function getDefaultFund(Round $round): ?Fund
    {
        return $this->tenantService->guild()->default_fund
            ->funds()->real()->where('round_id', $round->id)->first();
    }

    /**
     * Get an active fund of a session.
     *
     * @param Session $session
     * @param int $fundId
     * @param bool $onlyReal
     *
     * @return Fund|null
     */
    public function getSessionFund(Session $session, int $fundId, bool $onlyReal = true): ?Fund
    {
        if($fundId === 0)
        {
            return $this->getDefaultFund($session->round);
        }

        $fund = $session->funds()
            ->when($onlyReal, fn(Builder $query) => $query->real())
            ->with(['pool'])
            ->find($fundId);
        return $fund ?? $this->getDefaultFund($session->round);
    }

    /**
     * @param Fund $fund
     * @param Session $session
     *
     * @return Builder|Relation
     */
    private function getFundSessionsQuery(Fund $fund, Session $session): Builder|Relation
    {
        return $fund->sessions()
            ->where('day_date', '<=', $session->day_date)
            ->where('day_date', '<=', $fund->interest->day_date);
    }

    /**
     * Get the sessions to be used for profit calculation.
     *
     * @param Fund $fund
     * @param Session $session
     *
     * @return Collection
     */
    public function getFundSessions(Fund $fund, Session $session): Collection
    {
        return $this->getFundSessionsQuery($fund, $session)->get();
    }

    /**
     * Get the id of sessions to be used for profit calculation.
     *
     * @param Fund $fund
     * @param Session $session
     *
     * @return Collection
     */
    public function getFundSessionIds(Fund $fund, Session $session): Collection
    {
        return $this->getFundSessionsQuery($fund, $session)->pluck('id');
    }
}
