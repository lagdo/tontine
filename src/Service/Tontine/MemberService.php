<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Service\Events\EventTrait;
use Siak\Tontine\Service\TenantService;

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
     * @param int $page
     *
     * @return Collection
     */
    public function getMembers(int $page = 0): Collection
    {
        $members = $this->tenantService->tontine()->members();
        if($page > 0 )
        {
            $members->take($this->tenantService->getLimit());
            $members->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $members->get();
    }

    /**
     * Get the number of members.
     *
     * @return int
     */
    public function getMemberCount(): int
    {
        return $this->tenantService->tontine()->members()->count();
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
     * @return int
     */
    public function updateMember(Member $member, array $values): int
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
        $member->update(['active' => false]);
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
