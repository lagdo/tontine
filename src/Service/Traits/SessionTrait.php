<?php

namespace Siak\Tontine\Service\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Session;

trait SessionTrait
{
    /**
     * Find a session.
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
     * Get the number of sessions in the selected round.
     *
     * @return int
     */
    public function getSessionCount(): int
    {
        return $this->tenantService->round()->sessions()->count();
    }

    /**
     * Get a paginated list of sessions in the selected round.
     *
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getSessions(int $page = 0, bool $orderAsc = true): Collection
    {
        return $this->tenantService->round()->sessions()
            ->orderBy('start_at', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getRoundSessions(int $page = 0, bool $orderAsc = true): Collection
    {
        return $this->getSessions($page, $orderAsc);
    }

    /**
     * Find a session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getTontineSession(int $sessionId): ?Session
    {
        return $this->tenantService->tontine()->sessions()->find($sessionId);
    }

    /**
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getTontineSessions(int $page = 0, bool $orderAsc = true): Collection
    {
        return $this->tenantService->tontine()->sessions()
            ->orderBy('start_at', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * @param Builder $query
     * @param Session|null $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return Builder|Relation
     */
    private function getSessionsQuery($query, ?Session $currSession, bool $getAfter, bool $withCurr): Builder|Relation
    {
        $operator = $getAfter ? ($withCurr ? '>=' : '>') : ($withCurr ? '<=' : '<');
        $currSessionDate = !$currSession ? '' : $currSession->start_at->format('Y-m-d');
        return $query->when($currSession !== null, fn(Builder $query) =>
            $query->whereDate('start_at', $operator, $currSessionDate));
    }

    /**
     * @param Session|null $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return Builder|Relation
     */
    private function getRoundSessionsQuery(?Session $currSession, bool $getAfter, bool $withCurr): Builder|Relation
    {
        $query = $this->tenantService->round()->sessions();
        return $this->getSessionsQuery($query, $currSession, $getAfter, $withCurr);
    }

    /**
     * @param Session|null $session
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getRoundSessionIds(?Session $currSession = null,
        bool $getAfter = false, bool $withCurr = true): Collection
    {
        return $this->getRoundSessionsQuery($currSession, $getAfter, $withCurr)
            ->pluck('sessions.id');
    }

    /**
     * @param Session|null $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return int
     */
    public function getRoundSessionCount(?Session $currSession = null,
        bool $getAfter = false, bool $withCurr = true): int
    {
        return $this->getRoundSessionsQuery($currSession, $getAfter, $withCurr)->count();
    }

    /**
     * @param Session|null $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return Builder|Relation
     */
    private function getTontineSessionsQuery(?Session $currSession, bool $getAfter, bool $withCurr): Builder|Relation
    {
        $query = $this->tenantService->tontine()->sessions();
        return $this->getSessionsQuery($query, $currSession, $getAfter, $withCurr);
    }

    /**
     * @param Session|null $session
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getTontineSessionIds(?Session $currSession = null,
        bool $getAfter = false, bool $withCurr = true): Collection
    {
        return $this->getTontineSessionsQuery($currSession, $getAfter, $withCurr)
            ->pluck('sessions.id');
    }

    /**
     * @param Session|null $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return int
     */
    public function getTontineSessionCount(?Session $currSession = null,
        bool $getAfter = false, bool $withCurr = true): int
    {
        return $this->getTontineSessionsQuery($currSession, $getAfter, $withCurr)->count();
    }
}
