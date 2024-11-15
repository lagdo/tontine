<?php

use Ajax\App\Meeting\Payment\Payment;
use Ajax\App\Meeting\Presence\Presence;
use Ajax\App\Meeting\Session\Session as MeetingSession;
use Ajax\App\Planning\Pool\Pool;
use Ajax\App\Planning\Session\Round as PlanningRound;
use Ajax\App\Planning\Subscription\Subscription;
use Ajax\App\Report\Round\Round as ReportRound;
use Ajax\App\Report\Session\Session as ReportSession;
use Ajax\App\Tontine\Member\Member;
use Ajax\App\Tontine\Options\Options;

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
        '#meeting-menu-payments' => Payment::class,
        '#meeting-menu-presences' => Presence::class,
        '#report-menu-session' => ReportSession::class,
        '#report-menu-round' => ReportRound::class,
    ],
];
