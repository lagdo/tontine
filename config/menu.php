<?php

use Ajax\App\Admin\Guild\Guild;
use Ajax\App\Admin\User\User;
use Ajax\App\Guild\Member\Member;
use Ajax\App\Guild\Options\Options;
use Ajax\App\Guild\Pool\Pool;
use Ajax\App\Meeting\Payment\Payment;
use Ajax\App\Meeting\Presence\Presence;
use Ajax\App\Meeting\Session\Session as MeetingSession;
use Ajax\App\Planning\Calendar\Round as Calendar;
use Ajax\App\Planning\Financial\Pool as Financial;
use Ajax\App\Planning\Subscription\Subscription;
use Ajax\App\Report\Round\Round as ReportRound;
use Ajax\App\Report\Session\Session as ReportSession;

return [
    'admin' => [
        '#admin-menu-guilds' => Guild::class,
        '#admin-menu-users' => User::class,
    ],
    'guild' => [
        '#guild-menu-members' => Member::class,
        '#guild-menu-pools' => Pool::class,
        '#guild-menu-categories' => Options::class,
        '#guild-menu-calendar' => Calendar::class,
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
