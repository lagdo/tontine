<?php

return [
    'titles' => [
        'session' => "Session",
        'agenda' => "Agenda",
        'report' => "Report",
        'pools' => "Pools",
        'charges' => "Fees",
        'fees' => "Fees",
        'fines' => "Fines",
        'deposits' => "Deposits",
        'remitments' => "Remitments",
        'fundings' => "Fundings",
        'loans' => "Loans",
        'amounts' => "Amounts",
        'settlements' => "Settlements",
        'fine' => "Fine",
        'amount_to_lend' => "Amount to lend",
        'benefit' => "Benefit",
        'refunds' => "Refunds",
        'disbursements' => "Disbursements",
    ],
    'actions' => [
        'deposits' => "Deposits",
        'remitments' => "Remitments",
        'settlements' => "Settlements",
        'loans' => "Loans",
        'amounts' => "Amounts",
        'reports' => "Reports",
        'report' => "Report",
        'summary' => "Summary",
        'fine' => "Fine",
        'pools' => "Pools",
        'charges' => "Fees",
        'credits' => "Credits",
        'cash' => "Cash",
    ],
    'labels' => [
        'member' => "Member",
        'members' => "Members",
        'category' => "Category",
        'session' => "Session",
        'payments' => "Payments",
    ],
    'messages' => [
        'agenda' => [
            'updated' => "Saved!",
        ],
        'report' => [
            'updated' => "Saved!",
        ],
    ],
    'warnings' => [
        'session' => [
            'closed' => "This operation is not allowed when the session is closed.",
        ],
    ],
    'errors' => [
        'amount' => [
            'invalid' => ":amount is not a valid amount.",
        ],
    ],
    'charge' => [
        'titles' => [
            'fixed' => "Fixed fees",
            'variable' => "Variable fees",
        ],
    ],
    'category' => [
        'types' => [
            'expense' => "Expense",
            'support' => "Support",
            'reception' => "Reception",
            'other' => "Other",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding category.",
        ],
    ],
    'remitment' => [
        'titles' => [
            'add' => "Add a remitment",
            'auctions' => "Auctions",
        ],
        'labels' => [
            'auction' => "Auction",
        ],
    ],
    'funding' => [
        'titles' => [
            'add' => "Add a funding",
            'edit' => "Edit a funding",
        ],
        'questions' => [
            'delete' => "Delete this funding?",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding funding.",
        ],
    ],
    'loan' => [
        'titles' => [
            'add' => "Add a loan",
            'edit' => "Edit a loan",
        ],
        'labels' => [
            'p' => "Principal",
            'i' => "Interest",
            'principal' => "Principal",
            'interest' => "Interest",
            'percentage' => "Percentage",
            'amount_available' => "Amount available: :amount",
        ],
        'interest' => [
            'f' => "Fixed",
            's' => "Simple",
            'c' => "Compound",
            'if' => "Fixed interest",
            'is' => "Simple interest",
            'ic' => "Compound interest",
        ],
        'questions' => [
            'delete' => "Delete this loan?",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding loan.",
            'update' => "This loan cannot be updated.",
        ],
    ],
    'disbursement' => [
        'titles' => [
            'add' => "Add a disbursement",
            'edit' => "Edit a disbursement",
        ],
        'labels' => [
            'amount_available' => "Amount available: :amount",
        ],
        'questions' => [
            'delete' => "Delete this disbursement?",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding disbursement.",
        ],
    ],
];
