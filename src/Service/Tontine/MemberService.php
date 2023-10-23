<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\EventTrait;

use function strtolower;

class MemberService
{
    use EventTrait;

    /**
     * @var TenantService
     */
    protected TenantService $tenantService;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Get a paginated list of members.
     *
     * @param string $search
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(string $search, int $page = 0): Collection
    {
        return $this->tenantService->tontine()->members()
            ->when($search !== '', function(Builder $query) use($search) {
                $search = '%' . strtolower($search) . '%';
                return $query->where(DB::raw('lower(name)'), 'like', $search);
            })
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the number of members.
     *
     * @param string $search
     *
     * @return int
     */
    public function getMemberCount(string $search): int
    {
        return $this->tenantService->tontine()->members()
            ->when($search !== '', function(Builder $query) use($search) {
                $search = '%' . strtolower($search) . '%';
                return $query->where(DB::raw('lower(name)'), 'like', $search);
            })
            ->count();
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
        return $this->tenantService->tontine()->members()->find($id);
    }

    /**
     * Add a new member.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createMember(array $values): bool
    {
        DB::transaction(function() use($values) {
            $tontine = $this->tenantService->tontine();
            $member = $tontine->members()->create($values);
            // Create members bills
            $this->memberCreated($tontine, $member);
        });

        return true;
    }

    /**
     * Add new members.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createMembers(array $values): bool
    {
        DB::transaction(function() use($values) {
            $tontine = $this->tenantService->tontine();
            $members = $tontine->members()->createMany($values);
            // Create members bills
            foreach($members as $member)
            {
                $this->memberCreated($tontine, $member);
            }
        });

        return true;
    }

    /**
     * Update a member.
     *
     * @param Member $member
     * @param array $values    The member data
     *
     * @return bool
     */
    public function updateMember(Member $member, array $values): bool
    {
        return $member->update($values);
    }

    /**
     * Toggle a member.
     *
     * @param Member $member
     *
     * @return void
     */
    public function toggleMember(Member $member)
    {
        $member->update(['active' => !$member->active]);
    }

    /**
     * Delete a member.
     *
     * @param Member $member
     *
     * @return void
     */
    public function deleteMember(Member $member)
    {
        // Will fail if the member has related data.
        $member->delete();
    }

    /**
     * @param int $count
     *
     * @return Collection
     */
    public function getFakeMembers(int $count): Collection
    {
        return Member::factory()->count($count)->make([
            'tontine_id' => $this->tenantService->tontine(),
        ]);
    }
}
