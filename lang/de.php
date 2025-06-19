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
        'description' => 'Öffentliche Oberfläche zur Anzeige von Strafen und Bans des Servers'
    ],
    
    'nav' => [
        'home' => 'Startseite',
        'bans' => 'Bans',
        'mutes' => 'Stummschaltungen',
        'warnings' => 'Verwarnungen',
        'kicks' => 'Kicks'
    ],
    
    'home' => [
        'welcome' => 'Serverstrafen',
        'description' => 'Durchsuche Spielerstrafen und zeige die letzte Aktivität an',
        'recent_activity' => 'Letzte Aktivität',
        'recent_bans' => 'Letzte Bans',
        'recent_mutes' => 'Letzte Stummschaltungen',
        'no_recent_bans' => 'Keine aktuellen Bans',
        'no_recent_mutes' => 'Keine aktuellen Stummschaltungen',
        'view_all_bans' => 'Alle Bans anzeigen',
        'view_all_mutes' => 'Alle Stummschaltungen anzeigen'
    ],
    
    'search' => [
        'title' => 'Spielersuche',
        'placeholder' => 'Spielername oder UUID eingeben...',
        'help' => 'Du kannst nach Spielernamen oder vollständiger UUID suchen',
        'button' => 'Suchen',
        'no_results' => 'Keine Strafen für diesen Spieler gefunden',
        'error' => 'Fehler bei der Suche aufgetreten'
    ],
    
    'stats' => [
        'title' => 'Serverstatistiken',
        'active_bans' => 'Aktive Bans',
        'active_mutes' => 'Aktive Stummschaltungen',
        'total_warnings' => 'Gesamte Verwarnungen',
        'total_kicks' => 'Gesamte Kicks',
        'total_of' => 'von',
        'all_time' => 'gesamt'
    ],
    
    'table' => [
        'player' => 'Spieler',
        'reason' => 'Grund',
        'staff' => 'Moderator',
        'date' => 'Datum',
        'expires' => 'Läuft ab',
        'status' => 'Status'
    ],
    
    'status' => [
        'active' => 'Aktiv',
        'inactive' => 'Inaktiv',
        'expired' => 'Abgelaufen',
        'removed' => 'Entfernt',
        'completed' => 'Abgeschlossen',
        'removed_by' => 'Entfernt von'
    ],
    
    'punishment' => [
        'permanent' => 'Permanent',
        'expired' => 'Abgelaufen'
    ],
    
    'punishments' => [
        'no_data' => 'Keine Strafen gefunden',
        'no_data_desc' => 'Zurzeit gibt es keine Strafen zur Anzeige'
    ],
    
    'time' => [
        'days' => '{count} Tage',
        'hours' => '{count} Stunden',
        'minutes' => '{count} Minuten'
    ],
    
    'pagination' => [
        'label' => 'Seitennavigation',
        'previous' => 'Zurück',
        'next' => 'Weiter',
        'page_info' => 'Seite {current} von {total}'
    ],
    
    'footer' => [
        'rights' => 'Alle Rechte vorbehalten.',
        'powered_by' => 'Bereitgestellt von',
        'license' => 'Lizenziert unter'
    ],
    
    'error' => [
        'not_found' => 'Seite nicht gefunden',
        'server_error' => 'Serverfehler aufgetreten',
        'invalid_request' => 'Ungültige Anfrage'
    ]
];
