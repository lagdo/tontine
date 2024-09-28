<?php

use App\Ajax\Web\Meeting\Payment;
use App\Ajax\Web\Meeting\Presence\Home as Presence;
use App\Ajax\Web\Meeting\Session as MeetingSession;
use App\Ajax\Web\Planning\Pool;
use App\Ajax\Web\Planning\Round as PlanningRound;
use App\Ajax\Web\Report\Round as ReportRound;
use App\Ajax\Web\Report\Session as ReportSession;
use App\Ajax\Web\Tontine\Member;
use App\Ajax\Web\Tontine\Options;

return [
    'tontine' => [
        '#tontine-menu-members' => Member::class,
        '#tontine-menu-categories' => Options::class,
        '#planning-menu-sessions' => PlanningRound::class,
    ],
    'round' => [
        '#planning-menu-pools' => Pool::class,
        '#meeting-menu-sessions' => MeetingSession::class,
        '#meeting-menu-payments' => Payment::class,
        '#meeting-menu-presences' => Presence::class,
        '#report-menu-session' => ReportSession::class,
        '#report-menu-round' => ReportRound::class,
    ],
];
