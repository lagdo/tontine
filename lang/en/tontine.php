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
        'options' => "Options",
        'round' => "Round",
        'session' => "Session",
        'rounds' => "Rounds",
        'sessions' => "Sessions",
        'pools' => "Tontines",
        'subscriptions' => "Subscriptions",
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
        'deleted' => "The association was successfully deleted.",
        'selected' => "You have selected the association :tontine. You still need to add some rounds to its tontine.",
        'screen' => [
            'orientation' => "If you are using a mobile device, we advise you to place it in landscape mode, for a better display.",
        ],
        'learning' => [
            'intro' => "Learn about Siak Tontine's features in this introductory video <a href=\":url\" target=\"_blank\">:url</a> (in french).",
        ],
    ],
    'questions' => [
        'delete' => "Delete the association? The related members, rounds and charges will also be deleted.",
    ],
    'errors' => [
        'action' => "Cannot proceed.",
        'editable' => "This item cannot be edited or deleted.",
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
            'example' => "For example,<br/>Jean Amadou<br/>or<br/>Jean Amadou;jean.amadou@gmail.com;237670000000",
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
        'questions' => [
            'delete' => "Delete this member?",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding member.",
        ],
    ],
    'charge' => [
        'titles' => [
            'charges' => "Fees and fines",
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
    'fund' => [
        'titles' => [
            'funds' => "Savings funds",
            'add' => "Add a savings fund",
            'edit' => "Edit a savings fund",
        ],
        'labels' => [
            'fund' => "Savings fund",
        ],
        'messages' => [
            'created' => "The savings fund was successfully created.",
            'updated' => "The savings fund was successfully updated.",
            'deleted' => "The savings fund was successfully deleted.",
        ],
    ],
    'category' => [
        'titles' => [
            'categories' => "Disbursement categories",
            'add' => "Add a category",
            'edit' => "Edit a category",
        ],
        'types' => [
            'disbursement' => "Disbursement",
        ],
        'messages' => [
            'created' => "The category was successfully created.",
            'updated' => "The category was successfully updated.",
            'deleted' => "The category was successfully deleted.",
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
            'add' => "Add a session",
            'add-list' => "Add sessions",
            'edit' => "Edit a session",
            'title' => "Session of :month :year",
            'host' => "Edit the host",
            'venue' => "Venue",
        ],
        'tips' => [
            'add' => "Enter a session on each line. Separate the title and date with a semicolon. The date must be in 'YYYY-MM-DD' format.",
            'example' => "For example,<br/>November 2023 session;2023-11-03",
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
            'disable' => "Disable this session?<br/>If the session was already opened, this may also delete related data.",
        ],
        'errors' => [
            'opened' => "A session has already been opened.",
            'delete' => "Cannot delete this session.",
            'date_dup' => "There is another session with the same date.",
        ],
    ],
    'pool' => [
        'titles' => [
            'add' => "Add a tontine",
            'add_many' => "Add tontines",
            'edit' => "Edit a tontine",
            'deposits' => "Deposits",
            'remitments' => "Remitments",
            'subscriptions' => "Subscriptions",
            'members' => "Members",
            'sessions' => "Sessions",
        ],
        'labels' => [
            'fixed' => "Amount fixed",
            'planned' => "Planned",
            'auction' => "At auction",
            'lendable' => "Available for loan",
            'deposit' => [
                'fixed' => "The amount of deposits is fixed",
            ],
            'remit' => [
                'fixed' => "The amount of remitments is fixed",
                'planned' => "The number of remitments is defined in advance",
                'auction' => "The amounts are remitted after auction",
                'lendable' => "The remaining amounts can be loaned",
            ],
        ],
        'help' => [
            'intro' => "You are going to add a new tontine.<br/>" .
                "We are going to ask you to specify its characteristics.",
            'deposit' => [
                'fixed' => "Check the box below each member who subscribes to this tontine must pay a fixed amount at each session.",
            ],
            'remit' => [
                'fixed' => "Check the box below if each beneficiary will receive the full amount corresponding to its deposits.",
                'planned' => "Check the box below if the number of beneficiaries at each session is defined in advance.",
                'auction' => "Check the box below if the choice of the tontine beneficiaries is subject to auction.",
                'lendable' => "Check the box below if the amounts remaining in the pool after remitments can be loaned.",
            ],
        ],
        'actions' => [
            'subscriptions' => "Subscriptions",
        ],
        'questions' => [
            'delete' => "Delete this tontine?<br/>Please, make sure it has no subscription.",
        ],
        'messages' => [
            'created' => "The tontine was successfully created.",
            'updated' => "The tontine was successfully updated.",
            'deleted' => "The tontine was successfully deleted.",
        ],
        'errors' => [
            'number' => [
                'invalid' => "Please provide a valid number.",
                'max' => "You can add a maximum of :max entries.",
            ],
            'subscription' => "The tontine still has subscriptions.",
            'no_subscription' => "There are tontines with no subscription.",
        ],
    ],
    'subscription' => [
        'titles' => [
            'beneficiaries' => "Beneficiaries",
            'planning' => "Planning",
            'deposits' => "Deposits balance",
            'remitments' => "Remitments balance",
        ],
        'messages' => [
            'created' => "The member subscription was created.",
            'deleted' => "The member subscription was deleted.",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding subscription.",
            'create' => "Cannot create a new subscription.",
            'delete' => "Cannot delete this subscription.",
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
        'errors' => [
            'planning' => "The selected beneficiary is already planned on another session.",
        ],
    ],
    'report' => [
        'titles' => [
            'bills' => [
                'session' => "Session bill amounts",
                'total' => "Total bill amounts",
            ],
            'amounts' => [
                'cashed' => "Cashed",
                'disbursed' => "Disbursed",
            ],
        ],
    ],
];
