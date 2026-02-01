<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.8
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

// Error handling - capture all errors
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Load environment first
$envLoaderPath = __DIR__ . '/core/EnvLoader.php';
if (!file_exists($envLoaderPath)) {
    die('EnvLoader.php not found at: ' . $envLoaderPath);
}
require_once $envLoaderPath;

try {
    core\EnvLoader::load();
} catch (Exception $e) {
    die('Failed to load environment: ' . $e->getMessage());
}

// Load Logger immediately after EnvLoader so we can log errors
require_once __DIR__ . '/core/Logger.php';
$logger = \core\Logger::getInstance();
$logger->rotateIfNeeded();

// Helper function for error pages - defined early so it's available everywhere
function showErrorPage(int $code, string $title, string $message, ?string $details = null): string {
    http_response_code($code);
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    
    // Get base path for home link
    $scriptPath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = ($scriptPath === '/' || $scriptPath === '\\') ? '' : $scriptPath;
    $homeUrl = htmlspecialchars($basePath ?: '/', ENT_QUOTES, 'UTF-8');
    
    // Show details in debug mode
    $detailsHtml = '';
    $debug = core\EnvLoader::get('DEBUG', 'false') === 'true';
    if ($details && $debug) {
        $safeDetails = htmlspecialchars($details, ENT_QUOTES, 'UTF-8');
        $detailsHtml = "<div class='alert alert-secondary mt-3 text-start'><small><strong>Debug info:</strong><br><pre style='white-space: pre-wrap; word-break: break-all; margin: 0; font-size: 12px;'>{$safeDetails}</pre></small></div>";
    }

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>{$code} - {$safeTitle}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); max-width: 600px; }
        .copyright-footer { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.8); color: white; text-align: center; padding: 0.5rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card error-card mx-auto">
                    <div class="card-body text-center p-5">
                        <h1 class="display-1 text-danger mb-4">{$code}</h1>
                        <h2 class="mb-3">{$safeTitle}</h2>
                        <p class="lead text-muted mb-4">{$safeMessage}</p>
                        {$detailsHtml}
                        <a href="{$homeUrl}" class="btn btn-danger btn-lg mt-3"><i class="fas fa-home"></i> Go Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copyright-footer">Powered by <strong>LitebansU</strong> by <strong>Yamiru</strong></div>
</body>
</html>
HTML;
}

/**
 * Parse database error and return user-friendly message with details
 */
function parseDatabaseError(PDOException $e): array {
    $code = $e->getCode();
    $message = $e->getMessage();
    
    // Default
    $userMessage = 'Unable to connect to the database.';
    $details = "Error: {$message}";
    
    // Parse specific MySQL error codes
    if ($code == 1045 || stripos($message, 'Access denied') !== false) {
        $userMessage = 'Database access denied - invalid username or password.';
        $details = "Check DB_USER and DB_PASS in your .env file.\n\nOriginal error: {$message}";
    } 
    elseif ($code == 1049 || stripos($message, 'Unknown database') !== false) {
        $userMessage = 'Database does not exist.';
        $details = "Check DB_NAME in your .env file - the specified database was not found.\n\nOriginal error: {$message}";
    }
    elseif ($code == 1044) {
        $userMessage = 'Database access denied - user lacks privileges.';
        $details = "The database user does not have permission to access this database.\n\nOriginal error: {$message}";
    }
    elseif ($code == 2002 || stripos($message, 'Connection refused') !== false || stripos($message, 'No such file') !== false) {
        $userMessage = 'Cannot connect to database server.';
        $details = "Check DB_HOST and DB_PORT in your .env file.\nThe database server may be down or not accepting connections.\n\nOriginal error: {$message}";
    }
    elseif ($code == 1146 || stripos($message, "doesn't exist") !== false || stripos($message, "does not exist") !== false) {
        $userMessage = 'Database table not found.';
        $details = "Check TABLE_PREFIX in your .env file.\nMake sure LiteBans plugin has created the tables.\n\nOriginal error: {$message}";
    }
    elseif ($code == 2006 || $code == 2013 || stripos($message, 'gone away') !== false) {
        $userMessage = 'Database connection lost.';
        $details = "The connection to database was lost. Server may be overloaded.\n\nOriginal error: {$message}";
    }
    elseif ($code == 1040) {
        $userMessage = 'Too many database connections.';
        $details = "The database server has too many connections. Try again later.\n\nOriginal error: {$message}";
    }
    elseif (stripos($message, 'timeout') !== false) {
        $userMessage = 'Database connection timeout.';
        $details = "Connection to database timed out. Server may be slow or unreachable.\n\nOriginal error: {$message}";
    }
    
    return [
        'userMessage' => $userMessage,
        'details' => $details . "\n\nError code: {$code}\nFile: {$e->getFile()}\nLine: {$e->getLine()}"
    ];
}

