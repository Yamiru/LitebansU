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

class StatsController extends BaseController
{
    public function index(): void
    {
        try {
            // Get comprehensive statistics
            $stats = $this->getAdvancedStats();
            
            $this->render('stats', [
                'title' => $this->lang->get('nav.statistics'),
                'stats' => $stats,
                'currentPage' => 'stats'
            ]);
            
        } catch (Exception $e) {
            error_log("Error loading statistics: " . $e->getMessage());
            $this->render('error', [
                'title' => $this->lang->get('error.server_error'),
                'message' => $this->lang->get('error.loading_failed')
            ]);
        }
    }
    
    public function clearCache(): void
    {
        // Check if request is from admin or authorized user
        $isAdmin = false;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true) {
            $isAdmin = true;
        }
        
        // Verify CSRF token
        if (!isset($_POST['csrf_token'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Missing CSRF token'], 400);
            return;
        }
        
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid CSRF token'], 400);
            return;
        }
        
        // Rate limiting only for non-admin users
        if (!$isAdmin) {
            $clientIp = SecurityManager::getClientIp();
            if (!SecurityManager::rateLimitCheck('cache_clear_' . $clientIp, 5, 300)) {
                $this->jsonResponse(['success' => false, 'message' => 'Too many cache clear requests. Please wait.'], 429);
                return;
            }
        }
        
        try {
            $clearAll = isset($_POST['clear_all']) && $_POST['clear_all'] === '1';
            
            if ($clearAll) {
                // Clear all cache including opcache
                $cleared = $this->clearAllCache();
            } else {
                // Clear only stats cache
                $cleared = $this->clearStatsCache();
            }
            
            if ($cleared) {
                $this->jsonResponse([
                    'success' => true,
                    'message' => $clearAll ? 'All cache cleared successfully' : 'Statistics cache cleared successfully'
                ]);
            } else {
                $this->jsonResponse([
                    'success' => false,
                    'message' => 'No cache files were found or cleared'
                ], 200); // Return 200 but with success: false
            }
            
        } catch (Exception $e) {
            error_log("Cache clear error: " . $e->getMessage());
            $this->jsonResponse(['success' => false, 'message' => 'Cache clear failed: ' . $e->getMessage()], 500);
        }
    }
    
    private function getAdvancedStats(): array
    {
        $cacheKey = 'advanced_stats';
        $cacheFile = sys_get_temp_dir() . '/litebans_' . md5($cacheKey) . '.cache';
        
        // Check cache first (5 minute expiry)
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < 300) {
            $cached = @file_get_contents($cacheFile);
            if ($cached !== false) {
                $data = @json_decode($cached, true);
                if ($data !== null) {
                    return $data;
                }
            }
        }
        
        // Get basic stats
        $stats = $this->repository->getStats();
        
        // Get top banned players
        $stats['top_banned_players'] = $this->getTopBannedPlayers();
        
        // Get most active staff
        $stats['most_active_staff'] = $this->getMostActiveStaff();
        
        // Get punishment trends (last 30 days)
        $stats['punishment_trends'] = $this->getPunishmentTrends();
        
        // Get recent activity summary
        $stats['recent_activity'] = $this->getRecentActivity();
        
        // Get ban reasons analysis
        $stats['top_ban_reasons'] = $this->getTopBanReasons();
        
        // Get server activity by day
        $stats['daily_activity'] = $this->getDailyActivity();
        
        // Cache the results
        @file_put_contents($cacheFile, json_encode($stats), LOCK_EX);
        
