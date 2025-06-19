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
        'description' => 'Verejné rozhranie pre prezeranie trestov a banov servera'
    ],
    
    'nav' => [
        'home' => 'Domov',
        'bans' => 'Bany',
        'mutes' => 'Umlčania',
        'warnings' => 'Varovania',
        'kicks' => 'Vykopnutia'
    ],
    
    'home' => [
        'welcome' => 'Serverové Tresty',
        'description' => 'Vyhľadajte tresty hráčov a zobrazte nedávnu aktivitu',
        'recent_activity' => 'Nedávna Aktivita',
        'recent_bans' => 'Nedávne Bany',
        'recent_mutes' => 'Nedávne Umlčania',
        'no_recent_bans' => 'Žiadne nedávne bany',
        'no_recent_mutes' => 'Žiadne nedávne umlčania',
        'view_all_bans' => 'Zobraziť Všetky Bany',
        'view_all_mutes' => 'Zobraziť Všetky Umlčania'
    ],
    
    'search' => [
        'title' => 'Vyhľadávanie Hráčov',
        'placeholder' => 'Zadajte meno hráča alebo UUID...',
        'help' => 'Môžete vyhľadávať podľa mena hráča alebo úplného UUID',
        'button' => 'Hľadať',
        'no_results' => 'Pre tohto hráča neboli nájdené žiadne tresty',
        'error' => 'Nastala chyba pri vyhľadávaní'
    ],
    
    'stats' => [
        'title' => 'Štatistiky Servera',
        'active_bans' => 'Aktívne Bany',
        'active_mutes' => 'Aktívne Umlčania',
        'total_warnings' => 'Celkovo Varovaní',
        'total_kicks' => 'Celkovo Vykopnutí',
        'total_of' => 'z',
        'all_time' => 'celkovo'
    ],
    
    'table' => [
        'player' => 'Hráč',
        'reason' => 'Dôvod',
        'staff' => 'Správca',
        'date' => 'Dátum',
        'expires' => 'Vyprší',
        'status' => 'Stav'
    ],
    
    'status' => [
        'active' => 'Aktívny',
        'inactive' => 'Neaktívny',
        'expired' => 'Vypršaný',
        'removed' => 'Odstránený',
        'completed' => 'Dokončený',
        'removed_by' => 'Odstránené od'
    ],
    
    'punishment' => [
        'permanent' => 'Permanentné',
        'expired' => 'Vypršané'
    ],
    
    'punishments' => [
        'no_data' => 'Neboli nájdené žiadne tresty',
        'no_data_desc' => 'Momentálne nie sú žiadne tresty na zobrazenie'
    ],
    
    'time' => [
        'days' => '{count} dní',
        'hours' => '{count} hodín',
        'minutes' => '{count} minút'
    ],
    
    'pagination' => [
        'label' => 'Navigácia stránok',
        'previous' => 'Predchádzajúca',
        'next' => 'Ďalšia',
        'page_info' => 'Stránka {current} z {total}'
    ],
    
    'footer' => [
        'rights' => 'Všetky práva vyhradené.',
        'powered_by' => 'Vytvorené pomocou',
        'license' => 'Licencované pod'
    ],
    
    'error' => [
        'not_found' => 'Stránka sa nenašla',
        'server_error' => 'Nastala chyba servera',
        'invalid_request' => 'Neplatná požiadavka'
    ]
];