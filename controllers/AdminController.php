<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.1
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/AuthManager.php';

class AdminController extends BaseController
{
    private const ADMIN_SESSION_TIMEOUT = 7200; // 2 hours (increased from 1 hour)
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCKOUT_TIME = 900; // 15 minutes
    
    private ?\core\AuthManager $authManager = null;
    
    private function getAuthManager(): \core\AuthManager
    {
        if ($this->authManager === null) {
            $this->authManager = new \core\AuthManager($this->config);
        }
        return $this->authManager;
    }
    
    public function index(): void
    {
        // Check if admin is enabled
        if (!($this->config['admin_enabled'] ?? false)) {
            $this->redirect(url('/'));
            return;
        }
        
        // Check authentication
        if (!$this->isAuthenticated()) {
            $this->showLoginForm();
            return;
        }
        
        // Log admin access
        $this->logAdminAction('dashboard_access', 'Accessed admin dashboard');
        
        // Get current user for dashboard
        $currentUser = $this->getCurrentUser();
        
        // Show admin dashboard
        $this->render('admin/dashboard', [
            'title' => $this->lang->get('admin.dashboard'),
            'currentPage' => 'admin',
            'controller' => $this,
            'stats' => $this->repository->getStats(),
            'currentUser' => $currentUser,
            'authManager' => $this->getAuthManager()
        ]);
    }
    
    public function login(): void
    {
        if (!SecurityManager::validateRequestMethod('POST')) {
            $this->redirect(url('admin'));
            return;
        }
        
        // Check if password login is allowed
        $allowPasswordLogin = $this->config['allow_password_login'] ?? true;
        $googleAuthEnabled = $this->config['google_auth_enabled'] ?? false;
        
        if ($googleAuthEnabled && !$allowPasswordLogin) {
            $_SESSION['admin_error'] = 'Password login is disabled. Please use Google authentication.';
            $this->redirect(url('admin'));
            return;
        }
        
        // Validate CSRF token
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid security token';
            $this->redirect(url('admin'));
            return;
        }
        
        // Check login attempts
        if (!$this->checkLoginAttempts()) {
            $_SESSION['admin_error'] = 'Too many login attempts. Please try again later.';
            $this->redirect(url('admin'));
            return;
        }
        
        $password = $_POST['password'] ?? '';
        
        // Verify password
        $adminPassword = $this->config['admin_password'] ?? '';
        if (empty($adminPassword) || !password_verify($password, $adminPassword)) {
            $this->incrementLoginAttempts();
            $_SESSION['admin_error'] = 'Invalid password';
            $this->logAdminAction('login_failed', 'Failed login attempt', ['severity' => 'warning']);
            $this->redirect(url('admin'));
            return;
        }
        
        // Set admin session
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_user'] = 'Administrator';
        
        // Clear login attempts
        $this->clearLoginAttempts();
        
        // Log successful login
        $this->logAdminAction('login_success', 'Successfully logged in');
        
