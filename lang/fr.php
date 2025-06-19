<?php 
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       1.0
 *  Author:        Yamiru <yamiru@yamiru.com>
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

return [
    'site' => [
        'name' => 'LiteBans',
        'title' => '{page} - LiteBans',
        'description' => 'Interface publique pour consulter les sanctions et bannissements du serveur'
    ],
    
    'nav' => [
        'home' => 'Accueil',
        'bans' => 'Bannissements',
        'mutes' => 'Silencieux',
        'warnings' => 'Avertissements',
        'kicks' => 'Expulsions'
    ],
    
    'home' => [
        'welcome' => 'Sanctions du Serveur',
        'description' => 'Recherchez les sanctions des joueurs et affichez l’activité récente',
        'recent_activity' => 'Activité Récente',
        'recent_bans' => 'Bannissements Récents',
        'recent_mutes' => 'Silencieux Récents',
        'no_recent_bans' => 'Aucun bannissement récent',
        'no_recent_mutes' => 'Aucune mise en silence récente',
        'view_all_bans' => 'Voir tous les bannissements',
        'view_all_mutes' => 'Voir toutes les mises en silence'
    ],
    
    'search' => [
        'title' => 'Recherche de Joueurs',
        'placeholder' => 'Entrez le nom du joueur ou l’UUID...',
        'help' => 'Vous pouvez rechercher par nom de joueur ou UUID complet',
        'button' => 'Rechercher',
        'no_results' => 'Aucune sanction trouvée pour ce joueur',
        'error' => 'Une erreur est survenue lors de la recherche'
    ],
    
    'stats' => [
        'title' => 'Statistiques du Serveur',
        'active_bans' => 'Bannissements Actifs',
        'active_mutes' => 'Silencieux Actifs',
        'total_warnings' => 'Total des Avertissements',
        'total_kicks' => 'Total des Expulsions',
        'total_of' => 'sur',
        'all_time' => 'au total'
    ],
    
    'table' => [
        'player' => 'Joueur',
        'reason' => 'Raison',
        'staff' => 'Modérateur',
        'date' => 'Date',
        'expires' => 'Expire',
        'status' => 'Statut'
    ],
    
    'status' => [
        'active' => 'Actif',
        'inactive' => 'Inactif',
        'expired' => 'Expiré',
        'removed' => 'Supprimé',
        'completed' => 'Terminé',
        'removed_by' => 'Supprimé par'
    ],
    
    'punishment' => [
        'permanent' => 'Permanent',
        'expired' => 'Expiré'
    ],
    
    'punishments' => [
        'no_data' => 'Aucune sanction trouvée',
        'no_data_desc' => 'Il n’y a actuellement aucune sanction à afficher'
    ],
    
    'time' => [
        'days' => '{count} jours',
        'hours' => '{count} heures',
        'minutes' => '{count} minutes'
    ],
    
    'pagination' => [
        'label' => 'Navigation des pages',
        'previous' => 'Précédente',
        'next' => 'Suivante',
        'page_info' => 'Page {current} sur {total}'
    ],
    
    'footer' => [
        'rights' => 'Tous droits réservés.',
        'powered_by' => 'Propulsé par',
        'license' => 'Sous licence'
    ],
    
    'error' => [
        'not_found' => 'Page non trouvée',
        'server_error' => 'Erreur du serveur',
        'invalid_request' => 'Requête invalide'
    ]
];
