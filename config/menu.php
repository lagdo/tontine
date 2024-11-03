<?php

use App\Ajax\Web\Meeting\Payment\Payment;
use App\Ajax\Web\Meeting\Presence\Presence;
use App\Ajax\Web\Meeting\Session\Session as MeetingSession;
use App\Ajax\Web\Planning\Pool\Pool;
use App\Ajax\Web\Planning\Session\Round as PlanningRound;
use App\Ajax\Web\Planning\Subscription\Subscription;
use App\Ajax\Web\Report\Round\Round as ReportRound;
use App\Ajax\Web\Report\Session\Session as ReportSession;
use App\Ajax\Web\Tontine\Member\Member;
use App\Ajax\Web\Tontine\Options\Options;

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
