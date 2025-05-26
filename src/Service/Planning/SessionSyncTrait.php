<?php

namespace Siak\Tontine\Service\Planning;

use Siak\Tontine\Model\Round;
use Siak\Tontine\Model\Session;

trait SessionSyncTrait
{
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
}
