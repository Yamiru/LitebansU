<?php
/**
 * ============================================================================
 *  LiteBansU - Remember Me Manager
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   Secure persistent login (Remember Me) functionality
 *  Version:       3.8
 *  License:       MIT
 * ============================================================================
 */

declare(strict_types=1);

namespace core;

class RememberMeManager
{
    private const COOKIE_NAME = 'litebans_remember';
    private const TOKEN_EXPIRY_DAYS = 30;
    private const HASH_ALGO = 'sha256';
    
    private string $dataFile;
    private string $encryptionKey;
    private array $tokens = [];
    
    public function __construct()
    {
        $this->dataFile = dirname(__DIR__) . '/data/remember_tokens.dat';
        $this->encryptionKey = $this->getEncryptionKey();
        $this->ensureDataDirectory();
        $this->loadTokens();
        $this->cleanupExpiredTokens();
    }
    
    private function getEncryptionKey(): string
    {
        $keyFile = dirname(__DIR__) . '/data/.key';
        
        if (file_exists($keyFile)) {
            return file_get_contents($keyFile);
        }
        
        $key = hash(self::HASH_ALGO, 
            ($_SERVER['SERVER_NAME'] ?? 'localhost') . 
            ($_SERVER['DOCUMENT_ROOT'] ?? __DIR__) .
            php_uname() .
            random_bytes(32)
        );
        
        $this->ensureDataDirectory();
        file_put_contents($keyFile, $key);
        chmod($keyFile, 0600);
        
        return $key;
    }
    
