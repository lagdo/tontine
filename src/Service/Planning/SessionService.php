<?php

namespace Siak\Tontine\Service\Planning;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\TenantService;
use Siak\Tontine\Service\Traits\SessionTrait;
use stdClass;

use function intval;
use function now;
use function trans;

class SessionService
{
    use SessionTrait;

    /**
     * @param TenantService $tenantService
     * @param DataSyncService $dataSyncService
     */
    public function __construct(protected TenantService $tenantService,
        private DataSyncService $dataSyncService)
    {}

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
        $values['start_at'] = $values['date'] . ' ' . $values['start'] . ':00';
        $values['end_at'] = $values['date'] . ' ' . $values['end'] . ':00';
        DB::transaction(function() use($round, $values) {
            /** @var Session */
            $session = $round->sessions()->create($values);
            $this->dataSyncService->onNewSession($round, $session);
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
        foreach($values as &$value)
        {
            $value['start_at'] = $value['date'] . ' ' . $value['start'] . ':00';
            $value['end_at'] = $value['date'] . ' ' . $value['end'] . ':00';
        }
        DB::transaction(function() use($round, $values) {
            $sessions = $round->sessions()->createMany($values);
            foreach($sessions as $session)
            {
                $this->dataSyncService->onNewSession($round, $session);
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
        $guild = $this->tenantService->guild();
        $this->dataSyncService->onUpdateSession($guild, $session, $values);

        $values['start_at'] = $values['date'] . ' ' . $values['start'] . ':00';
        $values['end_at'] = $values['date'] . ' ' . $values['end'] . ':00';
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
            $session->delete();
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
