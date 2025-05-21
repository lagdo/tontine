<?php

namespace Siak\Tontine\Service\Guild;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Siak\Tontine\Exception\MessageException;
use Siak\Tontine\Model\Guild;
use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;
use Siak\Tontine\Service\Planning\BillSyncService;
use Siak\Tontine\Service\Planning\FundSyncService;
use Siak\Tontine\Service\Planning\PoolSyncService;
use stdClass;

use function now;
use function trans;

class SessionService
{
    /**
     * @param BillSyncService $billSyncService
     * @param PoolSyncService $poolSyncService
     * @param FundSyncService $fundSyncService
     */
    public function __construct(private BillSyncService $billSyncService,
        private PoolSyncService $poolSyncService, private FundSyncService $fundSyncService)
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
        ];
    }

    /**
     * Get a session.
     *
     * @param Guild $guild
     * @param int $sessionId
     *
     * @return Session|null
     */
    public function getSession(Guild $guild, int $sessionId): ?Session
    {
        return $guild->sessions()->find($sessionId);
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

            $this->billSyncService->sessionsCreated($round, [$session]);
            $this->poolSyncService->sessionsCreated($round, [$session]);
            $this->fundSyncService->sessionCreated($round, [$session]);
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

            $this->billSyncService->sessionsCreated($round, $sessions);
            $this->poolSyncService->sessionsCreated($round, $sessions);
            $this->fundSyncService->sessionCreated($round, $sessions);
        });
        return true;
    }

    /**
     * Find the prev session.
     *
     * @param Guild $guild
     * @param Session $session
     *
     * @return Session|null
     */
    private function getPrevSession(Guild $guild, Session $session): ?Session
    {
        return $guild->sessions()
            ->where('day_date', '<', $session->day_date)
            ->orderBy('day_date', 'desc')
            ->first();
    }

    /**
     * Find the next session.
     *
     * @param Guild $guild
     * @param Session $session
     *
     * @return Session|null
     */
    private function getNextSession(Guild $guild, Session $session): ?Session
    {
        return $guild->sessions()
            ->where('day_date', '>', $session->day_date)
            ->orderBy('day_date', 'asc')
            ->first();
    }

    /**
     * Called before a session is updated
     *
     * @param Guild $guild
     * @param Session $session
     * @param array $values
     *
     * @return void
     */
    private function chechSessionDates(Guild $guild, Session $session, array $values): void
    {
        // Check that the sessions date sorting is not modified.
        $date = Carbon::createFromFormat('Y-m-d', $values['day_date']);
        $prevSession = $this->getPrevSession($guild, $session);
        $nextSession = $this->getNextSession($guild, $session);
        if(($prevSession !== null && $prevSession->day_date->gte($date)) ||
            ($nextSession !== null && $nextSession->day_date->lte($date)))
        {
            throw new MessageException(trans('tontine.errors.action') .
                '<br/>' . trans('tontine.session.errors.sorting'));
        }
    }

    /**
     * Update a session.
     *
     * @param Guild $guild
     * @param Session $session
     * @param array $values
     *
     * @return void
     */
    public function updateSession(Guild $guild, Session $session, array $values): void
    {
        $this->chechSessionDates($guild, $session, $values);

        DB::transaction(function() use($session, $values) {
            $session->update($values);

            // Not necessary
            // $this->fundSyncService->sessionUpdated($session->round);
        });
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
            $this->billSyncService->sessionDeleted($session);
            $this->poolSyncService->sessionDeleted($session);
            $this->fundSyncService->sessionDeleted($session);

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
