<?php

namespace Siak\Tontine\Service\Planning;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Tontine\TenantService;

use function intval;
use function now;

class SessionService
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
     * Get a paginated list of sessions in the selected round.
     *
     * @param int $page
     *
     * @return Collection
     */
    public function getSessions(int $page = 0): Collection
    {
        $sessions = $this->tenantService->round()->sessions()->with(['host']);
        if($page > 0 )
        {
            $sessions->take($this->tenantService->getLimit());
            $sessions->skip($this->tenantService->getLimit() * ($page - 1));
        }
        return $sessions->get();
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
     * Get a single session.
     *
     * @param int $sessionId    The session id
     *
     * @return Session|null
     */
    public function getSession(int $sessionId): ?Session
    {
        return $this->tenantService->round()->sessions()->with(['host'])->find($sessionId);
    }

    /**
     * Add a new session.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createSessions(array $values): bool
    {
        foreach($values as &$value)
        {
            $value['start_at'] = $value['date'] . ' ' . $value['start'] . ':00';
            $value['end_at'] = $value['date'] . ' ' . $value['end'] . ':00';
        }
        DB::transaction(function() use($values) {
            $sessions = $this->tenantService->round()->sessions()->createMany($values);
        });

        return true;
    }

    /**
     * Update a session.
     *
     * @param Session $session
     * @param array $values
     *
     * @return int
     */
    public function updateSession(Session $session, array $values): int
    {
        $values['start_at'] = $values['date'] . ' ' . $values['start'] . ':00';
        $values['end_at'] = $values['date'] . ' ' . $values['end'] . ':00';
        // Make sure the host belongs to the same tontine
        $hostId = intval($values['host_id']);
        $values['host_id'] = null;
        if($hostId > 0)
        {
            $values['host_id'] = $this->tenantService->tontine()->members()->find($hostId)->id;
        }

        return $session->update($values);
    }

    /**
     * Update a session,venue.
     *
     * @param Session $session
     * @param array $values
     *
     * @return int
     */
    public function saveSessionVenue(Session $session, array $values): int
    {
        return $session->update($values);
    }

    /**
     * Delete a session.
     *
     * @param Session $session
     *
     * @return void
     */
    public function deleteSession(Session $session)
    {
        DB::transaction(function() use($session) {
            // Detach from the payables. Don't delete.
            $session->payables()->update(['session_id' => null]);
            // Delete the session
            $session->delete();
        });
    }

    /**
     * Get a list of members for the dropdown select component.
     *
     * @return Collection
     */
    public function getMembers(): Collection
    {
        return $this->tenantService->tontine()->members()
            ->orderBy('name', 'asc')->pluck('name', 'id')->prepend('', 0);
    }

    /**
     * @return array
     */
    public function getYearSessions(): array
    {
        $year = now()->format('Y');
        $date = Carbon::createFromDate($year, 1, 5, 'Africa/Douala')->locale('fr');
        $sessions = [];
        for($i = 0; $i < 12; $i++)
        {
            $session = new \stdClass();
            $session->title = 'Séance de ' . $date->isoFormat('MMMM YYYY');
            $session->date = $date->format('Y-m-d');
            $session->start = '16:00';
            $session->end = '20:00';
            $sessions[] = $session;
            $date->addMonth(1);
        }
        return $sessions;
    }
}
