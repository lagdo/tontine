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
        'savings' => "Savings",
        'closings' => "Closings",
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
        'summary' => "Summary",
    ],
    'actions' => [
        'deposits' => "Deposits",
        'remitments' => "Remitments",
        'settlements' => "Settlements",
        'savings' => "Savings",
        'loans' => "Loans",
        'amounts' => "Amounts",
        'reports' => "Reports",
        'report' => "Report",
        'summary' => "Summary",
        'fine' => "Fine",
        'pools' => "Pools",
        'charges' => "Fees",
        'cash' => "Cash",
        'credits' => "Credit",
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
        'saving' => "Saving",
        'profit' => "Profit",
        'duration' => "Duration",
        'distribution' => "Distribution",
    ],
    'messages' => [
        'saved' => "Saved!",
        'deleted' => "Deleted!",
        'agenda' => [
            'updated' => "Saved!",
        ],
        'report' => [
            'updated' => "Saved!",
        ],
        'profit' => [
            'saved' => "Saved!",
            'deleted' => "Deleted!",
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
    'target' => [
        'actions' => [
            'deadline' => "Deadline",
        ],
        'titles' => [
            'set' => "Définir un délai",
            'edit' => "Changer le délai",
            'summary' => "Amount: :amount. Deadline: :deadline",
        ],
        'labels' => [
            'global' => "Check if the amount above is a total for all members.",
            'deadline' => "Deadline",
            'remaining' => "Remaining amount: :amount",
        ],
        'questions' => [
            'remove' => "Remove this deadline?",
        ],
        'messages' => [
            'removed' => "The deadline was successfully removed.",
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
            'amount' => "Remitted amount",
            'auction' => "Auction",
            'beneficiary' => "Beneficiary",
        ],
    ],
    'saving' => [
        'titles' => [
            'add' => "Add a saving",
            'edit' => "Edit a saving",
            'closing' => "Closing: :fund",
        ],
        'labels' => [
            'closing' => "Closing",
        ],
        'questions' => [
            'delete' => "Delete this saving?",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding saving.",
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
            'amount_available' => "available: :amount",
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
        'labels' => [
            'partial' => "Partial",
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
            'amount_available' => "available: :amount",
        ],
        'questions' => [
            'delete' => "Delete this disbursement?",
        ],
        'errors' => [
            'not_found' => "Cannot find the disbursement.",
        ],
    ],
    'profit' => [
        'labels' => [
            'amount' => "Amount to share",
        ],
        'distribution' => [
            'total' => "Saving: :saving. Interests: :refund.",
            'amount' => "Distribution: :amount.",
            'parts' => ":parts parts.",
            'basis' => "For each session, :unit = one part.",
        ],
    ],
];
