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

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Load environment
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
    
    // Add database config to main config
    $dbConfig = new DatabaseConfig();
    $config['db_name'] = $dbConfig->getDatabase();
    $config['db_driver'] = $dbConfig->getDriver();
    
} catch (Exception $e) {
    die(showErrorPage(500, 'Configuration Error', 'Failed to load configuration: ' . $e->getMessage()));
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
    
    // Prevent duplicate base paths
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

function showErrorPage(int $code, string $title, string $message): string {
    http_response_code($code);
    $safeTitle = htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
    $safeMessage = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');
    $homeUrl = htmlspecialchars(url(), ENT_QUOTES, 'UTF-8');

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="generator" content="LitebansU">
    <meta name="author" content="Yamiru">
    <title>{$code} - {$safeTitle}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .error-card { background: rgba(255,255,255,0.95); backdrop-filter: blur(10px); border-radius: 1rem; box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }
        .copyright-footer { position: fixed; bottom: 0; left: 0; right: 0; background: rgba(0,0,0,0.8); color: white; text-align: center; padding: 0.5rem; font-size: 0.8rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card error-card">
                    <div class="card-body text-center p-5">
                        <h1 class="display-1 text-danger mb-4">{$code}</h1>
                        <h2 class="mb-3">{$safeTitle}</h2>
                        <p class="lead text-muted mb-4">{$safeMessage}</p>
                        <a href="{$homeUrl}" class="btn btn-danger btn-lg"><i class="fas fa-home"></i> Go Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="copyright-footer" data-copyright="litebansU">
        Powered by <strong>LitebansU</strong> by <strong>Yamiru</strong> - MIT License
    </div>
</body>
</html>
HTML;
}

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

    // Theme switch - Fixed to properly handle theme changes
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
        // Remove theme parameter from URL but keep other parameters
        $cleanUrl = $_SERVER['REQUEST_URI'];
        $cleanUrl = preg_replace('/[?&]theme=[^&]*/', '', $cleanUrl);
        $cleanUrl = str_replace('&&', '&', $cleanUrl);
        $cleanUrl = rtrim($cleanUrl, '?&');
        header("Location: " . $cleanUrl);
        exit;
    }

    // DB init
    $connection = $dbConfig->createConnection();
    $repository = new DatabaseRepository($connection, $dbConfig->getTablePrefix());
    if (!$repository->testConnection()) {
        throw new RuntimeException('Database connection test failed');
    }

    // Init managers
    $lang = new LanguageManager(LanguageManager::detectLanguage());
    $theme = new ThemeManager();
    $stats = $repository->getStats();

    $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    if (!empty(BASE_PATH) && strpos($requestUri, BASE_PATH) === 0) {
        $requestUri = substr($requestUri, strlen(BASE_PATH));
    }
    $requestUri = '/' . trim($requestUri, '/');
    $route = explode('/', trim($requestUri, '/'));

    $GLOBALS['stats'] = $stats;
    $GLOBALS['config'] = $config;

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
        // Check if admin is enabled first
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
            '/admin/clear-cache'        => $admin->clearCache(),  // Added clear-cache endpoint
            '/admin/export'             => $admin->export(),
            '/admin/import'             => $admin->import(),
            '/admin/phpinfo'            => $admin->phpinfo(),
            '/admin/search-punishments' => $admin->searchPunishments(),
            '/admin/remove-punishment'  => $admin->removePunishment(),
            '/admin/modify-reason'      => $admin->modifyReason(),
            '/admin/save-settings'      => $admin->saveSettings(),
            '/admin/oauth-callback'     => $admin->oauthCallback(),
            '/admin/users'              => $admin->getUsers(),
            '/admin/users/add'          => $admin->addUser(),
            '/admin/users/update'       => $admin->updateUser(),
            '/admin/users/delete'       => $admin->deleteUser(),
            default                     => print showErrorPage(404, 'Admin Page Not Found', 'The requested admin page does not exist.')
        };
    } else {
        // Handle 404
        echo showErrorPage(404, 'Page Not Found', 'The requested page does not exist.');
    }
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo showErrorPage(500, 'Database Error', 'Unable to connect to the database. Please check your configuration.');
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    if ($config['debug'] ?? false) {
        echo "<pre>Debug Error:\nMessage: " . htmlspecialchars($e->getMessage()) . "\nFile: " . htmlspecialchars($e->getFile()) . "\nLine: " . $e->getLine() . "\nTrace:\n" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    } else {
        echo showErrorPage(500, 'Server Error', 'An unexpected error occurred. Please try again later.');
    }
}
