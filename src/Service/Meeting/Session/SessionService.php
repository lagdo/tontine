<?php

namespace Siak\Tontine\Service\Meeting\Session;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Collection;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\WithTrait;

use function tap;
use function trans;

class SessionService
{
    use WithTrait;

    /**
     * @var bool
     */
    private bool $filterActive = false;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(private TenantService $tenantService)
    {}

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
     * @param Round $round
     *
     * @return Relation
     */
    private function getSessionsQuery(Round $round): Relation
    {
        return $round->sessions()->when($this->filterActive,
            fn(Builder $query) => $query->active());
    }

    /**
     * Find a session.
     *
     * @param Round $round
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(Round $round, int $sessionId): ?Session
    {
        return tap($this->getSessionsQuery($round),
            fn($query) => $this->addWith($query))->find($sessionId);
    }

    /**
     * Get the number of sessions in the selected round.
     *
     * @param Round $round
     *
     * @return int
     */
    public function getSessionCount(Round $round): int
    {
        return $this->getSessionsQuery($round)->count();
    }

    /**
     * Get a paginated list of sessions in the selected round.
     *
     * @param Round $round
     * @param int $page
     * @param bool $orderAsc
     *
     * @return Collection
     */
    public function getSessions(Round $round, int $page = 0, bool $orderAsc = true): Collection
    {
        return tap($this->getSessionsQuery($round),
                fn($query) => $this->addWith($query))
            ->orderBy('day_date', $orderAsc ? 'asc' : 'desc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
    }

    /**
     * Open a session.
     *
     * @param Session $session
     *
     * @return void
     */
    public function openSession(Session $session)
    {
        $session->update(['status' => Session::STATUS_OPENED]);
    }

    /**
     * Close a session.
     *
     * @param Session $session
     *
     * @return void
     */
    public function closeSession(Session $session)
    {
        $session->update(['status' => Session::STATUS_CLOSED]);
    }

    /**
     * Find the prev session.
     *
     * @param Round $round
     * @param Session $session
     *
     * @return Session|null
     */
    public function getPrevSession(Round $round, Session $session): ?Session
    {
        return $round->sessions()->opened()
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
    public function getNextSession(Round $round, Session $session): ?Session
    {
        return $round->sessions()->opened()
            ->where('day_date', '>', $session->day_date)
            ->orderBy('day_date', 'asc')
            ->first();
    }

    /**
     * Update a session agenda.
     *
     * @param Session $session
     * @param string $agenda
     *
     * @return void
     */
    public function saveAgenda(Session $session, string $agenda): void
    {
        $session->update(['agenda' => $agenda]);
    }

    /**
     * Update a session report.
     *
     * @param Session $session
     * @param string $report
     *
     * @return void
     */
    public function saveReport(Session $session, string $report): void
    {
        $session->update(['report' => $report]);
    }
}
