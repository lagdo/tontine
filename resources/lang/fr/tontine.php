<?php

return [
    'menus' => [
        'tontine' => "Tontine",
        'planning' => "Planning",
        'meeting' => "Réunion",
        'members' => "Membres",
        'charges' => "Frais et amendes",
        'sessions' => "Séances",
        'pools' => "Fonds",
        'tables' => "Tableaux",
    ],
    'titles' => [
        'tontines' => "Tontines",
        'rounds' => "Tours",
        'add' => "Ajouter une tontine",
        'edit' => "Modifier une tontine",
    ],
    'labels' => [
        'types' => [
            'mutual' => "Mutuelle",
            'financial' => "Financière",
        ],
    ],
    'actions' => [
        'rounds' => "Tours",
        'select' => "Sélectionner",
    ],
    'messages' => [
        'created' => "La tontine a été ajoutée",
        'updated' => "La tontine a été modifiée",
    ],
    'round' => [
        'titles' => [
            'add' => "Ajouter un tour",
            'edit' => "Modifier un tour",
        ],
        'messages' => [
            'created' => "le tour a été ajouté.",
            'updated' => "le tour a été modifié.",
            'deleted' => "le tour a été supprimé.",
        ],
    ],
    'member' => [
        'titles' => [
            'add' => "Ajouter des membres",
            'edit' => "Modifier un membre",
        ],
        'messages' => [
            'created' => "Le membre a été ajouté.",
            'updated' => "Le membre a été modifié.",
            'deleted' => "Le membre a été supprimé.",
        ],
    ],
    'charge' => [
        'titles' => [
            'add' => "Ajouter des frais et amendes",
            'edit' => "Modifier un frais ou une amende",
        ],
        'messages' => [
            'created' => "La charge a été ajoutée.",
            'updated' => "La charge a été modifiée.",
            'deleted' => "La charge a été supprimée.",
        ],
    ],
    'session' => [
        'titles' => [
            'add' => "Ajouter des séances",
            'edit' => "Modifier une séance",
            'title' => "Séance de :month :year",
            'host' => "Choisir l'hôte",
            'venue' => "Lieu",
        ],
        'labels' => [
            'times' => "Horaires",
            'host' => "Hôte",
            'address' => "Adresse",
        ],
        'actions' => [
            'host' => "Hôte",
            'venue' => "Lieu",
        ],
        'messages' => [
            'created' => "La séance a été ajoutée.",
            'updated' => "La séance a été modifiée.",
            'deleted' => "La séance a été supprimée.",
        ],
        'questions' => [
            'open' => "Ouvrir cette séance ?",
            'close' => "Fermer cette séance?",
        ],
    ],
    'pool' => [
        'titles' => [
            'add' => "Ajouter des fonds",
            'edit' => "Modifier un fond",
            'deposits' => "Tableau des cotisations",
            'remittances' => "Tableau des remises",
            'subscriptions' => "Souscriptions",
        ],
        'actions' => [
            'subscriptions' => "Souscriptions",
        ],
        'messages' => [
            'created' => "Le fond a été ajouté.",
            'updated' => "Le fond a été modifié.",
            'deleted' => "Le fond a été supprimé.",
        ],
        'errors' => [
            'number' => [
                'invalid' => "Vous devez entrer un nombre valide.",
                'max' => "Vous pouvez ajouter au plus :max entrées.",
            ],
        ],
    ],
    'subscription' => [
        'messages' => [
            'created' => "La souscription du membre a été enregistrée.",
            'deleted' => "La souscription du membre a été supprimée.",
        ],
    ],
    'loan' => [
        'titles' => [
            'add' => "Ajouter une enchère",
        ],
        'labels' => [
            'interest' => "Intérêt",
            'amount_to_lend' => "Montant à prêter",
        ],
    ],
];
