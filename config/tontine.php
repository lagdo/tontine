<?php

return [
    'templates' => [
        'login' => [
            'form' => 'tontine.app.auth.login-form',
        ],
        // 'report' => 'default',
    ],
    'access' => [
        'admin' => [
            'admin' => ['guilds'],
            'finance' => ['pools', 'charges', 'accounts'],
            'guild' => ['members', 'calendar'],
        ],
        'round' => [
            'planning' => ['finance'],
            'meeting' => ['sessions', 'payments', 'presences'],
            'report' => ['session', 'round'],
        ],
        'labels' => [
            'admin' => 'tontine.menus.admin',
            'admin_guilds' => 'tontine.menus.guilds',
            'finance' => 'tontine.menus.finance',
            'finance_pools' => 'tontine.menus.pools',
            'finance_charges' => 'tontine.menus.charges',
            'finance_accounts' => 'tontine.menus.accounts',
            'guild' => 'tontine.menus.guild',
            'guild_members' => 'tontine.menus.members',
            'guild_calendar' => 'tontine.menus.calendar',
            'planning' => 'tontine.menus.planning',
            'planning_finance' => 'tontine.menus.finance',
            'meeting' => 'tontine.menus.meeting',
            'meeting_sessions' => 'tontine.menus.sessions',
            'meeting_payments' => 'tontine.menus.payments',
            'meeting_presences' => 'tontine.menus.presences',
            'report' => 'tontine.menus.report',
            'report_session' => 'tontine.menus.session',
            'report_round' => 'tontine.menus.round',
        ],
    ]
];
