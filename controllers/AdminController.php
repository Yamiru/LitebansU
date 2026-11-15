<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       5.1
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../core/AuthManager.php';
require_once __DIR__ . '/../core/RememberMeManager.php';
require_once __DIR__ . '/../core/Logger.php';

class AdminController extends BaseController
{
    private const ADMIN_SESSION_TIMEOUT = 7200;
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_LOCKOUT_TIME = 900;
    
    private ?\core\AuthManager $authManager = null;
    private ?\core\RememberMeManager $rememberMeManager = null;
    private ?\core\Logger $logger = null;
    
    private function getAuthManager(): \core\AuthManager
    {
        if ($this->authManager === null) {
            $this->authManager = new \core\AuthManager($this->config);
        }
        return $this->authManager;
    }
    
    private function getRememberMeManager(): \core\RememberMeManager
    {
        if ($this->rememberMeManager === null) {
            $this->rememberMeManager = new \core\RememberMeManager();
        }
        return $this->rememberMeManager;
    }
    
    private function getLogger(): \core\Logger
    {
        if ($this->logger === null) {
            $this->logger = \core\Logger::getInstance();
        }
        return $this->logger;
    }
    
    public function index(): void
    {
        if (!($this->config['admin_enabled'] ?? false)) {
            $this->redirect(url('/'));
            return;
        }
        
        if (!$this->isAuthenticated()) {
            $this->showLoginForm();
            return;
        }
        
        $this->logAdminAction('dashboard_access', 'Accessed admin dashboard');
        
        $currentUser = $this->getCurrentUser();
        
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
        
        $allowPasswordLogin = $this->config['allow_password_login'] ?? true;
        $googleAuthEnabled = $this->config['google_auth_enabled'] ?? false;
        
        if ($googleAuthEnabled && !$allowPasswordLogin) {
            $_SESSION['admin_error'] = 'Password login is disabled. Please use Google authentication.';
            $this->redirect(url('admin'));
            return;
        }
        
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid security token';
            $this->redirect(url('admin'));
            return;
        }
        
        if (!$this->checkLoginAttempts()) {
            $_SESSION['admin_error'] = 'Too many login attempts. Please try again later.';
            $this->redirect(url('admin'));
            return;
        }
        
        if (!$this->checkLoginCaptcha()) {
            $_SESSION['admin_error'] = 'Wrong answer to the security question. Please try again.';
            $this->redirect(url('admin'));
            return;
        }

        $password = $_POST['password'] ?? '';
        $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';

        $adminPassword = $this->config['admin_password'] ?? '';
        if (empty($adminPassword) || !password_verify($password, $adminPassword)) {
            $_SESSION['admin_error'] = 'Invalid password';
            $this->logAdminAction('login_failed', 'Failed login attempt', ['severity' => 'warning']);
            $this->getLogger()->warning('Failed password login attempt', [
                'ip' => SecurityManager::getClientIp()
            ]);
            $this->redirect(url('admin'));
            return;
        }
        
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_user'] = 'Administrator';
        
        if ($rememberMe) {
            $this->getRememberMeManager()->createToken('legacy', 'password');
            $this->getLogger()->info('Remember me token created for password login');
        }
        
        $this->logAdminAction('login_success', 'Successfully logged in');
        $this->getLogger()->info('Successful password login', [
            'ip' => SecurityManager::getClientIp(),
            'remember_me' => $rememberMe
        ]);
        
