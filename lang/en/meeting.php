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
        'auctions' => "Auctions",
        'fundings' => "Fundings",
        'loans' => "Loans",
        'amounts' => "Amounts",
        'settlements' => "Settlements",
        'fine' => "Fine",
        'amount_to_lend' => "Amount to lend",
        'benefit' => "Benefit",
        'refunds' => "Refunds",
        'partial-refunds' => "Partial refunds",
        'disbursements' => "Disbursements",
        'profits' => "Profits distribution",
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
        'cash' => "Cash",
        'credits' => "Credits",
        'profits' => "Profits",
    ],
    'labels' => [
        'member' => "Member",
        'members' => "Members",
        'charge' => "Corresponding fee",
        'category' => "Category",
        'session' => "Session",
        'payments' => "Payments",
        'debt' => "Debt",
        'funding' => "Funding",
        'profit' => "Profit",
        'duration' => "Duration",
        'distribution' => "Distribution",
    ],
    'messages' => [
        'agenda' => [
            'updated' => "Saved!",
        ],
        'report' => [
            'updated' => "Saved!",
        ],
        'profit' => [
            'saved' => "Saved!",
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
            'fees' => "Fees",
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
            'amount' => "Amount to remit",
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
            'not_found' => "Cannot find the loan.",
            'update' => "This loan cannot be updated.",
        ],
    ],
    'refund' => [
        'titles' => [
            'add' => "Add a partial refund",
        ],
        'questions' => [
            'delete' => "Delete this partial refund?",
        ],
        'errors' => [
            'not_found' => "Cannot find the partial refund.",
            'cannot_delete' => "Cannot delete this partial refund.",
            'pr_amount' => "The partial refund amount must be lower than the amount due.",
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
            'not_found' => "Cannot find the disbursement.",
        ],
    ],
];
