<?php

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
        'kicks' => 'Kicks',
        'statistics' => 'Statistics',
        'language' => 'Language',
        'theme' => 'Theme',
        'admin' => 'Admin',
        'protest' => 'Ban Protest',
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
        'error' => 'Search error occurred',
        'network_error' => 'Network error occurred. Please try again.'
    ],
    
    'stats' => [
        'title' => 'Server Statistics',
        'active_bans' => 'Active Bans',
        'active_mutes' => 'Active Mutes',
        'total_warnings' => 'Total Warnings',
        'total_kicks' => 'Total Kicks',
        'total_of' => 'of',
        'all_time' => 'all time',
        'most_banned_players' => 'Most Banned Players',
        'most_active_staff' => 'Most Active Staff',
        'top_ban_reasons' => 'Top Ban Reasons',
        'recent_activity_overview' => 'Recent Activity Overview',
        'activity_by_day' => 'Activity by Day',
        'cache_cleared' => 'Statistics cache cleared successfully',
        'cache_clear_failed' => 'Failed to clear statistics cache',
        'clear_cache' => 'Clear Cache',
        'last_24h' => 'Last 24 Hours',
        'last_7d' => 'Last 7 Days',
        'last_30d' => 'Last 30 Days'
    ],
    
    'table' => [
        'player' => 'Player',
        'reason' => 'Reason',
        'staff' => 'Staff',
        'date' => 'Date',
        'expires' => 'Expires',
        'status' => 'Status',
        'actions' => 'Actions',
        'type' => 'Type',
        'view' => 'View',
        'total' => 'Total',
        'active' => 'Active',
        'last_ban' => 'Last Ban',
        'last_action' => 'Last Action',
        'server' => 'Server',
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
    
    'detail' => [
        'duration' => 'Duration',
        'time_left' => 'Time Left',
        'progress' => 'Progress',
        'removed_by' => 'Removed By',
        'removed_date' => 'Removed Date',
        'flags' => 'Flags',
        'other_punishments' => 'Other Punishments'
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
        'invalid_request' => 'Invalid request',
        'punishment_not_found' => 'The requested punishment could not be found.',
        'loading_failed' => 'Failed to load punishment details.'
    ],
    
    'protest' => [
        'title' => 'Ban Protest',
        'description' => 'If you believe your ban was issued in error, you can submit a protest for review.',
        'how_to_title' => 'How to Submit a Ban Protest',
        'how_to_subtitle' => 'Follow these steps to request an unban:',
        'step1_title' => '1. Gather Your Information',
        'step1_desc' => 'Before submitting a protest, make sure you have:',
        'step1_items' => [
            'Your Minecraft username',
            'The date and time of your ban',
            'The reason given for your ban',
            'Any evidence that supports your case'
        ],
        'step2_title' => '2. Contact Methods',
        'step2_desc' => 'You can submit your ban protest through one of the following methods:',
        'discord_title' => 'Discord (Recommended)',
        'discord_desc' => 'Join our Discord server and create a ticket in the #ban-protests channel',
        'discord_button' => 'Join Discord',
        'email_title' => 'Email',
        'email_desc' => 'Send a detailed email with your protest to:',
        'forum_title' => 'Forum',
        'forum_desc' => 'Create a new post in the Ban Protests section of our website forum.',
        'forum_button' => 'Visit Forum',
        'step3_title' => '3. What to Include',
        'step3_desc' => 'Your protest should include: Your nickname - What happened - Why you are protesting - What you want to happen - ID from the site (e.g. ban&id=181) - Optional: Screenshot for proof.',
        'step3_items' => [
            'Your Minecraft username',
            'The date and approximate time of the ban',
            'The staff member who banned you (if known)',
            'A detailed explanation of why you believe the ban was unfair',
            'Any screenshots or evidence that support your case',
            'An honest account of what happened'
        ],
        'step4_title' => '4. Wait for Review',
        'step4_desc' => 'Our staff team will review your protest within 48-72 hours. Please be patient and do not submit multiple protests for the same ban.',
        'guidelines_title' => 'Important Guidelines',
        'guidelines_items' => [
            'Be honest and respectful in your protest',
            'Do not lie or provide false information',
            'Do not spam or submit multiple protests',
            'Accept the final decision of the staff team',
            'Ban evasion will result in a permanent ban'
        ],
        'warning_title' => 'Warning',
        'warning_desc' => 'Submitting false information or attempting to deceive staff will result in your protest being denied and may lead to extended punishment.',
        'form_not_available' => 'Direct protest submission is not available at this time. Please use one of the contact methods above.'
    ],
    
    'admin' => [
        'dashboard' => 'Admin Dashboard',
        'login' => 'Admin Login',
        'logout' => 'Logout',
        'password' => 'Password',
        'export_data' => 'Export Data',
        'export_desc' => 'Export punishment data in various formats',
        'import_data' => 'Import Data',
        'import_desc' => 'Import punishment data from JSON or XML files',
        'data_type' => 'Data Type',
        'all_punishments' => 'All Punishments',
        'select_file' => 'Select File',
        'import' => 'Import',
        'settings' => 'Settings',
        'show_player_uuid' => 'Show Player UUID',
        'footer_site_name' => 'Footer Site Name',
        'footer_site_name_desc' => 'Site name displayed in the footer copyright text',
        'require_login' => 'Require Login',
        'require_login_desc' => 'Require authentication to view all pages',
        'login_required' => 'Login Required',
        'login_required_desc' => 'Please log in to access this page',
    ],
];