// Session start
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
    'gc_maxlifetime' => (int)core\EnvLoader::get('SESSION_LIFETIME', 3600)
]);

// Security headers
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Paths
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
$basePath = ($scriptPath === '/' || $scriptPath === '\\') ? '' : $scriptPath;
define('BASE_PATH', $basePath);
define('BASE_DIR', __DIR__);

// Required files
$requiredFiles = [
    'config/app.php',
    'config/database.php',
    'core/SecurityManager.php',
    'core/LanguageManager.php',
    'core/ThemeManager.php',
    'core/DatabaseRepository.php',
    'core/RememberMeManager.php',
    'controllers/BaseController.php',
    'controllers/HomeController.php',
    'controllers/PunishmentsController.php',
    'controllers/DetailController.php',
    'controllers/StatsController.php',
    'controllers/AdminController.php',
    'controllers/ProtestController.php'
];

foreach ($requiredFiles as $file) {
    $fullPath = BASE_DIR . '/' . $file;
    if (!file_exists($fullPath)) {
        $logger->critical("Required file missing: {$file}");
        die(showErrorPage(500, 'System Error', 'Required system file missing: ' . $file));
    }
    require_once $fullPath;
}

// Load config
try {
    $config = require BASE_DIR . '/config/app.php';
    if (!is_array($config)) {
        throw new RuntimeException('Configuration file must return an array');
    }
} catch (Exception $e) {
    $logger->critical("Config load error: " . $e->getMessage());
    die(showErrorPage(500, 'Configuration Error', 'Failed to load configuration.', $e->getMessage()));
}

// Initialize database config - this can throw if .env has invalid values
try {
    $dbConfig = new DatabaseConfig();
    $config['db_name'] = $dbConfig->getDatabase();
    $config['db_driver'] = $dbConfig->getDriver();
} catch (InvalidArgumentException $e) {
    $logger->error("Database config error: " . $e->getMessage());
    die(showErrorPage(500, 'Configuration Error', $e->getMessage(), "Check your .env file for correct database settings."));
} catch (RuntimeException $e) {
    $logger->error("Database config error: " . $e->getMessage());
    die(showErrorPage(500, 'Configuration Error', $e->getMessage()));
}

// Set timezone
date_default_timezone_set($config['timezone'] ?? 'UTC');

// Helpers
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    $basePath = rtrim(BASE_PATH, '/');
    
    if ($path === '') {
        return $basePath ?: '/';
    }
    
    if ($basePath && strpos($path, ltrim($basePath, '/')) === 0) {
        return '/' . $path;
    }
    
    return $basePath . '/' . $path;
}

function asset(string $path): string {
    $path = ltrim($path, '/');
    $fullPath = BASE_DIR . '/' . $path;
    $version = file_exists($fullPath) ? '?v=' . substr(md5_file($fullPath), 0, 8) : '';
    return rtrim(BASE_PATH, '/') . '/' . $path . $version;
}

/**
 * Check if user is authenticated for require_login feature
 */
