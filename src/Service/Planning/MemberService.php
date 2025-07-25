<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Lagdo\Facades\Logger;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\MemberDef;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\WithTrait;
use Siak\Tontine\Validation\SearchSanitizer;
use Exception;

use function trans;

class MemberService
{
    use WithTrait;

    /**
     * @param TenantService $tenantService
     * @param BillSyncService $billSyncService
     * @param SearchSanitizer $searchSanitizer
     */
    public function __construct(private TenantService $tenantService,
        private BillSyncService $billSyncService, private SearchSanitizer $searchSanitizer)
    {}

    /**
     * @param Round $round
     * @param string $search
     * @param bool $filter|null
     *
     * @return Relation
     */
    private function getQuery(Round $round, string $search, ?bool $filter): Relation
    {
        $onRoundFilter = fn(Builder $q) => $q->where('round_id', $round->id);
        return $round->guild->members()
            ->search($this->searchSanitizer->sanitize($search))
            ->when($filter === true, fn(Builder $query) => $query
                ->whereHas('members', $onRoundFilter))
            ->when($filter === false, fn(Builder $query) => $query
                ->whereDoesntHave('members', $onRoundFilter));
    }

    /**
     * Get a paginated list of members.
     *
     * @param Round $round
     * @param string $search
     * @param bool $filter|null
     * @param int $page
     *
     * @return Collection
     */
    public function getMemberDefs(Round $round, string $search, ?bool $filter, int $page = 0): Collection
    {
        return $this->getQuery($round, $search, $filter)
            ->withCount([
                'members' => fn(Builder $q) => $q->where('round_id', $round->id),
            ])
            ->page($page, $this->tenantService->getLimit())
            ->orderBy('name', 'asc')
            ->get();
    }

    /**
     * Get the number of members.
     *
     * @param Round $round
     * @param string $search
     * @param bool $filter|null
     *
     * @return int
     */
    public function getMemberDefCount(Round $round, string $search, ?bool $filter): int
    {
        return $this->getQuery($round, $search, $filter)->count();
    }

    /**
     * Get a single member.
     *
     * @param Round $round
     * @param int $memberId
     *
     * @return MemberDef|null
     */
    public function getMemberDef(Round $round, int $memberId): ?MemberDef
    {
        return $this->getQuery($round, '', null)
            // It's important to fetch the relations and filter on the round here.
            ->with([
                'members' => fn(Relation $q) => $q->where('round_id', $round->id),
            ])
            ->find($memberId);
    }

    /**
     * Add a new member.
     *
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function enableMember(Round $round, int $defId): void
    {
        $def = $this->getMemberDef($round, $defId);
        if(!$def || $def->members->count() > 0)
        {
            return;
        }

        DB::transaction(function() use($round, $def) {
            $member = $def->members()->create(['round_id' => $round->id]);

            $this->billSyncService->memberEnabled($round, $member);
        });
    }

    /**
     * Delete a member.
     *
     * @param Round $round
     * @param int $defId
     *
     * @return void
     */
    public function disableMember(Round $round, int $defId): void
    {
        $def = $this->getMemberDef($round, $defId);
        if(!$def || $def->members->count() === 0)
        {
            return;
        }

        $member = $def->members->first();
        try
        {
            DB::transaction(function() use($round, $member) {
                $this->billSyncService->memberRemoved($round, $member);

                $member->delete();
            });
        }
        catch(Exception $e)
        {
            Logger::error('Error while removing a member.', [
                'message' => $e->getMessage(),
            ]);
            throw new MessageException(trans('tontine.member.errors.cannot_remove'));
        }
    }

    /**
     * Get the number of active members in the round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getMemberCount(Round $round): int
    {
        return $round->members()->count();
    }
}
