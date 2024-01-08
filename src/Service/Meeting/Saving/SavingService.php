<?php

namespace Siak\Tontine\Service\Meeting\Saving;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Fund;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Saving;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\LocaleService;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Tontine\FundService;

use function trans;

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
     * @param int $fundId
     *
     * @return Builder|Relation
     */
    private function getSavingQuery(Session $session, int $fundId): Builder|Relation
    {
        // $fundId < 0 => all the savings
        // $fundId === 0 => savings of the default fund
        // $fundId > 0 => savings of the corresponding fund
        return $session->savings()
            ->when($fundId === 0, function(Builder $query) {
                $query->whereNull('fund_id');
            })
            ->when($fundId > 0, function(Builder $query) use($fundId) {
                $query->where('fund_id', $fundId);
            });
    }

    /**
     * Count the savings for a given session.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return int
     */
    public function getSavingCount(Session $session, int $fundId): int
    {
        return $this->getSavingQuery($session, $fundId)->count();
    }

    /**
     * Get the savings sum for a given session.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return int
     */
    public function getSavingSum(Session $session, int $fundId): int
    {
        return $this->getSavingQuery($session, $fundId)->sum('amount');
    }

    /**
     * Get the savings for a given session.
     *
     * @param Session $session
     * @param int $fundId
     * @param int $page
     *
     * @return Collection
     */
    public function getSavings(Session $session, int $fundId, int $page = 0): Collection
    {
        return $this->getSavingQuery($session, $fundId)
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
     * @param int $fundId
     * @param int $memberId
     *
     * @return Saving|null
     */
    public function findSaving(Session $session, int $fundId, int $memberId): ?Saving
    {
        return $this->getSavingQuery($session, $fundId)
            ->where('member_id', $memberId)
            ->first();
    }

    /**
     * @param Session $session
     * @param Member $member
     * @param Fund|null $fund
     * @param Saving $saving
     * @param int $amount
     *
     * @return void
     */
    private function persistSaving(Session $session, Member $member,
        ?Fund $fund, Saving $saving, int $amount)
    {
        $saving->amount = $amount;
        $saving->member()->associate($member);
        $saving->session()->associate($session);
        if($fund !== null)
        {
            $saving->fund()->associate($fund);
        }
        else
        {
            $saving->fund()->dissociate();
        }
        $saving->save();
    }

    /**
     * Create a saving.
     *
     * @param Session $session The session
     * @param array $values
     *
     * @return void
     */
    public function createSaving(Session $session, array $values): void
    {
        if(!($member = $this->getMember($values['member'])))
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }
        $fund = !$values['fund'] ? null : $this->fundService->getFund($values['fund']);

        $saving = new Saving();
        $this->persistSaving($session, $member, $fund, $saving, $values['amount']);
    }

    /**
     * Update a saving.
     *
     * @param Session $session The session
     * @param int $savingId
     * @param array $values
     *
     * @return void
     */
    public function updateSaving(Session $session, int $savingId, array $values): void
    {
        if(!($member = $this->getMember($values['member'])))
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }
        $saving = $session->savings()->find($savingId);
        if(!$saving)
        {
            throw new MessageException(trans('meeting.saving.errors.not_found'));
        }
        $fund = !$values['fund'] ? null : $this->fundService->getFund($values['fund']);

        $this->persistSaving($session, $member, $fund, $saving, $values['amount']);
    }

    /**
     * Create or update a saving.
     *
     * @param Session $session The session
     * @param int $fundId
     * @param int $memberId
     * @param int $amount
     *
     * @return void
     */
    public function saveSaving(Session $session, int $fundId, int $memberId, int $amount): void
    {
        if(!($member = $this->getMember($memberId)))
        {
            throw new MessageException(trans('tontine.member.errors.not_found'));
        }
        $fund = !$fundId ? null : $this->fundService->getFund($fundId);
        $saving = $this->findSaving($session, !$fund ? 0 : $fund->id, $member->id);
        if(!$saving)
        {
            $saving = new Saving();
        }

        $this->persistSaving($session, $member, $fund, $saving, $amount);
    }

    /**
     * Delete a saving.
     *
     * @param Session $session The session
     * @param int $savingId
     * @param int $memberId
     *
     * @return void
     */
    public function deleteSaving(Session $session, int $savingId, int $memberId = 0): void
    {
        $savingId > 0 ?
            $session->savings()->where('id', $savingId)->delete() :
            $session->savings()->where('member_id', $memberId)->delete();
    }

    /**
     * @param Session $session
     * @param int $fundId
     * @param string $search
     * @param bool|null $filter
     *
     * @return Closure
     */
    private function getMemberSavingsFilter(Session $session, int $fundId): Closure
    {
        return function(/*Builder|Relation*/ $query) use($session, $fundId) {
            $query->where('session_id', $session->id)
                ->where(function(Builder $query) use($fundId) {
                    $fundId > 0 ?
                        $query->where('fund_id', $fundId) :
                        $query->whereNull('fund_id');
                });
        };
    }

    /**
     * @param Session $session
     * @param int $fundId
     * @param string $search
     * @param bool|null $filter
     *
     * @return Builder|Relation
     */
    private function getMembersQuery(Session $session, int $fundId,
        string $search, ?bool $filter): Builder|Relation
    {
        $savingsFilter = $this->getMemberSavingsFilter($session, $fundId);
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
     * @param int $fundId
     * @param string $search
     * @param bool|null $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Session $session, int $fundId, string $search,
        ?bool $filter, int $page = 0): Collection
    {
        return $this->getMembersQuery($session, $fundId, $search, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->with('savings', $this->getMemberSavingsFilter($session, $fundId))
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
     * @param int $fundId
     * @param string $search
     * @param bool|null $filter
     *
     * @return int
     */
    public function getMemberCount(Session $session, int $fundId, string $search, ?bool $filter): int
    {
        return $this->getMembersQuery($session, $fundId, $search, $filter)->count();
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

    /**
     * Get all closings for a given fund.
     *
     * @param int $fundId
     *
     * @return array
     */
    public function getFundClosings(int $fundId): array
    {
        $closings = $this->tenantService->tontine()->properties['closings'] ?? [];
        $closings = Arr::where($closings, fn($closing) => isset($closing[$fundId]));
        return Arr::map($closings, fn($closing) => $closing[$fundId]);
    }

    /**
     * Save the given session as closing for the fund.
     *
     * @param Session $session
     * @param int $fundId
     * @param int $profitAmount
     *
     * @return void
     */
    public function saveFundClosing(Session $session, int $fundId, int $profitAmount)
    {
        $tontine = $this->tenantService->tontine();
        $properties = $tontine->properties;
        $properties['closings'][$session->id][$fundId] = $profitAmount;
        $tontine->saveProperties($properties);
    }

    /**
     * Check if the given session is closing the fund.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return bool
     */
    public function hasFundClosing(Session $session, int $fundId): bool
    {
        $properties = $this->tenantService->tontine()->properties;
        return isset($properties['closings'][$session->id][$fundId]);
    }

    /**
     * Set the given session as closing the fund.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return void
     */
    public function deleteFundClosing(Session $session, int $fundId)
    {
        $tontine = $this->tenantService->tontine();
        $properties = $tontine->properties;
        if(isset($properties['closings'][$session->id][$fundId]))
        {
            unset($properties['closings'][$session->id][$fundId]);
            if(count($properties['closings'][$session->id]) == 0)
            {
                unset($properties['closings'][$session->id]);
            }
        }
        $tontine->saveProperties($properties);
    }

    /**
     * Get the profit amount saved on a given session.
     *
     * @param Session $session
     * @param int $fundId
     *
     * @return int
     */
    public function getProfitAmount(Session $session, int $fundId): int
    {
        $tontine = $this->tenantService->tontine();
        return $tontine->properties['closings'][$session->id][$fundId] ?? 0;
    }

    /**
     * Get all the fund closings on a given session.
     *
     * @param Session $session
     *
     * @return array
     */
    public function getSessionClosings(Session $session): array
    {
        $tontine = $this->tenantService->tontine();
        return $tontine->properties['closings'][$session->id] ?? [];
    }
}
