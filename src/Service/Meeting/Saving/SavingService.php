<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

class SavingService
{
    /**
     * @param LocaleService $localeService
     * @param TenantService $tenantService
     * @param FundService $fundService
     */
    public function __construct(private LocaleService $localeService,
        private TenantService $tenantService, private FundService $fundService)
    {}

    /**
     * @param Session $session
     * @param Fund|null $fund
     *
     * @return Builder|Relation
     */
    private function getSavingQuery(Session $session, ?Fund $fund): Builder|Relation
    {
        return $session->savings()
            ->when($fund !== null, fn(Builder $query) => $query->where('fund_id', $fund->id));
    }

    /**
     * Count the savings for a given session.
     *
     * @param Session $session
     * @param Fund|null $fund
     *
     * @return int
     */
    public function getSavingCount(Session $session, ?Fund $fund): int
    {
        return $this->getSavingQuery($session, $fund)->count();
    }

    /**
     * Get the savings sum for a given session.
     *
     * @param Session $session
     * @param Fund|null $fund
     *
     * @return int
     */
    public function getSavingTotal(Session $session, ?Fund $fund): int
    {
        return $this->getSavingQuery($session, $fund)->sum('amount');
    }

    /**
     * Get the savings for a given session.
     *
     * @param Session $session
     * @param Fund|null $fund
     * @param int $page
     *
     * @return Collection
     */
    public function getSavings(Session $session, ?Fund $fund, int $page = 0): Collection
    {
        return $this->getSavingQuery($session, $fund)
            ->select(DB::raw('savings.*, members.name as member'))
            ->join('members', 'members.id', '=', 'savings.member_id')
            ->with(['fund'])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('members.name')
            ->get();
    }

    /**
     * Get a saving for a given session.
     *
     * @param Session $session
     * @param int $savingId
     *
     * @return Saving|null
     */
    public function getSaving(Session $session, int $savingId): ?Saving
    {
        return $session->savings()->with(['fund'])->find($savingId);
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
     * Create a saving.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param Member $member
     * @param int $amount
     *
     * @return void
     */
    public function createSaving(Session $session, Fund $fund, Member $member, int $amount): void
    {
        $saving = new Saving();
        $this->persistSaving($session, $fund, $member, $saving, $amount);
    }

    /**
     * Update a saving.
     *
     * @param Session $session The session
     * @param Fund $fund
     * @param Member $member
     * @param Saving $saving
     * @param int $amount
     *
     * @return void
     */
    public function updateSaving(Session $session, Fund $fund, Member $member,
        Saving $saving, int $amount): void
    {
        $this->persistSaving($session, $fund, $member, $saving, $amount);
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
     * Delete a saving.
     *
     * @param Session $session The session
     * @param int $savingId
     *
     * @return void
     */
    public function deleteSaving(Session $session, int $savingId): void
    {
        $session->savings()->where('id', $savingId)->delete();
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
        return $this->tenantService->tontine()->members()->active()
            ->when($search !== '', function(Builder $query) use($search) {
                $search = '%' . strtolower($search) . '%';
                return $query->where(DB::raw('lower(name)'), 'like', $search);
            })
            ->when($filter === true, function(Builder $query) use($savingsFilter) {
                $query->whereHas('savings', $savingsFilter);
            })
            ->when($filter === false, function(Builder $query) use($savingsFilter) {
                $query->whereDoesntHave('savings', $savingsFilter);
            });
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
            ->each(function($member) {
                $member->saving = $member->savings->first();
            });
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
     * @param int $id       The member id
     *
     * @return Member|null
     */
    public function getMember(int $id): ?Member
    {
        return $this->tenantService->tontine()->members()->active()->find($id);
    }
}
