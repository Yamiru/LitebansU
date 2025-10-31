<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       2.3
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

declare(strict_types=1);

class HomeController extends BaseController
{
    public function index(): void
    {
        $stats = $this->repository->getStats();
        $recentBans = $this->repository->getBans(5, 0, true);
        $recentMutes = $this->repository->getMutes(5, 0, true);
        
        // Ensure data is properly formatted
        $recentBans = $this->ensurePlayerNames($recentBans);
        $recentMutes = $this->ensurePlayerNames($recentMutes);
        
        // Check if there's a search parameter
        $searchQuery = $_GET['search'] ?? null;
        
        $this->render('home', [
            'stats' => $stats,
            'recentBans' => $recentBans,
            'recentMutes' => $recentMutes,
            'controller' => $this,
            'currentPage' => 'home',
            'searchQuery' => $searchQuery
        ]);
    }
    
    public function search(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSearch();
            return;
        }
        
        $this->render('search', [
            'currentPage' => 'search'
        ]);
    }
    
    private function handleSearch(): void
    {
        // Verify it's an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
            return;
        }
        
        // Validate CSRF token
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid CSRF token'], 400);
            return;
        }
        
        $query = trim($_POST['query'] ?? '');
        if (empty($query) || strlen($query) < 2) {
            $this->jsonResponse(['error' => 'Search query must be at least 2 characters'], 400);
            return;
        }
        
        // Rate limiting
        $clientIp = SecurityManager::getClientIp();
        if (!SecurityManager::rateLimitCheck('search_' . $clientIp, 30, 60)) {
            $this->jsonResponse(['error' => 'Too many requests. Please try again later.'], 429);
            return;
        }
        
        $query = SecurityManager::sanitizeInput($query);
        
        try {
            $punishments = [];
            
            // First, check if query is a numeric ID (ban ID search)
            if (is_numeric($query)) {
                $punishments = $this->searchById((int)$query);
            }
            
            // If no results from ID search, try player search
            if (empty($punishments)) {
                $punishments = $this->repository->getPlayerPunishments($query);
            }
            
            // If still no results, try flexible search
            if (empty($punishments)) {
                $punishments = $this->searchFlexible($query);
            }
            
            $this->jsonResponse([
                'success' => true,
                'player' => $query,
                'punishments' => $this->formatPunishmentsForSearch($punishments)
            ]);
        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Search failed. Please try again.'], 500);
        }
    }
    
    /**
     * Search punishments by ID (ban/mute/warning/kick ID)
     */
    private function searchById(int $id): array
    {
        try {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $results = [];
            $historyTable = $this->repository->getTablePrefix() . 'history';
            
            foreach ($tables as $table) {
                $fullTable = $this->repository->getTablePrefix() . $table;
                
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
                        WHERE p.id = :id
                        AND p.uuid IS NOT NULL AND p.uuid != '#'
                        LIMIT 1";
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->execute([':id' => $id]);
                $result = $stmt->fetch();
                
                if ($result) {
                    $results[] = $result;
                }
            }
            
            return $results;
        } catch (Exception $e) {
            error_log("ID search error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Flexible search that can handle partial names and case variations
     */
    private function searchFlexible(string $query): array
    {
        try {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $results = [];
            $historyTable = $this->repository->getTablePrefix() . 'history';
            
            // First try to find player in history by partial name match
            $sql = "SELECT DISTINCT uuid, name FROM {$historyTable} 
                    WHERE LOWER(name) LIKE LOWER(:query) 
                    ORDER BY date DESC 
                    LIMIT 10";
            
            $stmt = $this->repository->getConnection()->prepare($sql);
            $stmt->execute([':query' => '%' . $query . '%']);
            $players = $stmt->fetchAll();
            
            // For each matching player, get their punishments
            foreach ($players as $player) {
                if (!empty($player['uuid'])) {
                    $playerPunishments = $this->repository->getPlayerPunishments($player['uuid']);
                    $results = array_merge($results, $playerPunishments);
                }
            }
            
            // If still no results, search directly in punishment tables
            if (empty($results)) {
                foreach ($tables as $table) {
                    $fullTable = $this->repository->getTablePrefix() . $table;
                    
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
                            WHERE (h.name LIKE :query OR p.reason LIKE :query OR LOWER(p.banned_by_name) LIKE LOWER(:query))
                            AND p.uuid IS NOT NULL AND p.uuid != '#'
                            GROUP BY p.id
                            ORDER BY p.time DESC
                            LIMIT 50";
                    
                    $stmt = $this->repository->getConnection()->prepare($sql);
                    $stmt->execute([':query' => '%' . $query . '%']);
                    
                    $tableResults = $stmt->fetchAll();
                    foreach ($tableResults as &$row) {
                        if (empty($row['player_name']) && !empty($row['uuid'])) {
                            $row['player_name'] = $this->repository->getPlayerName($row['uuid']);
                        }
                    }
                    
                    $results = array_merge($results, $tableResults);
                }
            }
            
            // Sort by time descending
            usort($results, function($a, $b) {
                return ($b['time'] ?? 0) <=> ($a['time'] ?? 0);
            });
            
            return array_slice($results, 0, 50); // Limit to 50 results
            
        } catch (Exception $e) {
            error_log("Flexible search error: " . $e->getMessage());
            return [];
        }
    }
    
    private function formatPunishmentsForSearch(array $punishments): array
    {
        return array_map(function($punishment) {
            // Ensure player name exists
            $playerName = $punishment['player_name'] ?? $punishment['name'] ?? null;
            if (empty($playerName) && !empty($punishment['uuid'])) {
                $playerName = $this->repository->getPlayerName($punishment['uuid']);
            }
            
            return [
                'id' => $punishment['id'] ?? null,
                'type' => $punishment['type'] ?? 'unknown',
                'player_name' => SecurityManager::preventXss($playerName ?? 'Unknown'),
                'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
                'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? 'Console'),
                'date' => $this->formatDate((int)($punishment['time'] ?? 0)),
                'until' => isset($punishment['until']) && $punishment['until'] > 0 
                    ? $this->formatDuration((int)$punishment['until']) 
                    : null,
                'active' => (bool)($punishment['active'] ?? false)
            ];
        }, $punishments);
    }
    
    private function ensurePlayerNames(array $punishments): array
    {
        foreach ($punishments as &$punishment) {
            // Ensure player name exists
            if (empty($punishment['player_name']) && empty($punishment['name'])) {
                if (!empty($punishment['uuid'])) {
                    $name = $this->repository->getPlayerName($punishment['uuid']);
                    $punishment['player_name'] = $name ?? 'Unknown';
                } else {
                    $punishment['player_name'] = 'Unknown';
                }
            } elseif (empty($punishment['player_name']) && !empty($punishment['name'])) {
                $punishment['player_name'] = $punishment['name'];
            }
            
            // Ensure UUID exists
            if (empty($punishment['uuid'])) {
                $punishment['uuid'] = '';
            }
            
            // Ensure other required fields exist
            $punishment['reason'] = $punishment['reason'] ?? 'No reason provided';
            $punishment['time'] = $punishment['time'] ?? 0;
            $punishment['active'] = $punishment['active'] ?? 0;
        }
        
        return $punishments;
    }
}
