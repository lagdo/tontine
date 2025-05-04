<?php

use Ajax\App\Guild\Account\Account;
use Ajax\App\Admin\Guild\Guild;
use Ajax\App\Guild\Calendar\Round as Calendar;
use Ajax\App\Guild\Charge\Charge;
use Ajax\App\Guild\Member\Member;
use Ajax\App\Guild\Pool\Pool;
use Ajax\App\Meeting\Payment\Payment;
use Ajax\App\Meeting\Presence\Presence;
use Ajax\App\Meeting\Session\Session;
use Ajax\App\Planning\Finance;
use Ajax\App\Report\Round\Round as ReportRound;
use Ajax\App\Report\Session\Session as ReportSession;

return [
    'admin' => [
        '#admin-menu-guilds' => Guild::class,
    ],
    'finance' => [
        '#finance-menu-pools' => Pool::class,
        '#finance-menu-accounts' => Account::class,
        '#finance-menu-charges' => Charge::class,
    ],
    'tontine' => [
        '#guild-menu-members' => Member::class,
        '#guild-menu-calendar' => Calendar::class,
    ],
    'round' => [
        '#planning-menu-finance' => Finance::class,
        '#meeting-menu-sessions' => Session::class,
        '#meeting-menu-payments' => Payment::class,
        '#meeting-menu-presences' => Presence::class,
        '#report-menu-session' => ReportSession::class,
        '#report-menu-round' => ReportRound::class,
    ],
    'color' => [
        'active' => '#6777ef',
    ],
];
