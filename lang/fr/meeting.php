<?php

return [
    'titles' => [
        'session' => "Séance",
        'agenda' => "Ordre du jour",
        'report' => "Rapport",
        'pools' => "Fonds",
        'charges' => "Frais",
        'fees' => "Frais",
        'fines' => "Amendes",
        'deposits' => "Versements",
        'remitments' => "Remises",
        'auctions' => "Enchères",
        'savings' => "&Eacute;pargnes",
        'closings' => "Clotûres",
        'loans' => "Emprunts",
        'amounts' => "Montants",
        'settlements' => "Règlements",
        'fine' => "Appliquer",
        'amount_to_lend' => "Montant à prêter",
        'benefit' => "Bénéfice",
        'refunds' => "Remboursements",
        'partial-refunds' => "Remboursements partiels",
        'disbursements' => "Décaissements",
        'profits' => "Répartition des gains",
        'summary' => "Résumé",
    ],
    'actions' => [
        'deposits' => "Versements",
        'remitments' => "Remises",
        'settlements' => "Règlements",
        'savings' => "&Eacute;pargne",
        'loans' => "Emprunts",
        'amounts' => "Montants",
        'reports' => "Rapports",
        'report' => "Rapport",
        'summary' => "Résumé",
        'fine' => "Appliquer",
        'pools' => "Cotisations",
        'charges' => "Frais",
        'cash' => "Caisse",
        'credits' => "Crédit",
        'profits' => "Gains",
    ],
    'labels' => [
        'member' => "Membre",
        'members' => "Membres",
        'charge' => "Frais correspondant",
        'category' => "Catégorie",
        'session' => "Séance",
        'payments' => "Paiements",
        'debt' => "Dette",
        'saving' => "&Eacute;pargne",
        'profit' => "Gain",
        'duration' => "Durée",
        'distribution' => "Distribution",
    ],
    'messages' => [
        'saved' => "Enregistré !",
        'deleted' => "Supprimé !",
        'agenda' => [
            'updated' => "Enregistré !",
        ],
        'report' => [
            'updated' => "Enregistré !",
        ],
        'profit' => [
            'saved' => "Enregistré !",
            'deleted' => "Supprimé !",
        ],
    ],
    'warnings' => [
        'session' => [
            'closed' => "Cette opération n'est pas permise lorsque la session est fermée.",
        ],
    ],
    'errors' => [
        'amount' => [
            'invalid' => ":amount n'est pas un montant valide.",
        ],
    ],
    'charge' => [
        'titles' => [
            'fees' => "Frais",
            'fixed' => "Frais fixes",
            'variable' => "Frais variables",
        ],
    ],
    'target' => [
        'actions' => [
            'deadline' => "Date limite",
        ],
        'titles' => [
            'set' => "Définir un délai",
            'edit' => "Changer le délai",
            'summary' => "Montant: :amount. Date limite: :deadline",
        ],
        'labels' => [
            'global' => "Cocher si le montant ci-dessus est un total pour tous les membres.",
            'deadline' => "Dernier délai",
            'remaining' => "Montant restant : :amount",
        ],
        'questions' => [
            'remove' => "Supprimer cette date limite ?",
        ],
        'messages' => [
            'removed' => "La date limite a été supprimée.",
        ],
    ],
    'category' => [
        'types' => [
            'expense' => "Dépense",
            'support' => "Aide",
            'reception' => "Réception",
            'other' => "Autre",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver la catégorie correspondante.",
        ],
    ],
    'remitment' => [
        'titles' => [
            'add' => "Ajouter une remise",
            'auctions' => "Enchères",
        ],
        'labels' => [
            'amount' => "Montant remis",
            'auction' => "Enchère",
            'beneficiary' => "Bénéficiaire",
        ],
    ],
    'saving' => [
        'titles' => [
            'add' => "Ajouter une épargne",
            'edit' => "Modifier une épargne",
            'closing' => "Clotûre: :fund",
        ],
        'labels' => [
            'closing' => "Clotûre",
        ],
        'questions' => [
            'delete' => "Supprimer cette épargne ?",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver l'épargne correspondante.",
        ],
    ],
    'loan' => [
        'titles' => [
            'add' => "Ajouter un emprunt",
            'edit' => "Modifier un emprunt",
        ],
        'labels' => [
            'p' => "Principal",
            'i' => "Intérêt",
            'principal' => "Principal",
            'interest' => "Intérêt",
            'percentage' => "Pourcentage",
            'amount_available' => "disponible : :amount",
        ],
        'interest' => [
            'f' => "Fixe",
            's' => "Simple",
            'c' => "Composé",
            'if' => "Intérêt fixe",
            'is' => "Intérêt simple",
            'ic' => "Intérêt composé",
        ],
        'questions' => [
            'delete' => "Supprimer cet emprunt ?",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver l'emprunt.",
            'update' => "Cet emprunt ne peut pas être modifié.",
        ],
    ],
    'refund' => [
        'titles' => [
            'add' => "Ajouter un remboursement partiel",
        ],
        'labels' => [
            'partial' => "Partiel",
        ],
        'questions' => [
            'delete' => "Supprimer ce remboursement partiel ?",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le remboursement partiel.",
            'cannot_delete' => "Impossible de supprimer ce remboursement partiel.",
            'pr_amount' => "Le montant du remboursement partiel doit être inférieur au montant dû.",
        ],
    ],
    'disbursement' => [
        'titles' => [
            'add' => "Ajouter un décaissement",
            'edit' => "Modifier un décaissement",
        ],
        'labels' => [
            'amount_available' => "disponible : :amount",
        ],
        'questions' => [
            'delete' => "Supprimer ce décaissement ?",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le décaissement.",
        ],
    ],
    'profit' => [
        'labels' => [
            'amount' => "Montant à partager",
        ],
        'distribution' => [
            'total' => "&Eacute;pargne: :saving. Intérêts: :refund.",
            'amount' => "Distribution: :amount.",
            'parts' => ":parts parts.",
            'basis' => "Pour chaque séance, :unit = une part.",
        ],
    ],
];
