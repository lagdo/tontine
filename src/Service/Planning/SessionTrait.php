<?php

namespace Siak\Tontine\Service\Planning;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
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
     * @param Guild $guild
     *
     * @return int
     */
    public function getGuildSessionCount(Guild $guild): int
    {
        return $guild->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->count();
    }

    /**
     * @param Guild $guild
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getGuildSessions(Guild $guild, int $page = 0, bool $orderAsc = true): Collection
    {
        return $guild->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->orderBy('day_date', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * @param Guild $guild
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getGuildSession(Guild $guild, int $sessionId): ?Session
    {
        return $guild->sessions()
            ->when($this->filterActive, fn(Builder $query) => $query->active())
            ->find($sessionId);
    }

    /**
     * @param Guild $guild
     * @param Session $currSession
     * @param bool $getAfter Get the sessions after or before the provided one
     * @param bool $withCurr Keep the provided session in the list
     *
     * @return int
     */
    public function getSessionCount(Guild $guild, Session $currSession,
        bool $getAfter = false, bool $withCurr = true): int
    {
        $operator = $getAfter ? ($withCurr ? '>=' : '>') : ($withCurr ? '<=' : '<');
        return $guild->sessions()
            ->where('day_date', $operator, $currSession->day_date)->count();
    }

    /**
     * Find the prev session.
     *
     * @param Round $round
     * @param Session $session
     *
     * @return Session|null
     */
    private function getPrevSession(Round $round, Session $session): ?Session
    {
        return $round->sessions()
            ->where('day_date', '<', $session->day_date)
            ->orderBy('day_date', 'desc')
            ->first();
    }

    /**
     * Find the next session.
     *
     * @param Round $round
     * @param Session $session
     *
     * @return Session|null
     */
    private function getNextSession(Round $round, Session $session): ?Session
    {
        return $round->sessions()
            ->where('day_date', '>', $session->day_date)
            ->orderBy('day_date', 'asc')
            ->first();
    }

    /**
     * Find the first session for a new pool or fund.
     *
     * @param Round $round
     * @param Builder $itemQuery
     *
     * @return Session
     */
    private function getStartSession(Round $round, Builder $itemQuery): Session
    {
        $prevEndSession = Session::select(['id', 'day_date'])
            ->whereIn('id', $itemQuery->select(['end_sid']))
            ->where('day_date', '<', $round->end->day_date)
            ->orderBy('day_date', 'desc')
            ->first();
        if(!$prevEndSession)
        {
            return $round->start;
        }

        $firstSession = $round->sessions()
            ->where('day_date', '>', $prevEndSession->day_date)
            ->orderBy('day_date', 'asc')
            ->first();
        return $firstSession !== null &&
            $firstSession->day_date > $round->start->day_date ?
            $firstSession : $round->start;
    }

    /**
     * Find the last session for a new pool or fund.
     *
     * @param Round $round
     * @param Builder $itemQuery
     *
     * @return Session
     */
    private function getEndSession(Round $round, Builder $itemQuery): Session
    {
        $nextStartSession = Session::select(['id', 'day_date'])
            ->whereIn('id', $itemQuery->select(['start_sid']))
            ->where('day_date', '>', $round->start->day_date)
            ->orderBy('day_date', 'asc')
            ->first();
        if(!$nextStartSession)
        {
            return $round->end;
        }

        $lastSession = $round->sessions()
            ->where('day_date', '<', $nextStartSession->day_date)
            ->orderBy('day_date', 'desc')
            ->first();
        return $lastSession !== null &&
            $lastSession->day_date < $round->end->day_date ?
            $lastSession : $round->end;
    }
}
