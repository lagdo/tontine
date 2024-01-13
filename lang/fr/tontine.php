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
        'options' => "Options",
        'round' => "Tour",
        'session' => "Séance",
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
        'sessions' => "Séances",
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
        'learning' => [
            'intro' => "Découvrez les fonctionnalités de Siak Tontine dans cette vidéo d'introduction <a href=\":url\" target=\"_blank\">:url</a> (en français).",
        ],
    ],
    'questions' => [
        'delete' => "Supprimer cette association ? Ses members, tours et frais seront également supprimés.",
    ],
    'errors' => [
        'action' => "Action impossible.",
        'editable' => "Cet élément ne peut être modifié ou supprimé.",
        'checks' => [
            'members' => "Vous avez été redirigé vers la page des membres.<br/>" .
                "Vous devez ajouter un ou plusieurs membres avant d'aller plus loin.",
            'sessions' => "Vous avez été redirigé vers la page des séances.<br/>" .
                "Vous devez ajouter une ou plusieurs séances avant d'aller plus loin.",
            'pools' => "Vous avez été redirigé vers la page des tontines.<br/>" .
                "Vous devez ajouter une ou plusieurs tontines avant d'aller plus loin.",
            'opened_sessions' => "Vous avez été redirigé vers la page des séances.<br/>" .
                "Vous devez avoir une ou plusieurs séances ouvertes avant d'aller plus loin.",
        ],
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
            'open' => "Ouvrir ce tour ? Assurez-vous d'avoir correctement rempli son planning.",
            'close' => "Fermer ce tour ?",
            'delete' => "Supprimer ce tour ?",
        ],
        'errors' => [
            'delete' => "Impossible de supprimer ce tour.",
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
            'charges' => "Frais et amendes",
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
    'fund' => [
        'titles' => [
            'funds' => "Fonds d'épargne",
            'add' => "Ajouter un fonds d'épargne",
            'edit' => "Modifier un fonds d'épargne",
        ],
        'labels' => [
            'default' => "&Eacute;pargne",
            'fund' => "Fonds d'épargne",
        ],
        'messages' => [
            'created' => "Le fonds d'épargne a été ajouté.",
            'updated' => "Le fonds d'épargne a été modifié.",
            'deleted' => "Le fonds d'épargne a été supprimé.",
        ],
    ],
    'category' => [
        'titles' => [
            'categories' => "Catégories de décaissement",
            'add' => "Ajouter une catégorie",
            'edit' => "Modifier une catégorie",
        ],
        'types' => [
            'disbursement' => "Décaissement",
        ],
        'messages' => [
            'created' => "La catégorie a été ajoutée.",
            'updated' => "La catégorie a été modifiée.",
            'deleted' => "La catégorie a été supprimée.",
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
            'date_dup' => "Il existe déjà une séance avec la même date.",
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
                'lendable' => "Les montants en caisse peuvent être prêtés",
            ],
            'remit' => [
                'planned' => "Le nombre de remises est défini à l'avance",
                'auction' => "Les remises se font après enchères",
            ],
        ],
        'help' => [
            'intro' => "Vous allez ajouter une nouvelle tontine.<br/>" .
                "Nous allons vous demander de préciser ses caractéristiques.",
            'deposit' => [
                'fixed' => "Cochez la case ci-dessous chaque membre qui souscrit à cette tontine doit verser un montant fixe à chaque séance.",
                'lendable' => "Cochez la case ci-dessous si les montants des cotisations restant en caisse peuvent être prêtés.",
            ],
            'remit' => [
                'planned' => "Cochez la case ci-dessous si le nombre de bénéficiaires à chaque séance est déterminé à l'avance.",
                'auction' => "Cochez la case ci-dessous si le choix des bénéficiaires des cotisations est soumis aux enchères.",
            ],
        ],
        'actions' => [
            'subscriptions' => "Souscriptions",
            'sessions' => "Séances",
        ],
        'questions' => [
            'delete' => "Supprimer cette tontine ?<br/>Il ne faut pas qu'il ait de souscription.",
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
    'pool_round' => [
        'titles' => [
            'sessions' => "Séances de la tontine : :pool",
            'start_session' => "Séance de début : :session",
            'end_session' => "Séance de fin : :session",
        ],
        'labels' => [
            'default' => "(Même que le tour)",
        ],
        'questions' => [
            'delete' => "Supprimer les séances de cette tontine ?",
        ],
        'messages' => [
            'saved' => "Les séances de la tontine ont été enregistrées.",
            'deleted' => "Les séances de la tontine ont été supprimées.",
        ],
        'errors' => [
            'start_session' => "La séance de début est incorrecte.",
            'end_session' => "La séance de fin est incorrecte.",
            'session_dates' => "La séance de début doit préceder la séance de fin.",
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
        'actions' => [
            'show' => "Voir",
            'session' => "Rapport de séance",
            'round' => "Rapport de tour",
            'savings' => "Rapport d'épargne",
        ],
        'titles' => [
            'session' => "Rapport de séance",
            'round' => "Rapport de tour",
            'savings' => "Rapport d'épargne",
            'bills' => [
                'session' => "Montants des frais de la séance",
                'total' => "Montants totaux des frais",
            ],
            'amounts' => [
                'cashed' => "Encaissé",
                'disbursed' => "Décaissé",
            ],
        ],
    ],
    'options' => [
        'titles' => [
            'edit' => "Options de la tontine",
        ],
        'labels' => [
            'default' => 'Défaut',
            'report' => [
                'template' => "Template des rapports",
            ],
        ],
        'messages' => [
            'saved' => "Les options de la tontine ont été enregistrées.",
        ],
    ],
];
