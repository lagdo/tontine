<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Session;

trait SessionTrait
{
    /**
     * @var bool
     */
    private bool $filterActive = false;

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
     * @return int
     */
    public function getGuildSessionCount(): int
    {
        return $this->tenantService->guild()->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->count();
    }

    /**
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getGuildSessions(int $page = 0, bool $orderAsc = true): Collection
    {
        return $this->tenantService->guild()->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->orderBy('start_at', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getGuildSession(int $sessionId): ?Session
    {
        return $this->tenantService->guild()->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->find($sessionId);
    }

    /**
     * @param Session $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return int
     */
    public function getSessionCount(Session $currSession, bool $getAfter = false,
        bool $withCurr = true): int
    {
        $operator = $getAfter ? ($withCurr ? '>=' : '>') : ($withCurr ? '<=' : '<');
        $currSessionDate = !$currSession ? '' : $currSession->start_at->format('Y-m-d');
        return $this->tenantService->guild()->sessions()
            ->whereDate('start_at', $operator, $currSessionDate)->count();
    }
}
