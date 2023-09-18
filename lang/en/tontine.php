<?php

return [
    'date' => [
        'format' => 'D, M j, Y',
    ],
    'menus' => [
        'tontines' => "Associations",
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
        'tontines' => "Associations",
        'tontine' => "Tontine",
        'rounds' => "Rounds",
        'add' => "Add an association",
        'edit' => "Edit an association",
        'choose' => "Select an association",
        'type' => "Select the tontine type",
    ],
    'descriptions' => [
        'types' => [
            'help' => "The type of tontine depends on how the contributions are defined.",
            'libre' => "each member chooses the amount he contributes at each meeting.",
            'mutual' => "each member contributes a fixed amount. The remitments are planned in advance.",
            'financial' => "each member contributes a fixed amount. The remitments are done after auction.",
        ],
    ],
    'labels' => [
        'tontine' => "Association",
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
        'created' => "The association was successfully created.",
        'updated' => "The association was successfully updated.",
        'selected' => "You have selected the association :tontine. You still need to add some rounds to its tontine.",
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
            'choose' => "Select a round",
        ],
        'messages' => [
            'created' => "The round was successfully created.",
            'updated' => "The round was successfully updated.",
            'deleted' => "The round was successfully deleted.",
            'selected' => "You have selected the association :tontine and round :round.",
        ],
        'questions' => [
            'open' => "Open this round? Make sure you have set its data correctly.",
            'close' => "Close this round?",
        ],
    ],
    'member' => [
        'actions' => [
            'list' => "List",
        ],
        'tips' => [
            'list' => 'Click on the "List" button to enter a list of members in a text box.',
            'add' => "Enter a member name on each line. If a phone number or email is available, separate it with a semi-colon.",
            'example' => 'For example,<br/>"Jean Amadou"<br/>or<br/>"Jean Amadou;jean.amadou@gmail.com;237670000000"',
        ],
        'titles' => [
            'add' => "Add members",
            'edit' => "Edit a member",
        ],
        'messages' => [
            'created' => "The members was successfully created.",
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
        'labels' => [
            'lend' => "For lend",
            'lendable' => "Available for loan",
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
            'free' => "Free pool",
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
