<?php
/**
 * ============================================================================
 *  LiteBansU - Authentication Manager
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   Google OAuth and user management for admin panel
 *  Version:       3.6
 *  License:       MIT
 * ============================================================================
 */

declare(strict_types=1);

namespace core;

class AuthManager
{
    private string $dataFile;
    private string $encryptionKey;
    private array $users = [];
    private array $config;
    
    private const CIPHER_METHOD = 'AES-256-CBC';
    private const HASH_ALGO = 'sha256';
    
    public function __construct(array $config)
    {
        $this->config = $config;
        $this->dataFile = dirname(__DIR__) . '/data/users.dat';
        $this->encryptionKey = $this->generateEncryptionKey();
        $this->ensureDataDirectory();
        $this->loadUsers();
    }
    
    /**
     * Generate a unique encryption key based on server-specific data
     */
    private function generateEncryptionKey(): string
    {
        $keyFile = dirname(__DIR__) . '/data/.key';
        
        if (file_exists($keyFile)) {
            return file_get_contents($keyFile);
        }
        
        // Generate a new key based on multiple factors
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
    
    /**
     * Ensure data directory exists and is protected
     */
    private function ensureDataDirectory(): void
    {
        $dataDir = dirname(__DIR__) . '/data';
        
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0700, true);
        }
        
        // Create .htaccess to protect directory
        $htaccess = $dataDir . '/.htaccess';
        if (!file_exists($htaccess)) {
            file_put_contents($htaccess, "Order deny,allow\nDeny from all\n");
        }
        
