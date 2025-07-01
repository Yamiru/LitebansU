<?php

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Interface publique pour voir les sanctions et les bannissements du serveur'
    ],
    
    'nav' => [
        'home' => 'Accueil',
        'bans' => 'Bannissements',
        'mutes' => 'Mutes',
        'warnings' => 'Avertissements',
        'kicks' => 'Expulsions',
        'statistics' => 'Statistiques',
        'language' => 'Langue',
        'theme' => 'Thème',
        'admin' => 'Admin',
        'protest' => 'Contester un ban',
    ],
    
    'home' => [
        'welcome' => 'Sanctions du serveur',
        'description' => 'Recherchez les sanctions des joueurs et consultez l\'activité récente',
        'recent_activity' => 'Activité récente',
        'recent_bans' => 'Bannissements récents',
        'recent_mutes' => 'Mutes récents',
        'no_recent_bans' => 'Aucun bannissement récent trouvé',
        'no_recent_mutes' => 'Aucun mute récent trouvé',
        'view_all_bans' => 'Voir tous les bannissements',
        'view_all_mutes' => 'Voir tous les mutes'
    ],
    
    'search' => [
        'title' => 'Recherche de joueur',
        'placeholder' => 'Entrez le pseudo du joueur ou son UUID...',
        'help' => 'Vous pouvez rechercher par pseudo ou par UUID complet',
        'button' => 'Rechercher',
        'no_results' => 'Aucune sanction trouvée pour ce joueur',
        'error' => 'Une erreur de recherche est survenue',
        'network_error' => 'Une erreur réseau est survenue. Veuillez réessayer.'
    ],
    
    'stats' => [
        'title' => 'Statistiques du serveur',
        'active_bans' => 'Bannissements actifs',
        'active_mutes' => 'Mutes actifs',
        'total_warnings' => 'Total des avertissements',
        'total_kicks' => 'Total des expulsions',
        'total_of' => 'sur',
        'all_time' => 'depuis toujours',
        'most_banned_players' => 'Joueurs les plus bannis',
        'most_active_staff' => 'Staff le plus actif',
        'top_ban_reasons' => 'Principales raisons de bannissement',
        'recent_activity_overview' => 'Aperçu de l\'activité récente',
        'activity_by_day' => 'Activité par jour',
        'cache_cleared' => 'Le cache des statistiques a été vidé avec succès',
        'cache_clear_failed' => 'Échec de la suppression du cache des statistiques',
        'clear_cache' => 'Vider le cache',
        'last_24h' => 'Dernières 24 heures',
        'last_7d' => 'Derniers 7 jours',
        'last_30d' => 'Derniers 30 jours'
    ],
    
    'table' => [
        'player' => 'Joueur',
        'reason' => 'Raison',
        'staff' => 'Staff',
        'date' => 'Date',
        'expires' => 'Expire',
        'status' => 'Statut',
        'actions' => 'Actions',
        'type' => 'Type',
        'view' => 'Voir',
        'total' => 'Total',
        'active' => 'Actif',
        'last_ban' => 'Dernier ban',
        'last_action' => 'Dernière action',
        'server' => 'Serveur',
    ],
    
    'status' => [
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'expired' => 'Expiré',
        'removed' => 'Annulé',
        'completed' => 'Terminé',
        'removed_by' => 'Annulé par'
    ],
    
    'punishment' => [
        'permanent' => 'Permanent',
        'expired' => 'Expiré'
    ],
    
    'punishments' => [
        'no_data' => 'Aucune sanction trouvée',
        'no_data_desc' => 'Il n\'y a actuellement aucune sanction à afficher'
    ],
    
    'detail' => [
        'duration' => 'Durée',
        'time_left' => 'Temps restant',
        'progress' => 'Progression',
        'removed_by' => 'Annulé par',
        'removed_date' => 'Date d\'annulation',
        'flags' => 'Drapeaux',
        'other_punishments' => 'Autres sanctions'
    ],
    
    'time' => [
        'days' => '{count} jours',
        'hours' => '{count} heures',
        'minutes' => '{count} minutes'
    ],
    
    'pagination' => [
        'label' => 'Navigation de page',
        'previous' => 'Précédent',
        'next' => 'Suivant',
        'page_info' => 'Page {current} sur {total}'
    ],
    
    'footer' => [
        'rights' => 'Tous droits réservés.',
        'powered_by' => 'Propulsé par',
        'license' => 'Sous licence'
    ],
    
    'error' => [
        'not_found' => 'Page non trouvée',
        'server_error' => 'Une erreur de serveur est survenue',
        'invalid_request' => 'Requête invalide',
        'punishment_not_found' => 'La sanction demandée n\'a pas pu être trouvée.',
        'loading_failed' => 'Échec du chargement des détails de la sanction.'
    ],
    
    'protest' => [
        'title' => 'Contestation de bannissement',
        'description' => 'Si vous pensez que votre bannissement a été appliqué par erreur, vous pouvez soumettre une contestation pour examen.',
        'how_to_title' => 'Comment soumettre une contestation de bannissement',
        'how_to_subtitle' => 'Suivez ces étapes pour demander un dé-bannissement :',
        'step1_title' => '1. Rassemblez vos informations',
        'step1_desc' => 'Avant de soumettre une contestation, assurez-vous d\'avoir :',
        'step1_items' => [
            'Votre pseudo Minecraft',
            'La date et l\'heure de votre bannissement',
            'La raison donnée pour votre bannissement',
            'Toute preuve qui appuie votre cas'
        ],
        'step2_title' => '2. Méthodes de contact',
        'step2_desc' => 'Vous pouvez soumettre votre contestation par l\'une des méthodes suivantes :',
        'discord_title' => 'Discord (Recommandé)',
        'discord_desc' => 'Rejoignez notre serveur Discord et créez un ticket dans le canal #contestations-bans',
        'discord_button' => 'Rejoindre Discord',
        'email_title' => 'E-mail',
        'email_desc' => 'Envoyez un e-mail détaillé avec votre contestation à :',
        'forum_title' => 'Forum',
        'forum_desc' => 'Créez un nouveau message dans la section "Contestations de bannissement" de notre forum.',
        'forum_button' => 'Visiter le forum',
        'step3_title' => '3. Ce qu\'il faut inclure',
        'step3_desc' => 'Votre contestation doit inclure : Votre pseudo - Ce qui s\'est passé - Pourquoi vous contestez - Ce que vous souhaitez qu\'il se passe - ID du site (ex: ban&id=181) - Optionnel : Capture d\'écran comme preuve.',
        'step3_items' => [
            'Votre pseudo Minecraft',
            'La date et l\'heure approximative du bannissement',
            'Le membre du staff qui vous a banni (si connu)',
            'Une explication détaillée des raisons pour lesquelles vous pensez que le bannissement était injuste',
            'Toute capture d\'écran ou preuve qui appuie votre cas',
            'Un récit honnête de ce qui s\'est passé'
        ],
        'step4_title' => '4. Attendez l\'examen',
        'step4_desc' => 'Notre équipe examinera votre contestation dans les 48-72 heures. Veuillez être patient et ne pas soumettre plusieurs contestations pour le même bannissement.',
        'guidelines_title' => 'Directives importantes',
        'guidelines_items' => [
            'Soyez honnête et respectueux dans votre contestation',
            'Ne mentez pas et ne fournissez pas de fausses informations',
            'Ne spammez pas et ne soumettez pas de multiples contestations',
            'Acceptez la décision finale de l\'équipe',
            'Le contournement du bannissement entraînera un bannissement permanent'
        ],
        'warning_title' => 'Avertissement',
        'warning_desc' => 'La soumission de fausses informations ou la tentative de tromper le staff entraînera le rejet de votre contestation et pourra entraîner une sanction prolongée.',
        'form_not_available' => 'La soumission directe de contestation n\'est pas disponible pour le moment. Veuillez utiliser l\'une des méthodes de contact ci-dessus.'
    ],
    
    'admin' => [
        'dashboard' => 'Tableau de bord admin',
        'login' => 'Connexion admin',
        'logout' => 'Déconnexion',
        'password' => 'Mot de passe',
        'export_data' => 'Exporter les données',
        'export_desc' => 'Exporter les données de sanction dans divers formats',
        'import_data' => 'Importer les données',
        'import_desc' => 'Importer les données de sanction à partir de fichiers JSON ou XML',
        'data_type' => 'Type de données',
        'all_punishments' => 'Toutes les sanctions',
        'select_file' => 'Sélectionner un fichier',
        'import' => 'Importer',
        'settings' => 'Paramètres',
        'show_player_uuid' => 'Afficher l\'UUID du joueur',
        'footer_site_name' => 'Nom du site en pied de page',
        'footer_site_name_desc' => 'Nom du site affiché dans le texte de copyright du pied de page',
    ],
];