<?php

return [
    'date' => [
        'format' => 'D j M Y',
    ],
    'menus' => [
        'tontines' => "Associations",
        'tontine' => "Tontine",
        'planning' => "Planning",
        'meeting' => "Réunion",
        'report' => "Rapport",
        'members' => "Membres",
        'charges' => "Frais",
        'round' => "Tour",
        'session' => "Séance",
        'rounds' => "Tours",
        'sessions' => "Séances",
        'pools' => "Fonds",
        'balance' => "Bilan",
        'subscriptions' => "Souscriptions",
        'beneficiaries' => "Bénéficiaires",
        'payments' => "Paiements",
        'profile' => "Profil",
        'logout' => "Se déconnecter",
    ],
    'titles' => [
        'tontines' => "Associations",
        'tontine' => "Tontine",
        'rounds' => "Tours",
        'add' => "Ajouter une association",
        'edit' => "Modifier une association",
        'choose' => "Choisir une association",
        'type' => "Choisir un type de tontine",
    ],
    'descriptions' => [
        'types' => [
            'help' => "Le type de tontine dépend de comment sont définies les cotisations.",
            'libre' => "chaque membre choisit le montant de ses cotisations à chaque séance.",
            'mutual' => "le montant des cotisations est fixe. Les remises sont planifiées à l'avance.",
            'financial' => "le montant des cotisations est fixe. Les remises se font après enchères.",
        ],
    ],
    'labels' => [
        'tontine' => "Association",
        'round' => "Tour",
        'types' => [
            'libre' => "Libre",
            'mutual' => "Mutuelle",
            'financial' => "Financière",
        ],
        'fees' => [
            'variable' => "Variable",
            'fixed' => "Fixe",
        ],
    ],
    'actions' => [
        'rounds' => "Tours",
        'open' => "Ouvrir",
        'enter' => "Entrer",
        'select' => "Sélectionner",
        'choose' => "Choisir",
    ],
    'messages' => [
        'created' => "L'association a été ajoutée",
        'updated' => "L'association a été modifiée",
        'selected' => "Vous avez sélectionné l'association :tontine. Vous devez encore ajouter des tours sur sa tontine.",
        'screen' => [
            'orientation' => "Si vous utilisez un appareil mobile, nous vous conseillons de le placer en mode paysage, pour un meilleur affichage.",
        ],
    ],
    'errors' => [
        'action' => "Action impossible.",
    ],
    'round' => [
        'titles' => [
            'add' => "Ajouter un tour",
            'edit' => "Modifier un tour",
            'choose' => "Choisir un tour",
        ],
        'messages' => [
            'created' => "le tour a été ajouté.",
            'updated' => "le tour a été modifié.",
            'deleted' => "le tour a été supprimé.",
            'selected' => "Vous avez sélectionné l'association :tontine et le tour :round.",
        ],
        'questions' => [
            'open' => "Ouvrir ce tour ? Assurez-vous d'avoir saisi toutes ses données.",
            'close' => "Fermer ce tour ?",
        ],
    ],
    'member' => [
        'actions' => [
            'list' => "Liste",
        ],
        'tips' => [
            'list' => 'Cliquez sur le bouton "Liste" pour saisir une liste des membres dans une zone de texte.',
            'add' => "Saisir un nom sur chaque ligne. S'il y a un numéro de téléphone ou un email, le séparer avec un point-virgule.",
            'example' => 'Par example,<br/>"Jean Amadou"<br/>ou<br/>"Jean Amadou;jean.amadou@gmail.com;237670000000"',
        ],
        'titles' => [
            'add' => "Ajouter des membres",
            'edit' => "Modifier un membre",
        ],
        'messages' => [
            'created' => "Les membres ont été ajoutés.",
            'updated' => "Le membre a été modifié.",
            'deleted' => "Le membre a été supprimé.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le membre correspondant.",
        ],
    ],
    'charge' => [
        'titles' => [
            'add' => "Ajouter des frais",
            'edit' => "Modifier un frais",
        ],
        'labels' => [
            'lend' => "&Agrave; prêter",
            'lendable' => "Disponible pour prêt",
        ],
        'messages' => [
            'created' => "Le frais a été ajouté.",
            'updated' => "Le frais a été modifié.",
            'deleted' => "Le frais a été supprimé.",
        ],
        'questions' => [
            'delete' => "Supprimer ce frais ?",
        ],
        'errors' => [
            'cannot_delete' => "Impossible de supprimer ce frais.",
        ],
        'types' => [
            'fee' => "Frais",
            'fine' => "Amende",
        ],
        'periods' => [
            'none' => "Aucune",
            'unique' => "Unique",
            'round' => "Tour",
            'session' => "Séance",
        ],
        'groups' => [
            'fixed' => "Fixe",
            'variable' => "Variable",
        ],
    ],
    'bill' => [
        'errors' => [
            'not_found' => "Impossible de trouver la facture correspondante.",
        ],
    ],
    'session' => [
        'status' => [
            'pending' => "En attente",
            'opened' => "Ouverte",
            'closed' => "Fermée",
        ],
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
            'warning' => "Assurez-vous d'avoir bien entré toutes les informations " .
                "nécessaires sur les souscriptions des membres, dans la section planning.",
            'close' => "Fermer cette séance ?",
            'delete' => "Supprimer cette séance ?",
        ],
        'errors' => [
            'opened' => "Une séance a déjà été ouverte.",
        ],
    ],
    'pool' => [
        'titles' => [
            'add' => "Ajouter des fonds",
            'edit' => "Modifier un fonds",
            'deposits' => "Bilan des dépôts",
            'remitments' => "Bilan des remises",
            'subscriptions' => "Souscriptions",
            'free' => "Fonds libre",
        ],
        'actions' => [
            'subscriptions' => "Souscriptions",
        ],
        'questions' => [
            'delete' => "Supprimer ce fond?<br/>Il ne faut pas qu'il ait de souscription.",
        ],
        'messages' => [
            'created' => "Le fonds a été ajouté.",
            'updated' => "Le fonds a été modifié.",
            'deleted' => "Le fonds a été supprimé.",
        ],
        'errors' => [
            'number' => [
                'invalid' => "Vous devez entrer un nombre valide.",
                'max' => "Vous pouvez ajouter au plus :max entrées.",
            ],
            'subscription' => "Ce fonds a encore des souscriptions.",
            'no_subscription' => "Il y a encore des fonds sans souscription.",
        ],
    ],
    'subscription' => [
        'messages' => [
            'created' => "La souscription du membre a été enregistrée.",
            'deleted' => "La souscription du membre a été supprimée.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver la souscription correspondante.",
        ],
    ],
    'beneficiary' => [
        'errors' => [
            'cant_change' => "Impossible de modifier le bénéficiaire.",
        ],
    ],
    'remitment' => [
        'labels' => [
            'not-assigned' => "** Pas attribué **",
        ],
    ],
];