        // Create index.php to prevent directory listing
        $index = $dataDir . '/index.php';
        if (!file_exists($index)) {
            file_put_contents($index, "<?php http_response_code(403); exit('Forbidden');");
        }
    }
    
    /**
     * Encrypt data
     */
    private function encrypt(string $data): string
    {
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER_METHOD));
        $encrypted = openssl_encrypt($data, self::CIPHER_METHOD, $this->encryptionKey, 0, $iv);
        $hmac = hash_hmac(self::HASH_ALGO, $encrypted, $this->encryptionKey, true);
        
        return base64_encode($iv . $hmac . $encrypted);
    }
    
    /**
     * Decrypt data
     */
    private function decrypt(string $data): ?string
    {
        $data = base64_decode($data);
        if ($data === false) {
            return null;
        }
        
        $ivLength = openssl_cipher_iv_length(self::CIPHER_METHOD);
        $iv = substr($data, 0, $ivLength);
        $hmac = substr($data, $ivLength, 32);
        $encrypted = substr($data, $ivLength + 32);
        
        // Verify HMAC
        $calcHmac = hash_hmac(self::HASH_ALGO, $encrypted, $this->encryptionKey, true);
        if (!hash_equals($hmac, $calcHmac)) {
            return null; // Data tampered
        }
        
        return openssl_decrypt($encrypted, self::CIPHER_METHOD, $this->encryptionKey, 0, $iv);
    }
    
    /**
     * Load users from encrypted file
     */
    private function loadUsers(): void
    {
        if (!file_exists($this->dataFile)) {
            $this->users = [];
            return;
        }
        
        $encryptedData = file_get_contents($this->dataFile);
        if (empty($encryptedData)) {
            $this->users = [];
            return;
        }
        
        $decrypted = $this->decrypt($encryptedData);
        if ($decrypted === null) {
            error_log("AuthManager: Failed to decrypt users data - possible tampering");
            $this->users = [];
            return;
        }
        
        $this->users = json_decode($decrypted, true) ?? [];
    }
    
    /**
     * Save users to encrypted file
     */
    private function saveUsers(): bool
    {
        $json = json_encode($this->users, JSON_PRETTY_PRINT);
        $encrypted = $this->encrypt($json);
        
        $result = file_put_contents($this->dataFile, $encrypted, LOCK_EX);
        if ($result !== false) {
            chmod($this->dataFile, 0600);
        }
        
        return $result !== false;
    }
    
    /**
     * Check if Google Auth is enabled
     */
    public function isGoogleAuthEnabled(): bool
    {
        return ($this->config['google_auth_enabled'] ?? false) === true
            && !empty($this->config['google_client_id'])
            && !empty($this->config['google_client_secret']);
    }
    
    /**
     * Check if Discord Auth is enabled
     */
    public function isDiscordAuthEnabled(): bool
    {
        return ($this->config['discord_auth_enabled'] ?? false) === true
            && !empty($this->config['discord_client_id'])
            && !empty($this->config['discord_client_secret']);
    }
    
    /**
     * Get Google OAuth URL
     */
    public function getGoogleAuthUrl(): string
    {
        $clientId = $this->config['google_client_id'] ?? '';
        $redirectUri = $this->getRedirectUri();
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online',
            'prompt' => 'select_account'
        ];
        
        return 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query($params);
    }
    
    /**
     * Get redirect URI for OAuth
     */
    public function getRedirectUri(string $provider = 'google'): string
    {
        // Automatically detect the full base URL including subfolder
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $scriptPath = dirname($_SERVER['SCRIPT_NAME']);
        $basePath = ($scriptPath === '/' || $scriptPath === '\\') ? '' : $scriptPath;
        
        // Construct full base URL
        $baseUrl = $protocol . '://' . $host . $basePath;
        
        // For Google, use URI without provider param (backward compatibility)
        // For Discord, add provider param
        if ($provider === 'discord') {
            return $baseUrl . '/admin/oauth-callback?provider=discord';
        }
        
        return $baseUrl . '/admin/oauth-callback';
    }
    
    /**
     * Get Discord OAuth URL
     */
    public function getDiscordAuthUrl(): string
    {
        $clientId = $this->config['discord_client_id'] ?? '';
        $redirectUri = $this->getRedirectUri('discord');
        
        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'identify email'
        ];
        
        return 'https://discord.com/api/oauth2/authorize?' . http_build_query($params);
    }
    
    /**
     * Process Google OAuth callback
     */
    public function processGoogleCallback(string $code): ?array
    {
        $clientId = $this->config['google_client_id'] ?? '';
        $clientSecret = $this->config['google_client_secret'] ?? '';
        $redirectUri = $this->getRedirectUri('google');
        
        // Exchange code for token
        $tokenUrl = 'https://oauth2.googleapis.com/token';
        $tokenData = [
            'code' => $code,
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code'
        ];
        
        $ch = curl_init($tokenUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($tokenData),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            error_log("Google OAuth token exchange failed: HTTP $httpCode");
            return null;
        }
        
        $tokenInfo = json_decode($response, true);
        if (!isset($tokenInfo['access_token'])) {
            error_log("Google OAuth: No access token in response");
            return null;
        }
        
        // Get user info
        $userInfoUrl = 'https://www.googleapis.com/oauth2/v2/userinfo';
        $ch = curl_init($userInfoUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokenInfo['access_token']],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            error_log("Google OAuth user info failed: HTTP $httpCode");
            return null;
        }
        
        $userInfo = json_decode($response, true);
        if (!isset($userInfo['id']) || !isset($userInfo['email'])) {
            error_log("Google OAuth: Invalid user info response");
            return null;
        }
        
        return $userInfo;
    }
    
    /**
     * Process Discord OAuth callback
     */
    public function processDiscordCallback(string $code): ?array
    {
        $clientId = $this->config['discord_client_id'] ?? '';
        $clientSecret = $this->config['discord_client_secret'] ?? '';
        $redirectUri = $this->getRedirectUri('discord');
        
        // Exchange code for token
        $tokenUrl = 'https://discord.com/api/oauth2/token';
        $tokenData = [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $redirectUri
        ];
        
        $ch = curl_init($tokenUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($tokenData),
            CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            error_log("Discord OAuth token exchange failed: HTTP $httpCode");
            return null;
        }
        
        $tokenInfo = json_decode($response, true);
        if (!isset($tokenInfo['access_token'])) {
            error_log("Discord OAuth: No access token in response");
            return null;
        }
        
        // Get user info
        $userInfoUrl = 'https://discord.com/api/users/@me';
        $ch = curl_init($userInfoUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $tokenInfo['access_token']],
            CURLOPT_TIMEOUT => 30
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode !== 200 || !$response) {
            error_log("Discord OAuth user info failed: HTTP $httpCode");
            return null;
        }
        
        $userInfo = json_decode($response, true);
        if (!isset($userInfo['id']) || !isset($userInfo['email'])) {
            error_log("Discord OAuth: Invalid user info response");
            return null;
        }
        
        return $userInfo;
    }
    
    /**
     * Authenticate user via Discord
     */
    public function authenticateDiscord(array $discordUser): ?array
    {
        $discordId = $discordUser['id'];
        $email = $discordUser['email'];
        $username = $discordUser['username'] ?? $email;
        $discriminator = $discordUser['discriminator'] ?? '';
        $name = $discriminator !== '0' ? $username . '#' . $discriminator : $username;
        $avatar = '';
        
        if (isset($discordUser['avatar']) && !empty($discordUser['avatar'])) {
            $avatar = sprintf(
                'https://cdn.discordapp.com/avatars/%s/%s.png',
                $discordId,
                $discordUser['avatar']
            );
        }
        
        // Check if user exists
        $user = $this->getUserByDiscordId($discordId);
        
        if (!$user) {
            // Check if this is the first user
            $isFirstUser = empty($this->users);
            
            // Check if user is allowed (either first user or already registered)
            if (!$isFirstUser) {
                // Check if email is pre-registered
                $user = $this->getUserByEmail($email);
                if (!$user) {
                    return null; // Not authorized
                }
                
                // Update existing user with Discord ID
                $user['discord_id'] = $discordId;
                $user['picture'] = $avatar;
                $user['last_login'] = time();
                $this->updateUser($user['id'], $user);
            } else {
                // Create first admin user
                $user = $this->createUser([
                    'discord_id' => $discordId,
                    'email' => $email,
                    'name' => $name,
                    'picture' => $avatar,
                    'role' => 'admin',
                    'permissions' => ['all']
                ]);
            }
        } else {
            // Update last login
            $user['last_login'] = time();
            $user['picture'] = $avatar;
            $this->updateUser($user['id'], $user);
        }
        
        return $user;
    }
    
    /**
     * Get user by Discord ID
     */
    public function getUserByDiscordId(string $discordId): ?array
    {
        foreach ($this->users as $user) {
            if (($user['discord_id'] ?? '') === $discordId) {
                return $user;
            }
        }
        return null;
    }
    
    /**
     * Authenticate user via Google
     */
    public function authenticateGoogle(array $googleUser): ?array
    {
        $googleId = $googleUser['id'];
        $email = $googleUser['email'];
        $name = $googleUser['name'] ?? $email;
        $picture = $googleUser['picture'] ?? '';
        
        // Check if user exists
        $user = $this->getUserByGoogleId($googleId);
        
        if (!$user) {
            // Check if this is the first user
            $isFirstUser = empty($this->users);
            
            // Check if user is allowed (either first user or already registered)
            if (!$isFirstUser) {
                // Check if email is pre-registered
                $user = $this->getUserByEmail($email);
                if (!$user) {
                    return null; // Not authorized
                }
                
                // Update existing user with Google ID
                $user['google_id'] = $googleId;
                $user['picture'] = $picture;
                $user['last_login'] = time();
                $this->updateUser($user['id'], $user);
            } else {
                // Create first admin user
                $user = $this->createUser([
                    'google_id' => $googleId,
                    'email' => $email,
                    'name' => $name,
                    'picture' => $picture,
                    'role' => 'admin',
                    'permissions' => ['all']
                ]);
            }
        } else {
            // Update last login
            $user['last_login'] = time();
            $user['picture'] = $picture;
            $this->updateUser($user['id'], $user);
        }
        
        return $user;
    }
    
    /**
     * Get user by Google ID
     */
    public function getUserByGoogleId(string $googleId): ?array
    {
        foreach ($this->users as $user) {
            if (($user['google_id'] ?? '') === $googleId) {
                return $user;
            }
        }
        return null;
    }
    
    /**
     * Get user by email
     */
    public function getUserByEmail(string $email): ?array
    {
        foreach ($this->users as $user) {
            if (strtolower($user['email'] ?? '') === strtolower($email)) {
                return $user;
            }
        }
        return null;
    }
    
    /**
     * Get user by ID
     */
    public function getUserById(string $id): ?array
    {
        return $this->users[$id] ?? null;
    }
    
    /**
     * Create new user
     */
    public function createUser(array $data): array
    {
        $id = $this->generateUserId();
        
        $user = [
            'id' => $id,
            'google_id' => $data['google_id'] ?? '',
            'discord_id' => $data['discord_id'] ?? '',
            'email' => $data['email'],
            'name' => $data['name'] ?? $data['email'],
            'picture' => $data['picture'] ?? '',
            'role' => $data['role'] ?? 'moderator',
            'permissions' => $data['permissions'] ?? ['view', 'search'],
            'created_at' => time(),
            'last_login' => time(),
            'active' => true
        ];
        
        $this->users[$id] = $user;
        $this->saveUsers();
        
        return $user;
    }
    
    /**
     * Update user
     */
    public function updateUser(string $id, array $data): bool
    {
        if (!isset($this->users[$id])) {
            return false;
        }
        
        $this->users[$id] = array_merge($this->users[$id], $data);
        return $this->saveUsers();
    }
    
    /**
     * Delete user
     */
    public function deleteUser(string $id): bool
    {
        if (!isset($this->users[$id])) {
            return false;
        }
        
        // Cannot delete the last admin
        if ($this->users[$id]['role'] === 'admin' && $this->countAdmins() <= 1) {
            return false;
        }
        
        unset($this->users[$id]);
        return $this->saveUsers();
    }
    
    /**
     * Get all users
     */
    public function getAllUsers(): array
    {
        return array_values($this->users);
    }
    
    /**
     * Count admins
     */
    public function countAdmins(): int
    {
        $count = 0;
        foreach ($this->users as $user) {
            if ($user['role'] === 'admin') {
                $count++;
            }
        }
        return $count;
    }
    
    /**
     * Check if user has permission
     */
    public function hasPermission(array $user, string $permission): bool
    {
        if ($user['role'] === 'admin' || in_array('all', $user['permissions'] ?? [])) {
            return true;
        }
        return in_array($permission, $user['permissions'] ?? []);
    }
    
    /**
     * Generate unique user ID
     */
    private function generateUserId(): string
    {
        return bin2hex(random_bytes(16));
    }
    
    /**
     * Get available roles
     */
    public static function getRoles(): array
    {
        return [
            'admin' => [
                'name' => 'Administrator',
                'description' => 'Full access to all features',
                'permissions' => ['all']
            ],
            'moderator' => [
                'name' => 'Moderator',
                'description' => 'Can view, search, and remove punishments',
                'permissions' => ['view', 'search', 'remove', 'modify']
            ],
            'viewer' => [
                'name' => 'Viewer',
                'description' => 'Can only view and search punishments',
                'permissions' => ['view', 'search']
            ]
        ];
    }
    
    /**
     * Get available permissions
     */
    public static function getPermissions(): array
    {
        return [
            'view' => 'View punishments',
            'search' => 'Search punishments',
            'remove' => 'Remove punishments',
            'modify' => 'Modify punishment reasons',
            'export' => 'Export data',
            'import' => 'Import data',
            'settings' => 'Change settings',
            'users' => 'Manage users',
            'all' => 'All permissions'
        ];
    }
}
