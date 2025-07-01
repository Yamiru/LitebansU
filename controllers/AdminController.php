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

class AdminController extends BaseController
{
    private const ADMIN_SESSION_TIMEOUT = 3600; // 1 hour
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCKOUT_TIME = 900; // 15 minutes
    
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
        
        // Show admin dashboard
        $this->render('admin/dashboard', [
            'title' => $this->lang->get('admin.dashboard'),
            'currentPage' => 'admin',
            'controller' => $this,
            'stats' => $this->repository->getStats()
        ]);
    }
    
    public function login(): void
    {
        if (!SecurityManager::validateRequestMethod('POST')) {
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
        
        // Determine which tables to search
        if (!empty($type)) {
            $tables = [$type];
        } else {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        }
        
        // Check if query is a UUID
        $isUuid = SecurityManager::validateUuid($query);
        
        foreach ($tables as $table) {
            $fullTable = $this->repository->getTablePrefix() . $table;
            
            if ($isUuid) {
                // Direct UUID search
                $sql = "SELECT p.*, '{$table}' as type, h.name as player_name
                        FROM {$fullTable} p
                        LEFT JOIN (
                            SELECT h1.uuid, h1.name
                            FROM {$historyTable} h1
                            INNER JOIN (
                                SELECT uuid, MAX(date) as max_date
                                FROM {$historyTable}
                                GROUP BY uuid
                            ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                        ) h ON p.uuid = h.uuid
                        WHERE p.uuid = :uuid
                        AND p.uuid IS NOT NULL AND p.uuid != '#'
                        ORDER BY p.time DESC";
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->execute([':uuid' => $query]);
                $results = array_merge($results, $stmt->fetchAll());
            } else {
                // Multi-step name search
                
                // 1. Find UUIDs by player name (exact and partial matches)
                $uuidSql = "SELECT DISTINCT uuid FROM {$historyTable} 
                           WHERE (LOWER(name) = LOWER(:exact_name) 
                           OR LOWER(name) LIKE LOWER(:partial_name))
                           AND uuid IS NOT NULL AND uuid != '#'
                           ORDER BY date DESC
                           LIMIT 50";
                
                $stmt = $this->repository->getConnection()->prepare($uuidSql);
                $stmt->execute([
                    ':exact_name' => $query,
                    ':partial_name' => '%' . $query . '%'
                ]);
                $uuids = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                if (!empty($uuids)) {
                    // 2. Search punishments for found UUIDs
                    $placeholders = str_repeat('?,', count($uuids) - 1) . '?';
                    $sql = "SELECT p.*, '{$table}' as type, h.name as player_name
                            FROM {$fullTable} p
                            LEFT JOIN (
                                SELECT h1.uuid, h1.name
                                FROM {$historyTable} h1
                                INNER JOIN (
                                    SELECT uuid, MAX(date) as max_date
                                    FROM {$historyTable}
                                    GROUP BY uuid
                                ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                            ) h ON p.uuid = h.uuid
                            WHERE p.uuid IN ({$placeholders})
                            ORDER BY p.time DESC";
                    
                    $stmt = $this->repository->getConnection()->prepare($sql);
                    $stmt->execute($uuids);
                    $results = array_merge($results, $stmt->fetchAll());
                }
                
                // 3. Also search by reason (staff might search for ban reasons)
                $reasonSql = "SELECT p.*, '{$table}' as type, h.name as player_name
                             FROM {$fullTable} p
                             LEFT JOIN (
                                 SELECT h1.uuid, h1.name
                                 FROM {$historyTable} h1
                                 INNER JOIN (
                                     SELECT uuid, MAX(date) as max_date
                                     FROM {$historyTable}
                                     GROUP BY uuid
                                 ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                             ) h ON p.uuid = h.uuid
                             WHERE LOWER(p.reason) LIKE LOWER(:reason)
                             AND p.uuid IS NOT NULL AND p.uuid != '#'
                             ORDER BY p.time DESC
                             LIMIT 25";
                
                $stmt = $this->repository->getConnection()->prepare($reasonSql);
                $stmt->execute([':reason' => '%' . $query . '%']);
                $reasonResults = $stmt->fetchAll();
                
                // Merge and deduplicate
                foreach ($reasonResults as $result) {
                    $exists = false;
                    foreach ($results as $existing) {
                        if ($existing['id'] === $result['id'] && $existing['type'] === $result['type']) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $results[] = $result;
                    }
                }
                
                // 4. Search by staff name
                $staffSql = "SELECT p.*, '{$table}' as type, h.name as player_name
                            FROM {$fullTable} p
                            LEFT JOIN (
                                SELECT h1.uuid, h1.name
                                FROM {$historyTable} h1
                                INNER JOIN (
                                    SELECT uuid, MAX(date) as max_date
                                    FROM {$historyTable}
                                    GROUP BY uuid
                                ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                            ) h ON p.uuid = h.uuid
                            WHERE LOWER(p.banned_by_name) LIKE LOWER(:staff)
                            AND p.uuid IS NOT NULL AND p.uuid != '#'
                            ORDER BY p.time DESC
                            LIMIT 25";
                
                $stmt = $this->repository->getConnection()->prepare($staffSql);
                $stmt->execute([':staff' => '%' . $query . '%']);
                $staffResults = $stmt->fetchAll();
                
                // Merge and deduplicate staff results
                foreach ($staffResults as $result) {
                    $exists = false;
                    foreach ($results as $existing) {
                        if ($existing['id'] === $result['id'] && $existing['type'] === $result['type']) {
                            $exists = true;
                            break;
                        }
                    }
                    if (!$exists) {
                        $results[] = $result;
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
            $settings = [
                'SITE_NAME' => $_POST['site_name'] ?? $this->config['site_name'],
                'FOOTER_SITE_NAME' => $_POST['footer_site_name'] ?? $this->config['footer_site_name'],
                'ITEMS_PER_PAGE' => (int)($_POST['items_per_page'] ?? 20),
                'TIMEZONE' => $_POST['timezone'] ?? 'UTC',
                'DATE_FORMAT' => $_POST['date_format'] ?? 'Y-m-d H:i:s',
                'DEFAULT_THEME' => $_POST['default_theme'] ?? 'dark',
                'SHOW_PLAYER_UUID' => isset($_POST['show_player_uuid']) ? 'true' : 'false'
            ];
            
            // Update .env file
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
            
            $this->jsonResponse(['success' => true]);
            
        } catch (Exception $e) {
            error_log("Save settings error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Failed to save settings'], 500);
        }
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
            return false;
        }
        
        // Refresh session time on activity
        $_SESSION['admin_login_time'] = time();
        
        return true;
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
        $this->render('admin/login', [
            'title' => $this->lang->get('admin.login'),
            'error' => $_SESSION['admin_error'] ?? null,
            'currentPage' => 'admin'
        ]);
        
        unset($_SESSION['admin_error']);
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