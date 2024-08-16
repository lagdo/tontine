<?php

return [
    'titles' => [
        'session' => "Session",
        'agenda' => "Meeting agenda",
        'report' => "Meeting report",
        'credit' => "Credit report",
        'pools' => "Pools",
        'charges' => "Fees",
        'fees' => "Fees",
        'fines' => "Fines",
        'deposits' => "Deposits",
        'remitments' => "Remitments",
        'auctions' => "Auctions",
        'savings' => "Savings",
        'closings' => "Closings",
        'loan' => "Loan",
        'loans' => "Loans",
        'amounts' => "Amounts",
        'settlements' => "Settlements",
        'fine' => "Fine",
        'amount_to_lend' => "Amount to lend",
        'benefit' => "Benefit",
        'refund' => "Refund",
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
        'item' => "Item",
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
            'closed' => "This operation is not allowed because the session is closed.",
        ],
        'charge' => [
            'disabled' => "This operation is not allowed because the fee is disabled.",
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
            'summary' => "Deadline: :deadline",
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
    'closing' => [
        'titles' => [
            'edit' => "Closing: :fund",
        ],
        'labels' => [
            'amount' => "Amount to share",
        ],
        'questions' => [
            'delete' => "Delete this closing?",
        ],
    ],
    'profit' => [
        'distribution' => [
            'total' => "Saving: :saving. Interests: :refund.",
            'amount' => "Distribution: :amount.",
            'parts' => ":parts parts.",
            'basis' => "For each session, :unit = one part.",
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
            'fund' => "Fund",
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
    'entry' => [
        'titles' => [
            'session' => "Session entry form",
            'report' => "Report entry form",
            'transactions' => "Transactions entry form",
        ],
        'actions' => [
            'session' => "Session entry",
            'report' => "Report entry",
            'transactions' => "Transactions entry",
        ],
        'files' => [
            'session' => "session-entry-form",
            'report' => "report-entry-form",
            'transactions' => "transactions-entry-form",
        ],
    ],
    'report' => [
        'labels' => [
            'p' => "Principal:",
            'i' => "Interest:",
            'due' => "Due:",
            'paid' => "Paid:",
            'session' => "Session report",
            'credit' => "Credit report",
            'savings' => "Savings report",
            'round' => "Round report",
        ],
    ],
];
