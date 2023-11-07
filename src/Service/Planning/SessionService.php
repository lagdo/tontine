<?php

namespace Siak\Tontine\Service\Planning;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Pool;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;

use function intval;
use function now;
use function trans;

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
        return $this->tenantService->round()->sessions()
            ->orderBy('start_at', 'asc')
            ->page($page, $this->tenantService->getLimit())
            ->get();
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
        return $this->tenantService->round()->sessions()->find($sessionId);
    }

    private function disableSessionOnPools(Session $session)
    {
        // Disable this session on all planned pools
        $this->tenantService->round()->pools()
            ->where('properties->remit->planned', true)
            ->get()
            ->each(function($pool) use($session) {
                $pool->disabledSessions()->attach($session->id);
            });
    }

    /**
     * Add a new session.
     *
     * @param array $values
     *
     * @return bool
     */
    public function createSession(array $values): bool
    {
        $values['start_at'] = $values['date'] . ' ' . $values['start'] . ':00';
        $values['end_at'] = $values['date'] . ' ' . $values['end'] . ':00';
        DB::transaction(function() use($values) {
            $session = $this->tenantService->round()->sessions()->create($values);
            $this->disableSessionOnPools($session);
        });

        return true;
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
            foreach($sessions as $session)
            {
                $this->disableSessionOnPools($session);
            }
        });

        return true;
    }

    /**
     * Update a session.
     *
     * @param Session $session
     * @param array $values
     *
     * @return bool
     */
    public function updateSession(Session $session, array $values): bool
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
     * @return bool
     */
    public function saveSessionVenue(Session $session, array $values): bool
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
        // Delete the session. Will fail if there's still some data attached.
        try
        {
            DB::transaction(function() use($session) {
                // Also delete related data that may have been automatically created.
                $session->receivables()->delete();
                $session->session_bills()->delete();
                $session->disabledPools()->detach();
                $session->delete();
            });
        }
        catch(Exception $e)
        {
            throw new MessageException(trans('tontine.session.errors.delete'));
        }
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
     * Enable or disable a session for a pool.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    public function enableSession(Pool $pool, Session $session)
    {
        // When the remitments are planned, don't enable or disable a session
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     return;
        // }
        if($session->enabled($pool))
        {
            return;
        }

        // Enable the session for the pool.
        $pool->disabledSessions()->detach($session->id);
    }

    /**
     * Enable or disable a session for a pool.
     *
     * @param Pool $pool
     * @param Session $session
     *
     * @return void
     */
    public function disableSession(Pool $pool, Session $session)
    {
        // When the remitments are planned, don't enable or disable a session
        // if receivables already exist on the pool.
        // if($pool->remit_planned &&
        //     $pool->subscriptions()->whereHas('receivables')->count() > 0)
        // {
        //     return;
        // }
        if($session->disabled($pool))
        {
            return;
        }

        // Disable the session for the pool.
        DB::transaction(function() use($pool, $session) {
            // If a session was already opened, delete the receivables and payables.
            // Will fail if any of them is already paid.
            $subscriptionIds = $pool->subscriptions()->pluck('id');
            $session->receivables()->whereIn('subscription_id', $subscriptionIds)->delete();
            $session->payables()->whereIn('subscription_id', $subscriptionIds)->delete();
            // Disable the session for the pool.
            $pool->disabledSessions()->attach($session->id);
        });
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
            $session->start = '00:00';
            $session->end = '00:00';
            $sessions[] = $session;
            $date->addMonth(1);
        }
        return $sessions;
    }
}
