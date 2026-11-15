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

class SecurityManager
{
    private const UUID_PATTERN = '/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i';
    private const USERNAME_PATTERN = '/^[a-zA-Z0-9_]{1,16}$/';
    private const MAX_INPUT_LENGTH = 255;
    private const RATE_LIMIT_PREFIX = 'rate_limit_';
    
    public static function validateUuid(string $uuid): bool
    {
        return preg_match(self::UUID_PATTERN, $uuid) === 1;
    }
    
    public static function validateUsername(string $username): bool
    {
        return preg_match(self::USERNAME_PATTERN, $username) === 1;
    }
    
    public static function sanitizeInput(string $input): string
    {
        $input = trim($input);
        
        if (strlen($input) > self::MAX_INPUT_LENGTH) {
            $input = substr($input, 0, self::MAX_INPUT_LENGTH);
        }
        
        $input = str_replace("\0", '', $input);
        
        $input = preg_replace('/\s+/', ' ', $input);
        
        return htmlspecialchars($input, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
    
    public static function validateInteger(mixed $value, int $min = 0, int $max = PHP_INT_MAX): int
    {
        $int = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => $min,
                'max_range' => $max
            ]
        ]);
        
        if ($int === false) {
            throw new InvalidArgumentException('Invalid integer value');
        }
        
        return $int;
    }
    
    public static function generateCsrfToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token']) || !isset($_SESSION['csrf_token_time'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        if (time() - $_SESSION['csrf_token_time'] > 3600) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            $_SESSION['csrf_token_time'] = time();
        }
        
        return $_SESSION['csrf_token'];
    }
    
    public static function validateCsrfToken(string $token): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            return false;
        }
        
        if (!isset($_SESSION['csrf_token_time']) || time() - $_SESSION['csrf_token_time'] > 3600) {
            return false;
        }
        
        return hash_equals($_SESSION['csrf_token'], $token);
    }
    
    public static function preventXss(string $text): string
    {
        $text = preg_replace('/[§&][0-9a-fk-or]/ui', '', $text);
        
        $text = preg_replace('/#[0-9a-f]{6}/i', '', $text);
        
        $text = htmlspecialchars($text, ENT_QUOTES | ENT_HTML5, 'UTF-8', false);
        
        $text = nl2br($text);
        
        return $text;
    }
    
    public static function rateLimitCheck(string $identifier, int $maxRequests = 60, int $timeWindow = 3600): bool
    {
        $key = self::RATE_LIMIT_PREFIX . md5($identifier);
        
        if (function_exists('apcu_enabled') && apcu_enabled()) {
            $current = apcu_fetch($key) ?: 0;
            
            if ($current >= $maxRequests) {
                return false;
            }
            
            apcu_store($key, $current + 1, $timeWindow);
            return true;
        }
        
        $cacheDir = sys_get_temp_dir() . '/litebans_cache';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0700, true);
        }
        
        $file = $cacheDir . '/' . $key;
        $data = ['count' => 0, 'time' => time()];
        
        if (file_exists($file)) {
            $content = @file_get_contents($file);
            if ($content !== false) {
                $data = @json_decode($content, true) ?: $data;
            }
        }
        
        if (time() - $data['time'] > $timeWindow) {
            $data = ['count' => 0, 'time' => time()];
        }
        
        if ($data['count'] >= $maxRequests) {
            return false;
        }
        
        $data['count']++;
        @file_put_contents($file, json_encode($data), LOCK_EX);
        
        return true;
    }
    
    public static function validateRequestMethod(string $expected): bool
    {
        return $_SERVER['REQUEST_METHOD'] === strtoupper($expected);
    }
    
    public static function getClientIp(): string
    {
        $remote = $_SERVER['REMOTE_ADDR'] ?? '';
        $trusted = array_filter(array_map('trim', explode(',', (string)\core\EnvLoader::get('TRUSTED_PROXIES', ''))));

        if ($remote !== '' && in_array($remote, $trusted, true)) {
            foreach (['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP'] as $header) {
                $value = isset($_SERVER[$header]) ? trim(substr(strrchr(',' . $_SERVER[$header], ','), 1)) : '';
                if ($value !== '' && filter_var($value, FILTER_VALIDATE_IP)) {
                    return $value;
                }
            }
        }

        return filter_var($remote, FILTER_VALIDATE_IP) ? $remote : '0.0.0.0';
    }
    
    public static function generateRandomString(int $length = 32): string
    {
        return bin2hex(random_bytes($length / 2));
    }
}
