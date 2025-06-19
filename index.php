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

declare(strict_types=1);

// Error handling and session setup
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

// Start session with secure settings
session_start([
    'cookie_httponly' => true,
    'cookie_secure' => isset($_SERVER['HTTPS']),
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true
]);

// Set headers
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// Character encoding
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Auto-detect base path
$scriptPath = dirname($_SERVER['SCRIPT_NAME']);
if ($scriptPath === '/' || $scriptPath === '\\') {
    $basePath = '';
} else {
    $basePath = $scriptPath;
}

define('BASE_PATH', $basePath);
define('BASE_DIR', __DIR__);

// Required files
$requiredFiles = [
    'config/database.php',
    'core/SecurityManager.php', 
    'core/LanguageManager.php',
    'core/ThemeManager.php',
    'core/DatabaseRepository.php',
    'core/BaseController.php',
    'controllers/HomeController.php',
    'controllers/PunishmentsController.php'
];

// Load required files
foreach ($requiredFiles as $file) {
    $fullPath = BASE_DIR . '/' . $file;
    if (!file_exists($fullPath)) {
        die(showErrorPage(500, 'System Error', 'Required system file missing: ' . $file));
    }
    require_once $fullPath;
}

// Load environment
if (file_exists(BASE_DIR . '/.env')) {
    $env = @parse_ini_file(BASE_DIR . '/.env', false, INI_SCANNER_TYPED);
    if ($env !== false) {
        foreach ($env as $key => $value) {
            $_ENV[$key] = $value;
        }
    }
}

// Configuration
$config = [
    'site_name' => $_ENV['SITE_NAME'] ?? 'LiteBansU',
    'items_per_page' => (int)($_ENV['ITEMS_PER_PAGE'] ?? 20),
    'timezone' => $_ENV['TIMEZONE'] ?? 'UTC',
    'date_format' => $_ENV['DATE_FORMAT'] ?? 'Y-m-d H:i:s',
    'avatar_url' => $_ENV['AVATAR_URL'] ?? 'https://crafatar.com/avatars/{uuid}?size=32&overlay=true',
    'avatar_url_offline' => $_ENV['AVATAR_URL_OFFLINE'] ?? 'https://minotar.net/avatar/{name}/32',
    'base_path' => BASE_PATH,
    'debug' => ($_ENV['DEBUG'] ?? 'false') === 'true',
    
    // SEO Configuration
    'site_url' => $_ENV['SITE_URL'] ?? 'https://',
    'site_lang' => $_ENV['SITE_LANG'] ?? 'en',
    'site_charset' => $_ENV['SITE_CHARSET'] ?? 'UTF-8',
    'site_viewport' => $_ENV['SITE_VIEWPORT'] ?? 'width=device-width, initial-scale=1.0',
    'site_robots' => $_ENV['SITE_ROBOTS'] ?? 'index, follow',
    'site_description' => $_ENV['SITE_DESCRIPTION'] ?? 'Public interface for viewing server punishments and bans',
    'site_title_template' => $_ENV['SITE_TITLE_TEMPLATE'] ?? '{page} - {site}',
    'site_favicon' => $_ENV['SITE_FAVICON'] ?? 'favicon.ico',
    'site_apple_icon' => $_ENV['SITE_APPLE_ICON'] ?? 'apple-touch-icon.png',
    'site_theme_color' => $_ENV['SITE_THEME_COLOR'] ?? '#6366f1',
    'site_og_image' => $_ENV['SITE_OG_IMAGE'] ?? null,
    'site_twitter_site' => $_ENV['SITE_TWITTER_SITE'] ?? null,
    'site_keywords' => $_ENV['SITE_KEYWORDS'] ?? null,
    'site_author' => $_ENV['SITE_AUTHOR'] ?? null,
    'site_generator' => $_ENV['SITE_GENERATOR'] ?? 'LitebansU'
];

// Set timezone
date_default_timezone_set($config['timezone']);

// URL helper
function url(string $path = ''): string {
    $path = ltrim($path, '/');
    $basePath = BASE_PATH;
    return $path === '' ? ($basePath ?: '/') : rtrim($basePath, '/') . '/' . $path;
}

// Asset helper with cache busting
function asset(string $path): string {
    $path = ltrim($path, '/');
    $fullPath = BASE_DIR . '/' . $path;
    $version = '';
    
    if (file_exists($fullPath)) {
        $version = '?v=' . substr(md5_file($fullPath), 0, 8);
    }
    
    return rtrim(BASE_PATH, '/') . '/' . $path . $version;
}

// Error page function
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
    <title>{$code} - {$safeTitle}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-card { 
            background: rgba(255,255,255,0.95); 
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card error-card">
                    <div class="card-body text-center p-5">
                        <h1 class="display-1 text-primary mb-4">{$code}</h1>
                        <h2 class="mb-3">{$safeTitle}</h2>
                        <p class="lead text-muted mb-4">{$safeMessage}</p>
                        <a href="{$homeUrl}" class="btn btn-primary btn-lg">
                            <i class="fas fa-home"></i> Go Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
HTML;
}