        $this->redirect(url('admin'));
    }
    
    public function logout(): void
    {
        $this->logAdminAction('logout', 'Logged out');
        
        unset($_SESSION['admin_authenticated']);
        unset($_SESSION['admin_login_time']);
        unset($_SESSION['admin_user']);
        
        $this->redirect(url('/'));
    }
    
    public function searchPunishments(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $query = trim($input['query'] ?? '');
        $type = $input['type'] ?? '';
        
        try {
            if (empty($query) || strlen($query) < 2) {
                $this->jsonResponse(['success' => true, 'punishments' => []]);
                return;
            }
            
            // Enhanced search that handles both names and UUIDs
            $punishments = $this->performAdvancedSearch($query, $type);
            
            // Sort by time descending
            usort($punishments, function($a, $b) {
                return ($b['time'] ?? 0) <=> ($a['time'] ?? 0);
            });
            
            // Format punishments with enhanced data
            $formatted = array_map(function($p) {
                // Get player name more reliably
                $playerName = $p['player_name'] ?? null;
                if (!$playerName && !empty($p['uuid']) && $p['uuid'] !== '#') {
                    $playerName = $this->repository->getPlayerName($p['uuid']);
                }
                
                return [
                    'id' => (int)$p['id'],
                    'type' => rtrim($p['type'], 's'), // Normalize to singular
                    'player_name' => $playerName ?? 'Unknown',
                    'uuid' => $p['uuid'] ?? '',
                    'reason' => $p['reason'] ?? 'No reason provided',
                    'staff' => $p['banned_by_name'] ?? 'Console',
                    'date' => $this->formatDate((int)($p['time'] ?? 0)),
                    'active' => (bool)($p['active'] ?? false),
                    'until' => isset($p['until']) && $p['until'] > 0 
                        ? $this->formatDate((int)$p['until']) 
                        : null,
                    'server' => $p['server_origin'] ?? $p['server_scope'] ?? 'Global'
                ];
            }, array_slice($punishments, 0, 100)); // Increase limit to 100 results
            
            $this->logAdminAction('search_punishments', "Searched for '{$query}' in {$type}", [
                'query' => $query,
                'type' => $type,
                'results_count' => count($formatted)
            ]);
            
            $this->jsonResponse(['success' => true, 'punishments' => $formatted]);
            
        } catch (Exception $e) {
            error_log("Admin search error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Search failed: ' . $e->getMessage()], 500);
        }
    }
    
    private function performAdvancedSearch(string $query, string $type): array
    {
        $results = [];
        $historyTable = $this->repository->getTablePrefix() . 'history';
        $seenIds = []; // Track already found results to avoid duplicates
        
        // Determine which tables to search
        if (!empty($type)) {
            $tables = [$type];
        } else {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        }
        
        // Check if query is numeric (ID search)
        $isNumeric = is_numeric($query);
        
        // Check if query is a UUID
        $isUuid = !$isNumeric && SecurityManager::validateUuid($query);
        
        foreach ($tables as $table) {
            $fullTable = $this->repository->getTablePrefix() . $table;
            
            // 1. ID SEARCH (NEW - case-insensitive, numeric)
            if ($isNumeric) {
                $sql = "SELECT p.*, '{$table}' as type, h.name as player_name
                        FROM {$fullTable} p
                        LEFT JOIN {$historyTable} h ON p.uuid = h.uuid 
                            AND h.date = (SELECT MAX(date) FROM {$historyTable} WHERE uuid = p.uuid)
                        WHERE p.id = :id
                        AND p.uuid IS NOT NULL AND p.uuid != '#'
                        LIMIT 1";
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->execute([':id' => (int)$query]);
                $row = $stmt->fetch();
                
                if ($row) {
                    $key = $row['id'] . '_' . $table;
                    if (!isset($seenIds[$key])) {
                        $results[] = $row;
                        $seenIds[$key] = true;
                    }
                }
                continue; // Skip other searches for ID
            }
            
            // 2. UUID SEARCH
            if ($isUuid) {
                $sql = "SELECT p.*, '{$table}' as type, h.name as player_name
                        FROM {$fullTable} p
                        LEFT JOIN {$historyTable} h ON p.uuid = h.uuid 
                            AND h.date = (SELECT MAX(date) FROM {$historyTable} WHERE uuid = p.uuid)
                        WHERE p.uuid = :uuid
                        AND p.uuid IS NOT NULL AND p.uuid != '#'
                        ORDER BY p.time DESC";
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->execute([':uuid' => $query]);
                foreach ($stmt->fetchAll() as $row) {
                    $key = $row['id'] . '_' . $table;
                    if (!isset($seenIds[$key])) {
                        $results[] = $row;
                        $seenIds[$key] = true;
                    }
                }
            } else {
                // 3. NAME SEARCH (case-insensitive)
                $nameSql = "SELECT DISTINCT p.uuid FROM {$historyTable} p
                           WHERE (LOWER(p.name) = LOWER(:exact_name) 
                           OR LOWER(p.name) LIKE LOWER(:partial_name))
                           AND p.uuid IS NOT NULL AND p.uuid != '#'
                           ORDER BY p.date DESC
                           LIMIT 50";
                
                $stmt = $this->repository->getConnection()->prepare($nameSql);
                $stmt->execute([
                    ':exact_name' => $query,
                    ':partial_name' => '%' . $query . '%'
                ]);
                $uuids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (!empty($uuids)) {
                    // Search punishments for found UUIDs
                    $placeholders = implode(',', array_fill(0, count($uuids), '?'));
                    $sql = "SELECT p.*, '{$table}' as type, h.name as player_name
                            FROM {$fullTable} p
                            LEFT JOIN {$historyTable} h ON p.uuid = h.uuid 
                                AND h.date = (SELECT MAX(date) FROM {$historyTable} WHERE uuid = p.uuid)
                            WHERE p.uuid IN ({$placeholders})
                            ORDER BY p.time DESC";
                    
                    $stmt = $this->repository->getConnection()->prepare($sql);
                    $stmt->execute($uuids);
                    foreach ($stmt->fetchAll() as $row) {
                        $key = $row['id'] . '_' . $table;
                        if (!isset($seenIds[$key])) {
                            $results[] = $row;
                            $seenIds[$key] = true;
                        }
                    }
                }
                
                // 4. REASON SEARCH (case-insensitive)
                $reasonSql = "SELECT p.*, '{$table}' as type, h.name as player_name
                             FROM {$fullTable} p
                             LEFT JOIN {$historyTable} h ON p.uuid = h.uuid 
                                 AND h.date = (SELECT MAX(date) FROM {$historyTable} WHERE uuid = p.uuid)
                             WHERE LOWER(p.reason) LIKE LOWER(:reason)
                             AND p.uuid IS NOT NULL AND p.uuid != '#'
                             ORDER BY p.time DESC
                             LIMIT 25";
                
                $stmt = $this->repository->getConnection()->prepare($reasonSql);
                $stmt->execute([':reason' => '%' . $query . '%']);
                foreach ($stmt->fetchAll() as $row) {
                    $key = $row['id'] . '_' . $table;
                    if (!isset($seenIds[$key])) {
                        $results[] = $row;
                        $seenIds[$key] = true;
                    }
                }
                
                // 5. STAFF NAME SEARCH (case-insensitive)
                $staffSql = "SELECT p.*, '{$table}' as type, h.name as player_name
                            FROM {$fullTable} p
                            LEFT JOIN {$historyTable} h ON p.uuid = h.uuid 
                                AND h.date = (SELECT MAX(date) FROM {$historyTable} WHERE uuid = p.uuid)
                            WHERE LOWER(p.banned_by_name) LIKE LOWER(:staff)
                            AND p.uuid IS NOT NULL AND p.uuid != '#'
                            ORDER BY p.time DESC
                            LIMIT 25";
                
                $stmt = $this->repository->getConnection()->prepare($staffSql);
                $stmt->execute([':staff' => '%' . $query . '%']);
                foreach ($stmt->fetchAll() as $row) {
                    $key = $row['id'] . '_' . $table;
                    if (!isset($seenIds[$key])) {
                        $results[] = $row;
                        $seenIds[$key] = true;
                    }
                }
            }
        }
        
        return $results;
    }
    
    public function removePunishment(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        // Check if user has permission to remove punishments (admin or moderator only)
        $currentUser = $this->getCurrentUser();
        $userRole = $currentUser['role'] ?? 'admin';
        if ($userRole === 'viewer') {
            $this->jsonResponse(['error' => 'Permission denied. Viewers cannot remove punishments.'], 403);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? '';
        $id = (int)($input['id'] ?? 0);
        
        if (!in_array($type, ['ban', 'mute', 'warning', 'kick'])) {
            $this->jsonResponse(['error' => 'Invalid punishment type'], 400);
            return;
        }
        
        // Convert to table name
        $tableName = $type . 's';
        
        try {
            // Check if punishment exists and is active
            $checkSql = "SELECT id, active, uuid FROM {$this->repository->getTablePrefix()}{$tableName} WHERE id = :id";
            $stmt = $this->repository->getConnection()->prepare($checkSql);
            $stmt->execute([':id' => $id]);
            $punishment = $stmt->fetch();
            
            if (!$punishment) {
                $this->jsonResponse(['error' => 'Punishment not found'], 404);
                return;
            }
            
            if (!$punishment['active']) {
                $this->jsonResponse(['error' => 'Punishment is already inactive'], 400);
                return;
            }
            
            // Remove the punishment
            $table = $this->repository->getTablePrefix() . $tableName;
            $sql = "UPDATE {$table} SET 
                    active = 0,
                    removed_by_name = :removed_by,
                    removed_by_date = :removed_date
                    WHERE id = :id";
            
            $stmt = $this->repository->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':removed_by' => $_SESSION['admin_user'] ?? 'Admin',
                ':removed_date' => time() * 1000,
                ':id' => $id
            ]);
            
            if ($result) {
                // Get player name for logging
                $playerName = $this->repository->getPlayerName($punishment['uuid'] ?? '');
                
                $this->logAdminAction('remove_punishment', "Removed {$type} #{$id} for player {$playerName}", [
                    'punishment_id' => $id,
                    'punishment_type' => $type,
                    'player_uuid' => $punishment['uuid'] ?? '',
                    'player_name' => $playerName
                ]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Punishment removed successfully']);
            } else {
                $this->jsonResponse(['error' => 'Failed to update punishment'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Remove punishment error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Database error occurred'], 500);
        }
    }
    
    public function modifyReason(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        // Check if user has permission to modify punishments (admin or moderator only)
        $currentUser = $this->getCurrentUser();
        $userRole = $currentUser['role'] ?? 'admin';
        if ($userRole === 'viewer') {
            $this->jsonResponse(['error' => 'Permission denied. Viewers cannot modify punishments.'], 403);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $type = $input['type'] ?? '';
        $id = (int)($input['id'] ?? 0);
        $newReason = trim($input['reason'] ?? '');
        
        if (!in_array($type, ['ban', 'mute', 'warning', 'kick'])) {
            $this->jsonResponse(['error' => 'Invalid punishment type'], 400);
            return;
        }
        
        if (empty($newReason)) {
            $this->jsonResponse(['error' => 'Reason cannot be empty'], 400);
            return;
        }
        
        // Convert to table name
        $tableName = $type . 's';
        
        try {
            // Check if punishment exists
            $checkSql = "SELECT id, uuid, reason FROM {$this->repository->getTablePrefix()}{$tableName} WHERE id = :id";
            $stmt = $this->repository->getConnection()->prepare($checkSql);
            $stmt->execute([':id' => $id]);
            $punishment = $stmt->fetch();
            
            if (!$punishment) {
                $this->jsonResponse(['error' => 'Punishment not found'], 404);
                return;
            }
            
            $oldReason = $punishment['reason'];
            
            // Update the reason
            $table = $this->repository->getTablePrefix() . $tableName;
            $sql = "UPDATE {$table} SET reason = :reason WHERE id = :id";
            
            $stmt = $this->repository->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':reason' => $newReason,
                ':id' => $id
            ]);
            
            if ($result) {
                // Get player name for logging
                $playerName = $this->repository->getPlayerName($punishment['uuid'] ?? '');
                
                $this->logAdminAction('modify_reason', "Modified reason for {$type} #{$id} for player {$playerName}", [
                    'punishment_id' => $id,
                    'punishment_type' => $type,
                    'player_uuid' => $punishment['uuid'] ?? '',
                    'player_name' => $playerName,
                    'old_reason' => $oldReason,
                    'new_reason' => $newReason
                ]);
                
                $this->jsonResponse(['success' => true, 'message' => 'Reason updated successfully']);
            } else {
                $this->jsonResponse(['error' => 'Failed to update reason'], 500);
            }
            
        } catch (Exception $e) {
            error_log("Modify reason error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Database error occurred'], 500);
        }
    }
    
    public function saveSettings(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        // Validate CSRF token
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid security token'], 400);
            return;
        }
        
        try {
            // Only update settings that are actually submitted
            $settings = [];
            
            // Only add settings if they exist in POST
            if (isset($_POST['site_name'])) {
                $settings['SITE_NAME'] = $_POST['site_name'];
            }
            if (isset($_POST['footer_site_name'])) {
                $settings['FOOTER_SITE_NAME'] = $_POST['footer_site_name'];
            }
            if (isset($_POST['items_per_page'])) {
                $settings['ITEMS_PER_PAGE'] = (int)$_POST['items_per_page'];
            }
            if (isset($_POST['timezone'])) {
                $settings['TIMEZONE'] = $_POST['timezone'];
            }
            if (isset($_POST['date_format'])) {
                $settings['DATE_FORMAT'] = $_POST['date_format'];
            }
            if (isset($_POST['default_theme'])) {
                $settings['DEFAULT_THEME'] = $_POST['default_theme'];
            }
            
            // Checkboxes - only update if form was submitted with these fields
            $settings['SHOW_PLAYER_UUID'] = isset($_POST['show_player_uuid']) ? 'true' : 'false';
            
            // Protest Settings
            if (isset($_POST['protest_discord'])) {
                $settings['PROTEST_DISCORD'] = $_POST['protest_discord'];
            }
            if (isset($_POST['protest_email'])) {
                $settings['PROTEST_EMAIL'] = $_POST['protest_email'];
            }
            if (isset($_POST['protest_forum'])) {
                $settings['PROTEST_FORUM'] = $_POST['protest_forum'];
            }
            
            // Google Auth Settings
            $settings['GOOGLE_AUTH_ENABLED'] = isset($_POST['google_auth_enabled']) ? 'true' : 'false';
            if (isset($_POST['google_client_id'])) {
                $settings['GOOGLE_CLIENT_ID'] = $_POST['google_client_id'];
            }
            if (isset($_POST['google_client_secret'])) {
                $settings['GOOGLE_CLIENT_SECRET'] = $_POST['google_client_secret'];
            }
            $settings['ALLOW_PASSWORD_LOGIN'] = isset($_POST['allow_password_login']) ? 'true' : 'false';
            
            // Display Options
            $settings['SHOW_SILENT_PUNISHMENTS'] = isset($_POST['show_silent_punishments']) ? 'true' : 'false';
            $settings['SHOW_SERVER_ORIGIN'] = isset($_POST['show_server_origin']) ? 'true' : 'false';
            $settings['SHOW_SERVER_SCOPE'] = isset($_POST['show_server_scope']) ? 'true' : 'false';
            $settings['SHOW_CONTACT_DISCORD'] = isset($_POST['show_contact_discord']) ? 'true' : 'false';
            $settings['SHOW_CONTACT_EMAIL'] = isset($_POST['show_contact_email']) ? 'true' : 'false';
            $settings['SHOW_CONTACT_FORUM'] = isset($_POST['show_contact_forum']) ? 'true' : 'false';
            $settings['SHOW_MENU_PROTEST'] = isset($_POST['show_menu_protest']) ? 'true' : 'false';
            $settings['SHOW_MENU_STATS'] = isset($_POST['show_menu_stats']) ? 'true' : 'false';
            
            // SEO Settings
            $settings['SEO_ENABLE_SCHEMA'] = isset($_POST['seo_enable_schema']) ? 'true' : 'false';
            if (isset($_POST['seo_organization_name'])) {
                $settings['SEO_ORGANIZATION_NAME'] = $_POST['seo_organization_name'];
            }
            if (isset($_POST['seo_organization_logo'])) {
                $settings['SEO_ORGANIZATION_LOGO'] = $_POST['seo_organization_logo'];
            }
            if (isset($_POST['seo_social_facebook'])) {
                $settings['SEO_SOCIAL_FACEBOOK'] = $_POST['seo_social_facebook'];
            }
            if (isset($_POST['seo_social_twitter'])) {
                $settings['SEO_SOCIAL_TWITTER'] = $_POST['seo_social_twitter'];
            }
            if (isset($_POST['seo_social_youtube'])) {
                $settings['SEO_SOCIAL_YOUTUBE'] = $_POST['seo_social_youtube'];
            }
            if (isset($_POST['seo_contact_email'])) {
                $settings['SEO_CONTACT_EMAIL'] = $_POST['seo_contact_email'];
            }
            if (isset($_POST['seo_contact_phone'])) {
                $settings['SEO_CONTACT_PHONE'] = $_POST['seo_contact_phone'];
            }
            if (isset($_POST['seo_locale'])) {
                $settings['SEO_LOCALE'] = $_POST['seo_locale'];
            }
            if (isset($_POST['seo_geo_region'])) {
                $settings['SEO_GEO_REGION'] = $_POST['seo_geo_region'];
            }
            if (isset($_POST['seo_geo_placename'])) {
                $settings['SEO_GEO_PLACENAME'] = $_POST['seo_geo_placename'];
            }
            $settings['SEO_AI_TRAINING'] = isset($_POST['seo_ai_training']) ? 'true' : 'false';
            
            // Additional Site Settings
            if (isset($_POST['site_url'])) {
                $settings['SITE_URL'] = $_POST['site_url'];
            }
            if (isset($_POST['site_lang'])) {
                $settings['SITE_LANG'] = $_POST['site_lang'];
            }
            if (isset($_POST['site_description'])) {
                $settings['SITE_DESCRIPTION'] = $_POST['site_description'];
            }
            if (isset($_POST['site_title_template'])) {
                $settings['SITE_TITLE_TEMPLATE'] = $_POST['site_title_template'];
            }
            if (isset($_POST['site_keywords'])) {
                $settings['SITE_KEYWORDS'] = $_POST['site_keywords'];
            }
            if (isset($_POST['site_robots'])) {
                $settings['SITE_ROBOTS'] = $_POST['site_robots'];
            }
            if (isset($_POST['site_theme_color'])) {
                $settings['SITE_THEME_COLOR'] = $_POST['site_theme_color'];
            }
            if (isset($_POST['site_og_image'])) {
                $settings['SITE_OG_IMAGE'] = $_POST['site_og_image'];
            }
            if (isset($_POST['site_twitter_site'])) {
                $settings['SITE_TWITTER_SITE'] = $_POST['site_twitter_site'];
            }
            if (isset($_POST['default_language'])) {
                $settings['DEFAULT_LANGUAGE'] = $_POST['default_language'];
            }
            
            // Avatar Settings
            if (isset($_POST['avatar_provider'])) {
                $settings['AVATAR_PROVIDER'] = $_POST['avatar_provider'];
            }
            if (isset($_POST['avatar_url'])) {
                $settings['AVATAR_URL'] = $_POST['avatar_url'];
            }
            if (isset($_POST['avatar_url_offline'])) {
                $settings['AVATAR_URL_OFFLINE'] = $_POST['avatar_url_offline'];
            }
            
            // Update .env file - only update provided settings
            $envFile = BASE_DIR . '/.env';
            $envContent = file_get_contents($envFile);
            
            foreach ($settings as $key => $value) {
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}={$value}";
                
                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace($pattern, $replacement, $envContent);
                } else {
                    $envContent .= "\n{$key}={$value}";
                }
            }
            
            file_put_contents($envFile, $envContent);
            
            // Force reload .env to apply changes immediately
            \core\EnvLoader::reload();
            
            // Clear opcache to force reload of .env
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // Set cookie for show_uuid preference
            setcookie('show_uuid', $settings['SHOW_PLAYER_UUID'], [
                'expires' => time() + 86400 * 365,
                'path' => BASE_PATH ?: '/',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            
            $this->logAdminAction('settings_update', 'Updated system settings', [
                'updated_settings' => array_keys($settings)
            ]);
            
            $this->jsonResponse(['success' => true, 'message' => 'Settings saved successfully!']);
            
        } catch (Exception $e) {
            error_log("Save settings error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Failed to save settings'], 500);
        }
    }
    
    /**
     * Keep-alive endpoint to prevent session timeout
     */
    public function keepAlive(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['authenticated' => false], 401);
            return;
        }
        
        // Session is automatically refreshed by isAuthenticated()
        $this->jsonResponse([
            'authenticated' => true,
            'timestamp' => time()
        ]);
    }
    
    public function export(): void
    {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            exit('Unauthorized');
        }
        
        $format = $_GET['format'] ?? 'json';
        $type = $_GET['type'] ?? 'all';
        $filter = $_GET['filter'] ?? 'all'; // New: 'all' or 'active'
        
        $this->logAdminAction('data_export', "Exported $type data as $format (filter: $filter)");
        
        $data = $this->gatherExportData($type, $filter);
        
        switch ($format) {
            case 'json':
                $this->exportJson($data, $type);
                break;
            case 'xml':
                $this->exportXml($data, $type);
                break;
            case 'csv':
                $this->exportCsv($data, $type);
                break;
            default:
                http_response_code(400);
                exit('Invalid format');
        }
    }
    
    public function import(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        // Validate CSRF token
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid security token'], 400);
            return;
        }
        
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
            $this->jsonResponse(['error' => 'No file uploaded'], 400);
            return;
        }
        
        $file = $_FILES['import_file'];
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, ['json', 'xml'])) {
            $this->jsonResponse(['error' => 'Invalid file format'], 400);
            return;
        }
        
        try {
            $content = file_get_contents($file['tmp_name']);
            $imported = 0;
            
            if ($extension === 'json') {
                $data = json_decode($content, true);
                if ($data === null) {
                    throw new Exception('Invalid JSON format');
                }
                $imported = $this->importData($data);
            } else {
                // XML import
                $xml = simplexml_load_string($content);
                if ($xml === false) {
                    throw new Exception('Invalid XML format');
                }
                $data = json_decode(json_encode($xml), true);
                $imported = $this->importData($data);
            }
            
            $this->logAdminAction('data_import', "Imported $imported records from {$file['name']}");
            
            $this->jsonResponse(['success' => true, 'imported' => $imported]);
            
        } catch (Exception $e) {
            error_log("Import error: " . $e->getMessage());
            $this->jsonResponse(['error' => $e->getMessage()], 500);
        }
    }
    
    public function phpinfo(): void
    {
        if (!$this->isAuthenticated()) {
            http_response_code(401);
            exit;
        }
        
        $section = $_GET['section'] ?? 'general';
        
        ob_start();
        
        switch ($section) {
            case 'general':
                phpinfo(INFO_GENERAL);
                break;
            case 'configuration':
                phpinfo(INFO_CONFIGURATION);
                break;
            case 'modules':
                phpinfo(INFO_MODULES);
                break;
            case 'environment':
                phpinfo(INFO_ENVIRONMENT);
                break;
            case 'variables':
                phpinfo(INFO_VARIABLES);
                break;
            default:
                phpinfo(INFO_GENERAL);
        }
        
        $phpinfo = ob_get_clean();
        
        // Extract just the body content
        preg_match('/<body[^>]*>(.*?)<\/body>/si', $phpinfo, $matches);
        $content = $matches[1] ?? $phpinfo;
        
        // Apply custom styling
        $content = str_replace(
            ['<table', '<h1', '<h2', 'class="e"', 'class="v"', 'class="h"'],
            ['<table class="table table-sm"', '<h3', '<h4', 'class="text-muted"', '', 'class="table-active"'],
            $content
        );
        
        echo $content;
        exit;
    }
    
    // Helper Methods - Changed from private to public
    
    public function isAuthenticated(): bool
    {
        if (!isset($_SESSION['admin_authenticated'])) {
            return false;
        }
        
        // Check session timeout
        if (time() - ($_SESSION['admin_login_time'] ?? 0) > self::ADMIN_SESSION_TIMEOUT) {
            unset($_SESSION['admin_authenticated']);
            unset($_SESSION['admin_user_id']);
            return false;
        }
        
        // Refresh session time on activity
        $_SESSION['admin_login_time'] = time();
        
        return true;
    }
    
    /**
     * Get current logged in user
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        // If Google Auth is enabled and user has ID
        if (isset($_SESSION['admin_user_id'])) {
            return $this->getAuthManager()->getUserById($_SESSION['admin_user_id']);
        }
        
        // Legacy password auth
        return [
            'id' => 'legacy',
            'name' => $_SESSION['admin_user'] ?? 'Administrator',
            'email' => '',
            'role' => 'admin',
            'permissions' => ['all'],
            'picture' => ''
        ];
    }
    
    /**
     * Check if current user has permission
     */
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        // Legacy admin has all permissions
        if ($user['id'] === 'legacy') {
            return true;
        }
        
        return $this->getAuthManager()->hasPermission($user, $permission);
    }
    
    private function checkLoginAttempts(): bool
    {
        $ip = SecurityManager::getClientIp();
        $attempts = $_SESSION['login_attempts'][$ip] ?? 0;
        $lastAttempt = $_SESSION['last_login_attempt'][$ip] ?? 0;
        
        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            if (time() - $lastAttempt < self::LOGIN_LOCKOUT_TIME) {
                return false;
            }
            // Reset after lockout period
            unset($_SESSION['login_attempts'][$ip]);
            unset($_SESSION['last_login_attempt'][$ip]);
        }
        
        return true;
    }
    
    private function incrementLoginAttempts(): void
    {
        $ip = SecurityManager::getClientIp();
        $_SESSION['login_attempts'][$ip] = ($_SESSION['login_attempts'][$ip] ?? 0) + 1;
        $_SESSION['last_login_attempt'][$ip] = time();
    }
    
    private function clearLoginAttempts(): void
    {
        $ip = SecurityManager::getClientIp();
        unset($_SESSION['login_attempts'][$ip]);
        unset($_SESSION['last_login_attempt'][$ip]);
    }
    
    private function showLoginForm(): void
    {
        $authManager = $this->getAuthManager();
        $allowPasswordLogin = $this->config['allow_password_login'] ?? true;
        
        // Show password login if: not using OAuth, or explicitly allowed, and password is set
        $showPasswordLogin = ((!$authManager->isGoogleAuthEnabled() && !$authManager->isDiscordAuthEnabled()) || $allowPasswordLogin) 
                             && !empty($this->config['admin_password']);
        
        // Check if any users exist (for first user message)
        $hasUsers = count($authManager->getAllUsers()) > 0;
        
        $this->render('admin/login', [
            'title' => $this->lang->get('admin.login'),
            'error' => $_SESSION['admin_error'] ?? null,
            'currentPage' => 'admin',
            'googleAuthEnabled' => $authManager->isGoogleAuthEnabled(),
            'googleAuthUrl' => $authManager->isGoogleAuthEnabled() ? $authManager->getGoogleAuthUrl() : '',
            'discordAuthEnabled' => $authManager->isDiscordAuthEnabled(),
            'discordAuthUrl' => $authManager->isDiscordAuthEnabled() ? $authManager->getDiscordAuthUrl() : '',
            'showPasswordLogin' => $showPasswordLogin,
            'allowPasswordLogin' => $allowPasswordLogin,
            'hasUsers' => $hasUsers
        ]);
        
        unset($_SESSION['admin_error']);
    }
    
    /**
     * Google OAuth callback
     */
    public function oauthCallback(): void
    {
        $authManager = $this->getAuthManager();
        // Default to google for backward compatibility (when no provider param)
        $provider = $_GET['provider'] ?? 'google';
        
        // Validate provider
        if (!in_array($provider, ['google', 'discord'])) {
            $_SESSION['admin_error'] = 'Invalid OAuth provider';
            $this->redirect(url('admin'));
            return;
        }
        
        // Check if provider is enabled
        if ($provider === 'google' && !$authManager->isGoogleAuthEnabled()) {
            $_SESSION['admin_error'] = 'Google authentication is not enabled';
            $this->redirect(url('admin'));
            return;
        }
        
        if ($provider === 'discord' && !$authManager->isDiscordAuthEnabled()) {
            $_SESSION['admin_error'] = 'Discord authentication is not enabled';
            $this->redirect(url('admin'));
            return;
        }
        
        $code = $_GET['code'] ?? '';
        $error = $_GET['error'] ?? '';
        
        if ($error) {
            $_SESSION['admin_error'] = ucfirst($provider) . ' login was cancelled or failed';
            $this->redirect(url('admin'));
            return;
        }
        
        if (empty($code)) {
            $_SESSION['admin_error'] = 'Invalid OAuth response';
            $this->redirect(url('admin'));
            return;
        }
        
        // Process OAuth callback based on provider
        if ($provider === 'google') {
            $oauthUser = $authManager->processGoogleCallback($code);
            $user = $oauthUser ? $authManager->authenticateGoogle($oauthUser) : null;
        } else {
            $oauthUser = $authManager->processDiscordCallback($code);
            $user = $oauthUser ? $authManager->authenticateDiscord($oauthUser) : null;
        }
        
        if (!$oauthUser) {
            $_SESSION['admin_error'] = 'Failed to authenticate with ' . ucfirst($provider);
            $this->redirect(url('admin'));
            return;
        }
        
        if (!$user) {
            $_SESSION['admin_error'] = 'You are not authorized to access the admin panel. Contact an administrator.';
            $this->redirect(url('admin'));
            return;
        }
        
        if (!($user['active'] ?? true)) {
            $_SESSION['admin_error'] = 'Your account has been deactivated';
            $this->redirect(url('admin'));
            return;
        }
        
        // Set session
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_user'] = $user['name'];
        $_SESSION['admin_user_id'] = $user['id'];
        
        $this->logAdminAction($provider . '_login_success', 'Successfully logged in via ' . ucfirst($provider), [
            'user_id' => $user['id'],
            'email' => $user['email']
        ]);
        
        $this->redirect(url('admin'));
    }
    
    /**
     * Get all users (API)
     */
    public function getUsers(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        if (!$this->hasPermission('users')) {
            $this->jsonResponse(['error' => 'Permission denied'], 403);
            return;
        }
        
        $users = $this->getAuthManager()->getAllUsers();
        $this->jsonResponse(['success' => true, 'users' => $users]);
    }
    
    /**
     * Add new user
     */
    public function addUser(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        if (!$this->hasPermission('users')) {
            $this->jsonResponse(['error' => 'Permission denied'], 403);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $email = trim($input['email'] ?? '');
        $name = trim($input['name'] ?? '');
        $role = $input['role'] ?? 'viewer';
        
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['error' => 'Valid email is required'], 400);
            return;
        }
        
        // Check if email already exists
        if ($this->getAuthManager()->getUserByEmail($email)) {
            $this->jsonResponse(['error' => 'User with this email already exists'], 400);
            return;
        }
        
        $roles = \core\AuthManager::getRoles();
        if (!isset($roles[$role])) {
            $role = 'viewer';
        }
        
        $user = $this->getAuthManager()->createUser([
            'email' => $email,
            'name' => $name ?: $email,
            'role' => $role,
            'permissions' => $roles[$role]['permissions']
        ]);
        
        $this->logAdminAction('user_created', "Created user {$email}", [
            'user_id' => $user['id'],
            'email' => $email,
            'role' => $role
        ]);
        
        $this->jsonResponse(['success' => true, 'user' => $user]);
    }
    
    /**
     * Update user
     */
    public function updateUser(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        if (!$this->hasPermission('users')) {
            $this->jsonResponse(['error' => 'Permission denied'], 403);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['id'] ?? '';
        $name = trim($input['name'] ?? '');
        $role = $input['role'] ?? '';
        $active = $input['active'] ?? true;
        
        if (empty($userId)) {
            $this->jsonResponse(['error' => 'User ID is required'], 400);
            return;
        }
        
        $user = $this->getAuthManager()->getUserById($userId);
        if (!$user) {
            $this->jsonResponse(['error' => 'User not found'], 404);
            return;
        }
        
        // Cannot demote the last admin
        $roles = \core\AuthManager::getRoles();
        if ($user['role'] === 'admin' && $role !== 'admin' && $this->getAuthManager()->countAdmins() <= 1) {
            $this->jsonResponse(['error' => 'Cannot demote the last administrator'], 400);
            return;
        }
        
        $updates = [];
        if (!empty($name)) {
            $updates['name'] = $name;
        }
        if (!empty($role) && isset($roles[$role])) {
            $updates['role'] = $role;
            $updates['permissions'] = $roles[$role]['permissions'];
        }
        $updates['active'] = (bool)$active;
        
        $this->getAuthManager()->updateUser($userId, $updates);
        
        $this->logAdminAction('user_updated', "Updated user {$user['email']}", [
            'user_id' => $userId,
            'updates' => array_keys($updates)
        ]);
        
        $this->jsonResponse(['success' => true]);
    }
    
    /**
     * Delete user
     */
    public function deleteUser(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        if (!$this->hasPermission('users')) {
            $this->jsonResponse(['error' => 'Permission denied'], 403);
            return;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $userId = $input['id'] ?? '';
        
        if (empty($userId)) {
            $this->jsonResponse(['error' => 'User ID is required'], 400);
            return;
        }
        
        // Cannot delete yourself
        if ($userId === ($_SESSION['admin_user_id'] ?? '')) {
            $this->jsonResponse(['error' => 'Cannot delete your own account'], 400);
            return;
        }
        
        $user = $this->getAuthManager()->getUserById($userId);
        if (!$user) {
            $this->jsonResponse(['error' => 'User not found'], 404);
            return;
        }
        
        if (!$this->getAuthManager()->deleteUser($userId)) {
            $this->jsonResponse(['error' => 'Cannot delete the last administrator'], 400);
            return;
        }
        
        $this->logAdminAction('user_deleted', "Deleted user {$user['email']}", [
            'user_id' => $userId,
            'email' => $user['email']
        ]);
        
        $this->jsonResponse(['success' => true]);
    }
    
    private function gatherExportData(string $type, string $filter = 'all'): array
    {
        $data = [];
        
        if ($type === 'all') {
            $types = ['bans', 'mutes', 'warnings', 'kicks'];
        } else {
            $types = [$type];
        }
        
        foreach ($types as $t) {
            $table = $this->repository->getTablePrefix() . $t;
            
            // Build query based on filter
            $whereClause = "WHERE uuid IS NOT NULL AND uuid != '#'";
            if ($filter === 'active' && in_array($t, ['bans', 'mutes'])) {
                $whereClause .= " AND active = 1";
            }
            
            $sql = "SELECT * FROM {$table} {$whereClause} ORDER BY time DESC";
            $stmt = $this->repository->getConnection()->query($sql);
            $data[$t] = $stmt->fetchAll();
        }
        
        return $data;
    }
    
    private function exportJson(array $data, string $type): void
    {
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="litebans_' . $type . '_' . date('Y-m-d') . '.json"');
        
        echo json_encode($data, JSON_PRETTY_PRINT);
        exit;
    }
    
    private function exportXml(array $data, string $type): void
    {
        header('Content-Type: text/xml');
        header('Content-Disposition: attachment; filename="litebans_' . $type . '_' . date('Y-m-d') . '.xml"');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><litebans></litebans>');
        
        foreach ($data as $table => $rows) {
            $tableNode = $xml->addChild($table);
            foreach ($rows as $row) {
                $itemNode = $tableNode->addChild('item');
                foreach ($row as $key => $value) {
                    $itemNode->addChild($key, htmlspecialchars((string)$value));
                }
            }
        }
        
        echo $xml->asXML();
        exit;
    }
    
    private function exportCsv(array $data, string $type): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="litebans_' . $type . '_' . date('Y-m-d') . '.csv"');
        
        $output = fopen('php://output', 'w');
        
        foreach ($data as $table => $rows) {
            if (empty($rows)) continue;
            
            // Write table name
            fputcsv($output, ["Table: $table"]);
            
            // Write headers
            fputcsv($output, array_keys($rows[0]));
            
            // Write data
            foreach ($rows as $row) {
                fputcsv($output, array_values($row));
            }
            
            // Empty line between tables
            fputcsv($output, []);
        }
        
        fclose($output);
        exit;
    }
    
    private function importData(array $data): int
    {
        $imported = 0;
        
        foreach ($data as $table => $rows) {
            if (!is_array($rows)) continue;
            
            $tableName = $this->repository->getTablePrefix() . $table;
            
            foreach ($rows as $row) {
                if (!is_array($row)) continue;
                
                try {
                    // Check if record exists
                    $sql = "SELECT id FROM {$tableName} WHERE id = :id";
                    $stmt = $this->repository->getConnection()->prepare($sql);
                    $stmt->execute([':id' => $row['id'] ?? 0]);
                    
                    if ($stmt->fetch()) {
                        // Update existing
                        $this->updateRecord($tableName, $row);
                    } else {
                        // Insert new
                        $this->insertRecord($tableName, $row);
                    }
                    
                    $imported++;
                } catch (Exception $e) {
                    error_log("Import record error: " . $e->getMessage());
                }
            }
        }
        
        return $imported;
    }
    
    private function insertRecord(string $table, array $data): void
    {
        $columns = array_keys($data);
        $placeholders = array_map(function($col) { return ':' . $col; }, $columns);
        
        $sql = "INSERT INTO {$table} (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
        
        $stmt = $this->repository->getConnection()->prepare($sql);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
    }
    
    private function updateRecord(string $table, array $data): void
    {
        $id = $data['id'];
        unset($data['id']);
        
        $sets = array_map(function($col) { return $col . ' = :' . $col; }, array_keys($data));
        
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE id = :id";
        
        $stmt = $this->repository->getConnection()->prepare($sql);
        $stmt->bindValue(':id', $id);
        foreach ($data as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->execute();
    }
    
    private function logAdminAction(string $action, string $description, array $details = []): void
    {
        try {
            // Create admin_logs table if it doesn't exist
            $sql = "CREATE TABLE IF NOT EXISTS {$this->repository->getTablePrefix()}admin_logs (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        user VARCHAR(255),
                        ip VARCHAR(45),
                        action_type VARCHAR(50),
                        description TEXT,
                        details JSON,
                        severity VARCHAR(20) DEFAULT 'info'
                    )";
            $this->repository->getConnection()->exec($sql);
            
            // Insert log entry
            $sql = "INSERT INTO {$this->repository->getTablePrefix()}admin_logs 
                    (user, ip, action_type, description, details, severity) 
                    VALUES (:user, :ip, :action, :desc, :details, :severity)";
                    
            $stmt = $this->repository->getConnection()->prepare($sql);
            $stmt->execute([
                ':user' => $_SESSION['admin_user'] ?? 'System',
                ':ip' => SecurityManager::getClientIp(),
                ':action' => $action,
                ':desc' => $description,
                ':details' => json_encode($details),
                ':severity' => $details['severity'] ?? 'info'
            ]);
        } catch (PDOException $e) {
            error_log("Failed to log admin action: " . $e->getMessage());
        }
    }
    
    // Add missing methods
    public function getDatabaseSize(): string
    {
        try {
            $dbName = $this->config['db_name'] ?? 'litebans';
            $sql = "SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb
                    FROM information_schema.tables
                    WHERE table_schema = :dbname
                    AND table_name LIKE :prefix";
                    
            $stmt = $this->repository->getConnection()->prepare($sql);
            $stmt->execute([
                ':dbname' => $dbName,
                ':prefix' => $this->repository->getTablePrefix() . '%'
            ]);
            
            $result = $stmt->fetch();
            return ($result['size_mb'] ?? 0) . ' MB';
        } catch (Exception $e) {
            return 'Unknown';
        }
    }
    
    public function getStats(): array
    {
        return $this->repository->getStats();
    }
}
