<?php

use Ajax\App\Admin;
use Ajax\App\Guild;
use Ajax\App\Meeting;
use Ajax\App\Planning;
use Ajax\App\Report;

return [
    'admin' => [
        '#admin-menu-guilds' => Admin\Guild\Guild::class,
        '#admin-menu-users' => Admin\User\User::class,
    ],
    'guild' => [
        '#guild-menu-members' => Guild\Member\Member::class,
        '#guild-menu-calendar' => Guild\Calendar\Round::class,
        '#finance-menu-pools' => Guild\Pool\Pool::class,
        '#finance-menu-accounts' => Guild\Account\Account::class,
        '#finance-menu-charges' => Guild\Charge\Charge::class,
    ],
    'round' => [
        '#planning-menu-enrollment' => Planning\Enrollment::class,
        '#planning-menu-finance' => Planning\Finance::class,
        '#meeting-menu-sessions' => Meeting\Session\Session::class,
        '#meeting-menu-payments' => Meeting\Payment\Payment::class,
        '#meeting-menu-presences' => Meeting\Presence\Presence::class,
        '#report-menu-session' => Report\Session\Session::class,
        '#report-menu-round' => Report\Round\Round::class,
    ],
    'color' => [
        'active' => '#6777ef',
    ],
];
