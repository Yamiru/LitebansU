<?php
/**
 * LiteBansU - Installation Wizard
 * Complete configuration setup with all fields
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

// Load version from .version file
$version = file_exists(__DIR__ . '/.version') ? trim(file_get_contents(__DIR__ . '/.version')) : '3.3';

// Initialize step
if (!isset($_SESSION['step'])) {
    $_SESSION['step'] = 1;
}

// Reset
if (isset($_GET['reset'])) {
    session_destroy();
    header('Location: install.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($_SESSION['step'] == 1) {
        $_SESSION['step'] = 2;
    } elseif ($_SESSION['step'] == 2 && !isset($_POST['delete_installer'])) {
        $_SESSION['config'] = $_POST;
        $_SESSION['step'] = 3;
    }
    header('Location: install.php');
    exit;
}

$step = $_SESSION['step'];

function getVal($name, $default = '') {
    return $_SESSION['config'][$name] ?? $default;
}

function generateEnv($config) {
    global $version;
    $env = "# ============================================================================
# LiteBansU $version - Configuration File
# ============================================================================

# Database Configuration
DB_HOST=" . ($config['db_host'] ?? 'localhost') . "
DB_PORT=" . ($config['db_port'] ?? '3306') . "
DB_NAME=" . ($config['db_name'] ?? '') . "
DB_USER=" . ($config['db_user'] ?? '') . "
DB_PASS=" . ($config['db_pass'] ?? '') . "
DB_DRIVER=mysql
TABLE_PREFIX=" . ($config['table_prefix'] ?? 'litebans_') . "

# Site Configuration
SITE_NAME=" . ($config['site_name'] ?? 'LiteBansU') . "
FOOTER_SITE_NAME=" . ($config['footer_site_name'] ?? '') . "
ITEMS_PER_PAGE=" . ($config['items_per_page'] ?? '100') . "
TIMEZONE=" . ($config['timezone'] ?? 'UTC') . "
DATE_FORMAT=Y-m-d H:i:s
BASE_URL=" . ($config['base_url'] ?? '') . "

# Avatar Configuration
AVATAR_URL=" . ($config['avatar_url'] ?? 'https://mineskin.eu/helm/{name}') . "
AVATAR_URL_OFFLINE=" . ($config['avatar_url_offline'] ?? 'https://mineskin.eu/helm/{name}') . "

# Default Settings
DEFAULT_THEME=" . ($config['default_theme'] ?? 'dark') . "
DEFAULT_LANGUAGE=" . ($config['default_language'] ?? 'en') . "
SHOW_PLAYER_UUID=" . ($config['show_player_uuid'] ?? 'false') . "

# Debug Mode
DEBUG=false
LOG_ERRORS=false
ERROR_LOG_PATH=logs/error.log

# Security
SESSION_LIFETIME=7200
RATE_LIMIT_REQUESTS=60
RATE_LIMIT_WINDOW=3600

# Admin Configuration
ADMIN_ENABLED=true
ADMIN_PASSWORD=

# Allow password login
ALLOW_PASSWORD_LOGIN=true

# Google OAuth Configuration
GOOGLE_AUTH_ENABLED=" . ($config['google_auth_enabled'] ?? 'false') . "
GOOGLE_CLIENT_ID=" . ($config['google_client_id'] ?? '') . "
GOOGLE_CLIENT_SECRET=" . ($config['google_client_secret'] ?? '') . "

# Discord OAuth Configuration
DISCORD_AUTH_ENABLED=" . ($config['discord_auth_enabled'] ?? 'false') . "
DISCORD_CLIENT_ID=" . ($config['discord_client_id'] ?? '') . "
DISCORD_CLIENT_SECRET=" . ($config['discord_client_secret'] ?? '') . "

# Contact Configuration
PROTEST_DISCORD=" . ($config['protest_discord'] ?? '') . "
PROTEST_EMAIL=" . ($config['protest_email'] ?? '') . "
PROTEST_FORUM=" . ($config['protest_forum'] ?? '') . "

# Display Options
SHOW_SILENT_PUNISHMENTS=" . ($config['show_silent_punishments'] ?? 'true') . "
SHOW_SERVER_ORIGIN=" . ($config['show_server_origin'] ?? 'true') . "
SHOW_SERVER_SCOPE=" . ($config['show_server_scope'] ?? 'true') . "
SHOW_CONTACT_DISCORD=" . ($config['show_contact_discord'] ?? 'true') . "
SHOW_CONTACT_EMAIL=" . ($config['show_contact_email'] ?? 'true') . "
SHOW_CONTACT_FORUM=" . ($config['show_contact_forum'] ?? 'true') . "

# SEO Configuration
SITE_URL=" . ($config['site_url'] ?? '') . "
SITE_LANG=" . ($config['site_lang'] ?? 'en') . "
SITE_CHARSET=UTF-8
SITE_VIEWPORT=width=device-width, initial-scale=1.0
SITE_ROBOTS=" . ($config['site_robots'] ?? 'index, follow') . "
SITE_DESCRIPTION=" . ($config['site_description'] ?? 'View and search player punishments on our Minecraft server') . "
SITE_TITLE_TEMPLATE={page} - {site}
SITE_THEME_COLOR=" . ($config['site_theme_color'] ?? '#ef4444') . "
SITE_OG_IMAGE=" . ($config['site_og_image'] ?? '') . "
SITE_TWITTER_SITE=" . ($config['site_twitter_site'] ?? '') . "
SITE_KEYWORDS=" . ($config['site_keywords'] ?? 'minecraft,litebans,punishments,bans,mutes,server') . "

# SEO Advanced
SEO_ENABLE_SCHEMA=" . ($config['seo_enable_schema'] ?? 'true') . "
SEO_ORGANIZATION_NAME=" . ($config['seo_organization_name'] ?? '') . "
SEO_ORGANIZATION_LOGO=" . ($config['seo_organization_logo'] ?? '') . "
SEO_SOCIAL_FACEBOOK=" . ($config['seo_social_facebook'] ?? '') . "
SEO_SOCIAL_TWITTER=" . ($config['seo_social_twitter'] ?? '') . "
SEO_SOCIAL_YOUTUBE=" . ($config['seo_social_youtube'] ?? '') . "
SEO_ENABLE_BREADCRUMBS=true
SEO_ENABLE_SITEMAP=true
SEO_CONTACT_TYPE=CustomerService
SEO_CONTACT_PHONE=" . ($config['seo_contact_phone'] ?? '') . "
SEO_CONTACT_EMAIL=" . ($config['seo_contact_email'] ?? '') . "
SEO_PRICE_CURRENCY=EUR
SEO_LOCALE=" . ($config['seo_locale'] ?? 'en_US') . "
SEO_AI_TRAINING=" . ($config['seo_ai_training'] ?? 'true') . "
SEO_GEO_REGION=" . ($config['seo_geo_region'] ?? '') . "
SEO_GEO_PLACENAME=" . ($config['seo_geo_placename'] ?? '') . "
SEO_GEO_POSITION=
SEO_FACEBOOK_APP_ID=
SEO_TWITTER_CREATOR=

# Menu Display
SHOW_MENU_PROTEST=" . ($config['show_menu_protest'] ?? 'true') . "
SHOW_MENU_STATS=" . ($config['show_menu_stats'] ?? 'true') . "
SHOW_MENU_ADMIN=" . ($config['show_menu_admin'] ?? 'true') . "

# Performance & Cache
CACHE_ENABLED=true
CACHE_LIFETIME=3600

# Demo Mode
DEMO_MODE=false
";
    return $env;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiteBansU <?= $version ?> - Installation Wizard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 2rem 0;
        }
        .install-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 900px;
            margin: 0 auto;
        }
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 20px;
            left: 0;
            right: 0;
            height: 2px;
            background: #e0e0e0;
            z-index: 0;
        }
        .step {
            flex: 1;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .step-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e0e0e0;
            color: #999;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        .step.active .step-circle {
            background: #667eea;
            color: white;
        }
        .step.completed .step-circle {
            background: #10b981;
            color: white;
        }
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 0.5rem;
        }
        .form-section h5 {
            color: #667eea;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .form-text {
            font-size: 0.85rem;
            color: #6c757d;
        }
        .btn-primary {
            background: #667eea;
            border: none;
        }
        .btn-primary:hover {
            background: #5568d3;
        }
        .required-badge {
            color: #dc3545;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-card p-5">
            <!-- Header -->
            <div class="text-center mb-4">
                <h1 class="mb-2"><i class="fas fa-rocket text-primary"></i> LiteBansU <?= $version ?></h1>
                <p class="text-muted">Installation Wizard</p>
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">
                    <div class="step-circle">1</div>
                    <small>Welcome</small>
                </div>
                <div class="step <?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">
                    <div class="step-circle">2</div>
                    <small>Configuration</small>
                </div>
                <div class="step <?= $step >= 3 ? 'active' : '' ?>">
                    <div class="step-circle">3</div>
                    <small>Complete</small>
                </div>
            </div>

            <?php if ($step == 1): ?>
                <!-- Step 1: Welcome -->
                <div class="text-center">
                    <i class="fas fa-shield-alt fa-5x text-primary mb-4"></i>
                    <h3>Welcome to LiteBansU <?= $version ?></h3>
                    <p class="lead mb-4">Modern web interface for LiteBans punishment management system</p>
                    
                    <div class="alert alert-info text-start">
                        <h5><i class="fas fa-info-circle"></i> Before you start:</h5>
                        <ul class="mb-0">
                            <li>Ensure you have access to your database credentials</li>
                            <li>Have your site URL ready</li>
                            <li>Optional: Google OAuth credentials if you want to use them</li>
                            <li>All fields have descriptions to help you</li>
                        </ul>
                    </div>

                    <form method="post">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-arrow-right"></i> Start Configuration
                        </button>
                    </form>
                </div>

            <?php elseif ($step == 2): ?>
                <!-- Step 2: Configuration Form -->
                <form method="post">
                    
                    <!-- Database Configuration -->
                    <div class="form-section">
                        <h5><i class="fas fa-database"></i> Database Configuration <span class="required-badge">* Required</span></h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Host</label>
                                <input type="text" class="form-control" name="db_host" value="localhost" required>
                                <small class="form-text">Usually "localhost" or "127.0.0.1"</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Port</label>
                                <input type="text" class="form-control" name="db_port" value="3306" required>
                                <small class="form-text">Default MySQL port is 3306</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Name</label>
                                <input type="text" class="form-control" name="db_name" required>
                                <small class="form-text">Name of your LiteBans database</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database User</label>
                                <input type="text" class="form-control" name="db_user" required>
                                <small class="form-text">Username with access to the database</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Password</label>
                                <input type="password" class="form-control" name="db_pass">
                                <small class="form-text">Leave empty if no password</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Table Prefix</label>
                                <input type="text" class="form-control" name="table_prefix" value="litebans_" required>
                                <small class="form-text">LiteBans table prefix (default: litebans_)</small>
                            </div>
                        </div>
                    </div>

                    <!-- Site Configuration -->
                    <div class="form-section">
                        <h5><i class="fas fa-globe"></i> Site Configuration</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" value="LiteBansU" required>
                                <small class="form-text">Displayed in header and page titles</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Footer Site Name</label>
                                <input type="text" class="form-control" name="footer_site_name">
                                <small class="form-text">Name in footer copyright (Ă‚Â© Your Server 2024)</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site URL</label>
                                <input type="url" class="form-control" name="site_url" placeholder="https://yourdomain.com">
                                <small class="form-text">Full URL with https:// (for SEO and canonical links)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Base URL</label>
                                <input type="text" class="form-control" name="base_url" placeholder="/">
                                <small class="form-text">Base path if not in root (e.g., /litebans or /)</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Items Per Page</label>
                                <input type="number" class="form-control" name="items_per_page" value="100" min="10" max="200">
                                <small class="form-text">Number of punishments per page (10-200)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Timezone</label>
                                <select class="form-control" name="timezone">
                                    <option value="UTC">UTC</option>
                                    <option value="Europe/London">Europe/London</option>
                                    <option value="Europe/Paris">Europe/Paris</option>
                                    <option value="Europe/Berlin">Europe/Berlin</option>
                                    <option value="Europe/Prague">Europe/Prague</option>
                                    <option value="Europe/Bratislava">Europe/Bratislava</option>
                                    <option value="America/New_York">America/New_York</option>
                                    <option value="America/Chicago">America/Chicago</option>
                                    <option value="America/Los_Angeles">America/Los_Angeles</option>
                                </select>
                                <small class="form-text">Server timezone for date/time display</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Default Language</label>
                                <select class="form-control" name="default_language">
                                    <option value="en">English</option>
                                    <option value="sk">Slovak</option>
                                    <option value="cs">Czech</option>
                                    <option value="de">German</option>
                                    <option value="es">Spanish</option>
                                    <option value="fr">French</option>
                                    <option value="pl">Polish</option>
                                    <option value="ru">Russian</option>
                                </select>
                                <small class="form-text">Default language for new visitors</small>
                            </div>
                        </div>
                    </div>

                    <!-- Appearance -->
                    <div class="form-section">
                        <h5><i class="fas fa-palette"></i> Appearance</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Default Theme</label>
                                <select class="form-control" name="default_theme">
                                    <option value="dark">Dark</option>
                                    <option value="light">Light</option>
                                </select>
                                <small class="form-text">Default theme for new visitors</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Avatar URL (Online)</label>
                                <input type="text" class="form-control" name="avatar_url" value="https://mineskin.eu/helm/{name}" placeholder="https://mineskin.eu/helm/{name}">
                                <small class="form-text">URL for online avatars. Use {name} or {uuid}</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Avatar URL (Offline)</label>
                                <input type="text" class="form-control" name="avatar_url_offline" value="https://mineskin.eu/helm/{name}" placeholder="https://mineskin.eu/helm/{name}">
                                <small class="form-text">URL for offline avatars. Use {name} or {uuid}</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Theme Color</label>
                                <input type="color" class="form-control form-control-color" name="site_theme_color" value="#ef4444">
                                <small class="form-text">Browser theme color for mobile devices</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Show Player UUIDs</label>
                                <select class="form-control" name="show_player_uuid">
                                    <option value="false">No</option>
                                    <option value="true">Yes</option>
                                </select>
                                <small class="form-text">Display UUIDs in punishment lists</small>
                            </div>
                        </div>
                    </div>

                    <!-- SEO Configuration -->
                    <div class="form-section">
                        <h5><i class="fas fa-search"></i> SEO Configuration</h5>
                        <div class="mb-3">
                            <label class="form-label">Site Description</label>
                            <textarea class="form-control" name="site_description" rows="2">View and search player punishments on our Minecraft server</textarea>
                            <small class="form-text">Meta description for search engines (150-160 characters)</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Keywords</label>
                                <input type="text" class="form-control" name="site_keywords" value="minecraft,litebans,punishments,bans,mutes,server">
                                <small class="form-text">Comma-separated keywords</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Robots Setting</label>
                                <select class="form-control" name="site_robots">
                                    <option value="index, follow">Index, Follow (Recommended)</option>
                                    <option value="noindex, follow">No Index, Follow</option>
                                    <option value="index, nofollow">Index, No Follow</option>
                                    <option value="noindex, nofollow">No Index, No Follow</option>
                                </select>
                                <small class="form-text">Control search engine indexing</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Language Code</label>
                                <input type="text" class="form-control" name="site_lang" value="en" maxlength="5">
                                <small class="form-text">HTML language attribute (e.g., en, sk, de)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Open Graph Image</label>
                                <input type="text" class="form-control" name="site_og_image" placeholder="/og-image.png">
                                <small class="form-text">Image for social media sharing (1200x630px)</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Twitter Handle</label>
                                <input type="text" class="form-control" name="site_twitter_site" placeholder="@yourhandle">
                                <small class="form-text">Your Twitter/X username</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Enable Schema.org</label>
                                <select class="form-control" name="seo_enable_schema">
                                    <option value="true">Yes (Recommended)</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Structured data for search engines</small>
                            </div>
                        </div>
                    </div>

                    <!-- Organization Info -->
                    <div class="form-section">
                        <h5><i class="fas fa-building"></i> Organization Information (Optional)</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Organization Name</label>
                                <input type="text" class="form-control" name="seo_organization_name" placeholder="Your Server Network">
                                <small class="form-text">Your server or organization name</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Organization Logo URL</label>
                                <input type="text" class="form-control" name="seo_organization_logo" placeholder="/logo.png">
                                <small class="form-text">URL to your logo</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Email</label>
                                <input type="email" class="form-control" name="seo_contact_email" placeholder="contact@yourdomain.com">
                                <small class="form-text">Public contact email</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" class="form-control" name="seo_contact_phone" placeholder="+421123456789">
                                <small class="form-text">Public phone with country code</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">SEO Locale</label>
                                <input type="text" class="form-control" name="seo_locale" value="en_US">
                                <small class="form-text">Format: en_US, sk_SK, de_DE</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Allow AI Training</label>
                                <select class="form-control" name="seo_ai_training">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Allow search engines to use content for AI</small>
                            </div>
                        </div>
                    </div>

                    <!-- Geographic Info -->
                    <div class="form-section">
                        <h5><i class="fas fa-map-marker-alt"></i> Geographic Information (Optional)</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Geographic Region</label>
                                <input type="text" class="form-control" name="seo_geo_region" placeholder="SK-KI">
                                <small class="form-text">ISO region code (e.g., SK-KI, US-CA)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Place Name</label>
                                <input type="text" class="form-control" name="seo_geo_placename" placeholder="KoÄąË‡ice">
                                <small class="form-text">City or location name</small>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="form-section">
                        <h5><i class="fas fa-share-alt"></i> Social Media Links (Optional)</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Facebook Page</label>
                                <input type="url" class="form-control" name="seo_social_facebook" placeholder="https://facebook.com/yourpage">
                                <small class="form-text">Facebook page URL</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Twitter/X</label>
                                <input type="text" class="form-control" name="seo_social_twitter" placeholder="@yourhandle">
                                <small class="form-text">Twitter username</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">YouTube Channel</label>
                                <input type="url" class="form-control" name="seo_social_youtube" placeholder="https://youtube.com/@yourchannel">
                                <small class="form-text">YouTube channel URL</small>
                            </div>
                        </div>
                    </div>

                    <!-- Google OAuth -->
                    <div class="form-section">
                        <h5><i class="fab fa-google"></i> Google OAuth (Optional)</h5>
                        <div class="mb-3">
                            <label class="form-label">Enable Google Authentication</label>
                            <select class="form-control" name="google_auth_enabled">
                                <option value="false">No</option>
                                <option value="true">Yes</option>
                            </select>
                            <small class="form-text">Allow admins to login with Google</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Google Client ID</label>
                                <input type="text" class="form-control" name="google_client_id">
                                <small class="form-text">From Google Cloud Console</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Google Client Secret</label>
                                <input type="password" class="form-control" name="google_client_secret">
                                <small class="form-text">From Google Cloud Console</small>
                            </div>
                        </div>
                    </div>

                    <!-- Discord OAuth -->
                    <div class="form-section">
                        <h5><i class="fab fa-discord"></i> Discord OAuth (Optional)</h5>
                        <div class="mb-3">
                            <label class="form-label">Enable Discord Authentication</label>
                            <select class="form-control" name="discord_auth_enabled">
                                <option value="false">No</option>
                                <option value="true">Yes</option>
                            </select>
                            <small class="form-text">Allow admins to login with Discord</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discord Client ID</label>
                                <input type="text" class="form-control" name="discord_client_id">
                                <small class="form-text">From Discord Developer Portal</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Discord Client Secret</label>
                                <input type="password" class="form-control" name="discord_client_secret">
                                <small class="form-text">From Discord Developer Portal</small>
                            </div>
                        </div>
                    </div>

                    <!-- Protest/Contact Configuration -->
                    <div class="form-section">
                        <h5><i class="fas fa-gavel"></i> Ban Protest Contact Methods</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Discord Invite</label>
                                <input type="url" class="form-control" name="protest_discord" placeholder="https://discord.gg/...">
                                <small class="form-text">Discord server invite link</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Protest Email</label>
                                <input type="email" class="form-control" name="protest_email" placeholder="support@yourdomain.com">
                                <small class="form-text">Email for ban protests</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Forum URL</label>
                                <input type="url" class="form-control" name="protest_forum" placeholder="https://forum.yourdomain.com">
                                <small class="form-text">Forum link for protests</small>
                            </div>
                        </div>
                    </div>

                    <!-- Display Options -->
                    <div class="form-section">
                        <h5><i class="fas fa-eye"></i> Display Options</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Silent Punishments</label>
                                <select class="form-control" name="show_silent_punishments">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Display silent punishments</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Server Origin</label>
                                <select class="form-control" name="show_server_origin">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Show where punishment originated</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Server Scope</label>
                                <select class="form-control" name="show_server_scope">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Show punishment scope</small>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Discord Contact</label>
                                <select class="form-control" name="show_contact_discord">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Display Discord on protest page</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Email Contact</label>
                                <select class="form-control" name="show_contact_email">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Display Email on protest page</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Forum Contact</label>
                                <select class="form-control" name="show_contact_forum">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Display Forum on protest page</small>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Display -->
                    <div class="form-section">
                        <h5><i class="fas fa-bars"></i> Menu Display Options</h5>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Protest Menu</label>
                                <select class="form-control" name="show_menu_protest">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Show "Ban Protest" in menu</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Stats Menu</label>
                                <select class="form-control" name="show_menu_stats">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Show "Statistics" in menu</small>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Show Admin Menu</label>
                                <select class="form-control" name="show_menu_admin">
                                    <option value="true">Yes</option>
                                    <option value="false">No</option>
                                </select>
                                <small class="form-text">Show "Admin" in menu</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="fas fa-check"></i> Generate Configuration
                        </button>
                    </div>
                </form>

            <?php elseif ($step == 3): ?>
                <!-- Step 3: Complete -->
                <div class="text-center">
                    <i class="fas fa-check-circle fa-5x text-success mb-4"></i>
                    <h3 class="text-success mb-4">Installation Complete!</h3>

                    <div class="alert alert-success text-start">
                        <h5><i class="fas fa-clipboard-check"></i> Next Steps:</h5>
                        <ol class="mb-0">
                            <li>Copy the .env content below and save it as <code>.env</code> file in your root directory</li>
                            <li>Set admin password using <code>hash.php</code></li>
                            <li>Delete <code>install.php</code> for security</li>
                            <li>Visit your site and check if everything works</li>
                        </ol>
                    </div>

                    <div class="alert alert-warning text-start">
                        <strong><i class="fas fa-exclamation-triangle"></i> Important:</strong>
                        Delete this <code>install.php</code> file after installation for security reasons!
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-file-code"></i> .env Configuration</span>
                            <button class="btn btn-sm btn-light" onclick="copyToClipboard()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <div class="card-body p-0">
                            <pre class="mb-0 p-3" style="max-height: 500px; overflow-y: auto; background: #f8f9fa;"><code id="envContent"><?= htmlspecialchars(generateEnv($_SESSION['config'])) ?></code></pre>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="hash.php" class="btn btn-primary">
                            <i class="fas fa-key"></i> Set Admin Password
                        </a>
                        <a href="index.php" class="btn btn-success">
                            <i class="fas fa-home"></i> Go to Homepage
                        </a>
                        <a href="?reset" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Start Over
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const content = document.getElementById('envContent').textContent;
            navigator.clipboard.writeText(content).then(() => {
                alert('Configuration copied to clipboard!');
            }).catch(err => {
                console.error('Failed to copy:', err);
            });
        }
    </script>
</body>
</html>