        $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);
        
        if ($target = $this->safeRedirectTarget($redirectUrl)) {
            $this->redirect($target);
        } else {
            $this->redirect(url('admin'));
        }
    }
    
    public function logout(): void
    {
        $this->logAdminAction('logout', 'Logged out');
        $this->getLogger()->info('User logged out', [
            'user' => $_SESSION['admin_user'] ?? 'unknown'
        ]);
        
        $this->getRememberMeManager()->clearCurrentToken();
        
        unset($_SESSION['admin_authenticated']);
        unset($_SESSION['admin_login_time']);
        unset($_SESSION['admin_user']);
        unset($_SESSION['admin_user_id']);
        
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
            if (empty($query) || strlen($query) < 1) {
                $this->jsonResponse(['success' => true, 'punishments' => []]);
                return;
            }
            
            $punishments = $this->performAdvancedSearch($query, $type);

            $evidenceData = null;
            $evidenceMatches = [];
            if ($this->loadCaseEvidence()) {
                $evidenceData = sn_read();
                foreach (sn_search_cases($query) as [$hitType, $hitId]) {
                    $evidenceMatches[$hitType . '_' . $hitId] = true;
                }
            }
            


            
            usort($punishments, function($a, $b) {
                return ($b['time'] ?? 0) <=> ($a['time'] ?? 0);
            });
            
            $formatted = array_map(function($p) use ($evidenceData, $evidenceMatches) {
                $playerName = $p['player_name'] ?? null;
                if (!$playerName && !empty($p['uuid']) && $p['uuid'] !== '#') {
                    $playerName = $this->repository->getPlayerName($p['uuid']);
                }
                
                $rowType = rtrim($p['type'], 's');
                
                return [
                    'id' => (int)$p['id'],
                    'type' => $rowType,
                    'player_name' => $playerName ?? 'Unknown',
                    'uuid' => $p['uuid'] ?? '',
                    'reason' => $p['reason'] ?? 'No reason provided',
                    'staff' => $p['banned_by_name'] ?? 'Console',
                    'date' => $this->formatDate((int)($p['time'] ?? 0)),
                    'active' => $this->isPunishmentActive($p, $rowType),
                    'until' => isset($p['until']) && $p['until'] > 0 
                        ? $this->formatDate((int)$p['until']) 
                        : null,
                    'server' => $p['server_origin'] ?? $p['server_scope'] ?? 'Global',
                    'evidence' => $evidenceData ? sn_case_summary($rowType, (int)$p['id'], $evidenceData) : null,
                    'evidence_match' => isset($evidenceMatches[$rowType . '_' . (int)$p['id']]),
                    'ip' => !empty($p['ip']) && $p['ip'] !== '#' ? (string)$p['ip'] : null
                ];
            }, array_slice($punishments, 0, 100));
            
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
        $seenIds = [];
        
        $allTables = ['bans', 'mutes', 'warnings', 'kicks'];
        $tables = in_array($type, $allTables, true) ? [$type] : $allTables;

        $isIp = (bool)preg_match('/^[0-9a-fA-F:.]{3,45}$/', $query) && (bool)preg_match('/[.:]/', $query);

        $isNumeric = !$isIp && is_numeric($query);
        
        $isUuid = !$isNumeric && SecurityManager::validateUuid($query);
        
        foreach ($tables as $table) {
            $fullTable = $this->repository->getTablePrefix() . $table;
            
            if ($isIp) {
                $sql = "SELECT p.*, '{$table}' as type, h.name as player_name
                        FROM {$fullTable} p
                        LEFT JOIN {$historyTable} h ON p.uuid = h.uuid
                            AND h.date = (SELECT MAX(date) FROM {$historyTable} WHERE uuid = p.uuid)
                        WHERE p.ip LIKE :ip
                        AND p.uuid IS NOT NULL AND p.uuid != '#'
                        ORDER BY p.time DESC
                        LIMIT 100";

                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->execute([':ip' => addcslashes($query, '%_\\') . '%']);
                foreach ($stmt->fetchAll() as $row) {
                    $key = $row['id'] . '_' . $table;
                    if (!isset($seenIds[$key])) {
                        $results[] = $row;
                        $seenIds[$key] = true;
                    }
                }
                continue;
            }

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
                continue;
            }
            
            $couldBeAlphanumericId = strlen($query) <= 20 && preg_match('/^[a-zA-Z0-9]+$/', $query);
            if ($couldBeAlphanumericId && !$isUuid) {
                $sql = "SELECT p.*, '{$table}' as type, h.name as player_name
                        FROM {$fullTable} p
                        LEFT JOIN {$historyTable} h ON p.uuid = h.uuid 
                            AND h.date = (SELECT MAX(date) FROM {$historyTable} WHERE uuid = p.uuid)
                        WHERE CAST(p.id AS CHAR) = :id
                        AND p.uuid IS NOT NULL AND p.uuid != '#'
                        LIMIT 1";
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->execute([':id' => $query]);
                $row = $stmt->fetch();
                
                if ($row) {
                    $key = $row['id'] . '_' . $table;
                    if (!isset($seenIds[$key])) {
                        $results[] = $row;
                        $seenIds[$key] = true;
                    }
                }
            }
            
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
                continue;
            }
            
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
        
        return $results;
    }
    
    public function removePunishment(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
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
        
        $tableName = $type . 's';
        
        try {
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
        
        $tableName = $type . 's';
        
        try {
            $checkSql = "SELECT id, uuid, reason FROM {$this->repository->getTablePrefix()}{$tableName} WHERE id = :id";
            $stmt = $this->repository->getConnection()->prepare($checkSql);
            $stmt->execute([':id' => $id]);
            $punishment = $stmt->fetch();
            
            if (!$punishment) {
                $this->jsonResponse(['error' => 'Punishment not found'], 404);
                return;
            }
            
            $oldReason = $punishment['reason'];
            
            $table = $this->repository->getTablePrefix() . $tableName;
            $sql = "UPDATE {$table} SET reason = :reason WHERE id = :id";
            
            $stmt = $this->repository->getConnection()->prepare($sql);
            $result = $stmt->execute([
                ':reason' => $newReason,
                ':id' => $id
            ]);
            
            if ($result) {
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
    
    private function loadCaseEvidence(): bool
    {
        $file = BASE_DIR . '/demos/case-evidence.php';
        if (!is_file($file)) {
            return false;
        }
        require_once $file;
        return true;
    }

    public function caseEvidence(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        if (!$this->loadCaseEvidence()) {
            $this->jsonResponse(['error' => 'Case evidence module is missing (demos/case-evidence.php)'], 500);
            return;
        }

        $isPost = $_SERVER['REQUEST_METHOD'] === 'POST';
        if ($isPost && ($overflow = sn_upload_overflow())) {
            $this->jsonResponse(['ok' => false, 'message' => $overflow, 'error' => $overflow], 413);
            return;
        }
        $source = $isPost ? $_POST : $_GET;
        $type = (string)($source['type'] ?? '');
        $id = (int)($source['id'] ?? 0);

        if (!in_array($type, ['ban', 'mute', 'warning', 'kick'], true) || $id <= 0) {
            $this->jsonResponse(['error' => 'Invalid punishment', 'ok' => false, 'message' => 'Invalid punishment'], 400);
            return;
        }
        if (!$this->repository->getPunishmentById($type . 's', $id)) {
            $this->jsonResponse(['error' => 'Punishment not found', 'ok' => false, 'message' => 'Punishment not found'], 404);
            return;
        }

        if (!$isPost) {
            $this->jsonResponse(['success' => true] + sn_case_json($type, $id));
            return;
        }

        [$ok, $message] = sn_apply_post();
        if ($ok) {
            $this->logAdminAction('case_evidence', "Case evidence changed for {$type} #{$id}", [
                'punishment_id' => $id,
                'punishment_type' => $type,
                'action' => (string)($_POST['action'] ?? '')
            ]);
        }
        $this->jsonResponse(['ok' => $ok, 'message' => $message], $ok ? 200 : 400);
    }

    public function siteExtras(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        if (($this->getCurrentUser()['role'] ?? '') !== 'admin') {
            $this->jsonResponse(['error' => 'Permission denied. Only administrators can change these settings.'], 403);
            return;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => true, 'settings' => \core\SiteExtras::get()]);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input) || !SecurityManager::validateCsrfToken((string)($input['csrf_token'] ?? ''))) {
            $this->jsonResponse(['error' => 'Invalid security token. Reload the page and try again.'], 403);
            return;
        }

        $gaId = trim((string)($input['ga_id'] ?? ''));
        if ($gaId !== '' && \core\SiteExtras::normalizeGaId($gaId) === '') {
            $this->jsonResponse(['error' => 'The Google Analytics ID must look like G-XXXXXXXXXX.'], 400);
            return;
        }

        $homeUrl = trim((string)($input['home_link_url'] ?? ''));
        if ($homeUrl !== '' && \core\SiteExtras::normalizeLinkUrl($homeUrl) === '') {
            $this->jsonResponse(['error' => 'The main website link must start with https://, http:// or /.'], 400);
            return;
        }

        $privacy = [];
        $supported = $this->lang->getSupportedLanguages();
        foreach ((array)($input['privacy'] ?? []) as $code => $html) {
            if (in_array($code, $supported, true) && is_string($html) && trim($html) !== '') {
                $privacy[$code] = \core\SiteExtras::sanitizeHtml(mb_substr($html, 0, 50000));
            }
        }

        $saved = \core\SiteExtras::save([
            'allow_crawlers' => !empty($input['allow_crawlers']),
            'ga_id' => \core\SiteExtras::normalizeGaId($gaId),
            'cookie_banner' => !empty($input['cookie_banner']),
            'custom_head' => mb_substr((string)($input['custom_head'] ?? ''), 0, 20000),
            'custom_footer' => mb_substr((string)($input['custom_footer'] ?? ''), 0, 20000),
            'privacy' => $privacy,
            'home_link_enabled' => !empty($input['home_link_enabled']),
            'home_link_url' => $homeUrl,
            'home_link_label' => mb_substr(trim((string)($input['home_link_label'] ?? '')), 0, 40),
            'home_link_newtab' => !empty($input['home_link_newtab']),
        ]);
        if (!$saved) {
            $this->jsonResponse(['error' => 'Could not save. Check that the data/ folder is writable.'], 500);
            return;
        }

        $this->logAdminAction('site_extras_saved', 'SEO & Tracking settings changed', ['severity' => 'info']);
        $this->jsonResponse(['success' => true, 'message' => 'Saved.', 'settings' => \core\SiteExtras::get()]);
    }

    public function templates(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        $this->requireAdminRole();
        $active = \core\TemplateManager::activeId($this->config);
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->jsonResponse(['success' => true, 'templates' => \core\TemplateManager::all($active)]);
            return;
        }

        $isUpload = isset($_FILES['template_zip']);
        $input = $isUpload ? $_POST : (json_decode(file_get_contents('php://input'), true) ?: []);
        if (!SecurityManager::validateCsrfToken((string)($input['csrf_token'] ?? ''))) {
            $this->jsonResponse(['error' => 'Invalid security token. Reload the page and try again.'], 403);
            return;
        }

        if ($isUpload) {
            $file = $_FILES['template_zip'];
            if ($file['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($file['tmp_name']) || strtolower(pathinfo((string)$file['name'], PATHINFO_EXTENSION)) !== 'zip') {
                $this->jsonResponse(['error' => 'Upload a .zip file.'], 400);
                return;
            }
            [$ok, $message] = \core\TemplateManager::install($file['tmp_name']);
            if ($ok) {
                $this->logAdminAction('template_installed', $message, ['severity' => 'warning']);
            }
            $this->jsonResponse($ok ? ['success' => true, 'message' => $message] : ['error' => $message], $ok ? 200 : 400);
            return;
        }

        $id = (string)($input['id'] ?? '');
        $action = (string)($input['action'] ?? '');
        $known = array_column(\core\TemplateManager::all($active), 'id');
        if (!in_array($id, $known, true)) {
            $this->jsonResponse(['error' => 'Unknown template.'], 404);
            return;
        }
        if ($action === 'activate') {
            if (!\core\SiteExtras::save(['template' => $id])) {
                $this->jsonResponse(['error' => 'Could not save. Check that the data/ folder is writable.'], 500);
                return;
            }
            $this->logAdminAction('template_activated', 'Template set to ' . $id, ['severity' => 'info']);
            $this->jsonResponse(['success' => true, 'message' => 'Template "' . $id . '" is now active.']);
        } elseif ($action === 'delete') {
            if ($id === $active || !\core\TemplateManager::delete($id)) {
                $this->jsonResponse(['error' => 'The active and built-in templates cannot be deleted.'], 400);
                return;
            }
            $this->logAdminAction('template_deleted', 'Template ' . $id . ' deleted', ['severity' => 'warning']);
            $this->jsonResponse(['success' => true, 'message' => 'Template deleted.']);
        } else {
            $this->jsonResponse(['error' => 'Unknown action.'], 400);
        }
    }

    public function indexNow(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        if (($this->getCurrentUser()['role'] ?? '') !== 'admin') {
            $this->jsonResponse(['error' => 'Permission denied'], 403);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input) || !SecurityManager::validateCsrfToken((string)($input['csrf_token'] ?? ''))) {
            $this->jsonResponse(['error' => 'Invalid security token. Reload the page and try again.'], 403);
            return;
        }
        if (!(\core\SiteExtras::get()['allow_crawlers'] ?? true)) {
            $this->jsonResponse(['error' => 'Crawlers are switched off, so there is nothing to submit.'], 400);
            return;
        }

        $urls = \core\SiteExtras::indexableUrls($this->config, $this->repository);
        [$ok, $status, $message] = \core\SiteExtras::submitIndexNow($this->config, $urls);
        $this->logAdminAction('indexnow_submit', 'IndexNow: ' . $message, ['urls' => count($urls), 'http_status' => $status]);
        $this->jsonResponse(['success' => $ok, 'message' => $message, 'error' => $ok ? null : $message, 'status' => $status, 'urls' => $urls], $ok ? 200 : 502);
    }

    public function checkLlms(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        if (($this->getCurrentUser()['role'] ?? '') !== 'admin') {
            $this->jsonResponse(['error' => 'Permission denied'], 403);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input) || !SecurityManager::validateCsrfToken((string)($input['csrf_token'] ?? ''))) {
            $this->jsonResponse(['error' => 'Invalid security token. Reload the page and try again.'], 403);
            return;
        }

        $siteUrl = \core\SiteExtras::siteUrl($this->config);
        $siteName = trim((string)($this->config['site_name'] ?? '')) ?: 'LiteBansU';
        $ai = new AiController($this->repository, $this->lang, $this->theme, $this->config);
        $generated = $ai->buildLlms();

        $checks = [['info', 'Checked URL: ' . $siteUrl . '/llms.txt']];
        $checks = array_merge($checks, AiController::checkLlms($generated, $siteUrl, $siteName));

        $ctx = stream_context_create(['http' => ['timeout' => 5, 'ignore_errors' => true, 'header' => "User-Agent: LiteBansU-check\r\n"]]);
        $live = @file_get_contents($siteUrl . '/llms.txt', false, $ctx);
        $status = 0;
        foreach ($http_response_header ?? [] as $line) {
            if (preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $m)) {
                $status = (int)$m[1];
            }
        }
        if ($live === false || $status !== 200) {
            $checks[] = ['warn', 'Could not fetch the public URL (HTTP ' . ($status ?: 'no response') . '). Is the site URL correct and reachable from the server?'];
        } elseif (trim($live) !== trim($generated)) {
            $checks[] = ['error', 'The public URL serves different text than this site generates (a static file or cache is in the way). Check the llms.txt rewrite in .htaccess / nginx.'];
        } else {
            $checks[] = ['ok', 'The public URL serves exactly the generated text.'];
        }

        $levels = array_column($checks, 0);
        $this->jsonResponse([
            'success' => true,
            'ok' => !in_array('error', $levels, true),
            'checks' => $checks,
        ]);
    }

    public function saveSettings(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid security token'], 400);
            return;
        }

        $this->requireAdminRole();

        try {
            $settings = [];
            
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
            
            if (isset($_POST['site_template'])) {
                $tplId = (string)$_POST['site_template'];
                $known = array_column(\core\TemplateManager::all(), 'id');
                $tplChange = ['avatar_cache' => !empty($_POST['avatar_cache'])];
                if (in_array($tplId, $known, true)) {
                    $tplChange['template'] = $tplId;
                }
                if (!\core\SiteExtras::save($tplChange)) {
                    $this->jsonResponse(['error' => 'Could not save the template. Check that the data/ folder is writable.'], 500);
                    return;
                }
            }

            $settings['SHOW_PLAYER_UUID'] = isset($_POST['show_player_uuid']) ? 'true' : 'false';
            
            if (isset($_POST['protest_discord'])) {
                $settings['PROTEST_DISCORD'] = $_POST['protest_discord'];
            }
            if (isset($_POST['protest_email'])) {
                $settings['PROTEST_EMAIL'] = $_POST['protest_email'];
            }
            if (isset($_POST['protest_forum'])) {
                $settings['PROTEST_FORUM'] = $_POST['protest_forum'];
            }
            
            
            $settings['SHOW_SILENT_PUNISHMENTS'] = isset($_POST['show_silent_punishments']) ? 'true' : 'false';
            $settings['SHOW_SERVER_ORIGIN'] = isset($_POST['show_server_origin']) ? 'true' : 'false';
            $settings['SHOW_SERVER_SCOPE'] = isset($_POST['show_server_scope']) ? 'true' : 'false';
            $settings['SHOW_CONTACT_DISCORD'] = isset($_POST['show_contact_discord']) ? 'true' : 'false';
            $settings['SHOW_CONTACT_EMAIL'] = isset($_POST['show_contact_email']) ? 'true' : 'false';
            $settings['SHOW_CONTACT_FORUM'] = isset($_POST['show_contact_forum']) ? 'true' : 'false';
            $settings['SHOW_MENU_PROTEST'] = isset($_POST['show_menu_protest']) ? 'true' : 'false';
            $settings['SHOW_MENU_STATS'] = isset($_POST['show_menu_stats']) ? 'true' : 'false';
            
            $settings['REQUIRE_LOGIN'] = isset($_POST['require_login']) ? 'true' : 'false';
            
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
            if (isset($_POST['site_og_image'])) {
                $settings['SITE_OG_IMAGE'] = $_POST['site_og_image'];
            }
            if (isset($_POST['site_twitter_site'])) {
                $settings['SITE_TWITTER_SITE'] = $_POST['site_twitter_site'];
            }
            if (isset($_POST['default_language'])) {
                $settings['DEFAULT_LANGUAGE'] = $_POST['default_language'];
            }
            
            if (isset($_POST['avatar_url'])) {
                $settings['AVATAR_URL'] = $_POST['avatar_url'];
            }
            if (isset($_POST['avatar_url_offline'])) {
                $settings['AVATAR_URL_OFFLINE'] = $_POST['avatar_url_offline'];
            }
            
            $envFile = BASE_DIR . '/.env';
            $envContent = file_get_contents($envFile);
            
            foreach ($settings as $key => $value) {
                $value = str_replace(["\r", "\n", "\0"], '', (string)$value);
                $pattern = "/^{$key}=.*/m";
                $replacement = "{$key}={$value}";

                if (preg_match($pattern, $envContent)) {
                    $envContent = preg_replace_callback($pattern, fn() => $replacement, $envContent);
                } else {
                    $envContent .= "\n{$key}={$value}";
                }
            }
            
            file_put_contents($envFile, $envContent);
            
            \core\EnvLoader::reload();
            
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            if (function_exists('apcu_clear_cache')) {
                apcu_clear_cache();
            }
            
            $this->config = require BASE_DIR . '/config/app.php';
            
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
            
            $this->jsonResponse([
                'success' => true, 
                'message' => 'Settings saved and config reloaded successfully! Refresh page to see changes.',
                'reload_recommended' => true
            ]);
            
        } catch (Exception $e) {
            error_log("Save settings error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Failed to save settings'], 500);
        }
    }
    
    public function keepAlive(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['authenticated' => false], 401);
            return;
        }
        
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
        
        $this->requireAdminRole(false);

        $format = $_GET['format'] ?? 'json';
        $type = $_GET['type'] ?? 'all';
        $filter = $_GET['filter'] ?? 'all';

        if (!in_array($type, ['all', 'bans', 'mutes', 'warnings', 'kicks'], true) || !in_array($filter, ['all', 'active'], true)) {
            http_response_code(400);
            exit('Invalid type');
        }

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
        
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid security token'], 400);
            return;
        }

        $this->requireAdminRole();

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
        
        $this->requireAdminRole(false);

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
            default:
                phpinfo(INFO_GENERAL);
        }
        
        $phpinfo = ob_get_clean();
        
        preg_match('/<body[^>]*>(.*?)<\/body>/si', $phpinfo, $matches);
        $content = $matches[1] ?? $phpinfo;
        
        $content = str_replace(
            ['<table', '<h1', '<h2', 'class="e"', 'class="v"', 'class="h"'],
            ['<table class="table table-sm"', '<h3', '<h4', 'class="text-muted"', '', 'class="table-active"'],
            $content
        );
        
        echo $content;
        exit;
    }
    
    
    public function isAuthenticated(): bool
    {
        if (isset($_SESSION['admin_authenticated'])) {
            if (time() - ($_SESSION['admin_login_time'] ?? 0) > self::ADMIN_SESSION_TIMEOUT) {
                unset($_SESSION['admin_authenticated']);
                unset($_SESSION['admin_user_id']);
                $this->getLogger()->debug('Session expired');
            } else {
                $_SESSION['admin_login_time'] = time();
                return true;
            }
        }
        
        $rememberMe = $this->getRememberMeManager();
        $tokenData = $rememberMe->validateToken();
        
        if ($tokenData !== null) {
            $userId = $tokenData['user_id'];
            $provider = $tokenData['provider'];
            
            if ($userId === 'legacy') {
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_login_time'] = time();
                $_SESSION['admin_user'] = 'Administrator';
                
                $this->getLogger()->info('Session restored from remember me token (password)');
                return true;
            } else {
                $user = $this->getAuthManager()->getUserById($userId);
                
                if ($user && ($user['active'] ?? true)) {
                    $_SESSION['admin_authenticated'] = true;
                    $_SESSION['admin_login_time'] = time();
                    $_SESSION['admin_user'] = $user['name'];
                    $_SESSION['admin_user_id'] = $user['id'];
                    
                    $this->getLogger()->info('Session restored from remember me token', [
                        'user_id' => $userId,
                        'provider' => $provider
                    ]);
                    return true;
                } else {
                    $rememberMe->clearCurrentToken();
                    $this->getLogger()->warning('Remember me token invalid - user not found or inactive', [
                        'user_id' => $userId
                    ]);
                }
            }
        }
        
        return false;
    }
    
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        if (isset($_SESSION['admin_user_id'])) {
            return $this->getAuthManager()->getUserById($_SESSION['admin_user_id']);
        }
        
        return [
            'id' => 'legacy',
            'name' => $_SESSION['admin_user'] ?? 'Administrator',
            'email' => '',
            'role' => 'admin',
            'permissions' => ['all'],
            'picture' => ''
        ];
    }
    
    public function hasPermission(string $permission): bool
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            return false;
        }
        
        if ($user['id'] === 'legacy') {
            return true;
        }
        
        return $this->getAuthManager()->hasPermission($user, $permission);
    }
    
    private function checkLoginAttempts(): bool
    {
        return SecurityManager::rateLimitCheck('admin_login_' . ($_SERVER['REMOTE_ADDR'] ?? ''), self::MAX_LOGIN_ATTEMPTS, self::LOGIN_LOCKOUT_TIME);
    }

    private function newLoginCaptcha(): string
    {
        $a = random_int(2, 9);
        $b = random_int(2, 9);
        $_SESSION['login_captcha'] = ['answer' => $a + $b, 'time' => time()];
        return $a . ' + ' . $b;
    }

    private function checkLoginCaptcha(): bool
    {
        $captcha = $_SESSION['login_captcha'] ?? null;
        unset($_SESSION['login_captcha']);
        $given = trim((string)($_POST['captcha'] ?? ''));
        return is_array($captcha) && time() - $captcha['time'] < 600 && ctype_digit($given) && (int)$given === $captcha['answer'];
    }

    private function requireAdminRole(bool $json = true): void
    {
        if (($this->getCurrentUser()['role'] ?? '') === 'admin') {
            return;
        }
        if ($json) {
            $this->jsonResponse(['error' => 'Permission denied. Only administrators can do this.'], 403);
        }
        http_response_code(403);
        exit('Forbidden');
    }

    private function safeRedirectTarget($url): ?string
    {
        return is_string($url) && preg_match('#^/(?![/\\\\])[^\r\n\\\\]*$#', $url) && strpos($url, '/admin') !== 0 ? $url : null;
    }

    private function showLoginForm(): void
    {
        $authManager = $this->getAuthManager();
        $allowPasswordLogin = $this->config['allow_password_login'] ?? true;
        $storageProblem = $authManager->getStorageProblem();
        if ($storageProblem !== null) {
            $_SESSION['admin_error'] = 'Google and Discord sign-in are disabled: ' . $storageProblem . ' Fix the permissions of the data/ folder.';
        }
        
        $showPasswordLogin = ((!$authManager->isGoogleAuthEnabled() && !$authManager->isDiscordAuthEnabled()) || $allowPasswordLogin) 
                             && !empty($this->config['admin_password']);
        
        $hasUsers = count($authManager->getAllUsers()) > 0;
        
        $this->render('admin/login', [
            'title' => $this->lang->get('admin.login'),
            'error' => $_SESSION['admin_error'] ?? null,
            'currentPage' => 'admin',
            'googleAuthEnabled' => $storageProblem === null && $authManager->isGoogleAuthEnabled(),
            'googleAuthUrl' => $storageProblem === null && $authManager->isGoogleAuthEnabled() ? $authManager->getGoogleAuthUrl() : '',
            'discordAuthEnabled' => $storageProblem === null && $authManager->isDiscordAuthEnabled(),
            'discordAuthUrl' => $storageProblem === null && $authManager->isDiscordAuthEnabled() ? $authManager->getDiscordAuthUrl() : '',
            'showPasswordLogin' => $showPasswordLogin,
            'captchaQuestion' => $showPasswordLogin ? $this->newLoginCaptcha() : '',
            'allowPasswordLogin' => $allowPasswordLogin,
            'hasUsers' => $hasUsers
        ]);
        
        unset($_SESSION['admin_error']);
    }
    
    public function oauthCallback(): void
    {
        $authManager = $this->getAuthManager();
        if ($authManager->getStorageProblem() !== null) {
            $_SESSION['admin_error'] = 'Sign-in is disabled: ' . $authManager->getStorageProblem() . ' Fix the permissions of the data/ folder.';
            $this->getLogger()->warning('OAuth sign-in blocked, user storage unavailable', ['problem' => $authManager->getStorageProblem()]);
            $this->redirect(url('admin'));
            return;
        }
        $provider = $_GET['provider'] ?? 'google';
        
        $rememberMe = $_SESSION['oauth_remember_me'] ?? false;
        unset($_SESSION['oauth_remember_me']);
        
        if (!in_array($provider, ['google', 'discord'])) {
            $_SESSION['admin_error'] = 'Invalid OAuth provider';
            $this->redirect(url('admin'));
            return;
        }
        
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
            $this->getLogger()->warning('OAuth cancelled or failed', [
                'provider' => $provider,
                'error' => $error
            ]);
            $this->redirect(url('admin'));
            return;
        }
        
        if (empty($code)) {
            $_SESSION['admin_error'] = 'Invalid OAuth response';
            $this->redirect(url('admin'));
            return;
        }

        if (!\core\AuthManager::checkOauthState((string)($_GET['state'] ?? ''))) {
            $_SESSION['admin_error'] = 'Invalid OAuth state. Please try signing in again.';
            $this->getLogger()->warning('OAuth callback with a wrong state', ['provider' => $provider]);
            $this->redirect(url('admin'));
            return;
        }
        
        if ($provider === 'google') {
            $oauthUser = $authManager->processGoogleCallback($code);
            $user = $oauthUser ? $authManager->authenticateGoogle($oauthUser) : null;
        } else {
            $oauthUser = $authManager->processDiscordCallback($code);
            $user = $oauthUser ? $authManager->authenticateDiscord($oauthUser) : null;
        }
        
        if (!$oauthUser) {
            $_SESSION['admin_error'] = 'Failed to authenticate with ' . ucfirst($provider);
            $this->getLogger()->error('OAuth authentication failed', ['provider' => $provider]);
            $this->redirect(url('admin'));
            return;
        }
        
        if (!$user) {
            $_SESSION['admin_error'] = 'You are not authorized to access the admin panel. Contact an administrator.';
            $this->getLogger()->warning('OAuth user not authorized', [
                'provider' => $provider,
                'email' => $oauthUser['email'] ?? 'unknown'
            ]);
            $this->redirect(url('admin'));
            return;
        }
        
        if (!($user['active'] ?? true)) {
            $_SESSION['admin_error'] = 'Your account has been deactivated';
            $this->redirect(url('admin'));
            return;
        }
        
        session_regenerate_id(true);
        $_SESSION['admin_authenticated'] = true;
        $_SESSION['admin_login_time'] = time();
        $_SESSION['admin_user'] = $user['name'];
        $_SESSION['admin_user_id'] = $user['id'];
        
        if ($rememberMe) {
            $this->getRememberMeManager()->createToken($user['id'], $provider);
            $this->getLogger()->info('Remember me token created for OAuth login', [
                'user_id' => $user['id'],
                'provider' => $provider
            ]);
        }
        
        $this->logAdminAction($provider . '_login_success', 'Successfully logged in via ' . ucfirst($provider), [
            'user_id' => $user['id'],
            'email' => $user['email']
        ]);
        
        $this->getLogger()->info('OAuth login successful', [
            'provider' => $provider,
            'user_id' => $user['id'],
            'remember_me' => $rememberMe
        ]);
        
        $redirectUrl = $_SESSION['redirect_after_login'] ?? null;
        unset($_SESSION['redirect_after_login']);
        
        if ($target = $this->safeRedirectTarget($redirectUrl)) {
            $this->redirect($target);
        } else {
            $this->redirect(url('admin'));
        }
    }
    
    public function oauthPrepare(): void
    {
        if (!SecurityManager::validateRequestMethod('POST')) {
            $this->redirect(url('admin'));
            return;
        }
        
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $_SESSION['admin_error'] = 'Invalid security token';
            $this->redirect(url('admin'));
            return;
        }
        
        $provider = $_POST['provider'] ?? 'google';
        $rememberMe = isset($_POST['remember_me']) && $_POST['remember_me'] === '1';
        
        $_SESSION['oauth_remember_me'] = $rememberMe;
        
        $this->getLogger()->debug('OAuth prepare', [
            'provider' => $provider,
            'remember_me' => $rememberMe
        ]);
        
        $authManager = $this->getAuthManager();
        if ($authManager->getStorageProblem() !== null) {
            $_SESSION['admin_error'] = 'Sign-in is disabled: ' . $authManager->getStorageProblem() . ' Fix the permissions of the data/ folder.';
            $this->redirect(url('admin'));
            return;
        }
        
        if ($provider === 'google' && $authManager->isGoogleAuthEnabled()) {
            $this->redirect($authManager->getGoogleAuthUrl());
        } elseif ($provider === 'discord' && $authManager->isDiscordAuthEnabled()) {
            $this->redirect($authManager->getDiscordAuthUrl());
        } else {
            $_SESSION['admin_error'] = 'Invalid OAuth provider';
            $this->redirect(url('admin'));
        }
    }
    
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
        $discordId = trim((string)($input['discord_id'] ?? ''));
        $name = trim($input['name'] ?? '');
        $role = $input['role'] ?? 'viewer';

        if ($email === '' && $discordId === '') {
            $this->jsonResponse(['error' => 'Enter an email or a Discord ID'], 400);
            return;
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->jsonResponse(['error' => 'Valid email is required'], 400);
            return;
        }
        if ($discordId !== '' && !preg_match('/^\d{15,25}$/', $discordId)) {
            $this->jsonResponse(['error' => 'Discord ID must be the numeric user ID (15-25 digits)'], 400);
            return;
        }

        if ($email !== '' && $this->getAuthManager()->getUserByEmail($email)) {
            $this->jsonResponse(['error' => 'User with this email already exists'], 400);
            return;
        }
        if ($discordId !== '' && $this->getAuthManager()->getUserByDiscordId($discordId)) {
            $this->jsonResponse(['error' => 'User with this Discord ID already exists'], 400);
            return;
        }
        
        $roles = \core\AuthManager::getRoles();
        if (!isset($roles[$role])) {
            $role = 'viewer';
        }
        
        $label = $email !== '' ? $email : 'Discord ' . $discordId;
        try {
            $user = $this->getAuthManager()->createUser([
                'email' => $email,
                'discord_id' => $discordId,
                'name' => $name ?: $label,
                'role' => $role,
                'permissions' => $roles[$role]['permissions']
            ]);
        } catch (\RuntimeException $e) {
            $this->jsonResponse(['error' => $e->getMessage()], 500);
            return;
        }

        $this->logAdminAction('user_created', "Created user {$label}", [
            'user_id' => $user['id'],
            'email' => $email,
            'discord_id' => $discordId,
            'role' => $role
        ]);
        
        $this->jsonResponse(['success' => true, 'user' => $user]);
    }
    
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
            
            fputcsv($output, ["Table: $table"]);
            
            fputcsv($output, array_keys($rows[0]));
            
            foreach ($rows as $row) {
                fputcsv($output, array_values($row));
            }
            
            fputcsv($output, []);
        }
        
        fclose($output);
        exit;
    }
    
    private function importData(array $data): int
    {
        $imported = 0;
        
        foreach ($data as $table => $rows) {
            if (!is_array($rows) || !in_array($table, ['bans', 'mutes', 'warnings', 'kicks'], true)) continue;

            $tableName = $this->repository->getTablePrefix() . $table;

            foreach ($rows as $row) {
                if (!is_array($row) || array_filter(array_keys($row), fn($c) => !preg_match('/^[A-Za-z_][A-Za-z0-9_]{0,63}$/', (string)$c))) continue;

                try {
                    $sql = "SELECT id FROM {$tableName} WHERE id = :id";
                    $stmt = $this->repository->getConnection()->prepare($sql);
                    $stmt->execute([':id' => $row['id'] ?? 0]);
                    
                    if ($stmt->fetch()) {
                        $this->updateRecord($tableName, $row);
                    } else {
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
    
    public function diagnosticAvatar(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        try {
            $envFile = BASE_DIR . '/.env';
            $envContent = file_get_contents($envFile);
            
            $envAvatarProvider = null;
            $envAvatarUrl = null;
            $envAvatarUrlOffline = null;
            
            if (preg_match('/^AVATAR_PROVIDER=(.*)$/m', $envContent, $matches)) {
                $envAvatarProvider = trim($matches[1]);
            }
            if (preg_match('/^AVATAR_URL=(.*)$/m', $envContent, $matches)) {
                $envAvatarUrl = trim($matches[1]);
            }
            if (preg_match('/^AVATAR_URL_OFFLINE=(.*)$/m', $envContent, $matches)) {
                $envAvatarUrlOffline = trim($matches[1]);
            }
            
            $envLoaderProvider = \core\EnvLoader::get('AVATAR_PROVIDER');
            $envLoaderUrl = \core\EnvLoader::get('AVATAR_URL');
            $envLoaderUrlOffline = \core\EnvLoader::get('AVATAR_URL_OFFLINE');
            
            $configProvider = $this->config['avatar_provider'] ?? null;
            $configUrl = $this->config['avatar_url'] ?? null;
            $configUrlOffline = $this->config['avatar_url_offline'] ?? null;
            
            $testUuid = '069a79f4-44e9-4726-a5be-fca90e38aaf5';
            $testName = 'Notch';
            $testAvatarUrl = $this->getAvatarUrl($testUuid, $testName);
            
            $opcacheEnabled = function_exists('opcache_get_status') && opcache_get_status();
            $apcuEnabled = function_exists('apcu_cache_info') && apcu_enabled();
            
            $this->jsonResponse([
                'success' => true,
                'timestamp' => date('Y-m-d H:i:s'),
                'env_file' => [
                    'exists' => file_exists($envFile),
                    'readable' => is_readable($envFile),
                    'writable' => is_writable($envFile),
                    'avatar_provider' => $envAvatarProvider,
                    'avatar_url' => $envAvatarUrl,
                    'avatar_url_offline' => $envAvatarUrlOffline
                ],
                'env_loader' => [
                    'avatar_provider' => $envLoaderProvider,
                    'avatar_url' => $envLoaderUrl,
                    'avatar_url_offline' => $envLoaderUrlOffline
                ],
                'config_array' => [
                    'avatar_provider' => $configProvider,
                    'avatar_url' => $configUrl,
                    'avatar_url_offline' => $configUrlOffline
                ],
                'test_avatar' => [
                    'uuid' => $testUuid,
                    'name' => $testName,
                    'generated_url' => $testAvatarUrl
                ],
                'cache_status' => [
                    'opcache_enabled' => $opcacheEnabled,
                    'apcu_enabled' => $apcuEnabled
                ],
                'recommendation' => $this->getAvatarDiagnosticRecommendation(
                    $envAvatarProvider,
                    $envLoaderProvider,
                    $configProvider,
                    $testAvatarUrl
                )
            ]);
            
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function getAvatarDiagnosticRecommendation(
        ?string $envProvider,
        ?string $loaderProvider,
        ?string $configProvider,
        string $testUrl
    ): string {
        if ($envProvider !== $loaderProvider) {
            return 'EnvLoader is not reading from .env correctly. Reload EnvLoader or restart PHP-FPM.';
        }
        
        if ($loaderProvider !== $configProvider) {
            return 'Config is not loading from EnvLoader. Clear OPcache and reload config.';
        }
        
        if ($configProvider === 'mineatar' && strpos($testUrl, 'mineatar.io') === false) {
            return 'getAvatarUrl() is not using the correct provider. Config is not being applied to BaseController.';
        }
        
        if ($configProvider === 'crafatar' && strpos($testUrl, 'crafatar.com') === false) {
            return 'getAvatarUrl() is not using the correct provider. Config is not being applied to BaseController.';
        }
        
        if ($configProvider === 'cravatar' && strpos($testUrl, 'cravatar.eu') === false) {
            return 'getAvatarUrl() is not using the correct provider. Config is not being applied to BaseController.';
        }
        
        return 'All settings match correctly. Clear browser cache (Ctrl+F5) if avatars still appear old.';
    }
    
    public function testDatabase(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        try {
            $results = [
                'success' => true,
                'tables' => [],
                'timestamp_test' => [],
                'warnings' => []
            ];
            
            $tables = ['bans', 'mutes', 'warnings', 'kicks', 'history'];
            
            foreach ($tables as $tableName) {
                $fullTable = $this->repository->getTablePrefix() . $tableName;
                
                try {
                    $stmt = $this->repository->getConnection()->query(
                        "SHOW TABLES LIKE '" . $fullTable . "'"
                    );
                    $exists = $stmt->rowCount() > 0;
                    
                    if (!$exists) {
                        $results['tables'][$tableName] = [
                            'exists' => false,
                            'status' => 'missing'
                        ];
                        $results['warnings'][] = "Table {$fullTable} does not exist";
                        continue;
                    }
                    
                    $stmt = $this->repository->getConnection()->query(
                        "DESCRIBE {$fullTable}"
                    );
                    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    
                    $hasTime = false;
                    $hasUntil = false;
                    
                    foreach ($columns as $col) {
                        if ($col['Field'] === 'time') $hasTime = true;
                        if ($col['Field'] === 'until') $hasUntil = true;
                    }
                    
                    if ($hasTime && in_array($tableName, ['bans', 'mutes'])) {
                        $stmt = $this->repository->getConnection()->query(
                            "SELECT time, until, active FROM {$fullTable} ORDER BY time DESC LIMIT 1"
                        );
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        
                        if ($row) {
                            $timeInSeconds = intval($row['time'] / 1000);
                            $untilInSeconds = $row['until'] > 0 ? intval($row['until'] / 1000) : 0;
                            
                            $results['timestamp_test'][$tableName] = [
                                'raw_time' => $row['time'],
                                'time_seconds' => $timeInSeconds,
                                'time_date' => date('Y-m-d H:i:s', $timeInSeconds),
                                'raw_until' => $row['until'],
                                'until_seconds' => $untilInSeconds,
                                'until_date' => $untilInSeconds > 0 ? date('Y-m-d H:i:s', $untilInSeconds) : 'Permanent',
                                'active' => $row['active'],
                                'is_valid' => $timeInSeconds > 0 && $timeInSeconds < 2147483647
                            ];
                            
                            if ($timeInSeconds <= 0) {
                                $results['warnings'][] = "Invalid timestamp in {$tableName}: time={$row['time']}";
                            }
                            
                            if ($untilInSeconds > 0 && $untilInSeconds < $timeInSeconds) {
                                $results['warnings'][] = "Until time is before start time in {$tableName}";
                            }
                        }
                    }
                    
                    $results['tables'][$tableName] = [
                        'exists' => true,
                        'status' => 'ok',
                        'has_time' => $hasTime,
                        'has_until' => $hasUntil,
                        'columns' => count($columns)
                    ];
                    
                } catch (PDOException $e) {
                    $results['tables'][$tableName] = [
                        'exists' => false,
                        'status' => 'error',
                        'error' => $e->getMessage()
                    ];
                    $results['warnings'][] = "Error testing {$tableName}: " . $e->getMessage();
                }
            }
            
            $results['server_info'] = [
                'php_time' => time(),
                'php_time_ms' => time() * 1000,
                'php_date' => date('Y-m-d H:i:s'),
                'timezone' => date_default_timezone_get()
            ];
            
            $this->jsonResponse($results);
            
        } catch (Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function clearAllCache(): void
    {
        if (!$this->isAuthenticated()) {
            $this->jsonResponse(['error' => 'Unauthorized'], 401);
            return;
        }
        
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid security token'], 400);
            return;
        }
        
        try {
            $cleared = [];
            
            if (function_exists('opcache_reset')) {
                opcache_reset();
                $cleared[] = 'OPcache';
            }
            
            \core\EnvLoader::reload();
            $cleared[] = 'Environment config';
            
            if (function_exists('apcu_clear_cache')) {
                apcu_clear_cache();
                $cleared[] = 'APCu cache';
            }
            
            $this->logAdminAction('cache_clear', 'Cleared all caches', [
                'caches' => $cleared
            ]);
            
            $this->jsonResponse([
                'success' => true,
                'message' => 'All caches cleared successfully',
                'cleared' => $cleared
            ]);
            
        } catch (Exception $e) {
            error_log("Clear cache error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Failed to clear caches'], 500);
        }
    }
    
    public function checkGitHubVersion(): void
    {
        try {
            $owner = 'Yamiru';
            $repo = 'LiteBansU';
            
            $url = "https://raw.githubusercontent.com/{$owner}/{$repo}/main/.version";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 5,
                    'user_agent' => 'LiteBansU-Version-Checker'
                ]
            ]);
            
            $githubVersion = @file_get_contents($url, false, $context);
            
            if ($githubVersion === false) {
                $this->jsonResponse([
                    'success' => false,
                    'error' => 'Could not fetch GitHub version'
                ]);
                return;
            }
            
            $localVersion = file_exists(BASE_DIR . '/.version') 
                ? trim(file_get_contents(BASE_DIR . '/.version')) 
                : '3.3';
            
            $githubVersion = trim($githubVersion);
            
            $updateAvailable = version_compare($githubVersion, $localVersion, '>');
            
            $this->jsonResponse([
                'success' => true,
                'local_version' => $localVersion,
                'github_version' => $githubVersion,
                'update_available' => $updateAvailable
            ]);
            
        } catch (Exception $e) {
            error_log("GitHub version check error: " . $e->getMessage());
            $this->jsonResponse([
                'success' => false,
                'error' => 'Failed to check GitHub version'
            ]);
        }
    }
    
    public function generateSitemap(): string
    {
        $baseUrl = rtrim($this->config['site_url'] ?? 'https://yoursite.com', '/');
        $currentDate = date('Y-m-d\TH:i:sP');
        
        $urls = [
            ['loc' => '/', 'priority' => '1.0'],
            ['loc' => '/bans', 'priority' => '0.9'],
            ['loc' => '/mutes', 'priority' => '0.9'],
            ['loc' => '/warnings', 'priority' => '0.8'],
            ['loc' => '/kicks', 'priority' => '0.7'],
            ['loc' => '/stats', 'priority' => '0.8'],
            ['loc' => '/protest', 'priority' => '0.6'],
        ];
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"' . "\n";
        $xml .= '        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"' . "\n";
        $xml .= '        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9' . "\n";
        $xml .= '        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= "    <url>\n";
            $xml .= "        <loc>" . htmlspecialchars($baseUrl . $url['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "        <lastmod>{$currentDate}</lastmod>\n";
            $xml .= "        <priority>" . htmlspecialchars($url['priority'], ENT_XML1, 'UTF-8') . "</priority>\n";
            $xml .= "    </url>\n";
        }
        
        $xml .= "</urlset>";
        
        return $xml;
    }
}
