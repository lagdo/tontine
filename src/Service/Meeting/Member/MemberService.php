<?php

namespace Siak\Tontine\Service\Meeting\Member;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\WithTrait;
use Siak\Tontine\Validation\SearchSanitizer;

use function tap;

class MemberService
{
    use WithTrait;

    /**
     * @param TenantService $tenantService
     * @param SearchSanitizer $searchSanitizer
     */
    public function __construct(private TenantService $tenantService,
        private SearchSanitizer $searchSanitizer)
    {}

    /**
     * @param Round $round
     * @param string $search
     *
     * @return Builder|Relation
     */
    private function getQuery(Round $round, string $search): Builder|Relation
    {
        return $round->members()
            ->search($this->searchSanitizer->sanitize($search));
    }

    /**
     * Get a paginated list of members.
     *
     * @param Round $round
     * @param string $search
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Round $round, string $search = '', int $page = 0): Collection
    {
        return tap($this->getQuery($round, $search),
                fn($query) => $this->addWith($query))
            ->page($page, $this->tenantService->getLimit())
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->orderBy('member_defs.name', 'asc')
            ->get();
    }

    /**
     * Get the number of members.
     *
     * @param Round $round
     * @param string $search
     *
     * @return int
     */
    public function getMemberCount(Round $round, string $search = ''): int
    {
        return $this->getQuery($round, $search)->count();
    }

    /**
     * Get a list of members for dropdown.
     *
     * @param Round $round
     *
     * @return Collection
     */
    public function getMemberList(Round $round): Collection
    {
        return $round->members()
            ->without('def')
            ->join('member_defs', 'members.def_id', '=', 'member_defs.id')
            ->orderBy('member_defs.name', 'asc')
            ->get()
            ->pluck('name', 'id');
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
        return tap($round->members(), fn($query) => $this
            ->addWith($query))->find($memberId);
    }
}
