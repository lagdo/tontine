<?php

namespace Siak\Tontine\Service\Guild;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\MemberDef;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Validation\SearchSanitizer;

class MemberService
{
    /**
     * @param TenantService $tenantService
     * @param SearchSanitizer $searchSanitizer
     */
    public function __construct(private TenantService $tenantService,
        private SearchSanitizer $searchSanitizer)
    {}

    /**
     * @param Guild $guild
     * @param string $search
     * @param bool|null $filter
     *
     * @return Builder|Relation
     */
    private function getQuery(Guild $guild, string $search, ?bool $filter): Builder|Relation
    {
        return $guild->members()
            ->when($filter !== null, fn(Builder $query) => $query->active($filter))
            ->search($this->searchSanitizer->sanitize($search));
    }

    /**
     * Get a paginated list of members.
     *
     * @param Guild $guild
     * @param string $search
     * @param bool|null $filter
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(Guild $guild, string $search = '',
        ?bool $filter = null, int $page = 0): Collection
    {
        return $this->getQuery($guild, $search, $filter)
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the number of members.
     *
     * @param Guild $guild
     * @param string $search
     * @param bool|null $filter
     *
     * @return int
     */
    public function getMemberCount(Guild $guild, string $search = '', ?bool $filter = null): int
    {
        return $this->getQuery($guild, $search, $filter)->count();
    }

    /**
     * Get a single member.
     *
     * @param Guild $guild
     * @param int $memberId
     *
     * @return MemberDef|null
     */
    public function getMember(Guild $guild, int $memberId): ?MemberDef
    {
        return $this->getQuery($guild, '', null)->find($memberId);
    }

    /**
     * Add a new member.
     *
     * @param Guild $guild
     * @param array $values
     *
     * @return bool
     */
    public function createMember(Guild $guild, array $values): bool
    {
        $guild->members()->create($values);
        return true;
    }

    /**
     * Add new members.
     *
     * @param Guild $guild
     * @param array $values
     *
     * @return bool
     */
    public function createMembers(Guild $guild, array $values): bool
    {
        $guild->members()->createMany($values);
        return true;
    }

    /**
     * Update a member.
     *
     * @param MemberDef $member
     * @param array $values    The member data
     *
     * @return bool
     */
    public function updateMember(MemberDef $member, array $values): bool
    {
        return $member->update($values);
    }

    /**
     * Toggle a member.
     *
     * @param Guild $guild
     * @param MemberDef $member
     *
     * @return void
     */
    public function toggleMember(MemberDef $member)
    {
        $member->update(['active' => !$member->active]);
    }

    /**
     * Delete a member.
     *
     * @param MemberDef $member
     *
     * @return void
     */
    public function deleteMember(MemberDef $member)
    {
        try
        {
            $member->delete();
        }
        catch(Exception)
        {
            throw new MessageException(trans('tontine.member.errors.cannot_delete'));
        }
    }

    /**
     * @param Guild $guild
     * @param int $count
     *
     * @return Collection
     */
    public function getFakeMembers(Guild $guild, int $count): Collection
    {
        return MemberDef::factory()->count($count)->make([
            'guild_id' => $guild,
        ]);
    }
}
