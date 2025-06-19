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
        'description' => 'Public interface for viewing server punishments and bans'
    ],
    
    'nav' => [
        'home' => 'Home',
        'bans' => 'Bans',
        'mutes' => 'Mutes', 
        'warnings' => 'Warnings',
        'kicks' => 'Kicks'
    ],
    
    'home' => [
        'welcome' => 'Server Punishments',
        'description' => 'Search for player punishments and view recent activity',
        'recent_activity' => 'Recent Activity',
        'recent_bans' => 'Recent Bans',
        'recent_mutes' => 'Recent Mutes',
        'no_recent_bans' => 'No recent bans found',
        'no_recent_mutes' => 'No recent mutes found',
        'view_all_bans' => 'View All Bans',
        'view_all_mutes' => 'View All Mutes'
    ],
    
    'search' => [
        'title' => 'Player Search',
        'placeholder' => 'Enter player name or UUID...',
        'help' => 'You can search by player name or full UUID',
        'button' => 'Search',
        'no_results' => 'No punishments found for this player',
        'error' => 'Search error occurred'
    ],
    
    'stats' => [
        'title' => 'Server Statistics',
        'active_bans' => 'Active Bans',
        'active_mutes' => 'Active Mutes',
        'total_warnings' => 'Total Warnings',
        'total_kicks' => 'Total Kicks',
        'total_of' => 'of',
        'all_time' => 'all time'
    ],
    
    'table' => [
        'player' => 'Player',
        'reason' => 'Reason',
        'staff' => 'Staff',
        'date' => 'Date',
        'expires' => 'Expires',
        'status' => 'Status'
    ],
    
    'status' => [
        'active' => 'Active',
        'inactive' => 'Inactive',
        'expired' => 'Expired',
        'removed' => 'Removed',
        'completed' => 'Completed',
        'removed_by' => 'Removed by'
    ],
    
    'punishment' => [
        'permanent' => 'Permanent',
        'expired' => 'Expired'
    ],
    
    'punishments' => [
        'no_data' => 'No punishments found',
        'no_data_desc' => 'There are currently no punishments to display'
    ],
    
    'time' => [
        'days' => '{count} days',
        'hours' => '{count} hours',
        'minutes' => '{count} minutes'
    ],
    
    'pagination' => [
        'label' => 'Page navigation',
        'previous' => 'Previous',
        'next' => 'Next',
        'page_info' => 'Page {current} of {total}'
    ],
    
    'footer' => [
        'rights' => 'All rights reserved.',
        'powered_by' => 'Powered by',
        'license' => 'Licensed under'
    ],
    
    'error' => [
        'not_found' => 'Page not found',
        'server_error' => 'Server error occurred',
        'invalid_request' => 'Invalid request'
    ]
];