    private function ensureDataDirectory(): void
    {
        $dataDir = dirname(__DIR__) . '/data';
        
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0700, true);
        }
    }
    
    private function loadTokens(): void
    {
        if (!file_exists($this->dataFile)) {
            $this->tokens = [];
            return;
        }
        
        $content = @file_get_contents($this->dataFile);
        if (empty($content)) {
            $this->tokens = [];
            return;
        }
        
        $decrypted = $this->decrypt($content);
        if ($decrypted === null) {
            $this->tokens = [];
            return;
        }
        
        $this->tokens = json_decode($decrypted, true) ?? [];
    }
    
    private function saveTokens(): bool
    {
        $json = json_encode($this->tokens, JSON_PRETTY_PRINT);
        $encrypted = $this->encrypt($json);
        
        $result = @file_put_contents($this->dataFile, $encrypted, LOCK_EX);
        if ($result !== false) {
            @chmod($this->dataFile, 0600);
        }
        
        return $result !== false;
    }
    
    private function encrypt(string $data): string
    {
        $iv = random_bytes(openssl_cipher_iv_length('AES-256-CBC'));
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $this->encryptionKey, 0, $iv);
        $hmac = hash_hmac(self::HASH_ALGO, $encrypted, $this->encryptionKey, true);
        
        return base64_encode($iv . $hmac . $encrypted);
    }
    
    private function decrypt(string $data): ?string
    {
        $data = base64_decode($data);
        if ($data === false) {
            return null;
        }
        
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($data, 0, $ivLength);
        $hmac = substr($data, $ivLength, 32);
        $encrypted = substr($data, $ivLength + 32);
        
        $calcHmac = hash_hmac(self::HASH_ALGO, $encrypted, $this->encryptionKey, true);
        if (!hash_equals($hmac, $calcHmac)) {
            return null;
        }
        
        return openssl_decrypt($encrypted, 'AES-256-CBC', $this->encryptionKey, 0, $iv);
    }
    
    public function createToken(string $userId, string $provider = 'password'): bool
    {
        $selector = bin2hex(random_bytes(16));
        $validator = bin2hex(random_bytes(32));
        $validatorHash = hash(self::HASH_ALGO, $validator);
        
        $expiry = time() + (self::TOKEN_EXPIRY_DAYS * 24 * 60 * 60);
        
        $this->tokens[$selector] = [
            'user_id' => $userId,
            'validator_hash' => $validatorHash,
            'provider' => $provider,
            'expiry' => $expiry,
            'created_at' => time(),
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? 'unknown', 0, 255)
        ];
        
        if (!$this->saveTokens()) {
            return false;
        }
        
        $cookieValue = $selector . ':' . $validator;
        $basePath = defined('BASE_PATH') ? (BASE_PATH ?: '/') : '/';
        
        return setcookie(self::COOKIE_NAME, $cookieValue, [
            'expires' => $expiry,
            'path' => $basePath,
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    public function validateToken(): ?array
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return null;
        }
        
        $cookie = $_COOKIE[self::COOKIE_NAME];
        $parts = explode(':', $cookie);
        
        if (count($parts) !== 2) {
            $this->clearCookie();
            return null;
        }
        
        [$selector, $validator] = $parts;
        
        if (!isset($this->tokens[$selector])) {
            $this->clearCookie();
            return null;
        }
        
        $token = $this->tokens[$selector];
        
        if (time() > $token['expiry']) {
            unset($this->tokens[$selector]);
            $this->saveTokens();
            $this->clearCookie();
            return null;
        }
        
        $validatorHash = hash(self::HASH_ALGO, $validator);
        if (!hash_equals($token['validator_hash'], $validatorHash)) {
            $this->invalidateUserTokens($token['user_id']);
            $this->clearCookie();
            return null;
        }
        
        $this->rotateToken($selector, $token['user_id'], $token['provider']);
        
        return [
            'user_id' => $token['user_id'],
            'provider' => $token['provider']
        ];
    }
    
    private function rotateToken(string $selector, string $userId, string $provider): void
    {
        $newValidator = bin2hex(random_bytes(32));
        $validatorHash = hash(self::HASH_ALGO, $newValidator);
        
        $expiry = time() + (self::TOKEN_EXPIRY_DAYS * 24 * 60 * 60);
        
        $this->tokens[$selector]['validator_hash'] = $validatorHash;
        $this->tokens[$selector]['expiry'] = $expiry;
        $this->tokens[$selector]['rotated_at'] = time();
        
        $this->saveTokens();
        
        $cookieValue = $selector . ':' . $newValidator;
        $basePath = defined('BASE_PATH') ? (BASE_PATH ?: '/') : '/';
        
        setcookie(self::COOKIE_NAME, $cookieValue, [
            'expires' => $expiry,
            'path' => $basePath,
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    
    public function invalidateUserTokens(string $userId): void
    {
        foreach ($this->tokens as $selector => $token) {
            if ($token['user_id'] === $userId) {
                unset($this->tokens[$selector]);
            }
        }
        $this->saveTokens();
    }
    
    public function clearCookie(): void
    {
        $basePath = defined('BASE_PATH') ? (BASE_PATH ?: '/') : '/';
        
        setcookie(self::COOKIE_NAME, '', [
            'expires' => time() - 3600,
            'path' => $basePath,
            'secure' => isset($_SERVER['HTTPS']),
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
        
        unset($_COOKIE[self::COOKIE_NAME]);
    }
    
    public function clearCurrentToken(): void
    {
        if (!isset($_COOKIE[self::COOKIE_NAME])) {
            return;
        }
        
        $cookie = $_COOKIE[self::COOKIE_NAME];
        $parts = explode(':', $cookie);
        
        if (count($parts) === 2) {
            $selector = $parts[0];
            if (isset($this->tokens[$selector])) {
                unset($this->tokens[$selector]);
                $this->saveTokens();
            }
        }
        
        $this->clearCookie();
    }
    
    private function cleanupExpiredTokens(): void
    {
        $now = time();
        $changed = false;
        
        foreach ($this->tokens as $selector => $token) {
            if ($now > $token['expiry']) {
                unset($this->tokens[$selector]);
                $changed = true;
            }
        }
        
        if ($changed) {
            $this->saveTokens();
        }
    }
    
    public function getUserTokens(string $userId): array
    {
        $userTokens = [];
        
        foreach ($this->tokens as $selector => $token) {
            if ($token['user_id'] === $userId) {
                $userTokens[] = [
                    'selector' => substr($selector, 0, 8) . '...',
                    'provider' => $token['provider'],
                    'created_at' => $token['created_at'],
                    'expiry' => $token['expiry'],
                    'ip' => $token['ip'] ?? 'unknown',
                    'user_agent' => $token['user_agent'] ?? 'unknown'
                ];
            }
        }
        
        return $userTokens;
    }
    
    public function hasCookie(): bool
    {
        return isset($_COOKIE[self::COOKIE_NAME]);
    }
}
