<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       2.0
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

declare(strict_types=1);

namespace core;

class EnvLoader
{
    private static bool $loaded = false;
    
    public static function load(string $path = null): void
    {
        if (self::$loaded) {
            return;
        }
        
        $envFile = $path ?? dirname(__DIR__) . '/.env';
        
        if (!file_exists($envFile)) {
            throw new \RuntimeException('.env file not found at: ' . $envFile);
        }
        
        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse key=value
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if ((substr($value, 0, 1) === '"' && substr($value, -1) === '"') ||
                    (substr($value, 0, 1) === "'" && substr($value, -1) === "'")) {
                    $value = substr($value, 1, -1);
                }
                
                // Set environment variable
                $_ENV[$key] = $value;
                
                // Also set in $_SERVER for compatibility
                $_SERVER[$key] = $value;
                
                // Set as actual environment variable
                putenv("$key=$value");
            }
        }
        
        self::$loaded = true;
    }
    
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key) ?: $default;
    }
}