try {
    // Handle language switching
    if (isset($_GET['lang'])) {
        $selectedLang = preg_replace('/[^a-z]/', '', substr($_GET['lang'], 0, 2));
        if (in_array($selectedLang, ['en', 'sk', 'ru', 'de', 'es', 'fr'])) {
            $_SESSION['selected_lang'] = $selectedLang;
            setcookie('selected_lang', $selectedLang, [
                'expires' => time() + (86400 * 30),
                'path' => BASE_PATH ?: '/',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
        }
        
        // Redirect to clean URL
        $cleanUrl = strtok($_SERVER['REQUEST_URI'], '?');
        $existingParams = $_GET;
        unset($existingParams['lang']);
        
        if (!empty($existingParams)) {
            $cleanUrl .= '?' . http_build_query($existingParams);
        }
        
        header("Location: $cleanUrl");
        exit;
    }
    
    // Handle theme switching - FIXED VERSION
    if (isset($_GET['theme'])) {
        $selectedTheme = preg_replace('/[^a-z]/', '', strtolower($_GET['theme']));
        if (in_array($selectedTheme, ['light', 'dark', 'auto'])) {
            $cookieOptions = [
                'expires' => time() + (86400 * 30), // 30 days
                'path' => BASE_PATH ?: '/',
                'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
                'httponly' => false, // Allow JavaScript access for immediate theme switching
                'samesite' => 'Lax'
            ];
            
            setcookie('selected_theme', $selectedTheme, $cookieOptions);
            
            // Also set in $_COOKIE for immediate availability
            $_COOKIE['selected_theme'] = $selectedTheme;
            
            if ($config['debug']) {
                error_log("Theme switched to: {$selectedTheme}, Cookie path: " . ($cookieOptions['path'] ?? '/'));
            }
        }
        
        // Redirect to clean URL (remove theme parameter)
        $cleanUrl = strtok($_SERVER['REQUEST_URI'], '?');
        $existingParams = $_GET;
        unset($existingParams['theme']);
        
        if (!empty($existingParams)) {
            $cleanUrl .= '?' . http_build_query($existingParams);
        }
        
        header("Location: $cleanUrl");
        exit;
    }
    
    // Initialize database
    $dbConfig = new DatabaseConfig();
    $connection = $dbConfig->createConnection();
    $repository = new DatabaseRepository($connection, $dbConfig->getTablePrefix());
    
    // Test database connection
    if (!$repository->testConnection()) {
        throw new RuntimeException('Database connection test failed');
    }
    
    // Initialize managers
    $lang = new LanguageManager(LanguageManager::detectLanguage());
    $theme = new ThemeManager();
    
    // Get stats for navigation
    $stats = $repository->getStats();
    
    // Parse request
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
    $requestUri = parse_url($requestUri, PHP_URL_PATH) ?? '/';
    
    // Remove base path from request
    if (!empty(BASE_PATH) && strpos($requestUri, BASE_PATH) === 0) {
        $requestUri = substr($requestUri, strlen(BASE_PATH));
    }
    
    $requestUri = '/' . trim($requestUri, '/');
    
    // Determine current page
    $currentPage = match($requestUri) {
        '/', '/index.php' => 'home',
        '/bans' => 'bans',
        '/mutes' => 'mutes', 
        '/warnings' => 'warnings',
        '/kicks' => 'kicks',
        '/search' => 'search',
        default => ''
    };
    
    // Set globals for templates
    $GLOBALS['currentPage'] = $currentPage;
    $GLOBALS['stats'] = $stats;
    $GLOBALS['config'] = $config;
    
    // Route handling
    switch ($requestUri) {
        case '/':
        case '/index.php':
            $controller = new HomeController($repository, $lang, $theme, $config);
            $controller->index();
            break;
            
        case '/search':
            $controller = new HomeController($repository, $lang, $theme, $config);
            $controller->search();
            break;
            
        case '/bans':
            $controller = new PunishmentsController($repository, $lang, $theme, $config);
            $controller->bans();
            break;
            
        case '/mutes':
            $controller = new PunishmentsController($repository, $lang, $theme, $config);
            $controller->mutes();
            break;
            
        case '/warnings':
            $controller = new PunishmentsController($repository, $lang, $theme, $config);
            $controller->warnings();
            break;
            
        case '/kicks':
            $controller = new PunishmentsController($repository, $lang, $theme, $config);
            $controller->kicks();
            break;
            
        default:
            // Check for punishment info routes
            if (preg_match('/^\/info\/(bans|mutes|warnings|kicks)\/(\d+)$/', $requestUri, $matches)) {
                $_GET['type'] = $matches[1];
                $_GET['id'] = $matches[2];
                $controller = new PunishmentsController($repository, $lang, $theme, $config);
                $controller->info();
            } else {
                echo showErrorPage(404, 'Page Not Found', 'The page you are looking for does not exist.');
            }
            break;
    }
    
} catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo showErrorPage(500, 'Database Error', 'Unable to connect to the database. Please check your configuration.');
} catch (Exception $e) {
    error_log("Application error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    if ($config['debug'] ?? false) {
        echo "<pre>Debug Error:\n";
        echo "Message: " . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') . "\n";
        echo "File: " . htmlspecialchars($e->getFile(), ENT_QUOTES, 'UTF-8') . "\n";
        echo "Line: " . htmlspecialchars((string)$e->getLine(), ENT_QUOTES, 'UTF-8') . "\n";
        echo "Trace:\n" . htmlspecialchars($e->getTraceAsString(), ENT_QUOTES, 'UTF-8');
        echo "</pre>";
    } else {
        echo showErrorPage(500, 'Server Error', 'An unexpected error occurred. Please try again later.');
    }
}