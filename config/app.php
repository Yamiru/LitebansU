<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       2.3
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

declare(strict_types=1);

// Ensure EnvLoader is loaded with proper path
if (!class_exists('core\\EnvLoader')) {
    require_once dirname(__DIR__) . '/core/EnvLoader.php';
}

use core\EnvLoader;

return [
    // Site Configuration
    'site_name' => EnvLoader::get('SITE_NAME', 'LiteBansU'),
    'footer_site_name' => EnvLoader::get('FOOTER_SITE_NAME', 'YourSite'),
    'items_per_page' => (int)EnvLoader::get('ITEMS_PER_PAGE', 20),
    'timezone' => EnvLoader::get('TIMEZONE', 'UTC'),
    'date_format' => EnvLoader::get('DATE_FORMAT', 'Y-m-d H:i:s'),
    'avatar_url' => EnvLoader::get('AVATAR_URL', 'https://crafatar.com/avatars/{uuid}?size=64&overlay=true'),
    'avatar_url_offline' => EnvLoader::get('AVATAR_URL_OFFLINE', 'https://crafatar.com/avatars/{uuid}?size=64'),
    'avatar_provider' => EnvLoader::get('AVATAR_PROVIDER', 'crafatar'), // crafatar, cravatar, or custom
    'base_path' => defined('BASE_PATH') ? BASE_PATH : '',
    'debug' => EnvLoader::get('DEBUG', 'false') === 'true',
    'log_errors' => EnvLoader::get('LOG_ERRORS', 'true') === 'true',
    'error_log_path' => EnvLoader::get('ERROR_LOG_PATH', 'logs/error.log'),
    
    // Admin Configuration
    'show_uuid' => filter_var($_COOKIE['show_uuid'] ?? EnvLoader::get('SHOW_PLAYER_UUID', 'true'), FILTER_VALIDATE_BOOLEAN),
    'admin_enabled' => EnvLoader::get('ADMIN_ENABLED', 'false') === 'true',
    'admin_password' => EnvLoader::get('ADMIN_PASSWORD', ''),
    'default_theme' => EnvLoader::get('DEFAULT_THEME', 'dark'),
    'default_language' => EnvLoader::get('DEFAULT_LANGUAGE', 'en'),
    
    // Protest Configuration
    'protest_discord' => EnvLoader::get('PROTEST_DISCORD', '#'),
    'protest_email' => EnvLoader::get('PROTEST_EMAIL', 'admin@example.com'),
    'protest_forum' => EnvLoader::get('PROTEST_FORUM', '#'),
    
    // SEO Configuration
    'site_url' => EnvLoader::get('SITE_URL', 'https://'),
    'site_lang' => EnvLoader::get('SITE_LANG', 'en'),
    'site_charset' => EnvLoader::get('SITE_CHARSET', 'UTF-8'),
    'site_viewport' => EnvLoader::get('SITE_VIEWPORT', 'width=device-width, initial-scale=1.0'),
    'site_robots' => EnvLoader::get('SITE_ROBOTS', 'index, follow'),
    'site_description' => EnvLoader::get('SITE_DESCRIPTION', 'Public interface for viewing server punishments and bans'),
    'site_title_template' => EnvLoader::get('SITE_TITLE_TEMPLATE', '{page} - {site}'),
    'site_favicon' => EnvLoader::get('SITE_FAVICON', 'favicon.ico'),
    'site_apple_icon' => EnvLoader::get('SITE_APPLE_ICON', 'apple-touch-icon.png'),
    'site_theme_color' => EnvLoader::get('SITE_THEME_COLOR', '#ef4444'),
    'site_og_image' => EnvLoader::get('SITE_OG_IMAGE'),
    'site_twitter_site' => EnvLoader::get('SITE_TWITTER_SITE'),
    'site_keywords' => EnvLoader::get('SITE_KEYWORDS'),
    'site_author' => EnvLoader::get('SITE_AUTHOR'),
    'site_generator' => EnvLoader::get('SITE_GENERATOR', 'LitebansU'),
    
    // Security Configuration
    'session_lifetime' => (int)EnvLoader::get('SESSION_LIFETIME', 3600),
    'rate_limit_requests' => (int)EnvLoader::get('RATE_LIMIT_REQUESTS', 60),
    'rate_limit_window' => (int)EnvLoader::get('RATE_LIMIT_WINDOW', 3600),
];
