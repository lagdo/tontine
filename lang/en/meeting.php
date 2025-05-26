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
        'loan' => "Loan",
        'loans' => "Loans",
        'amounts' => "Amounts",
        'settlements' => "Settlements",
        'fine' => "Fine",
        'amount_to_lend' => "Amount to lend",
        'benefit' => "Benefit",
        'refund' => "Refund",
        'refunds' => "Refunds",
        'outflows' => "Outflows",
        'profits' => "Profits distribution",
        'summary' => "Summary",
    ],
    'actions' => [
        'deposits' => "Deposits",
        'remitments' => "Remitments",
        'settlements' => "Settlements",
        'savings' => "Savings & Loans",
        'loans' => "Loans",
        'refunds' => "Refunds",
        'amounts' => "Amounts",
        'reports' => "Reports",
        'report' => "Report",
        'summary' => "Summary",
        'fine' => "Fine",
        'pools' => "Tontines",
        'charges' => "Fees",
        'cash' => "Cash",
        'credits' => "Credits",
        'profits' => "Profits",
        'outflows' => "Outflows",
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
            'options' => "Edit the fund options",
            'start_amount' => "Edit the start amount",
            'end_amount' => "Edit the end amount",
        ],
        'actions' => [
            'close' => "Close",
            'saving' => "Saving",
            'interest' => "Interest",
            'deposits' => "Deposits",
            'start_amount' => "Start amount",
            'end_amount' => "End amount",
        ],
        'labels' => [
            'start_amount' => "Start amount: :amount",
            'end_amount' => "End amount: :amount",
        ],
        'questions' => [
            'delete' => "Delete this saving?",
        ],
        'errors' => [
            'not_found' => "Cannot find the corresponding saving.",
        ],
        'messages' => [
            'select_fund' => "Please select a fund from the list.",
            'amount_saved' => "The amount has been saved.",
        ],
    ],
    'profit' => [
        'distribution' => [
            'total' => "Saving: :saving. Interests: :refund.",
            'amount' => "Distribution: :amount.",
            'parts' => ":parts parts.",
            'basis' => "For each session, one part = :unit.",
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
    'outflow' => [
        'titles' => [
            'add' => "Add a cash outflow",
            'edit' => "Edit a cash outflow",
        ],
        'labels' => [
            'amount_available' => "Available: :amount",
        ],
        'questions' => [
            'delete' => "Delete this cash outflow?",
        ],
        'messages' => [
            'created' => "The cash outflow has been created.",
            'updated' => "The cash outflow has been updated.",
            'deleted' => "The cash outflow has been deleted.",
        ],
        'errors' => [
            'not_found' => "Cannot find the cash outflow.",
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
