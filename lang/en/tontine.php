<?php

return [
    'date' => [
        'format' => 'D, M j, Y',
        'format_medium' => 'M j, Y',
        'format_my' => 'F Y',
    ],
    'menus' => [
        'admin' => "Administration",
        'guilds' => "Organisations",
        'guild' => "Organisation",
        'tontine' => "Tontine",
        'users' => "Users",
        'planning' => "Planning",
        'meeting' => "Meeting",
        'report' => "Report",
        'members' => "Members",
        'finance' => "Finance",
        'participation' => "Participation",
        'accounts' => "Accounts",
        'charges' => "Charges",
        'calendar' => "Calendar",
        'round' => "Round",
        'session' => "Session",
        'sessions' => "Sessions",
        'pools' => "Tontines",
        'subscriptions' => "Subscriptions",
        'presences' => "Presences",
        'payments' => "Payments",
        'profile' => "Profile",
        'logout' => "Logout",
    ],
    'titles' => [
        'guilds' => "Organisations",
        'tontine' => "Tontine",
        'members' => "Members",
        'rounds' => "Rounds",
        'sessions' => "Sessions",
        'session' => "Session",
        'pools' => "Tontines",
        'add' => "Add an organisation",
        'edit' => "Edit an organisation",
        'choose' => "Select an organisation",
        'type' => "Select the tontine type",
        'presences' => "Presences: :of",
        'select' => [
            'guild' => "(Select an organisation)",
            'round' => "(Select a round)",
        ],
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
        'tontine' => "Organisation",
        'round' => "Round",
        'pool' => "Tontine",
        'types' => [
            'libre' => "Free",
            'mutual' => "Mutual",
            'financial' => "Financial",
        ],
        'fees' => [
            'variable' => "Variable",
            'fixed' => "Fixed",
        ],
        'present' => "Present",
    ],
    'actions' => [
        'rounds' => "Rounds",
        'sessions' => "Sessions",
        'open' => "Open",
        'enter' => "Enter",
        'select' => "Select",
        'choose' => "Choose",
    ],
    'messages' => [
        'bonjour' => "Hi, :name",
        'created' => "The organisation was successfully created.",
        'updated' => "The organisation was successfully updated.",
        'deleted' => "The organisation was successfully deleted.",
        'selected' => "You have selected the organisation :guild.",
        'back_to_admin' => "You are back to the Administration section of the organisation :guild.",
        'screen' => [
            'orientation' => "If you are using a mobile device, we advise you to place it in landscape mode, for a better display.",
        ],
        'learning' => [
            'intro' => "Learn about Siak Tontine's features in this introductory video <a class=\"highlight\" href=\":url\" target=\"_blank\">:url</a> (in french).",
        ],
    ],
    'questions' => [
        'delete' => "Delete the organisation? The related members, rounds and charges will also be deleted.",
    ],
    'errors' => [
        'action' => "Cannot proceed.",
        'editable' => "This item cannot be edited or deleted.",
        'checks' => [
            'members' => "You have beed redirected to the members page.<br/>" .
                "You need to add one or more members before you can move forward.",
            'sessions' => "You have beed redirected to the sessions page.<br/>" .
                "You need to add one or more sessions before you can move forward.",
            'pools' => "You have beed redirected to the pools page.<br/>" .
                "You need to add one or more pools before you can move forward.",
            'opened_sessions' => "You have beed redirected to the sessions page.<br/>" .
                "You need to have one or more sessions opened before you can move forward.",
        ],
    ],
    'invite' => [
        'titles' => [
            'hosts' => "Invites sent",
            'guests' => "Invites received",
            'add' => "Send an invite",
            'add_desc' => "The user must already have a valid account.",
            'access' => "Access",
            'organisations' => "Guest :: Organisations",
        ],
        'labels' => [
            'host' => "Host",
            'guest' => "Guest",
        ],
        'actions' => [
            'accept' => "Accept",
            'refuse' => "Refuse",
            'cancel' => "Cancel",
            'access' => "Access",
        ],
        'active' => [
            'expires' => "Expires on :date",
            'expired' => "Has expired on :date",
            'active' => "Active since :date",
            'inactive' => "Inactive since :date",
        ],
        'status' => [
            'pending' => "Pending",
            'expired' => "Expired",
            'accepted' => "Accepted",
            'refused' => "Refused",
            'cancelled' => "Cancelled",
            'unknown' => "(Unknown)",
        ],
        'questions' => [
            'accept' => "Accept this invite?",
            'refuse' => "Refuse this invite?",
            'cancel' => "Cancel this invite?",
            'delete' => "Delete this invite?",
        ],
        'messages' => [
            'sent' => "Invite sent. The user must connect to his account to be able to accept it.",
            'accepted' => "You have accepted the invite.",
            'refused' => "You have refused the invite.",
            'cancelled' => "You have cancelled the invite.",
            'deleted' => "You have deleted the invite.",
        ],
        'errors' => [
            'user_not_found' => "Unable to find the user to invite. Check that he has already created his account.",
            'cannot_invite' => "Unable to send the invite. Maybe you have already invited this user?",
            'not_allowed' => "This operation is not allowed.",
            'invite_not_found' => "Unable to find the invite.",
            'invite_expired' => "Sorry, this invite has expired.",
            'access_denied' => "As a guest, you don't have access to this section. Sorry.",
            'no_guild' => "You need to add at least one organisation.",
        ],
    ],
    'round' => [
        'titles' => [
            'add' => "Add a round",
            'edit' => "Edit a round",
            'choose' => "Select a round",
        ],
        'labels' => [
            'savings' => "Add a default savings fund",
        ],
        'messages' => [
            'created' => "The round was successfully created.",
            'updated' => "The round was successfully updated.",
            'deleted' => "The round was successfully deleted.",
            'selected' => "You have selected the organisation :guild and round :round.",
        ],
        'questions' => [
            'open' => "Open this round? Make sure you have setup its planning correctly.",
            'close' => "Close this round?",
            'delete' => "Delete this round?",
        ],
        'errors' => [
            'delete' => "Cannot delete this round.",
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
            'members' => "Members",
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
            'cannot_delete' => "Cannot delete this member.",
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
            'savings' => "Savings",
            'funds' => "Savings funds",
            'add' => "Add a savings fund",
            'edit' => "Edit a savings fund",
        ],
        'labels' => [
            'default' => "Saving",
            'fund' => "Savings fund",
            'savings' => "Savings",
        ],
        'questions' => [
            'delete' => "Delete this savings fund?",
            'disable' => "Disable this savings fund?",
        ],
        'messages' => [
            'created' => "The savings fund was successfully created.",
            'updated' => "The savings fund was successfully updated.",
            'deleted' => "The savings fund was successfully deleted.",
            'enabled' => "The savings fund was successfully enabled.",
            'disabled' => "The savings fund was successfully disabled.",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding fund.",
        ],
    ],
    'account' => [
        'titles' => [
            'outflows' => "Cash outflows",
            'add' => "Add an account",
            'edit' => "Edit an account",
        ],
        'types' => [
            'outflow' => "Cash outflow",
        ],
        'questions' => [
            'delete' => "Delete this account?",
        ],
        'messages' => [
            'created' => "The account was successfully created.",
            'updated' => "The account was successfully updated.",
            'deleted' => "The account was successfully deleted.",
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
            'start' => "Start session",
            'end' => "End session",
        ],
        'tips' => [
            'add' => "Enter a session on each line. Separate the title and date with a semicolon. The date must be in 'YYYY-MM-DD' format.",
            'example' => "For example,<br/>November 2023 session;2023-11-03",
        ],
        'labels' => [
            'times' => "Times",
            'host' => "Host",
            'address' => "Address",
            'title' => ":date session",
            'start_session' => "Start session: :session",
            'end_session' => "End session: :session",
            'end_interest' => "End interest: :session",
            'count' => "Number of sessions: :count",
            'active' => "Active",
            'start' => "Start",
            'end' => "End",
            'interest' => "Int.",
        ],
        'actions' => [
            'host' => "Host",
            'venue' => "Venue",
            'resync' => "Resynchronize",
        ],
        'messages' => [
            'created' => "The session was successfully created.",
            'updated' => "The session was successfully updated.",
            'deleted' => "The session was successfully deleted.",
            'resynced' => "The sessions were successfully resynced.",
            'fund' => [
                'saved' => "The sessions of the savings fund was successfully saved.",
            ],
            'pool' => [
                'saved' => "The sessions of the tontine fund was successfully saved.",
            ],
        ],
        'questions' => [
            'open' => "Open this session?",
            'warning' => "First make sure that you have entered all the informations " .
                "required about members subscriptions, in the planning section.",
            'close' => "Close this session?",
            'delete' => "Delete this session?",
            'disable' => "Disable this session?<br/>If the session was already opened, this may also delete related data.",
            'resync' => "Resynchronize the sessions data?<br/>You need to do this if you have changed the members, the sessions, the charges, or the subscriptions after a session was opened.",
        ],
        'errors' => [
            'not_found' => "Unable to find the session.",
            'opened' => "A session has already been opened.",
            'not_opened' => "The session is not opened",
            'delete' => "Cannot delete this session.",
            'date_dup' => "There is another session with the same date.",
            'sorting' => "The session sorting cannot be modified.",
            'start' => "The start session is incorrect.",
            'end' => "The end session is incorrect.",
            'interest' => "The interest end session is incorrect.",
            'dates' => [
                'end' => "The start session must precede the end session.",
                'int' => "The interest end session must be between the start and the end sessions.",
            ],
        ],
    ],
    'pool' => [
        'titles' => [
            'pools' => "Tontine funds",
            'add' => "Add a tontine fund",
            'add_many' => "Add tontine funds",
            'edit' => "Edit a tontine fund",
            'deposits' => "Deposits",
            'remitments' => "Remitments",
            'subscriptions' => "Subscriptions",
            'members' => "Members",
            'sessions' => "Sessions",
        ],
        'labels' => [
            'fixed' => "Fixed amount",
            'planned' => "Planned",
            'auction' => "At auction",
            'lendable' => "Available for loan",
            'deposit' => [
                'fixed' => "The amount of deposits is fixed",
                'lendable' => "The remaining amounts can be loaned",
            ],
            'remit' => [
                'planned' => "The number of remitments is defined in advance",
                'auction' => "The amounts are remitted after auction",
            ],
        ],
        'help' => [
            'intro' => "You are going to add a new tontine fund.<br/>" .
                "We are going to ask you to specify its characteristics.",
            'deposit' => [
                'fixed' => "Check the box below each member who subscribes to this tontine fund must pay a fixed amount at each session.",
                'lendable' => "Check the box below if the amounts remaining in the pool after remitments can be loaned.",
            ],
            'remit' => [
                'planned' => "Check the box below if the number of beneficiaries at each session is defined in advance.",
                'auction' => "Check the box below if the choice of the tontine beneficiaries is subject to auction.",
            ],
        ],
        'questions' => [
            'delete' => "Delete this tontine fund?<br/>Please, make sure it has no subscription.",
            'disable' => "Disable this tontine fund?<br/>Please, make sure it has no subscription.",
        ],
        'messages' => [
            'created' => "The tontine fund was successfully created.",
            'updated' => "The tontine fund was successfully updated.",
            'deleted' => "The tontine was fund successfully deleted.",
            'selected' => "Now showing the subscriptions of the tontine fund :pool.",
            'enabled' => "The tontine fund was successfully enabled.",
            'disabled' => "The tontine fund was successfully disabled.",
        ],
        'errors' => [
            'not_found' => "Unable to find this tontine fund.",
            'number' => [
                'invalid' => "Please provide a valid number.",
                'max' => "You can add a maximum of :max entries.",
            ],
            'subscription' => "The tontine still has subscriptions.",
            'no_subscription' => "There are tontines with no subscription.",
            'payments' => "All deposits and remitments must be deleted prior to deleting or disabling a tontine fund.",
            'not_planned' => "Cannot show the planning or beneficiaries of a tontine with remitments not planned.",
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
        'actions' => [
            'subscriptions' => "Subscriptions",
            'sessions' => "Sessions",
            'planning' => "Planning",
            'beneficiaries' => "Beneficiaries",
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
            'max-count' => "The max number of remitments for this session is already reached.",
        ],
    ],
    'report' => [
        'actions' => [
            'show' => "Show",
            'session' => "Session report",
            'round' => "Round report",
            'credit' => "Credit report",
            'savings' => "Savings report",
        ],
        'titles' => [
            'session' => "Session report",
            'round' => "Round report",
            'credit' => "Credit report",
            'savings' => "Savings report",
            'fund' => "Fund",
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
    'options' => [
        'titles' => [
            'edit' => "Tontine options",
        ],
        'labels' => [
            'default' => 'Default',
            'report' => [
                'template' => "Reports template",
            ],
        ],
        'messages' => [
            'saved' => "The tontine options was successfully saved.",
        ],
    ],
];