function isUserAuthenticated(): bool {
    if (!isset($_SESSION['admin_authenticated'])) {
        // Try remember me
        $rememberMe = new \core\RememberMeManager();
        $tokenData = $rememberMe->validateToken();
        
        if ($tokenData !== null) {
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_login_time'] = time();
            
            if ($tokenData['user_id'] === 'legacy') {
                $_SESSION['admin_user'] = 'Administrator';
            } else {
                require_once BASE_DIR . '/core/AuthManager.php';
                $config = require BASE_DIR . '/config/app.php';
                $authManager = new \core\AuthManager($config);
                $user = $authManager->getUserById($tokenData['user_id']);
                
                if ($user && ($user['active'] ?? true)) {
                    $_SESSION['admin_user'] = $user['name'];
                    $_SESSION['admin_user_id'] = $user['id'];
                } else {
                    $rememberMe->clearCurrentToken();
                    return false;
                }
            }
            
            \core\Logger::getInstance()->info('Session restored from remember me token');
            return true;
        }
        
        return false;
    }
    
    if (time() - ($_SESSION['admin_login_time'] ?? 0) > 7200) {
        unset($_SESSION['admin_authenticated']);
        unset($_SESSION['admin_user_id']);
        return false;
    }
    
    $_SESSION['admin_login_time'] = time();
    return true;
}

