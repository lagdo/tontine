<?php

return [
    'date' => [
        'format' => 'D j M Y',
        'format_medium' => 'j M Y',
        'format_my' => 'F Y',
    ],
    'menus' => [
        'admin' => "Administration",
        'guilds' => "Organisations",
        'tontine' => "Tontine",
        'users' => "Utilisateurs",
        'planning' => "Planning",
        'meeting' => "Réunion",
        'report' => "Rapport",
        'members' => "Membres",
        'finance' => "Finance",
        'accounts' => "Caisses",
        'charges' => "Charges",
        'calendar' => "Calendrier",
        'round' => "Tour",
        'session' => "Séance",
        'sessions' => "Séances",
        'pools' => "Tontines",
        'subscriptions' => "Souscriptions",
        'presences' => "Présences",
        'payments' => "Paiements",
        'profile' => "Profil",
        'logout' => "Se déconnecter",
    ],
    'titles' => [
        'guilds' => "Organisations",
        'tontine' => "Tontine",
        'members' => "Membres",
        'rounds' => "Tours",
        'sessions' => "Séances",
        'session' => "Séance",
        'pools' => "Tontines",
        'add' => "Ajouter une organisation",
        'edit' => "Modifier une organisation",
        'choose' => "Choisir une organisation",
        'type' => "Choisir un type de tontine",
        'presences' => "Présences : :of",
        'select' => [
            'guild' => "(Choisir une organisation)",
            'round' => "(Choisir un tour)",
        ],
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
        'tontine' => "Organisation",
        'round' => "Tour",
        'pool' => "Tontine",
        'types' => [
            'libre' => "Libre",
            'mutual' => "Mutuelle",
            'financial' => "Financière",
        ],
        'fees' => [
            'variable' => "Variable",
            'fixed' => "Fixe",
        ],
        'present' => "Présent",
    ],
    'actions' => [
        'rounds' => "Tours",
        'sessions' => "Séances",
        'open' => "Ouvrir",
        'enter' => "Entrer",
        'select' => "Sélectionner",
        'choose' => "Choisir",
    ],
    'messages' => [
        'bonjour' => "Bonjour, :name",
        'created' => "L'organisation a été ajoutée",
        'updated' => "L'organisation a été modifiée",
        'deleted' => "L'organisation a été supprimée",
        'selected' => "Vous avez sélectionné l'organisation :guild.",
        'back_to_admin' => "Vous êtes de retour sur la section Administration de l'organisation :guild.",
        'screen' => [
            'orientation' => "Si vous utilisez un appareil mobile, nous vous conseillons de le placer en mode paysage, pour un meilleur affichage.",
        ],
        'learning' => [
            'intro' => "Découvrez les fonctionnalités de Siak Tontine dans cette vidéo d'introduction <a class=\"highlight\" href=\":url\" target=\"_blank\">:url</a> (en français).",
        ],
    ],
    'questions' => [
        'delete' => "Supprimer cette organisation ? Ses membres, tours et frais seront également supprimés.",
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
    'invite' => [
        'titles' => [
            'hosts' => "Invitations envoyées",
            'guests' => "Invitations reçues",
            'add' => "Inviter un utilisateur",
            'add_desc' => "L'utilisateur doit déjà avoir créé un compte.",
            'access' => "Accès",
            'organisations' => "Invité :: Organisations",
        ],
        'labels' => [
            'host' => "Hôte",
            'guest' => "Invité",
        ],
        'actions' => [
            'accept' => "Accepter",
            'refuse' => "Refuser",
            'cancel' => "Annuler",
            'access' => "Accès",
        ],
        'active' => [
            'expires' => "Expire le :date",
            'expired' => "A expiré le :date",
            'active' => "Actif depuis le :date",
            'inactive' => "Inactif depuis le :date",
        ],
        'status' => [
            'pending' => "En attente",
            'expired' => "Expirée",
            'accepted' => "Acceptée",
            'refused' => "Refusée",
            'cancelled' => "Annulée",
            'unknown' => "(Inconnu)",
        ],
        'questions' => [
            'accept' => "Accepter cette invitation ?",
            'refuse' => "Refuser cette invitation ?",
            'cancel' => "Annuler cette invitation ?",
            'delete' => "Supprimer cette invitation ?",
        ],
        'messages' => [
            'sent' => "Invitation envoyée. L'utilisateur doit se connecter à son compte pour l'accepter.",
            'accepted' => "Vous avez accepté l'invitation.",
            'refused' => "Vous avez refusé l'invitation.",
            'cancelled' => "Vous avez annulé l'invitation.",
            'deleted' => "Vous avez supprimé l'invitation.",
        ],
        'errors' => [
            'user_not_found' => "L'utilisateur à inviter n'a pas été trouvé. Vérifiez qu'il a déjà créé son compte.",
            'cannot_invite' => "Impossible d'envoyer l'invitation. Peut-être avez-vous déjà invité cet utilisateur ?",
            'not_allowed' => "Cette action n'est pas autorisée.",
            'invite_not_found' => "Impossible de trouver l'invitation.",
            'invite_expired' => "Désolé, cette invitation a expiré.",
            'access_denied' => "En tant qu'invité, vous n'avez pas accès à cette section. Désolé.",
            'no_guild' => "Vous devez avoir ajouté une organisation.",
        ],
    ],
    'round' => [
        'titles' => [
            'add' => "Ajouter un tour",
            'edit' => "Modifier un tour",
            'choose' => "Choisir un tour",
        ],
        'labels' => [
            'savings' => "Ajouter un premier fonds d'épargne",
        ],
        'messages' => [
            'created' => "le tour a été ajouté.",
            'updated' => "le tour a été modifié.",
            'deleted' => "le tour a été supprimé.",
            'selected' => "Vous avez sélectionné l'organisation :guild et le tour :round.",
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
            'savings' => "&Eacute;pargne",
            'funds' => "Fonds d'épargne",
            'add' => "Ajouter un fonds d'épargne",
            'edit' => "Modifier un fonds d'épargne",
        ],
        'labels' => [
            'default' => "&Eacute;pargne",
            'fund' => "Fonds d'épargne",
            'savings' => "&Eacute;pargne",
        ],
        'questions' => [
            'delete' => "Supprimer ce fonds d'épargne ?",
            'disable' => "Supprimer ce fonds d'épargne ?",
        ],
        'messages' => [
            'created' => "Le fonds d'épargne a été ajouté.",
            'updated' => "Le fonds d'épargne a été modifié.",
            'deleted' => "Le fonds d'épargne a été supprimé.",
            'enabled' => "Le fonds d'épargne a été activé.",
            'disabled' => "Le fonds d'épargne a été désactivé.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver le fonds correspondant.",
        ],
    ],
    'account' => [
        'titles' => [
            'outflows' => "Sorties de caisse",
            'add' => "Ajouter une caisse",
            'edit' => "Modifier une caisse",
        ],
        'types' => [
            'outflow' => "Sortie de caisse",
        ],
        'questions' => [
            'delete' => "Supprimer cette caisse ?",
        ],
        'messages' => [
            'created' => "La caisse a été ajoutée.",
            'updated' => "La caisse a été modifiée.",
            'deleted' => "La caisse a été supprimée.",
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
            'start' => "Séance de début",
            'end' => "Séance de fin",
        ],
        'tips' => [
            'add' => "Saisir une séance sur chaque ligne. Séparer le titre et la date avec un point-virgule. La date doit être au format AAAA-MM-JJ",
            'example' => "Par example,<br/>Séance de novembre 2023;2023-11-03",
        ],
        'labels' => [
            'times' => "Horaires",
            'host' => "Hôte",
            'address' => "Adresse",
            'title' => "Séance de :date",
            'start_session' => "Séance de début : :session",
            'end_session' => "Séance de fin : :session",
            'end_interest' => "Fin intérêts: :session",
            'count' => "Nombre de séances : :count",
            'active' => "Active",
            'start' => "Début",
            'end' => "Fin",
            'interest' => "Int.",
        ],
        'actions' => [
            'host' => "Hôte",
            'venue' => "Lieu",
            'resync' => "Resynchroniser",
        ],
        'messages' => [
            'created' => "La séance a été ajoutée.",
            'updated' => "La séance a été modifiée.",
            'deleted' => "La séance a été supprimée.",
            'resynced' => "Les séances ont été resynchronisées.",
            'fund' => [
                'saved' => "Les séances du fonds d'épargne ont été enregistrées.",
            ],
            'pool' => [
                'saved' => "Les séances du fonds de tontine ont été enregistrées.",
            ],
        ],
        'questions' => [
            'open' => "Ouvrir cette séance ?",
            'warning' => "Assurez-vous d'avoir bien entré toutes les informations " .
                "nécessaires sur les souscriptions des membres, dans la section planning.",
            'close' => "Fermer cette séance ?",
            'delete' => "Supprimer cette séance ?",
            'disable' => "Désactiver cette séance ?<br/>Si elle avait déjà été ouverte, ceci pourrait supprimer des données relatives.",
            'resync' => "Resynchroniser les données des séances ?<br/>Vous devez le faire si vous avez changé les membres, les séances, les frais, ou les souscriptions après avoir ouvert une séance.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver la séance.",
            'opened' => "Une séance a déjà été ouverte.",
            'not_opened' => "La séance n'est pas ouverte.",
            'delete' => "Impossible de supprimer cette séance.",
            'date_dup' => "Il existe déjà une séance avec la même date.",
            'sorting' => "L'ordre des séances ne peut pas être modifié.",
            'start' => "La séance de début est incorrecte.",
            'end' => "La séance de fin est incorrecte.",
            'interest' => "La séance de fin des intérêts est incorrecte.",
            'dates' => [
                'end' => "La séance de début doit préceder la séance de fin.",
                'int' => "La séance de fin des intérêts doit être entre les séances de début et de fin.",
            ],
        ],
    ],
    'pool' => [
        'titles' => [
            'pools' => "Fonds de tontine",
            'add' => "Ajouter un fonds de tontine",
            'add_many' => "Ajouter des fonds de tontine",
            'edit' => "Modifier un fonds de tontine",
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
            'intro' => "Vous allez ajouter un nouveau fonds de tontine.<br/>" .
                "Nous allons vous demander de préciser ses caractéristiques.",
            'deposit' => [
                'fixed' => "Cochez la case ci-dessous chaque membre qui souscrit à ce fonds de tontine doit verser un montant fixe à chaque séance.",
                'lendable' => "Cochez la case ci-dessous si les montants des cotisations restant en caisse peuvent être prêtés.",
            ],
            'remit' => [
                'planned' => "Cochez la case ci-dessous si le nombre de bénéficiaires à chaque séance est déterminé à l'avance.",
                'auction' => "Cochez la case ci-dessous si le choix des bénéficiaires des cotisations est soumis aux enchères.",
            ],
        ],
        'questions' => [
            'delete' => "Supprimer ce fonds de tontine ?<br/>Il ne faut pas qu'il ait de souscription.",
            'disable' => "Supprimer ce fonds de tontine ?<br/>Il ne faut pas qu'il ait de souscription.",
        ],
        'messages' => [
            'created' => "Le fonds de tontine a été ajouté.",
            'updated' => "Le fonds de tontine a été modifié.",
            'deleted' => "Le fonds de tontine a été supprimé.",
            'selected' => "Vous affichez les souscriptions du fonds de tontine :pool.",
            'enabled' => "Le fonds de tontine a été activé.",
            'disabled' => "Le fonds de tontine a été désactivé.",
        ],
        'errors' => [
            'not_found' => "Impossible de trouver ce fonds de tontine.",
            'number' => [
                'invalid' => "Vous devez entrer un nombre valide.",
                'max' => "Vous pouvez ajouter au plus :max entrées.",
            ],
            'subscription' => "Ce fonds de tontine a encore des souscriptions.",
            'no_subscription' => "Il y a encore des fonds sans souscription.",
            'payments' => "Tous les versements et remises doivent être supprimés avant de supprimer ou désactiver un fonds de tontine.",
            'not_planned' => "Impossible d'afficher le planning ou les bénéficiaires d'une tontine dont les remises ne sont pas planifiées.",
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
        'actions' => [
            'subscriptions' => "Souscriptions",
            'sessions' => "Séances",
            'planning' => "Planning",
            'beneficiaries' => "Bénéficiaires",
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
            'max-count' => "Le nombre maximum de remises pour cette séance est déjà atteint.",
        ],
    ],
    'report' => [
        'actions' => [
            'show' => "Voir",
            'session' => "Rapport de séance",
            'round' => "Rapport de tour",
            'credit' => "Rapport de crédit",
            'savings' => "Rapport d'épargne",
        ],
        'titles' => [
            'session' => "Rapport de séance",
            'round' => "Rapport de tour",
            'credit' => "Rapport de crédit",
            'savings' => "Rapport d'épargne",
            'fund' => "Fonds",
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
