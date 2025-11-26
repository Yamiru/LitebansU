<?php
/**
 * LiteBansU v3.0 - Simple Installation Wizard
 * No validation, no required fields - just fill and generate
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

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
    // Redirect to prevent form resubmission
    header('Location: install.php');
    exit;
}

$step = $_SESSION['step'];

function getVal($name, $default = '') {
    return $_SESSION['config'][$name] ?? $default;
}

function generateEnv($config) {
    $env = "# Database Configuration
DB_HOST={$config['db_host']}
DB_PORT={$config['db_port']}
DB_NAME={$config['db_name']}
DB_USER={$config['db_user']}
DB_PASS={$config['db_pass']}
DB_DRIVER=mysql
TABLE_PREFIX={$config['table_prefix']}

# Site Configuration
SITE_NAME={$config['site_name']}
FOOTER_SITE_NAME={$config['footer_site_name']}
ITEMS_PER_PAGE={$config['items_per_page']}
TIMEZONE={$config['timezone']}
DATE_FORMAT=Y-m-d H:i:s
BASE_URL={$config['base_url']}

# Avatar Configuration
AVATAR_PROVIDER={$config['avatar_provider']}
AVATAR_URL=https://crafatar.com/avatars/{{uuid}}?size=64&overlay=true
AVATAR_URL_OFFLINE=https://cravatar.eu/avatar/{{name}}/64

# Default Settings
DEFAULT_THEME={$config['default_theme']}
DEFAULT_LANGUAGE={$config['default_language']}
SHOW_PLAYER_UUID=false

# Debug Mode
DEBUG=false
LOG_ERRORS=true
ERROR_LOG_PATH=logs/error.log

# Security
SESSION_LIFETIME=3600
RATE_LIMIT_REQUESTS=60
RATE_LIMIT_WINDOW=3600

# Admin Configuration
ADMIN_ENABLED=true
ADMIN_PASSWORD=

# Google OAuth Configuration
GOOGLE_AUTH_ENABLED={$config['google_enabled']}
GOOGLE_CLIENT_ID={$config['google_client_id']}
GOOGLE_CLIENT_SECRET={$config['google_client_secret']}

# Discord OAuth Configuration
DISCORD_AUTH_ENABLED={$config['discord_enabled']}
DISCORD_CLIENT_ID={$config['discord_client_id']}
DISCORD_CLIENT_SECRET={$config['discord_client_secret']}

# Allow password login
ALLOW_PASSWORD_LOGIN=true

# Contact Configuration
PROTEST_DISCORD={$config['protest_discord']}
PROTEST_EMAIL={$config['protest_email']}
PROTEST_FORUM={$config['protest_forum']}

# Display Options
SHOW_SILENT_PUNISHMENTS=true
SHOW_SERVER_ORIGIN=true
SHOW_SERVER_SCOPE=true
SHOW_CONTACT_DISCORD=true
SHOW_CONTACT_EMAIL=true
SHOW_CONTACT_FORUM=true

# SEO Configuration
SITE_URL={$config['base_url']}
SITE_CHARSET=UTF-8
SITE_VIEWPORT=width=device-width, initial-scale=1.0
SITE_ROBOTS=index, follow
SITE_DESCRIPTION={$config['site_description']}
SITE_TITLE_TEMPLATE={{page}} | {{site}}
SITE_THEME_COLOR=#ef4444
SITE_OG_IMAGE={$config['base_url']}/og-image.png
SITE_TWITTER_SITE=@yourtwitter
SITE_KEYWORDS=minecraft,litebans,punishments,bans,mutes,server

# SEO Advanced
SEO_ENABLE_SCHEMA=true
SEO_ORGANIZATION_NAME={$config['site_name']}
SEO_ORGANIZATION_LOGO={$config['base_url']}/logo.png
SEO_SOCIAL_FACEBOOK=
SEO_SOCIAL_TWITTER=
SEO_SOCIAL_YOUTUBE=
SEO_ENABLE_BREADCRUMBS=true
SEO_ENABLE_SITEMAP=true
SEO_CONTACT_TYPE=CustomerService
SEO_CONTACT_PHONE=
SEO_CONTACT_EMAIL={$config['protest_email']}
SEO_PRICE_CURRENCY=EUR
SEO_LOCALE=en_US
SEO_AI_TRAINING=true
SEO_GEO_REGION=
SEO_GEO_PLACENAME=
SEO_GEO_POSITION=
SEO_FACEBOOK_APP_ID=
SEO_TWITTER_CREATOR=

# Menu Display
SHOW_MENU_PROTEST=true
SHOW_MENU_STATS=true
SHOW_MENU_ADMIN=true

# Performance & Cache
CACHE_ENABLED=true
CACHE_LIFETIME=3600

# Demo Mode
DEMO_MODE=false";
    
    return $env;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LiteBansU v3.0 - Installation</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 2rem 0; }
        .card { background: white; border-radius: 16px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-width: 900px; margin: 0 auto; }
        .card-header { background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white; padding: 2rem; text-align: center; border-radius: 16px 16px 0 0; }
        .step-indicator { display: flex; justify-content: center; gap: 1rem; margin: 2rem 0; padding: 0 2rem; }
        .step { display: flex; flex-direction: column; align-items: center; flex: 1; max-width: 150px; }
        .step-circle { width: 50px; height: 50px; border-radius: 50%; background: #e5e7eb; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.25rem; margin-bottom: 0.5rem; }
        .step.active .step-circle { background: #8b5cf6; color: white; }
        .step.completed .step-circle { background: #10b981; color: white; }
        .step-label { font-size: 0.875rem; color: #6b7280; text-align: center; }
        .form-label { font-weight: 600; color: #374151; margin-bottom: 0.5rem; }
        .form-control { border-radius: 8px; border: 2px solid #e5e7eb; padding: 0.75rem; }
        .form-control:focus { border-color: #8b5cf6; box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1); }
        .btn { padding: 0.75rem 1.5rem; border-radius: 8px; font-weight: 600; }
        .btn-primary { background: #8b5cf6; border: none; }
        .btn-primary:hover { background: #7c3aed; }
        .section-title { font-size: 1.125rem; font-weight: 600; color: #8b5cf6; margin: 1.5rem 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid #e5e7eb; }
        .env-output { background: #1f2937; color: #f3f4f6; padding: 1.5rem; border-radius: 8px; font-family: 'Courier New', monospace; font-size: 0.875rem; max-height: 500px; overflow-y: auto; white-space: pre-wrap; word-break: break-all; }
        .info-box { background: #eff6ff; border-left: 4px solid #3b82f6; padding: 1rem; border-radius: 4px; margin: 1rem 0; }
        .success-box { background: #f0fdf4; border-left: 4px solid #10b981; padding: 1rem; border-radius: 4px; margin: 1rem 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1 class="mb-0"><i class="fas fa-shield-alt"></i> LiteBansU v3.0</h1>
                <p class="mb-0 mt-2">Simple Installation Wizard</p>
            </div>

            <div class="step-indicator">
                <div class="step <?= $step >= 1 ? 'active' : '' ?> <?= $step > 1 ? 'completed' : '' ?>">
                    <div class="step-circle"><?= $step > 1 ? '<i class="fas fa-check"></i>' : '1' ?></div>
                    <div class="step-label">Requirements</div>
                </div>
                <div class="step <?= $step >= 2 ? 'active' : '' ?> <?= $step > 2 ? 'completed' : '' ?>">
                    <div class="step-circle"><?= $step > 2 ? '<i class="fas fa-check"></i>' : '2' ?></div>
                    <div class="step-label">Configuration</div>
                </div>
                <div class="step <?= $step >= 3 ? 'active' : '' ?>">
                    <div class="step-circle">3</div>
                    <div class="step-label">Complete</div>
                </div>
            </div>

            <div class="p-4">
                <?php if ($step == 1): ?>
                    <h3 class="mb-4">Step 1: Requirements</h3>
                    <div class="info-box">
                        <strong><i class="fas fa-info-circle"></i> Ready to install</strong>
                        <p class="mb-0">This wizard will generate your .env configuration file. Fill in the fields on the next page.</p>
                    </div>
                    <form method="post">
                        <button type="submit" class="btn btn-primary w-100">
                            Continue to Configuration <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </form>
                <?php endif; ?>

                <?php if ($step == 2): ?>
                    <h3 class="mb-4">Step 2: Configuration</h3>
                    <form method="post">
                        <div class="section-title"><i class="fas fa-database"></i> Database</div>
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">Database Host</label>
                                <input type="text" class="form-control" name="db_host" value="localhost">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Port</label>
                                <input type="text" class="form-control" name="db_port" value="3306">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Database Name</label>
                            <input type="text" class="form-control" name="db_name" placeholder="minecraft">
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database User</label>
                                <input type="text" class="form-control" name="db_user" placeholder="root">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Database Password</label>
                                <input type="password" class="form-control" name="db_pass" placeholder="Leave empty if no password">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Table Prefix</label>
                            <input type="text" class="form-control" name="table_prefix" value="litebans_">
                        </div>

                        <div class="section-title"><i class="fas fa-globe"></i> Site</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Site Name</label>
                                <input type="text" class="form-control" name="site_name" value="LiteBansU">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Footer Name</label>
                                <input type="text" class="form-control" name="footer_site_name" placeholder="YourServer">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Site URL</label>
                            <input type="text" class="form-control" name="base_url" placeholder="https://yoursite.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Site Description</label>
                            <textarea class="form-control" name="site_description" rows="2">View and search player punishments on our Minecraft server</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Items Per Page</label>
                                <input type="number" class="form-control" name="items_per_page" value="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Timezone</label>
                                <select class="form-control" name="timezone">
                                    <option value="UTC">UTC</option>
                                    <option value="Europe/London">Europe/London</option>
                                    <option value="Europe/Prague">Europe/Prague</option>
                                    <option value="Europe/Bratislava">Europe/Bratislava</option>
                                    <option value="America/New_York">America/New_York</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Avatar Provider</label>
                                <select class="form-control" name="avatar_provider">
                                    <option value="crafatar">Crafatar</option>
                                    <option value="cravatar">Cravatar</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Theme</label>
                                <select class="form-control" name="default_theme">
                                    <option value="dark">Dark</option>
                                    <option value="light">Light</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Language</label>
                                <select class="form-control" name="default_language">
                                    <option value="en">English</option>
                                    <option value="sk">Slovak</option>
                                </select>
                            </div>
                        </div>

                        <div class="section-title"><i class="fab fa-google"></i> Google OAuth (Optional)</div>
                        <input type="hidden" name="google_enabled" value="false">
                        <div class="mb-3">
                            <label class="form-label">Google Client ID</label>
                            <input type="text" class="form-control" name="google_client_id" placeholder="Leave empty to disable">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Google Client Secret</label>
                            <input type="text" class="form-control" name="google_client_secret" placeholder="Leave empty to disable">
                        </div>

                        <div class="section-title"><i class="fab fa-discord"></i> Discord OAuth (Optional)</div>
                        <input type="hidden" name="discord_enabled" value="false">
                        <div class="mb-3">
                            <label class="form-label">Discord Client ID</label>
                            <input type="text" class="form-control" name="discord_client_id" placeholder="Leave empty to disable">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Discord Client Secret</label>
                            <input type="text" class="form-control" name="discord_client_secret" placeholder="Leave empty to disable">
                        </div>

                        <div class="section-title"><i class="fas fa-envelope"></i> Contact (Optional)</div>
                        <div class="mb-3">
                            <label class="form-label">Discord Server</label>
                            <input type="text" class="form-control" name="protest_discord" placeholder="https://discord.gg/yourinvite">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="protest_email" placeholder="support@yourserver.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Forum</label>
                            <input type="text" class="form-control" name="protest_forum" placeholder="https://forum.yourserver.com">
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="?reset=1" class="btn btn-outline-secondary">
                                <i class="fas fa-redo"></i> Start Over
                            </a>
                            <button type="submit" class="btn btn-primary flex-grow-1">
                                Generate Configuration <i class="fas fa-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                <?php endif; ?>

                <?php if ($step == 3): ?>
                    <?php
                    $config = $_SESSION['config'];
                    $config['google_enabled'] = !empty($config['google_client_id']) ? 'true' : 'false';
                    $config['discord_enabled'] = !empty($config['discord_client_id']) ? 'true' : 'false';
                    $envContent = generateEnv($config);
                    ?>
                    <h3 class="mb-4"><i class="fas fa-check-circle text-success"></i> Installation Complete!</h3>

                    <div class="success-box">
                        <strong><i class="fas fa-check-circle"></i> Configuration Generated!</strong>
                        <p class="mb-0">Copy the content below and save it as <code>.env</code> in your root directory.</p>
                    </div>

                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h5 class="mb-0"><i class="fas fa-file-code"></i> .env File Content</h5>
                            <button type="button" class="btn btn-sm btn-success" onclick="copyEnv()">
                                <i class="fas fa-copy"></i> Copy
                            </button>
                        </div>
                        <div class="env-output" id="envContent"><?= htmlspecialchars($envContent) ?></div>
                    </div>

                    <div class="info-box">
                        <h5><i class="fas fa-terminal"></i> Next Steps:</h5>
                        <ol class="mb-0">
                            <li>Save content as <code>.env</code> file</li>
                            <li>Set permissions: <code>chmod 600 .env</code></li>
                            <li>Generate admin password via <code>hash.php</code></li>
                            <li>Add password hash to <code>.env</code></li>
                            <li>Delete <code>install.php</code> and <code>hash.php</code></li>
                        </ol>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <a href="?reset=1" class="btn btn-outline-secondary">
                            <i class="fas fa-redo"></i> Start Over
                        </a>
                        <a href="<?= htmlspecialchars($config['base_url']) ?>" class="btn btn-primary flex-grow-1" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Visit Your Site
                        </a>
                        <button type="button" class="btn btn-danger" onclick="deleteInstaller()">
                            <i class="fas fa-trash"></i> Delete Installer
                        </button>
                    </div>

                    <script>
                    function copyEnv() {
                        const content = document.getElementById('envContent').textContent;
                        navigator.clipboard.writeText(content).then(function() {
                            const btn = event.target.closest('button');
                            const original = btn.innerHTML;
                            btn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                            setTimeout(() => btn.innerHTML = original, 2000);
                        });
                    }

                    function deleteInstaller() {
                        if (confirm('Delete install.php?\n\nMake sure you saved the .env file!')) {
                            fetch('install.php', {
                                method: 'POST',
                                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                                body: 'delete_installer=1'
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    alert('✓ Installer deleted!');
                                    window.location.href = '<?= htmlspecialchars($config['base_url']) ?>';
                                } else {
                                    alert('Please delete manually: rm install.php hash.php');
                                }
                            });
                        }
                    }
                    </script>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_installer'])) {
    header('Content-Type: application/json');
    $success = @unlink('install.php');
    if (file_exists('hash.php')) @unlink('hash.php');
    echo json_encode(['success' => $success]);
    exit;
}
?>