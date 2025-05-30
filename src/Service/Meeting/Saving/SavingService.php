<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\SearchSanitizer;
use Closure;

class SavingService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param SearchSanitizer $searchSanitizer
     */
    public function __construct(private LocaleService $localeService,
        private TenantService $tenantService, private SearchSanitizer $searchSanitizer)
    {}

    /**
     * Count the session funds.
     *
     * @param Session $session
     *
     * @return int
     */
    public function getFundCount(Session $session): int
    {
        return $session->funds()->real()->count();
    }

    /**
     * @param Session $session
     *
     * @return Relation
     */
    private function getFundQuery(Session $session): Relation
    {
        $sqlFrom = "savings s where s.fund_id=funds.id and s.session_id={$session->id}";
        return $session->funds()->real()
            ->addSelect([
                DB::raw("(select count(*) from $sqlFrom) as s_count"),
                DB::raw("(select sum(s.amount) from $sqlFrom) as s_amount"),
            ]);
    }

    /**
     * Get the session funds.
     *
     * @param Session $session
     * @param int $page
     *
     * @return Collection
     */
    public function getFunds(Session $session, int $page = 0): Collection
    {
        return $this->getFundQuery($session)
            ->page($page, $this->tenantService->getLimit())
            ->join('fund_defs', 'fund_defs.id', '=', 'funds.def_id')
            ->orderBy('fund_defs.type') // The default fund is first in the list.
            ->orderBy('funds.id')
            ->get();
    }

    /**
     * Get a session fund.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return Fund|null
     */
    public function getFund(Session $session, int $fundId): ?Fund
    {
        return $this->getFundQuery($session)->find($fundId);
    }

    /**
     * @param Session $session
     * @param Fund|null $fund
     *
     * @return Builder|Relation
     */
    private function getSavingQuery(Session $session, ?Fund $fund): Builder|Relation
    {
        return $session->savings()->when($fund !== null,
            fn(Builder $query) => $query->where('fund_id', $fund->id));
    }

    /**
     * Find a member saving for a given session.
     *
     * @param Session $session
     * @param Fund $fund
     * @param Member $member
     *
     * @return Saving|null
     */
    public function findSaving(Session $session, Fund $fund, Member $member): ?Saving
    {
        return $this->getSavingQuery($session, $fund)
            ->where('member_id', $member->id)
            ->first();
    }

    /**
     * @param Session $session
     * @param Fund $fund
     * @param Member $member
     * @param Saving $saving
     * @param int $amount
     *
     * @return void
     */
    private function persistSaving(Session $session, Fund $fund, Member $member,
        Saving $saving, int $amount)
    {
        $saving->amount = $amount;
        $saving->session()->associate($session);
        $saving->fund()->associate($fund);
        $saving->member()->associate($member);
        $saving->save();
    }

    /**
     * Create or update a saving.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param Member $member
     * @param int $amount
     *
     * @return void
     */
    public function saveSaving(Session $session, Fund $fund, Member $member, int $amount): void
    {
        $saving = $this->findSaving($session, $fund, $member);
        if(!$saving)
        {
            $saving = new Saving();
        }

        $this->persistSaving($session, $fund, $member, $saving, $amount);
    }

    /**
     * @param Session $session
     * @param Fund $fund
     *
     * @return Closure
     */
    private function getMemberSavingsFilter(Session $session, Fund $fund): Closure
    {
        return fn(/*Builder|Relation*/ $query) =>
            $query->where('session_id', $session->id)->where('fund_id', $fund->id);
    }

    /**
     * @param Session $session
     * @param Fund $fund
     * @param string $search
     * @param bool|null $filter
     *
     * @return Builder|Relation
     */
    private function getMembersQuery(Session $session, Fund $fund,
        string $search, ?bool $filter): Builder|Relation
    {
        $savingsFilter = $this->getMemberSavingsFilter($session, $fund);
        return $session->round->members()
            ->search($this->searchSanitizer->sanitize($search))
            ->when($filter === true, fn(Builder $query) =>
                $query->whereHas('savings', $savingsFilter))
            ->when($filter === false, fn(Builder $query) =>
                $query->whereDoesntHave('savings', $savingsFilter));
    }

    /**
     * Get a paginated list of members.
     *
     * @param Session $session
     * @param Fund $fund
     * @param string $search
     * @param bool|null $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Session $session, Fund $fund, string $search,
        ?bool $filter, int $page = 0): Collection
    {
        return $this->getMembersQuery($session, $fund, $search, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->with('savings', $this->getMemberSavingsFilter($session, $fund))
            ->orderBy('name', 'asc')
            ->get()
            ->each(fn($member) => $member->saving = $member->savings->first());
    }

    /**
     * Get the number of members.
     *
     * @param Session $session
     * @param Fund $fund
     * @param string $search
     * @param bool|null $filter
     *
     * @return int
     */
    public function getMemberCount(Session $session, Fund $fund, string $search, ?bool $filter): int
    {
        return $this->getMembersQuery($session, $fund, $search, $filter)->count();
    }

    /**
     * Delete a saving.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param Member $member
     *
     * @return void
     */
    public function deleteMemberSaving(Session $session, Fund $fund, Member $member): void
    {
        $session->savings()
            ->where('fund_id', $fund->id)
            ->where('member_id', $member->id)
            ->delete();
    }

    /**
     * Get a single member.
     *
     * @param Round $round
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(Round $round, int $memberId): ?Member
    {
        return $round->members()->find($memberId);
    }

    /**
     * @param Fund $fund
     * @param int $amount
     *
     * @return void
     */
    public function saveFundStartAmount(Fund $fund, int $amount): void
    {
        $options = $fund->options;
        $options['amount']['start'] = $amount;
        $fund->options = $options;
        $fund->save();
    }

    /**
     * @param Fund $fund
     * @param int $amount
     *
     * @return void
     */
    public function saveFundEndAmount(Fund $fund, int $amount): void
    {
        $options = $fund->options;
        $options['amount']['end'] = $amount;
        $fund->options = $options;
        $fund->save();
    }

    /**
     * @param Fund $fund
     * @param int $amount
     *
     * @return void
     */
    public function saveFundProfitAmount(Fund $fund, int $amount): void
    {
        $options = $fund->options;
        $options['amount']['profit'] = $amount;
        $fund->options = $options;
        $fund->save();
    }
}
