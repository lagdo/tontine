<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Session;
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
        $defaultFund = $this->tenantService->tontine()->default_fund;
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
     * @param bool $withDefault
     *
     * @return Fund|null
     */
    public function getFund(int $fundId, bool $onlyActive = false, bool $withDefault = false): ?Fund
    {
        if($withDefault && $fundId === $this->tenantService->tontine()->default_fund->id)
        {
            return $this->getDefaultFund();
        }
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

    /**
     * @param Session $currentSession
     * @param Fund $fund
     *
     * @return Builder|Relation
     */
    private function getFundSessionsQuery(Session $currentSession, Fund $fund): Builder|Relation
    {
        // Will return all the tontine sessions,
        // or all those after the last closing, if there's any.
        $lastSessionDate = $currentSession->start_at->format('Y-m-d');
        $sessionsQuery = $this->tenantService->tontine()->sessions()
            ->whereDate('start_at', '<=', $lastSessionDate);

        // The closing sessions before te current session.
        $closingSessions = $this->tenantService->tontine()->sessions()
            ->whereDate('start_at', '<', $lastSessionDate)
            ->whereHas('closings', function(Builder|Relation $query) use($fund) {
                $query->where('fund_id', $fund->id);
            })
            ->orderByDesc('start_at')
            ->get();
        if($closingSessions->count() === 0)
        {
            // All the closing sessions are after the current session.
            return $sessionsQuery;
        }

        // The most recent previous closing session
        $firstSessionDate = $closingSessions->last()->start_at->format('Y-m-d');
        // Return all the sessions after the most recent previous closing session
        return $sessionsQuery->whereDate('start_at', '>', $firstSessionDate);
    }

    /**
     * Get the sessions to be used for profit calculation.
     *
     * @param Session $currentSession
     * @param Fund $fund
     *
     * @return Collection
     */
    public function getFundSessions(Session $currentSession, Fund $fund): Collection
    {
        return $this->getFundSessionsQuery($currentSession, $fund)->get();
    }

    /**
     * Get the id of sessions to be used for profit calculation.
     *
     * @param Session $currentSession
     * @param Fund $fund
     *
     * @return Collection
     */
    public function getFundSessionIds(Session $currentSession, Fund $fund): Collection
    {
        return $this->getFundSessionsQuery($currentSession, $fund)->pluck('id');
    }
}
