<?php

return [
    'menus' => [
        'tontine' => "Tontine",
        'planning' => "Planning",
        'meeting' => "Meeting",
        'members' => "Members",
        'charges' => "Fees and fines",
        'sessions' => "Sessions",
        'funds' => "Funds",
        'tables' => "Tables",
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
        'labels' => [
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
        'labels' => [
            'add' => "Add members",
        ],
        'messages' => [
            'created' => "The member was successfully created.",
            'updated' => "The member was successfully updated.",
            'deleted' => "The member was successfully deleted.",
        ],
    ],
    'charge' => [
        'labels' => [
            'add' => "Add fees and fines",
        ],
        'messages' => [
            'created' => "The charge was successfully created.",
            'updated' => "The charge was successfully updated.",
            'deleted' => "The charge was successfully deleted.",
        ],
    ],
    'session' => [
        'labels' => [
            'add' => "Add sessions",
            'times' => "Times",
            'host' => "Host",
            'address' => "Address",
        ],
        'titles' => [
            'title' => "Session of :month :year",
            'host' => "Edit the host",
            'venue' => "Venue",
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
    'fund' => [
        'labels' => [
            'add' => "Add funds",
        ],
        'titles' => [
            'deposits' => "Deposits table",
            'remittances' => "Remittances table",
            'subscriptions' => "Subscriptions",
        ],
        'actions' => [
            'subscriptions' => "Subscriptions",
        ],
        'messages' => [
            'created' => "The fund was successfully created.",
            'updated' => "The fund was successfully updated.",
            'deleted' => "The fund was successfully deleted.",
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
    'bidding' => [
        'titles' => [
            'add' => "Add a bidding",
        ],
        'labels' => [
            'bid' => "Bid",
            'amount_to_bid' => "Amount to bid",
        ],
    ],
];
