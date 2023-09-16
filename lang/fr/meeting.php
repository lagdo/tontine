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
        'fundings' => "Dépôts",
        'loans' => "Emprunts",
        'amounts' => "Montants",
        'settlements' => "Règlements",
        'fine' => "Appliquer",
        'amount_to_lend' => "Montant à prêter",
        'benefit' => "Bénéfice",
        'refunds' => "Remboursements",
        'disbursements' => "Décaissements",
    ],
    'actions' => [
        'deposits' => "Versements",
        'remitments' => "Remises",
        'settlements' => "Règlements",
        'loans' => "Emprunts",
        'amounts' => "Montants",
        'reports' => "Rapports",
        'report' => "Rapport",
        'summary' => "Résumé",
        'fine' => "Appliquer",
        'pools' => "Cotisations",
        'charges' => "Frais",
        'credits' => "Crédits",
        'cash' => "Caisse",
    ],
    'labels' => [
        'member' => "Membre",
        'members' => "Membres",
        'charge' => "Frais correspondant",
        'category' => "Catégorie",
        'session' => "Séance",
        'payments' => "Paiements",
    ],
    'messages' => [
        'agenda' => [
            'updated' => "Enregistré !",
        ],
        'report' => [
            'updated' => "Enregistré !",
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
            'fixed' => "Frais fixes",
            'variable' => "Frais variables",
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
            'auction' => "Enchère",
        ],
    ],
    'funding' => [
        'titles' => [
            'add' => "Ajouter un dépôt",
            'edit' => "Modifier un dépôt",
        ],
        'questions' => [
            'delete' => "Supprimer ce dépôt ?",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le dépôt correspondant.",
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
            'amount_available' => "Montant disponible : :amount",
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
            'not_found' => "Impossible de trouver l'emprunt correspondant.",
            'update' => "Cet emprunt ne peut pas être modifié.",
        ],
    ],
    'disbursement' => [
        'titles' => [
            'add' => "Ajouter un décaissement",
            'edit' => "Modifier un décaissement",
        ],
        'labels' => [
            'amount_available' => "Montant disponible : :amount",
        ],
        'questions' => [
            'delete' => "Supprimer ce décaissement ?",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le décaissement correspondant.",
        ],
    ],
];
