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
        'refunds' => "Refunds",
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
        'session' => [
            'not_found' => "Unable to find the session.",
            'not_opened' => "This operation is not allowed because the session is not opened.",
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
            'set' => "Set a target",
            'edit' => "Edit the target",
        ],
        'labels' => [
            'global' => "Check if the amount above is a total for all members.",
            'deadline' => "Deadline",
            'target' => "Target: :amount",
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
    'session' => [
        'actions' => [
            'prev' => "Prev. session",
            'next' => "Next session",
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
        'questions' => [
            'delete' => "Delete this remitment?",
        ],
    ],
    'saving' => [
        'titles' => [
            'add' => "Add a saving",
            'edit' => "Edit a saving",
        ],
        'actions' => [
            'close' => "Close",
            'saving' => "Saving",
            'interest' => "Interest",
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
        'messages' => [
            'select_fund' => "Please select a fund from the list.",
        ],
    ],
    'closing' => [
        'titles' => [
            'round' => "Round closing",
            'interest' => "Interest closing",
            'r' => "Round",
            'i' => "Interest",
        ],
        'labels' => [
            'fund' => "Fund",
            'amount' => "Amount to share",
            'interest' => "The calculation of interest stops at this session.",
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
            'amount_available' => "Available: :amount",
            'fund' => "Fund",
        ],
        'interest' => [
            'f' => "Fixed",
            'u' => "Unique",
            's' => "Simple",
            'c' => "Compound",
            'if' => "Fixed amount",
            'iu' => "Unique interest",
            'is' => "Simple interest",
            'ic' => "Compound interest",
        ],
        'questions' => [
            'delete' => "Delete this loan?",
        ],
        'messages' => [
            'created' => "The loan has been created.",
            'updated' => "The loan has been updated.",
            'deleted' => "The loan has been deleted.",
        ],
        'errors' => [
            'not_found' => "Cannot find the loan.",
            'update' => "This loan cannot be updated.",
            'delete' => "This loan cannot be deleted.",
        ],
    ],
    'refund' => [
        'titles' => [
            'loan' => "Loan",
            'paid' => "Paid",
            'debt' => "Debt",
            'partial' => "Partial",
            'final' => "Final",
        ],
        'labels' => [
            'loan' => ":member: :amount",
            'debt' => ":session: :type",
            'total' => "Total: :amount",
            'before' => "Be. session: :amount",
            'after' => "Af. session: :amount",
        ],
        'questions' => [
            'delete' => "Delete this refund?",
        ],
        'messages' => [
            'created' => "The refund has been created.",
            'updated' => "The refund has been updated.",
            'deleted' => "The refund has been deleted.",
        ],
        'errors' => [
            'not_found' => "Cannot find the partial refund.",
            'pr_amount' => "The partial refund amount must be lower than the amount due.",
            'nul_amount' => "The partial refund amount must be greater than 0.",
            'cannot_create' => "Cannot create a partial refund.",
            'cannot_update' => "Cannot update this partial refund.",
            'cannot_delete' => "Cannot delete this partial refund.",
        ],
    ],
    'disbursement' => [
        'titles' => [
            'add' => "Add a disbursement",
            'edit' => "Edit a disbursement",
        ],
        'labels' => [
            'amount_available' => "Available: :amount",
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
