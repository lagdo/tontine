<?php

namespace Siak\Tontine\Service\Traits;

use Illuminate\Database\Eloquent\Builder;
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
     * @return int
     */
    public function getRoundSessionCount(): int
    {
        return $this->getSessionCount();
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
     * @return int
     */
    public function getTontineSessionCount(): int
    {
        return $this->tenantService->tontine()->sessions()->count();
    }

    /**
     * @param Session|null $lastSession
     * @param bool $withLast
     *
     * @return Builder
     */
    private function getRoundSessionsQuery(?Session $lastSession, bool $withLast)
    {
        $lastSessionDate = !$lastSession ? '' : $lastSession->start_at->format('Y-m-d');
        return $this->tenantService->round()->sessions()
            ->when($lastSession !== null && !$withLast,
                fn(Builder $query) => $query->whereDate('start_at', '<', $lastSessionDate))
            ->when($lastSession !== null && $withLast,
                fn(Builder $query) => $query->whereDate('start_at', '<=', $lastSessionDate));
    }

    /**
     * @param Session|null $session
     * @param bool $withLast
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getRoundSessionIds(?Session $lastSession = null,
        bool $withLast = true, bool $orderAsc = true): Collection
    {
        return $this->getRoundSessionsQuery($lastSession, $withLast)
            ->orderBy('sessions.start_at', $orderAsc ? 'asc' : 'desc')
            ->pluck('sessions.id');
    }

    /**
     * @param Session|null $lastSession
     * @param bool $withLast
     *
     * @return Builder
     */
    private function getTontineSessionsQuery(?Session $lastSession, bool $withLast)
    {
        $lastSessionDate = $lastSession->start_at->format('Y-m-d');
        return $this->tenantService->tontine()->sessions()
            ->when($lastSession !== null && !$withLast,
                fn(Builder $query) => $query->whereDate('start_at', '<', $lastSessionDate))
            ->when($lastSession !== null && $withLast,
                fn(Builder $query) => $query->whereDate('start_at', '<=', $lastSessionDate));
    }

    /**
     * @param Session|null $session
     * @param bool $withLast
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getTontineSessionIds(?Session $lastSession = null,
        bool $withLast = true, bool $orderAsc = true): Collection
    {
        return $this->getTontineSessionsQuery($lastSession, $withLast)
            ->orderBy('sessions.start_at', $orderAsc ? 'asc' : 'desc')
            ->pluck('sessions.id');
    }
}
