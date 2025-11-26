<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.0
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
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
    
    // Google OAuth Configuration
    'google_auth_enabled' => EnvLoader::get('GOOGLE_AUTH_ENABLED', 'false') === 'true',
    'google_client_id' => EnvLoader::get('GOOGLE_CLIENT_ID', ''),
    'google_client_secret' => EnvLoader::get('GOOGLE_CLIENT_SECRET', ''),
    
    // Discord OAuth Configuration
    'discord_auth_enabled' => EnvLoader::get('DISCORD_AUTH_ENABLED', 'false') === 'true',
    'discord_client_id' => EnvLoader::get('DISCORD_CLIENT_ID', ''),
    'discord_client_secret' => EnvLoader::get('DISCORD_CLIENT_SECRET', ''),
    
    'allow_password_login' => EnvLoader::get('ALLOW_PASSWORD_LOGIN', 'true') === 'true',
    
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
    
    // Display Options (v2.7)
    'show_silent_punishments' => EnvLoader::get('SHOW_SILENT_PUNISHMENTS', 'true') === 'true',
    'show_server_origin' => EnvLoader::get('SHOW_SERVER_ORIGIN', 'true') === 'true',
    'show_server_scope' => EnvLoader::get('SHOW_SERVER_SCOPE', 'true') === 'true',
    'show_contact_discord' => EnvLoader::get('SHOW_CONTACT_DISCORD', 'true') === 'true',
    'show_contact_email' => EnvLoader::get('SHOW_CONTACT_EMAIL', 'true') === 'true',
    'show_contact_forum' => EnvLoader::get('SHOW_CONTACT_FORUM', 'true') === 'true',
    
    // Menu Display Options (v2.7)
    'show_menu_protest' => EnvLoader::get('SHOW_MENU_PROTEST', 'true') === 'true',
    'show_menu_stats' => EnvLoader::get('SHOW_MENU_STATS', 'true') === 'true',
    'show_menu_admin' => EnvLoader::get('SHOW_MENU_ADMIN', 'true') === 'true',
    
    // SEO Advanced Configuration (v2.7)
    'seo_enable_schema' => EnvLoader::get('SEO_ENABLE_SCHEMA', 'true') === 'true',
    'seo_organization_name' => EnvLoader::get('SEO_ORGANIZATION_NAME'),
    'seo_organization_logo' => EnvLoader::get('SEO_ORGANIZATION_LOGO'),
    'seo_social_facebook' => EnvLoader::get('SEO_SOCIAL_FACEBOOK'),
    'seo_social_twitter' => EnvLoader::get('SEO_SOCIAL_TWITTER'),
    'seo_social_youtube' => EnvLoader::get('SEO_SOCIAL_YOUTUBE'),
    'seo_enable_breadcrumbs' => EnvLoader::get('SEO_ENABLE_BREADCRUMBS', 'true') === 'true',
    'seo_enable_sitemap' => EnvLoader::get('SEO_ENABLE_SITEMAP', 'true') === 'true',
    'seo_contact_type' => EnvLoader::get('SEO_CONTACT_TYPE', 'CustomerService'),
    'seo_contact_phone' => EnvLoader::get('SEO_CONTACT_PHONE'),
    'seo_contact_email' => EnvLoader::get('SEO_CONTACT_EMAIL'),
    'seo_price_currency' => EnvLoader::get('SEO_PRICE_CURRENCY', 'EUR'),
    'seo_locale' => EnvLoader::get('SEO_LOCALE', 'en_US'),
    'seo_ai_training' => EnvLoader::get('SEO_AI_TRAINING', 'true') === 'true',
    'seo_geo_region' => EnvLoader::get('SEO_GEO_REGION'),
    'seo_geo_placename' => EnvLoader::get('SEO_GEO_PLACENAME'),
    'seo_geo_position' => EnvLoader::get('SEO_GEO_POSITION'),
    'seo_facebook_app_id' => EnvLoader::get('SEO_FACEBOOK_APP_ID'),
    'seo_twitter_creator' => EnvLoader::get('SEO_TWITTER_CREATOR'),
    
    // Cache Configuration (v2.7)
    'cache_enabled' => EnvLoader::get('CACHE_ENABLED', 'true') === 'true',
    'cache_lifetime' => (int)EnvLoader::get('CACHE_LIFETIME', 3600),
];
