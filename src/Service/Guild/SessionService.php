<?php

namespace Siak\Tontine\Service\Guild;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\DataSyncService;
use Siak\Tontine\Service\TenantService;
use stdClass;

use function intval;
use function now;
use function trans;

class SessionService
{
    /**
     * @param TenantService $tenantService
     * @param DataSyncService $dataSyncService
     */
    public function __construct(protected TenantService $tenantService,
        private DataSyncService $dataSyncService)
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
     * Add a new session.
     *
     * @param Round $round
     * @param array $values
     *
     * @return bool
     */
    public function createSession(Round $round, array $values): bool
    {
        DB::transaction(function() use($round, $values) {
            /** @var Session */
            $session = $round->sessions()->create($values);
            $this->dataSyncService->onNewSession($session);
            // Update the default savings fund
            $this->dataSyncService->saveDefaultFund($round);
        });
        return true;
    }

    /**
     * Add a new session.
     *
     * @param Round $round
     * @param array $values
     *
     * @return bool
     */
    public function createSessions(Round $round, array $values): bool
    {
        DB::transaction(function() use($round, $values) {
            $sessions = $round->sessions()->createMany($values);
            foreach($sessions as $session)
            {
                $this->dataSyncService->onNewSession($session);
            }
            // Update the default savings fund
            $this->dataSyncService->saveDefaultFund($round);
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
        $guild = $this->tenantService->guild();
        $this->dataSyncService->onUpdateSession($guild, $session, $values);

        // Make sure the host belongs to the same guild
        $hostId = intval($values['host_id']);
        $values['host_id'] = null;
        if($hostId > 0)
        {
            $values['host_id'] = $guild->members()->find($hostId)?->id ?? null;
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
        DB::transaction(function() use($session) {
            $this->dataSyncService->onDeleteSession($session);
            $round = $session->round;
            $sessionCount = $round->sessions()->count();

            $fundDef = $round->guild->default_fund;
            $defaultFund = $fundDef->funds()->where('round_id', $round->id)->first();
            // Delete the default fund if it is related to the session
            if($defaultFund->start_sid === $session->id || $defaultFund->end_sid === $session->id)
            {
                $defaultFund->delete();
            }

            $session->delete();
            // Update the default savings fund, only if this wasn't the last session in the round.
            if($sessionCount > 1)
            {
                $this->dataSyncService->saveDefaultFund($round);
            }
        });
    }

    /**
     * @return array
     */
    public function getYearSessions(): array
    {
        $year = now()->format('Y');
        $date = Carbon::createFromDate($year, 1, 5, 'Africa/Douala');
        $sessions = [];
        for($i = 0; $i < 12; $i++)
        {
            $session = new stdClass();
            $session->title = trans('tontine.session.labels.title', [
                'date' => $date->translatedFormat(trans('tontine.date.format_my')),
            ]);
            $session->date = $date->format('Y-m-d');
            $session->start = '00:00';
            $session->end = '00:00';
            $sessions[] = $session;
            $date->addMonth(1);
        }
        return $sessions;
    }
}
