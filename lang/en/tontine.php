<?php

return [
    'date' => [
        'format' => 'D, M j, Y',
    ],
    'menus' => [
        'tontines' => "Tontines",
        'tontine' => "Tontine",
        'planning' => "Planning",
        'meeting' => "Meeting",
        'report' => "Report",
        'members' => "Members",
        'charges' => "Fees",
        'round' => "Round",
        'session' => "Session",
        'rounds' => "Rounds",
        'sessions' => "Sessions",
        'pools' => "Pools",
        'balance' => "Balance",
        'subscriptions' => "Subscriptions",
        'beneficiaries' => "Beneficiaries",
        'payments' => "Payments",
        'profile' => "Profile",
        'logout' => "Logout",
    ],
    'titles' => [
        'tontines' => "Tontines",
        'rounds' => "Rounds",
        'add' => "Add a tontine",
        'edit' => "Edit a tontine",
        'choose' => "Choose a tontine",
    ],
    'labels' => [
        'tontine' => "Tontine",
        'round' => "Round",
        'types' => [
            'libre' => "Free",
            'mutual' => "Mutual",
            'financial' => "Financial",
        ],
        'fees' => [
            'variable' => "Variable",
            'fixed' => "Fixed",
        ],
    ],
    'actions' => [
        'rounds' => "Rounds",
        'open' => "Open",
        'enter' => "Enter",
        'select' => "Select",
        'choose' => "Choose",
    ],
    'messages' => [
        'created' => "The tontine was successfully created.",
        'updated' => "The tontine was successfully updated.",
        'screen' => [
            'orientation' => "If you are using a mobile device, we advise you to place it in landscape mode, for a better display.",
        ],
    ],
    'errors' => [
        'action' => "Cannot proceed.",
    ],
    'round' => [
        'titles' => [
            'add' => "Add a round",
            'edit' => "Edit a round",
            'choose' => "Choose a round",
        ],
        'messages' => [
            'created' => "The round was successfully created.",
            'updated' => "The round was successfully updated.",
            'deleted' => "The round was successfully deleted.",
        ],
        'questions' => [
            'open' => "Open this round? Make sure you have set its data correctly.",
            'close' => "Close this round?",
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
        'errors' => [
            'not_found' => "Cannot find the corresponding member.",
        ],
    ],
    'charge' => [
        'titles' => [
            'add' => "Add fees",
            'edit' => "Edit a fee",
        ],
        'messages' => [
            'created' => "The fee was successfully created.",
            'updated' => "The fee was successfully updated.",
            'deleted' => "The fee was successfully deleted.",
        ],
        'questions' => [
            'delete' => "Delete this fee?",
        ],
        'errors' => [
            'cannot_delete' => "Cannot delete this fee.",
        ],
        'types' => [
            'fee' => "Fee",
            'fine' => "Fine",
        ],
        'periods' => [
            'none' => "None",
            'unique' => "Unique",
            'round' => "Round",
            'session' => "Session",
        ],
        'groups' => [
            'fixed' => "Fixed",
            'variable' => "Variable",
        ],
    ],
    'bill' => [
        'errors' => [
            'not_found' => "Cannot find the corresponding bill.",
        ],
    ],
    'session' => [
        'status' => [
            'pending' => "Pending",
            'opened' => "Opened",
            'closed' => "Closed",
        ],
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
            'warning' => "First make sure that you have entered all the informations " .
                "required about members subscriptions, in the planning section.",
            'close' => "Close this session?",
            'delete' => "Delete this session?",
        ],
        'errors' => [
            'opened' => "A session has already been opened.",
        ],
    ],
    'pool' => [
        'titles' => [
            'add' => "Add pools",
            'edit' => "Edit a pool",
            'deposits' => "Deposits balance",
            'remitments' => "Remitments balance",
            'subscriptions' => "Subscriptions",
        ],
        'actions' => [
            'subscriptions' => "Subscriptions",
        ],
        'questions' => [
            'delete' => "Delete this pool?<br/>Please, make sure it has no subscription.",
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
            'subscription' => "The pool still has subscriptions.",
            'no_subscription' => "There are pools with no subscription.",
        ],
    ],
    'subscription' => [
        'messages' => [
            'created' => "The member subscription was created.",
            'deleted' => "The member subscription was deleted.",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding subscription.",
        ],
    ],
    'beneficiary' => [
        'errors' => [
            'cant_change' => "Cannot change the beneficiary.",
        ],
    ],
    'remitment' => [
        'labels' => [
            'not-assigned' => "** Not assigned **",
        ],
    ],
];
