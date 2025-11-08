<?php

return [
    'templates' => [
        'login' => [
            'form' => 'tontine.app.auth.login-form',
        ],
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
    ],
    'videos' => [
        'playlist' => 'https://www.youtube.com/watch?v=Hvq6fqkJ7E8&list=PLN07txLdIJixbuzmuE4xrqDRtLGIksoDd',
        'parts' => [
            'part1' => [
                'menu' => 'Partie 1',
                'title' => "Partie 1: Présentation de l'application",
                'items' => [[
                    'url' => 'https://www.youtube-nocookie.com/embed/Hvq6fqkJ7E8?si=uxf9qeT6hzp-L9Mq',
                    'title' => "Présentation générale de l'application Siak Tontine",
                    'description' => "",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/cWbouc41mWo?si=tG6K2HejqkMKgUz-',
                    'title' => "La gestion des utilisateurs dans l'application Siak Tontine",
                    'description' => "",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/TK3XfW3lP9M?si=Tqe9pzva8tsYK_Uq',
                    'title' => "La navigation dans les pages de l'application Siak Tontine",
                    'description' => "",
                ]],
            ],
            'part2' => [
                'menu' => 'Partie 2',
                'title' => "Partie 2: Les données d'une organisation",
                'items' => [[
                //     'url' => '',
                //     'title' => "",
                //     'description' => "",
                // ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/cWbouc41mWo?si=pRICZt4eul6_ewfN',
                    'title' => "La saisie du calendrier des séances dans l'application Siak Tontine",
                    'description' => "La gestion des informations concernant les tours et les séances: dates, hôtes, lieux.",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/H5WmJZ6ZOvs?si=rBm6aYaHPjs8Pxbm',
                    'title' => "La saisie des rubriques financières dans l'application Siak Tontine",
                    'description' => "La gestion des rubriques dans lesquelles sont classées les opérations financières.",
                ]],
            ],
            'part3' => [
                'menu' => 'Partie 3',
                'title' => "Partie 3: La planification d'un tour",
                'items' => [[
                    'url' => 'https://www.youtube-nocookie.com/embed/qqHRQ5Xbf4I?si=XkAicmwRCCHFp2GK',
                    'title' => "La sélection des membres, frais et épargnes pour un tour dans l'application Siak Tontine",
                    'description' => "La sélection et le paramétrage des membres, frais et fonds d'épargne qui seront actifs pendant un tour.",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/w1OwKvjTp2E?si=4VpPZWOrLQIpP8uy',
                    'title' => "La sélection des fonds de tontine pour un tour dans l'application Siak Tontine",
                    'description' => "La sélection et le paramétrage des fonds de tontine qui seront actifs pendant un tour.",
                ]],
            ],
            'part4' => [
                'menu' => 'Partie 4',
                'title' => "Partie 4: La gestion des séances",
                'items' => [[
                    'url' => 'https://www.youtube-nocookie.com/embed/3ldyjtsGIkI?si=pBk2xirCDPGp2rHd',
                    'title' => "La navigation entre les pages de séance dans l'application Siak Tontine",
                    'description' => "La navigation entre les pages où sont saisies ou affichées les données des séances.",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/Uj0EDyFiXvU?si=AW0D1iEapdlL6XRN',
                    'title' => "Saisie des séances dans l'application Siak Tontine: fonds de tontine",
                    'description' => "",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/cXVNppvF6I4?si=UHOg66oFjgsm9F7N',
                    'title' => "Saisie des séances dans l'application Siak Tontine: frais et amendes",
                    'description' => "",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/roN0d5ipdI0?si=Zo5TtzTcgtg-DT4H',
                    'title' => "Saisie des séances dans l'application Siak Tontine: emprunts et épargnes",
                    'description' => "",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/QiwAAGpBDFo?si=0uPG2dt8KZYpAn_T',
                    'title' => "Saisie des séances dans l'application Siak Tontine: remboursements et profits",
                    'description' => "",
                ], [
                    'url' => 'https://www.youtube-nocookie.com/embed/B4qEdMkhHtU?si=D1GVLlu3aHSk5xuk',
                    'title' => "Saisie des séances dans l'application Siak Tontine: autres données",
                    'description' => "",
                ]],
            ],
            'part5' => [
                'menu' => 'Partie 5',
                'title' => "Partie 5: Les rapports et bilans",
                'items' => [[
                    'url' => 'https://www.youtube-nocookie.com/embed/zAknM3NWrSA?si=yjR_FVKBJCnnU050',
                    'title' => "Les rapports de séance et de tour dans l'application Siak Tontine",
                    'description' => "",
                ]],
            ],
        ],
    ]
];
