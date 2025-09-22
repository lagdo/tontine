<?php

return [
    'titles' => [
        'session' => "Séance",
        'agenda' => "Ordre du jour",
        'report' => "Rapport de séance",
        'credit' => "Rapport de crédit",
        'pools' => "Fonds",
        'charges' => "Frais",
        'fees' => "Frais",
        'fines' => "Amendes",
        'deposits' => "Versements",
        'remitments' => "Remises",
        'auctions' => "Enchères",
        'savings' => "&Eacute;pargnes",
        'loan' => "Emprunt",
        'loans' => "Emprunts",
        'amounts' => "Montants",
        'settlements' => "Règlements",
        'fine' => "Appliquer",
        'amount_to_lend' => "Montant à prêter",
        'benefit' => "Bénéfice",
        'refund' => "Remboursement",
        'refunds' => "Remboursements",
        'outflows' => "Sorties",
        'profits' => "Répartition des gains",
        'summary' => "Résumé",
    ],
    'actions' => [
        'deposits' => "Versements",
        'remitments' => "Remises",
        'settlements' => "Règlements",
        'savings' => "&Eacute;pargne & Crédits",
        'loans' => "Emprunts",
        'refunds' => "Remboursements",
        'amounts' => "Montants",
        'reports' => "Rapports",
        'report' => "Rapport",
        'summary' => "Résumé",
        'fine' => "Appliquer",
        'pools' => "Tontines",
        'charges' => "Frais",
        'cash' => "Caisse",
        'credits' => "Crédits",
        'profits' => "Gains",
        'outflows' => "Sorties",
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
        'item' => "Item",
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
        'fund' => [
            'none' => "Aucun fonds d'épargne n'a été ajouté à ce tour.",
        ],
        'session' => [
            'closed' => "Cette opération n'est pas permise parce que la séance est fermée.",
        ],
        'charge' => [
            'disabled' => "Cette opération n'est pas permise parce que le frais est désactivé.",
        ],
    ],
    'errors' => [
        'amount' => [
            'invalid' => ":amount n'est pas un montant valide.",
        ],
        'session' => [
            'not_found' => "Impossible de trouver la séance.",
            'not_opened' => "Cette opération n'est pas permise parce que la séance n'est pas ouverte.",
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
            'set' => "Définir un objectif",
            'edit' => "Changer l'objectif",
        ],
        'labels' => [
            'global' => "Cocher si le montant ci-dessus est un total pour tous les membres.",
            'deadline' => "Dernier délai",
            'target' => "Objectif : :amount",
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
    'session' => [
        'actions' => [
            'prev' => "Séance préc.",
            'next' => "Séance suiv.",
        ],
    ],
    'deposit' => [
        'titles' => [
            'late-deposits' => "Versements en retard",
            'early-deposits' => "Versements en avance",
        ],
        'labels' => [
            'late' => "Retard",
            'early' => "Avance",
        ],
        'counts' => [
            'ontime' => "A temps : :count",
            'late' => "Retard : :count",
            'early' => "Avance : :count",
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
        'questions' => [
            'delete' => "Supprimer cette remise ?",
        ],
    ],
    'saving' => [
        'titles' => [
            'add' => "Ajouter une épargne",
            'edit' => "Modifier une épargne",
            'options' => "Modifier les options du fonds",
            'start_amount' => "Modifier le montant de départ",
            'end_amount' => "Modifier le montant à la fin",
        ],
        'actions' => [
            'close' => "Clotûrer",
            'saving' => "&Eacute;pargne",
            'interest' => "Intérêts",
            'deposits' => "Versements",
            'start_amount' => "Montant de départ",
            'end_amount' => "Montant à la fin",
        ],
        'labels' => [
            'start_amount' => "Montant de départ : :amount",
            'end_amount' => "Montant à la fin : :amount",
        ],
        'questions' => [
            'delete' => "Supprimer cette épargne ?",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver l'épargne correspondante.",
        ],
        'messages' => [
            'select_fund' => "Veuillez sélectionner un fond dans la liste.",
            'amount_saved' => "Le montant a été enregistré.",
        ],
    ],
    'profit' => [
        'distribution' => [
            'total' => "&Eacute;pargne: :saving. Intérêts: :refund.",
            'amount' => "Distribution: :amount.",
            'parts' => ":parts parts.",
            'basis' => "Pour chaque séance, une part = :unit.",
        ],
    ],
    'loan' => [
        'titles' => [
            'add' => "Ajouter un emprunt",
            'edit' => "Modifier un emprunt",
            'deadline' => "Modifier une date limite",
        ],
        'actions' => [
            'deadline' => "Date limite",
        ],
        'labels' => [
            'p' => "Principal",
            'i' => "Intérêt",
            'principal' => "Principal",
            'interest' => "Intérêt",
            'percentage' => "Pourcentage",
            'amount_available' => "Disponible : :amount",
            'fund' => "Origine",
            'deadline' => "Date limite : :deadline",
            'passed' => "passée",
        ],
        'interest' => [
            'f' => "Fixe",
            'u' => "Unique",
            's' => "Simple",
            'c' => "Composé",
            'if' => "Montant fixe",
            'iu' => "Intérêt unique",
            'is' => "Intérêt simple",
            'ic' => "Intérêt composé",
        ],
        'deadline' => [
            'session' => "Séance : :date",
            'date' => "Date : :date",
        ],
        'questions' => [
            'delete' => "Supprimer cet emprunt ?",
        ],
        'messages' => [
            'created' => "L'emprunt a été créé.",
            'updated' => "L'emprunt a été modifié.",
            'deleted' => "L'emprunt a été supprimé.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver l'emprunt.",
            'update' => "Cet emprunt ne peut pas être modifié.",
            'delete' => "Cet emprunt ne peut pas être supprimé.",
            'deadline' => "La date limite doit être après la séance.",
        ],
    ],
    'refund' => [
        'titles' => [
            'loan' => "Emprunt",
            'paid' => "Payé",
            'debt' => "Dette",
            'partial' => "Partiel",
            'final' => "Final",
        ],
        'labels' => [
            'loan' => ":member : :amount",
            'debt' => ":session : :type",
            'total' => "Total : :amount",
            'before' => "Av. séance : :amount",
            'after' => "Ap. séance : :amount",
        ],
        'questions' => [
            'delete' => "Supprimer ce remboursement ?",
        ],
        'messages' => [
            'created' => "Le remboursement a été créé.",
            'updated' => "Le remboursement a été modifié.",
            'deleted' => "Le remboursement a été supprimé.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le remboursement partiel.",
            'pr_amount' => "Le montant du remboursement partiel doit être inférieur au montant dû.",
            'nul_amount' => "Le montant du remboursement partiel doit être supérieur à 0.",
            'cannot_create' => "Impossible de créer un remboursement partiel.",
            'cannot_update' => "Impossible de modifier ce remboursement partiel.",
            'cannot_delete' => "Impossible de supprimer ce remboursement partiel.",
        ],
    ],
    'outflow' => [
        'titles' => [
            'add' => "Ajouter un décaissement",
            'edit' => "Modifier un décaissement",
        ],
        'labels' => [
            'amount_available' => "Disponible : :amount",
        ],
        'questions' => [
            'delete' => "Supprimer ce décaissement ?",
        ],
        'messages' => [
            'created' => "Le décaissement a été créé.",
            'updated' => "Le décaissement a été modifié.",
            'deleted' => "Le décaissement a été supprimé.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le décaissement.",
        ],
    ],
    'entry' => [
        'titles' => [
            'session' => "Fiche de saisie de séance",
            'report' => "Fiche de saisie de rapport",
            'transactions' => "Fiche de saisie d'opérations",
        ],
        'actions' => [
            'session' => "Saisie de séance",
            'report' => "Saisie de rapport",
            'transactions' => "Saisie d'opérations",
        ],
        'files' => [
            'session' => "fiche-saisie-seance",
            'report' => "fiche-saisie-rapport",
            'transactions' => "fiche-saisie-operations",
        ],
    ],
    'report' => [
        'labels' => [
            'p' => "Principal :",
            'i' => "Intérêt :",
            'due' => "Dû :",
            'paid' => "Payé :",
            'session' => "Rapport de séance",
            'credit' => "Rapport de crédit",
            'savings' => "Rapport d'épargne",
            'round' => "Rapport de tour",
        ],
    ],
];
