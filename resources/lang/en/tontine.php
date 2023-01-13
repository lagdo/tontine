<?php

return [
    'menus' => [
        'tontine' => "Tontine",
        'planning' => "Planning",
        'meeting' => "Meeting",
        'members' => "Members",
        'charges' => "Fees and fines",
        'sessions' => "Sessions",
        'pools' => "Pools",
        'reports' => "Reports",
    ],
    'titles' => [
        'tontines' => "Tontines",
        'rounds' => "Rounds",
        'add' => "Add a tontine",
        'edit' => "Edit a tontine",
    ],
    'labels' => [
        'types' => [
            'mutual' => "Mutual",
            'financial' => "Financial",
        ],
    ],
    'actions' => [
        'rounds' => "Rounds",
        'select' => "Select",
    ],
    'messages' => [
        'created' => "The tontine was successfully created.",
        'updated' => "The tontine was successfully updated.",
    ],
    'round' => [
        'titles' => [
            'add' => "Add a round",
            'edit' => "Edit a round",
        ],
        'messages' => [
            'created' => "The round was successfully created.",
            'updated' => "The round was successfully updated.",
            'deleted' => "The round was successfully deleted.",
        ],
    ],
    'member' => [
        'titles' => [
            'add' => "Add members",
            'edit' => "Edit a member",
        ],
        'messages' => [
            'created' => "The member was successfully created.",
            'updated' => "The member was successfully updated.",
            'deleted' => "The member was successfully deleted.",
        ],
    ],
    'charge' => [
        'titles' => [
            'add' => "Add fees and fines",
            'edit' => "Edit a fee or fine",
        ],
        'messages' => [
            'created' => "The charge was successfully created.",
            'updated' => "The charge was successfully updated.",
            'deleted' => "The charge was successfully deleted.",
        ],
    ],
    'session' => [
        'titles' => [
            'add' => "Add sessions",
            'edit' => "Edit a session",
            'title' => "Session of :month :year",
            'host' => "Edit the host",
            'venue' => "Venue",
        ],
        'labels' => [
            'times' => "Times",
            'host' => "Host",
            'address' => "Address",
        ],
        'actions' => [
            'host' => "Host",
            'venue' => "Venue",
        ],
        'messages' => [
            'created' => "The session was successfully created.",
            'updated' => "The session was successfully updated.",
            'deleted' => "The session was successfully deleted.",
        ],
        'questions' => [
            'open' => "Open this session?",
            'close' => "Close this session?",
        ],
    ],
    'pool' => [
        'titles' => [
            'add' => "Add pools",
            'edit' => "Edit a pool",
            'deposits' => "Deposits report",
            'remitments' => "Remitments report",
            'subscriptions' => "Subscriptions",
        ],
        'actions' => [
            'subscriptions' => "Subscriptions",
        ],
        'messages' => [
            'created' => "The pool was successfully created.",
            'updated' => "The pool was successfully updated.",
            'deleted' => "The pool was successfully deleted.",
        ],
        'errors' => [
            'number' => [
                'invalid' => "Please provide a valid number.",
                'max' => "You can add a maximum of :max entries.",
            ],
        ],
    ],
    'subscription' => [
        'messages' => [
            'created' => "The member subscription was created.",
            'deleted' => "The member subscription was deleted.",
        ],
    ],
    'loan' => [
        'titles' => [
            'add' => "Add a loan",
        ],
        'labels' => [
            'bid' => "Bid",
            'amount_to_bid' => "Amount to bid",
        ],
    ],
];
