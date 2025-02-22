<?php

use Ajax\App\Admin\Organisation\Organisation;
use Ajax\App\Admin\User\User;
use Ajax\App\Meeting\Payment\Payment;
use Ajax\App\Meeting\Presence\Presence;
use Ajax\App\Meeting\Session\Session as MeetingSession;
use Ajax\App\Planning\Calendar\Round as Calendar;
use Ajax\App\Planning\Financial\Pool as Financial;
use Ajax\App\Planning\Subscription\Subscription;
use Ajax\App\Report\Round\Round as ReportRound;
use Ajax\App\Report\Session\Session as ReportSession;
use Ajax\App\Tontine\Member\Member;
use Ajax\App\Tontine\Options\Options;

return [
    'admin' => [
        '#admin-menu-tontines' => Organisation::class,
        '#admin-menu-users' => User::class,
    ],
    'tontine' => [
        '#tontine-menu-members' => Member::class,
        '#tontine-menu-categories' => Options::class,
        '#tontine-menu-calendar' => Calendar::class,
    ],
    'round' => [
        '#planning-menu-financial' => Financial::class,
        '#planning-menu-subscriptions' => Subscription::class,
        '#meeting-menu-sessions' => MeetingSession::class,
        '#meeting-menu-payments' => Payment::class,
        '#meeting-menu-presences' => Presence::class,
        '#report-menu-session' => ReportSession::class,
        '#report-menu-round' => ReportRound::class,
    ],
    'color' => [
        'active' => '#6777ef',
    ],
];