// Main application logic
try {
    // Language switch
    if (isset($_GET['lang'])) {
        $selectedLang = preg_replace('/[^a-z]/', '', substr($_GET['lang'], 0, 2));
        if (in_array($selectedLang, ['ar', 'cs', 'de', 'gr', 'en', 'es', 'fr', 'hu', 'it', 'ja', 'pl', 'ro', 'ru', 'sk', 'sr', 'tr', 'cn'])) {
            $_SESSION['selected_lang'] = $selectedLang;
            setcookie('selected_lang', $selectedLang, [
                'expires' => time() + 86400 * 30,
                'path' => BASE_PATH ?: '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        $cleanUrl = strtok($_SERVER['REQUEST_URI'], '?');
        header("Location: " . $cleanUrl);
        exit;
    }

    // Theme switch
    if (isset($_GET['theme'])) {
        $selectedTheme = preg_replace('/[^a-z]/', '', strtolower($_GET['theme']));
        if (in_array($selectedTheme, ['light', 'dark'])) {
            setcookie('selected_theme', $selectedTheme, [
                'expires' => time() + 86400 * 30,
                'path' => BASE_PATH ?: '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => false,
                'samesite' => 'Lax'
            ]);
            $_COOKIE['selected_theme'] = $selectedTheme;
        }
        $cleanUrl = $_SERVER['REQUEST_URI'];
        $cleanUrl = preg_replace('/[?&]theme=[^&]*/', '', $cleanUrl);
        $cleanUrl = str_replace('&&', '&', $cleanUrl);
        $cleanUrl = rtrim($cleanUrl, '?&');
        header("Location: " . $cleanUrl);
        exit;
    }

    // Database connection - with detailed error handling
    try {
        $connection = $dbConfig->createConnection();
        $repository = new DatabaseRepository($connection, $dbConfig->getTablePrefix());
        
        if (!$repository->testConnection()) {
            throw new RuntimeException('Database connection test failed');
        }
    } catch (PDOException $e) {
        $errorInfo = parseDatabaseError($e);
        $logger->databaseError($e, 'connection');
        die(showErrorPage(500, 'Database Error', $errorInfo['userMessage'], $errorInfo['details']));
    }

    // Init managers
    $lang = new LanguageManager(LanguageManager::detectLanguage());
    $theme = new ThemeManager();
    
    try {
        $stats = $repository->getStats();
    } catch (PDOException $e) {
        $logger->databaseError($e, 'getStats');
        $stats = ['bans' => 0, 'mutes' => 0, 'warnings' => 0, 'kicks' => 0];
    }

    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    if (!empty(BASE_PATH) && strpos($requestUri, BASE_PATH) === 0) {
        $requestUri = substr($requestUri, strlen(BASE_PATH));
    }
    $requestUri = '/' . trim($requestUri, '/');
    $route = explode('/', trim($requestUri, '/'));

    $GLOBALS['stats'] = $stats;
    $GLOBALS['config'] = $config;

    // Check require_login
    $requireLogin = $config['require_login'] ?? false;
    $isAdminRoute = str_starts_with($requestUri, '/admin');
    
    if ($requireLogin && !$isAdminRoute && !isUserAuthenticated()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        header("Location: " . url('admin'));
        exit;
    }

    // Routing
    if ($requestUri === '/' || $requestUri === '/index.php') {
        (new HomeController($repository, $lang, $theme, $config))->index();
    } elseif ($requestUri === '/search') {
        (new HomeController($repository, $lang, $theme, $config))->search();
    } elseif ($requestUri === '/bans') {
        (new PunishmentsController($repository, $lang, $theme, $config))->bans();
    } elseif ($requestUri === '/mutes') {
        (new PunishmentsController($repository, $lang, $theme, $config))->mutes();
    } elseif ($requestUri === '/warnings') {
        (new PunishmentsController($repository, $lang, $theme, $config))->warnings();
    } elseif ($requestUri === '/kicks') {
        (new PunishmentsController($repository, $lang, $theme, $config))->kicks();
    } elseif ($requestUri === '/stats' || $requestUri === '/statistics') {
        (new StatsController($repository, $lang, $theme, $config))->index();
    } elseif ($requestUri === '/stats/clear-cache' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        (new StatsController($repository, $lang, $theme, $config))->clearCache();
    } elseif ($requestUri === '/detail' || $requestUri === '/detail.php' || (isset($route[0]) && $route[0] === 'detail')) {
        (new DetailController($repository, $lang, $theme, $config))->show();
    } elseif ($requestUri === '/protest') {
        (new ProtestController($repository, $lang, $theme, $config))->index();
    } elseif ($requestUri === '/protest/submit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        (new ProtestController($repository, $lang, $theme, $config))->submit();
    } elseif (str_starts_with($requestUri, '/admin')) {
        if (!($config['admin_enabled'] ?? false)) {
            echo showErrorPage(403, 'Forbidden', 'Admin panel is disabled.');
            exit;
        }
        
        $admin = new AdminController($repository, $lang, $theme, $config);
        match ($requestUri) {
            '/admin'                    => $admin->index(),
            '/admin/login'              => $admin->login(),
            '/admin/logout'             => $admin->logout(),
            '/admin/keep-alive'         => $admin->keepAlive(),
            '/admin/clear-cache'        => $admin->clearCache(),
            '/admin/clear-all-cache'    => $admin->clearAllCache(),
            '/admin/test-database'      => $admin->testDatabase(),
            '/admin/check-github-version' => $admin->checkGitHubVersion(),
            '/admin/export'             => $admin->export(),
            '/admin/import'             => $admin->import(),
            '/admin/phpinfo'            => $admin->phpinfo(),
            '/admin/search-punishments' => $admin->searchPunishments(),
            '/admin/remove-punishment'  => $admin->removePunishment(),
            '/admin/modify-reason'      => $admin->modifyReason(),
            '/admin/save-settings'      => $admin->saveSettings(),
            '/admin/oauth-callback'     => $admin->oauthCallback(),
            '/admin/oauth-prepare'      => $admin->oauthPrepare(),
            '/admin/users'              => $admin->getUsers(),
            '/admin/users/add'          => $admin->addUser(),
            '/admin/users/update'       => $admin->updateUser(),
            '/admin/users/delete'       => $admin->deleteUser(),
            default                     => print showErrorPage(404, 'Not Found', 'The requested admin page does not exist.')
        };
    } else {
        $logger->debug('404 Not Found', ['uri' => $requestUri]);
        echo showErrorPage(404, 'Page Not Found', 'The requested page does not exist.');
    }

} catch (PDOException $e) {
    $errorInfo = parseDatabaseError($e);
    $logger->databaseError($e, 'runtime');
    echo showErrorPage(500, 'Database Error', $errorInfo['userMessage'], $errorInfo['details']);
    
} catch (Exception $e) {
    $logger->exception($e, 'Application error');
    $details = "Message: {$e->getMessage()}\nFile: {$e->getFile()}\nLine: {$e->getLine()}";
    echo showErrorPage(500, 'Server Error', 'An unexpected error occurred.', $details);
}
