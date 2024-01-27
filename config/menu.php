<?php

use App\Ajax\Web\Meeting\Presence;
use App\Ajax\Web\Meeting\Session as MeetingSession;
use App\Ajax\Web\Planning\Pool;
use App\Ajax\Web\Planning\Round as PlanningRound;
use App\Ajax\Web\Planning\Subscription;
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
        '#planning-menu-subscriptions' => Subscription::class,
        '#meeting-menu-sessions' => MeetingSession::class,
        '#meeting-menu-presences' => Presence::class,
        '#report-menu-session' => ReportSession::class,
        '#report-menu-round' => ReportRound::class,
    ],
];
