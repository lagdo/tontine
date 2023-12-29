<?php

namespace Siak\Tontine\Service\Planning;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\SessionTrait;

use function intval;
use function now;
use function trans;

class SessionService
{
    use SessionTrait;

    /**
     * @param TenantService $tenantService
     */
    public function __construct(protected TenantService $tenantService)
    {}

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