        return $stats;
    }
    
    private function getTopBannedPlayers(int $limit = 10): array
    {
        try {
            $bansTable = $this->repository->getTablePrefix() . 'bans';
            $historyTable = $this->repository->getTablePrefix() . 'history';
            
            $sql = "SELECT b.uuid, h.name as player_name, COUNT(*) as ban_count,
                           MAX(b.time) as last_ban_time,
                           SUM(CASE WHEN b.active = 1 THEN 1 ELSE 0 END) as active_bans
                    FROM {$bansTable} b
                    LEFT JOIN (
                        SELECT h1.uuid, h1.name
                        FROM {$historyTable} h1
                        INNER JOIN (
                            SELECT uuid, MAX(date) as max_date
                            FROM {$historyTable}
                            GROUP BY uuid
                        ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                    ) h ON b.uuid = h.uuid
                    WHERE b.uuid IS NOT NULL AND b.uuid != '#'
                    GROUP BY b.uuid, h.name
                    ORDER BY ban_count DESC, last_ban_time DESC
                    LIMIT :limit";
            
            $stmt = $this->repository->getConnection()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top banned players: " . $e->getMessage());
            return [];
        }
    }
    
    private function getMostActiveStaff(int $limit = 10): array
    {
        try {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $results = [];
            
            foreach ($tables as $table) {
                $fullTable = $this->repository->getTablePrefix() . $table;
                
                $sql = "SELECT banned_by_name as staff_name, 
                               COUNT(*) as count,
                               '{$table}' as punishment_type,
                               MAX(time) as last_action
                        FROM {$fullTable} 
                        WHERE banned_by_name IS NOT NULL 
                        AND banned_by_name != '' 
                        AND banned_by_name != 'CONSOLE'
                        AND uuid IS NOT NULL 
                        AND uuid != '#'
                        GROUP BY banned_by_name";
                
                $stmt = $this->repository->getConnection()->query($sql);
                $tableResults = $stmt->fetchAll();
                
                foreach ($tableResults as $row) {
                    $staffName = $row['staff_name'];
                    if (!isset($results[$staffName])) {
                        $results[$staffName] = [
                            'staff_name' => $staffName,
                            'total_punishments' => 0,
                            'bans' => 0,
                            'mutes' => 0,
                            'warnings' => 0,
                            'kicks' => 0,
                            'last_action' => 0
                        ];
                    }
                    
                    $results[$staffName]['total_punishments'] += (int)$row['count'];
                    $results[$staffName][$table] = (int)$row['count'];
                    $results[$staffName]['last_action'] = max($results[$staffName]['last_action'], (int)$row['last_action']);
                }
            }
            
            // Sort by total punishments
            uasort($results, function($a, $b) {
                return $b['total_punishments'] <=> $a['total_punishments'];
            });
            
            return array_slice(array_values($results), 0, $limit);
        } catch (PDOException $e) {
            error_log("Error getting most active staff: " . $e->getMessage());
            return [];
        }
    }
    
    private function getPunishmentTrends(): array
    {
        try {
            $thirtyDaysAgo = (time() - (30 * 24 * 60 * 60)) * 1000;
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $trends = [];
            
            foreach ($tables as $table) {
                $fullTable = $this->repository->getTablePrefix() . $table;
                
                $sql = "SELECT DATE(FROM_UNIXTIME(time/1000)) as date, COUNT(*) as count
                        FROM {$fullTable}
                        WHERE time >= :since
                        AND uuid IS NOT NULL AND uuid != '#'
                        GROUP BY DATE(FROM_UNIXTIME(time/1000))
                        ORDER BY date DESC";
                
                $stmt = $this->repository->getConnection()->prepare($sql);
                $stmt->bindValue(':since', $thirtyDaysAgo, PDO::PARAM_INT);
                $stmt->execute();
                
                $trends[$table] = $stmt->fetchAll();
            }
            
            return $trends;
        } catch (PDOException $e) {
            error_log("Error getting punishment trends: " . $e->getMessage());
            return [];
        }
    }
    
    private function getRecentActivity(): array
    {
        try {
            $oneDayAgo = (time() - 86400) * 1000;
            $oneWeekAgo = (time() - (7 * 86400)) * 1000;
            $oneMonthAgo = (time() - (30 * 86400)) * 1000;
            
            $activity = [
                'last_24h' => [],
                'last_7d' => [],
                'last_30d' => []
            ];
            
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $periods = [
                'last_24h' => $oneDayAgo,
                'last_7d' => $oneWeekAgo,
                'last_30d' => $oneMonthAgo
            ];
            
            foreach ($periods as $period => $timestamp) {
                foreach ($tables as $table) {
                    $fullTable = $this->repository->getTablePrefix() . $table;
                    
                    $sql = "SELECT COUNT(*) as count FROM {$fullTable} 
                            WHERE time >= :since 
                            AND uuid IS NOT NULL AND uuid != '#'";
                    
                    $stmt = $this->repository->getConnection()->prepare($sql);
                    $stmt->bindValue(':since', $timestamp, PDO::PARAM_INT);
                    $stmt->execute();
                    
                    $result = $stmt->fetch();
                    $activity[$period][$table] = (int)($result['count'] ?? 0);
                }
            }
            
            return $activity;
        } catch (PDOException $e) {
            error_log("Error getting recent activity: " . $e->getMessage());
            return [];
        }
    }
    
    private function getTopBanReasons(int $limit = 10): array
    {
        try {
            $bansTable = $this->repository->getTablePrefix() . 'bans';
            
            $sql = "SELECT reason, COUNT(*) as count
                    FROM {$bansTable}
                    WHERE reason IS NOT NULL 
                    AND reason != ''
                    AND uuid IS NOT NULL AND uuid != '#'
                    GROUP BY reason
                    ORDER BY count DESC
                    LIMIT :limit";
            
            $stmt = $this->repository->getConnection()->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top ban reasons: " . $e->getMessage());
            return [];
        }
    }
    
    private function getDailyActivity(): array
    {
        try {
            $sevenDaysAgo = (time() - (7 * 24 * 60 * 60)) * 1000;
            $bansTable = $this->repository->getTablePrefix() . 'bans';
            
            $sql = "SELECT 
                        DAYOFWEEK(FROM_UNIXTIME(time/1000)) as day_of_week,
                        DAYNAME(FROM_UNIXTIME(time/1000)) as day_name,
                        COUNT(*) as count
                    FROM {$bansTable}
                    WHERE time >= :since
                    AND uuid IS NOT NULL AND uuid != '#'
                    GROUP BY DAYOFWEEK(FROM_UNIXTIME(time/1000)), DAYNAME(FROM_UNIXTIME(time/1000))
                    ORDER BY day_of_week";
            
            $stmt = $this->repository->getConnection()->prepare($sql);
            $stmt->bindValue(':since', $sevenDaysAgo, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting daily activity: " . $e->getMessage());
            return [];
        }
    }
    
    private function clearStatsCache(): bool
    {
        try {
            $cachePattern = sys_get_temp_dir() . '/litebans_*.cache';
            $files = glob($cachePattern);
            
            $cleared = 0;
            foreach ($files as $file) {
                if (@unlink($file)) {
                    $cleared++;
                }
            }
            
            // Also clear APCu cache if available
            if (function_exists('apcu_enabled') && apcu_enabled()) {
                apcu_clear_cache();
            }
            
            return $cleared > 0 || true; // Return true even if no files (cache might be empty)
        } catch (Exception $e) {
            error_log("Error clearing cache: " . $e->getMessage());
            return false;
        }
    }
    
    private function clearAllCache(): bool
    {
        try {
            // Clear stats cache
            $statsCleared = $this->clearStatsCache();
            
            // Clear OPcache if available
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            
            // Clear APCu completely if available
            if (function_exists('apcu_enabled') && apcu_enabled()) {
                apcu_clear_cache();
            }
            
            // Clear session cache files
            $sessionPattern = sys_get_temp_dir() . '/sess_*';
            $sessionFiles = glob($sessionPattern);
            if ($sessionFiles) {
                foreach ($sessionFiles as $file) {
                    // Only delete old sessions (older than 1 hour)
                    if (filemtime($file) < (time() - 3600)) {
                        @unlink($file);
                    }
                }
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error clearing all cache: " . $e->getMessage());
            return false;
        }
    }
}