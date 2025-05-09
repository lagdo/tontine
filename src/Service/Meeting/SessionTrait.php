<?php

namespace Siak\Tontine\Service\Meeting;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Traits\WithTrait;

use function tap;
use function trans;

trait SessionTrait
{
    use WithTrait;

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
     * @return Relation
     */
    private function _getRoundSessionsQuery(): Relation
    {
        return $this->tenantService->round()->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active());
    }

    /**
     * Find a session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return tap($this->_getRoundSessionsQuery(), fn($query) => $this->addWith($query))
            ->find($sessionId);
    }

    /**
     * Get the number of sessions in the selected round.
     *
     * @return int
     */
    public function getSessionCount(): int
    {
        return $this->_getRoundSessionsQuery()->count();
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
        return tap($this->_getRoundSessionsQuery(), fn($query) => $this->addWith($query))
            ->orderBy('day_date', $orderAsc ? 'asc' : 'desc')
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
        return $this->tenantService->guild()->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->find($sessionId);
    }

    /**
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getTontineSessions(int $page = 0, bool $orderAsc = true): Collection
    {
        return $this->tenantService->guild()->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->orderBy('day_date', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * @param Builder|Relation $query
     * @param Session|null $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return Builder|Relation
     */
    private function getSessionsQuery($query, ?Session $currSession, bool $getAfter, bool $withCurr): Builder|Relation
    {
        $operator = $getAfter ? ($withCurr ? '>=' : '>') : ($withCurr ? '<=' : '<');
        return $query->when($currSession !== null, fn(Builder $query) =>
            $query->where('day_date', $operator, $currSession->day_date));
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
        return $this->getSessionsQuery($this->_getRoundSessionsQuery(), $currSession, $getAfter, $withCurr);
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
        $query = $this->tenantService->guild()->sessions();
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

    /**
     * Get the session statuses
     *
     * @return array
     */
    public function getSessionStatuses(): array
    {
        return [
            Session::STATUS_PENDING => trans('tontine.session.status.pending'),
            Session::STATUS_OPENED => trans('tontine.session.status.opened'),
            Session::STATUS_CLOSED => trans('tontine.session.status.closed'),
        ];;
    }
}
