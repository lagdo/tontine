<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\EventTrait;
use Siak\Tontine\Service\Traits\WithTrait;

use function count;
use function strtolower;
use function tap;

class MemberService
{
    use EventTrait;
    use WithTrait;

    /**
     * @var bool
     */
    private bool $filterActive = false;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

    /**
     * @param bool $filter
     *
     * @return self
     */
    public function active(bool $filter = true): self
    {
        $this->filterActive = $filter;
        return $this;
    }

    /**
     * @param string $search
     *
     * @return Builder|Relation
     */
    private function getQuery(string $search = ''): Builder|Relation
    {
        return $this->tenantService->tontine()->members()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->when($search !== '', function(Builder $query) use($search) {
                $search = '%' . strtolower($search) . '%';
                return $query->where(DB::raw('lower(name)'), 'like', $search);
            });
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
        return tap($this->getQuery($search), fn($query) => $this->addWith($query))
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
        return $this->getQuery($search)->count();
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
        return tap($this->getQuery(), fn($query) => $this->addWith($query))
            ->find($id);
    }

    /**
     * Get a list of members for dropdown.
     *
     * @return Collection
     */
    public function getMemberList(): Collection
    {
        return $this->tenantService->tontine()->members()->active()
            ->orderBy('name', 'asc')->pluck('name', 'id');
    }

    /**
     * Save active members ids for the round
     *
     * @return void
     */
    private function saveActiveMembers()
    {
        if(!($tontine = $this->tenantService->tontine()) ||
            !($round = $this->tenantService->round()))
        {
            return;
        }
        $properties = $round->properties;
        $properties['members'] = $tontine->members()->active()->pluck('id')->all();
        $round->saveProperties($properties);
    }

    /**
     * Get the number of active members for the current round
     *
     * @return int
     */
    public function countActiveMembers(): int
    {
        // The number of active members is saved in the round, so its current
        // value can be retrieved forever, even when the membership will change.
        $round = $this->tenantService->round();
        if(!isset($round->properties['members']))
        {
            // Create and save the property with the content
            $this->saveActiveMembers();
            $round->refresh();
        }

        return count($round->properties['members']);
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
            $this->saveActiveMembers();
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
            $this->saveActiveMembers();
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
        $this->saveActiveMembers();
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
        // Will fail if any bill is already paid.
        $billIds = $member->tontine_bills()->pluck('bill_id')
            ->concat($member->round_bills()->pluck('bill_id'))
            ->concat($member->session_bills()->pluck('bill_id'))
            ->concat($member->libre_bills()->pluck('bill_id'));
        DB::transaction(function() use($member, $billIds) {
            $member->tontine_bills()->delete();
            $member->round_bills()->delete();
            $member->session_bills()->delete();
            $member->libre_bills()->delete();
            DB::table('bills')->whereIn('id', $billIds)->delete();
            $member->delete();
            $this->saveActiveMembers();
        });
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
