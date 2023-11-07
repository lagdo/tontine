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
        'pools' => "Tontines",
        'subscriptions' => "Souscriptions",
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
        'deleted' => "L'association a été supprimée",
        'selected' => "Vous avez sélectionné l'association :tontine. Vous devez encore ajouter des tours sur sa tontine.",
        'screen' => [
            'orientation' => "Si vous utilisez un appareil mobile, nous vous conseillons de le placer en mode paysage, pour un meilleur affichage.",
        ],
    ],
    'questions' => [
        'delete' => "Supprimer cette association ? Ses members, tours et frais seront également supprimés.",
    ],
    'errors' => [
        'action' => "Action impossible.",
        'editable' => "Cet élément ne peut être modifié ou supprimé.",
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
            'example' => "Par example,<br/>Jean Amadou<br/>ou<br/>Jean Amadou;jean.amadou@gmail.com;237670000000",
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
        'questions' => [
            'delete' => "Supprimer ce membre ?",
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
            'add' => "Ajouter une séance",
            'add-list' => "Ajouter des séances",
            'edit' => "Modifier une séance",
            'title' => "Séance de :month :year",
            'host' => "Choisir l'hôte",
            'venue' => "Lieu",
        ],
        'tips' => [
            'add' => "Saisir une séance sur chaque ligne. Séparer le titre et la date avec un point-virgule. La date doit être au format AAAA-MM-JJ",
            'example' => "Par example,<br/>Séance de novembre 2023;2023-11-03",
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
            'disable' => "Désactiver cette séance?<br/>Si elle avait déjà été ouverte, ceci pourrait supprimer des données relatives.",
        ],
        'errors' => [
            'opened' => "Une séance a déjà été ouverte.",
            'delete' => "Impossible de supprimer cette séance.",
        ],
    ],
    'pool' => [
        'titles' => [
            'add' => "Ajouter une tontine",
            'add_many' => "Ajouter des tontines",
            'edit' => "Modifier une tontine",
            'deposits' => "Dépôts",
            'remitments' => "Remises",
            'subscriptions' => "Souscriptions",
            'members' => "Membres",
            'sessions' => "Séances",
        ],
        'labels' => [
            'fixed' => "Montant fixe",
            'planned' => "Planifiée",
            'auction' => "Aux enchères",
            'lendable' => "Disponible pour prêt",
            'deposit' => [
                'fixed' => "Le montant des dépôts est fixe",
            ],
            'remit' => [
                'fixed' => "Le montant des remises est fixe",
                'planned' => "Le nombre de remises est défini à l'avance",
                'auction' => "Les remises se font après enchères",
                'lendable' => "Les montants en caisse peuvent être prêtés",
            ],
        ],
        'help' => [
            'intro' => "Vous allez ajouter une nouvelle tontine.<br/>" .
                "Nous allons vous demander de préciser ses caractéristiques.",
            'deposit' => [
                'fixed' => "Cochez la case ci-dessous chaque membre qui souscrit à cette tontine doit verser un montant fixe à chaque séance.",
            ],
            'remit' => [
                'fixed' => "Cochez la case ci-dessous si chaque bénéficiaire recevra la totalité du montant correspondant à ses dépôts.",
                'planned' => "Cochez la case ci-dessous si le nombre de bénéficiaires à chaque séance est déterminé à l'avance.",
                'auction' => "Cochez la case ci-dessous si le choix des bénéficiaires des cotisations est soumis aux enchères.",
                'lendable' => "Cochez la case ci-dessous si les montants des cotisations restant en caisse peuvent être prêtés.",
            ],
        ],
        'actions' => [
            'subscriptions' => "Souscriptions",
        ],
        'questions' => [
            'delete' => "Supprimer cette tontine?<br/>Il ne faut pas qu'il ait de souscription.",
        ],
        'messages' => [
            'created' => "La tontine a été ajoutée.",
            'updated' => "La tontine a été modifiée.",
            'deleted' => "La tontine a été supprimée.",
        ],
        'errors' => [
            'number' => [
                'invalid' => "Vous devez entrer un nombre valide.",
                'max' => "Vous pouvez ajouter au plus :max entrées.",
            ],
            'subscription' => "Cette tontines a encore des souscriptions.",
            'no_subscription' => "Il y a encore des fonds sans souscription.",
        ],
    ],
    'subscription' => [
        'titles' => [
            'beneficiaries' => "Bénéficiaires",
            'planning' => "Planning",
            'deposits' => "Bilan des dépôts",
            'remitments' => "Bilan des remises",
        ],
        'messages' => [
            'created' => "La souscription du membre a été enregistrée.",
            'deleted' => "La souscription du membre a été supprimée.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver la souscription correspondante.",
            'create' => "Impossible de créer une nouvelle souscription.",
            'delete' => "Impossible de supprimer la souscription.",
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
        'errors' => [
            'planning' => "Le bénéficiaire choisi est déjà planifié sur une autre séance.",
        ],
    ],
    'report' => [
        'titles' => [
            'bills' => [
                'session' => "Montants des frais de la session",
                'total' => "Montants totaux des frais",
            ],
            'amounts' => [
                'cashed' => "Encaissé",
                'disbursed' => "Décaissé",
            ],
        ],
    ],
];
