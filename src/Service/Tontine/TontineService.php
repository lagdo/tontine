<?php

namespace Siak\Tontine\Service\Tontine;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Member;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Model\Tontine;
use Siak\Tontine\Service\TenantService;

class TontineService
{
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
     * Get a paginated list of tontines in the selected round.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getTontines(int $page = 0): Collection
    {
        return $this->tenantService->user()->tontines()
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Get the number of tontines in the selected round.
     *
     * @return int
     */
    public function getTontineCount(): int
    {
        return $this->tenantService->user()->tontines()->count();
    }

    /**
     * Get a single tontine.
     *
     * @param int $tontineId    The tontine id
     *
     * @return Tontine|null
     */
    public function getTontine(int $tontineId): ?Tontine
    {
        return $this->tenantService->user()->tontines()->find($tontineId);
    }

    /**
     * Get the rounds of the selected tontine.
     *
     * @return Collection
     */
    public function getRounds(): Collection
    {
        $tontine = $this->tenantService->tontine();
        return ($tontine) ? $tontine->rounds()->get() : collect([]);
    }

    /**
     * Get a single round.
     *
     * @param int $roundId    The round id
     *
     * @return Round|null
     */
    public function getRound(int $roundId): ?Round
    {
        return $this->tenantService->tontine()->rounds()->find($roundId);
    }

    /**
     * Get a list of sessions.
     *
     * @param bool $pluck
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getSessions(bool $pluck = true, bool $orderAsc = true): Collection
    {
        $query = $this->tenantService->round()->sessions()
            ->orderBy('start_at', $orderAsc ? 'asc' : 'desc');
        return $pluck ? $query->pluck('title', 'id') : $query->get();
    }

    /**
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    /**
     * Get a list of members.
     *
     * @param bool $pluck
     *
     * @return Collection
     */
    public function getMembers(bool $pluck = true): Collection
    {
        $query = $this->tenantService->tontine()->members()->active()->orderBy('name', 'asc');
        return $pluck ? $query->pluck('name', 'id') : $query->get();
    }

    /**
     * Find a member.
     *
     * @param int $memberId
     *
     * @return Member|null
     */
    public function getMember(int $memberId): ?Member
    {
        return $this->tenantService->tontine()->members()->find($memberId);
    }

    /**
     * Check if the current tontine has at least financial pool.
     *
     * @return bool
     */
    public function hasPoolWithAuction(): bool
    {
        $round = $this->tenantService->round();
        return $round && $round->pools->contains(function($pool) {
            return $pool->remit_auction;
        });
    }

    /**
     * Add a new tontine.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createTontine(array $values): bool
    {
        $this->tenantService->user()->tontines()->create($values);
        return true;
    }

    /**
     * Update a tontine.
     *
     * @param int $id
     * @param array $values
     *
     * @return bool
     */
    public function updateTontine(int $id, array $values): bool
    {
        return $this->tenantService->user()->tontines()->where('id', $id)->update($values);
    }

    /**
     * Delete a tontine.
     *
     * @param int $id
     *
     * @return void
     */
    public function deleteTontine(int $id)
    {
        $tontine = $this->tenantService->user()->tontines()->find($id);
        if(!$tontine)
        {
            return;
        }
        DB::transaction(function() use($tontine) {
            $tontine->members()->delete();
            $tontine->rounds()->delete();
            $tontine->charges()->delete();
            $tontine->delete();
        });
    }
}
