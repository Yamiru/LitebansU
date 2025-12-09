<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.5
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

class DatabaseRepository
{
    private PDO $connection;
    private string $tablePrefix;
    
    public function __construct(PDO $connection, string $tablePrefix = 'litebans_')
    {
        $this->connection = $connection;
        $this->tablePrefix = $tablePrefix;
    }
    
    public function getConnection(): PDO
    {
        return $this->connection;
    }
    
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }
    
    public function getBans(int $limit = 20, int $offset = 0, bool $activeOnly = true, string $sort = 'time', string $order = 'DESC', bool $showSilent = true): array
    {
        try {
            $table = $this->tablePrefix . 'bans';
            $historyTable = $this->tablePrefix . 'history';
            
            // Validate sort and order parameters
            $allowedSorts = ['id', 'name', 'server', 'reason', 'banned_by_name', 'time', 'until', 'active'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'time';
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            // Map sort field to correct column
            $sortColumn = match($sort) {
                'name' => 'h.name',
                'reason' => 'b.reason',
                'banned_by_name' => 'b.banned_by_name',
                'server' => 'b.server',
                'active' => 'b.active',
                'until' => 'b.until',
                'id' => 'b.id',
                default => 'b.time'
            };
            
            // For active column, ensure DESC shows active first (1 > 0)
            if ($sort === 'active' && $order === 'DESC') {
                $orderClause = "{$sortColumn} DESC";
            } else if ($sort === 'active' && $order === 'ASC') {
                $orderClause = "{$sortColumn} ASC";
            } else {
                $orderClause = "{$sortColumn} {$order}";
            }
            
            $where = $activeOnly ? 'WHERE b.active = 1 AND b.uuid IS NOT NULL AND b.uuid != \'#\'' : 'WHERE b.uuid IS NOT NULL AND b.uuid != \'#\'';
            
            // Add silent filter if needed
            if (!$showSilent) {
                $where .= ' AND (b.silent = 0 OR b.silent IS NULL)';
            }
            
            $sql = "SELECT b.id, b.uuid, b.reason, b.banned_by_name, b.banned_by_uuid, b.time, b.until, 
                           CAST(b.active AS UNSIGNED) as active, b.removed_by_name, b.removed_by_uuid, 
                           b.removed_by_date, CAST(b.silent AS UNSIGNED) as silent,
                           b.server_origin, b.server_scope,
                           h.name as player_name
                    FROM {$table} b
                    LEFT JOIN (
                        SELECT h1.uuid, h1.name
                        FROM {$historyTable} h1
                        INNER JOIN (
                            SELECT uuid, MAX(date) as max_date
                            FROM {$historyTable}
                            GROUP BY uuid
                        ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                    ) h ON b.uuid = h.uuid
                    {$where}
                    ORDER BY {$orderClause}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getBans: " . $e->getMessage());
            return [];
        }
    }
    
    public function getMutes(int $limit = 20, int $offset = 0, bool $activeOnly = true, string $sort = 'time', string $order = 'DESC', bool $showSilent = true): array
    {
        try {
            $table = $this->tablePrefix . 'mutes';
            $historyTable = $this->tablePrefix . 'history';
            
            // Validate sort and order parameters
            $allowedSorts = ['id', 'name', 'server', 'reason', 'banned_by_name', 'time', 'until', 'active'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'time';
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            // Map sort field to correct column
            $sortColumn = match($sort) {
                'name' => 'h.name',
                'reason' => 'm.reason',
                'banned_by_name' => 'm.banned_by_name',
                'server' => 'm.server',
                'active' => 'm.active',
                'until' => 'm.until',
                'id' => 'm.id',
                default => 'm.time'
            };
            
            $orderClause = "{$sortColumn} {$order}";
            
            $where = $activeOnly ? 'WHERE m.active = 1 AND m.uuid IS NOT NULL AND m.uuid != \'#\'' : 'WHERE m.uuid IS NOT NULL AND m.uuid != \'#\'';
            
            // Add silent filter if needed
            if (!$showSilent) {
                $where .= ' AND (m.silent = 0 OR m.silent IS NULL)';
            }
            
            $sql = "SELECT m.id, m.uuid, m.reason, m.banned_by_name, m.banned_by_uuid, m.time, m.until, 
                           CAST(m.active AS UNSIGNED) as active, m.removed_by_name, m.removed_by_uuid, 
                           m.removed_by_date, CAST(m.silent AS UNSIGNED) as silent,
                           m.server_origin, m.server_scope,
                           h.name as player_name
                    FROM {$table} m
                    LEFT JOIN (
                        SELECT h1.uuid, h1.name
                        FROM {$historyTable} h1
                        INNER JOIN (
                            SELECT uuid, MAX(date) as max_date
                            FROM {$historyTable}
                            GROUP BY uuid
                        ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                    ) h ON m.uuid = h.uuid
                    {$where}
                    ORDER BY {$orderClause}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getMutes: " . $e->getMessage());
            return [];
        }
    }
    
    public function getWarnings(int $limit = 20, int $offset = 0, string $sort = 'time', string $order = 'DESC'): array
    {
        try {
            $table = $this->tablePrefix . 'warnings';
            $historyTable = $this->tablePrefix . 'history';
            
            // Validate sort and order parameters
            $allowedSorts = ['id', 'name', 'server', 'reason', 'banned_by_name', 'time', 'active'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'time';
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            // Map sort field to correct column
            $sortColumn = match($sort) {
                'name' => 'h.name',
                'reason' => 'w.reason',
                'banned_by_name' => 'w.banned_by_name',
                'server' => 'w.server',
                'active' => 'w.active',
                'id' => 'w.id',
                default => 'w.time'
            };
            
            $orderClause = "{$sortColumn} {$order}";
            
            $currentTime = time() * 1000; // Current time in milliseconds
            
            $sql = "SELECT w.id, w.uuid, w.reason, w.banned_by_name, w.banned_by_uuid, w.time, 
                           CAST(w.warned AS UNSIGNED) as warned, 
                           w.until,
                           CASE 
                               WHEN w.until IS NOT NULL AND w.until > 0 AND w.until <= :current_time THEN 0
                               ELSE CAST(w.active AS UNSIGNED)
                           END as active,
                           w.server_origin, w.server_scope,
                           h.name as player_name
                    FROM {$table} w
                    LEFT JOIN (
                        SELECT h1.uuid, h1.name
                        FROM {$historyTable} h1
                        INNER JOIN (
                            SELECT uuid, MAX(date) as max_date
                            FROM {$historyTable}
                            GROUP BY uuid
                        ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                    ) h ON w.uuid = h.uuid
                    WHERE w.uuid IS NOT NULL AND w.uuid != '#'
                    ORDER BY {$orderClause}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':current_time', $currentTime, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getWarnings: " . $e->getMessage());
            return [];
        }
    }
    
    public function getKicks(int $limit = 20, int $offset = 0, string $sort = 'time', string $order = 'DESC'): array
    {
        try {
            $table = $this->tablePrefix . 'kicks';
            $historyTable = $this->tablePrefix . 'history';
            
            // Validate sort and order parameters
            $allowedSorts = ['id', 'name', 'server', 'reason', 'banned_by_name', 'time', 'active'];
            $sort = in_array($sort, $allowedSorts) ? $sort : 'time';
            $order = strtoupper($order);
            $order = in_array($order, ['ASC', 'DESC']) ? $order : 'DESC';
            
            // Map sort field to correct column
            $sortColumn = match($sort) {
                'name' => 'h.name',
                'reason' => 'k.reason',
                'banned_by_name' => 'k.banned_by_name',
                'server' => 'k.server',
                'active' => 'k.active',
                'id' => 'k.id',
                default => 'k.time'
            };
            
            $orderClause = "{$sortColumn} {$order}";
            
            $sql = "SELECT k.id, k.uuid, k.reason, k.banned_by_name, k.banned_by_uuid, k.time,
                           CAST(k.active AS UNSIGNED) as active,
                           k.server_origin, k.server_scope,
                           h.name as player_name
                    FROM {$table} k
                    LEFT JOIN (
                        SELECT h1.uuid, h1.name
                        FROM {$historyTable} h1
                        INNER JOIN (
                            SELECT uuid, MAX(date) as max_date
                            FROM {$historyTable}
                            GROUP BY uuid
                        ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                    ) h ON k.uuid = h.uuid
                    WHERE k.uuid IS NOT NULL AND k.uuid != '#'
                    ORDER BY {$orderClause}
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getKicks: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPlayerPunishments(string $identifier): array
    {
        try {
            // Sanitize input
            $identifier = trim($identifier);
            if (empty($identifier)) {
                return [];
            }
            
            $isUuid = SecurityManager::validateUuid($identifier);
            $historyTable = $this->tablePrefix . 'history';
            
            if ($isUuid) {
                $field = 'uuid';
                $value = $identifier;
            } else {
                // Validate username format
                if (!SecurityManager::validateUsername($identifier)) {
                    return [];
                }
                
                // If searching by name, first get UUID from history
                $stmt = $this->connection->prepare("SELECT uuid FROM {$historyTable} WHERE name = :name ORDER BY date DESC LIMIT 1");
                $stmt->bindValue(':name', $identifier, PDO::PARAM_STR);
                $stmt->execute();
                $result = $stmt->fetch();
                
                if (!$result) {
                    return [];
                }
                
                $field = 'uuid';
                $value = $result['uuid'];
            }
            
            $tables = [
                'bans' => ['until', 'active'],
                'mutes' => ['until', 'active'],
                'warnings' => ['warned'],
                'kicks' => []
            ];
            $results = [];
            
            foreach ($tables as $table => $extraColumns) {
                $fullTable = $this->tablePrefix . $table;
                
                $columns = "'{$table}' as type, id, uuid, reason, banned_by_name, time";
                foreach ($extraColumns as $col) {
                    if ($col === 'active' || $col === 'warned') {
                        $columns .= ", CAST({$col} AS UNSIGNED) as {$col}";
                    } else {
                        $columns .= ", {$col}";
                    }
                }
                
                $sql = "SELECT {$columns} FROM {$fullTable} WHERE {$field} = :identifier AND uuid != '#' ORDER BY time DESC";
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':identifier', $value, PDO::PARAM_STR);
                $stmt->execute();
                
                $tableResults = $stmt->fetchAll();
                foreach ($tableResults as &$row) {
                    $row['player_name'] = $this->getPlayerName($row['uuid']);
                }
                
                $results = array_merge($results, $tableResults);
            }
            
            // Sort by time descending
            usort($results, function($a, $b) {
                return $b['time'] <=> $a['time'];
            });
            
            return $results;
        } catch (PDOException $e) {
            error_log("Error in getPlayerPunishments: " . $e->getMessage());
            return [];
        }
    }
    
    public function getStats(): array
    {
        $tables = ['bans', 'mutes', 'warnings', 'kicks'];
        $stats = [];
        
        foreach ($tables as $table) {
            $fullTable = $this->tablePrefix . $table;
            
            try {
                $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$fullTable} WHERE uuid IS NOT NULL AND uuid != '#'");
                $result = $stmt->fetch();
                $stats[$table] = (int)($result['total'] ?? 0);
                
                if (in_array($table, ['bans', 'mutes'])) {
                    $stmt = $this->connection->query("SELECT COUNT(*) as active FROM {$fullTable} WHERE active = 1 AND uuid IS NOT NULL AND uuid != '#'");
                    $result = $stmt->fetch();
                    $stats[$table . '_active'] = (int)($result['active'] ?? 0);
                }
            } catch (PDOException $e) {
                error_log("Error getting stats for {$table}: " . $e->getMessage());
                $stats[$table] = 0;
                if (in_array($table, ['bans', 'mutes'])) {
                    $stats[$table . '_active'] = 0;
                }
            }
        }
        
        return $stats;
    }
    
    public function getPlayerName(string $uuid): ?string
    {
        if (empty($uuid) || $uuid === '#' || $uuid === 'CONSOLE') {
            return $uuid === 'CONSOLE' ? 'Console' : null;
        }
        
        $table = $this->tablePrefix . 'history';
        
        try {
            $sql = "SELECT name FROM {$table} WHERE uuid = :uuid ORDER BY date DESC LIMIT 1";
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':uuid', $uuid);
            $stmt->execute();
            
            $result = $stmt->fetch();
            return $result ? $result['name'] : null;
        } catch (PDOException $e) {
            error_log("Error getting player name: " . $e->getMessage());
            return null;
        }
    }
    
    public function getTotalBans(bool $activeOnly = true): int
    {
        return $this->getTotalCount('bans', $activeOnly);
    }
    
    public function getTotalMutes(bool $activeOnly = true): int
    {
        return $this->getTotalCount('mutes', $activeOnly);
    }
    
    public function getTotalWarnings(): int
    {
        return $this->getTotalCount('warnings');
    }
    
    public function getTotalKicks(): int
    {
        return $this->getTotalCount('kicks');
    }
    
    private function getTotalCount(string $table, bool $activeOnly = true): int
    {
        try {
            $fullTable = $this->tablePrefix . $table;
            
            // For bans and mutes, support activeOnly filter
            if (in_array($table, ['bans', 'mutes'])) {
                $where = $activeOnly ? 'WHERE active = 1 AND uuid IS NOT NULL AND uuid != \'#\'' : 'WHERE uuid IS NOT NULL AND uuid != \'#\'';
            } else {
                $where = 'WHERE uuid IS NOT NULL AND uuid != \'#\'';
            }
            
            $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$fullTable} {$where}");
            $result = $stmt->fetch();
            return (int)($result['total'] ?? 0);
        } catch (PDOException $e) {
            error_log("Error getting total count for {$table}: " . $e->getMessage());
            return 0;
        }
    }
    
    public function getPunishmentById(string $type, int $id): ?array
    {
        $table = $this->tablePrefix . $type;
        $historyTable = $this->tablePrefix . 'history';
        
        try {
            $sql = "SELECT p.*, h.name as player_name
                    FROM {$table} p
                    LEFT JOIN (
                        SELECT h1.uuid, h1.name
                        FROM {$historyTable} h1
                        INNER JOIN (
                            SELECT uuid, MAX(date) as max_date
                            FROM {$historyTable}
                            GROUP BY uuid
                        ) h2 ON h1.uuid = h2.uuid AND h1.date = h2.max_date
                    ) h ON p.uuid = h.uuid
                    WHERE p.id = :id LIMIT 1";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $result = $stmt->fetch();
            
            if ($result) {
                // Convert BIT fields to integers
                $bitFields = ['active', 'silent', 'ipban', 'warned'];
                foreach ($bitFields as $field) {
                    if (isset($result[$field])) {
                        $result[$field] = (int)$result[$field];
                    }
                }
            }
            
            return $result ?: null;
        } catch (PDOException $e) {
            error_log("Error getting punishment by ID: " . $e->getMessage());
            return null;
        }
    }
    
    public function testConnection(): bool
    {
        try {
            $stmt = $this->connection->query("SELECT 1");
            return $stmt !== false;
        } catch (PDOException $e) {
            error_log("Database connection test failed: " . $e->getMessage());
            return false;
        }
    }
    
    public function getTableStructure(string $table): array
    {
        try {
            $fullTable = $this->tablePrefix . $table;
            $stmt = $this->connection->query("DESCRIBE {$fullTable}");
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting table structure for {$table}: " . $e->getMessage());
            return [];
        }
    }
    
    // Advanced statistics methods for the StatsController
    
    public function getTopBannedPlayers(int $limit = 10): array
    {
        try {
            $bansTable = $this->tablePrefix . 'bans';
            $historyTable = $this->tablePrefix . 'history';
            
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
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top banned players: " . $e->getMessage());
            return [];
        }
    }
    
    public function getMostActiveStaff(int $limit = 10): array
    {
        try {
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $results = [];
            
            foreach ($tables as $table) {
                $fullTable = $this->tablePrefix . $table;
                
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
                
                $stmt = $this->connection->query($sql);
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
    
    public function getTopBanReasons(int $limit = 10): array
    {
        try {
            $bansTable = $this->tablePrefix . 'bans';
            
            $sql = "SELECT reason, COUNT(*) as count
                    FROM {$bansTable}
                    WHERE reason IS NOT NULL 
                    AND reason != ''
                    AND uuid IS NOT NULL AND uuid != '#'
                    GROUP BY reason
                    ORDER BY count DESC
                    LIMIT :limit";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting top ban reasons: " . $e->getMessage());
            return [];
        }
    }
    
    public function getPunishmentTrends(int $days = 30): array
    {
        try {
            $sinceTimestamp = (time() - ($days * 24 * 60 * 60)) * 1000;
            $tables = ['bans', 'mutes', 'warnings', 'kicks'];
            $trends = [];
            
            foreach ($tables as $table) {
                $fullTable = $this->tablePrefix . $table;
                
                $sql = "SELECT DATE(FROM_UNIXTIME(time/1000)) as date, COUNT(*) as count
                        FROM {$fullTable}
                        WHERE time >= :since
                        AND uuid IS NOT NULL AND uuid != '#'
                        GROUP BY DATE(FROM_UNIXTIME(time/1000))
                        ORDER BY date DESC";
                
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(':since', $sinceTimestamp, PDO::PARAM_INT);
                $stmt->execute();
                
                $trends[$table] = $stmt->fetchAll();
            }
            
            return $trends;
        } catch (PDOException $e) {
            error_log("Error getting punishment trends: " . $e->getMessage());
            return [];
        }
    }
    
    public function getRecentActivity(): array
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
                    $fullTable = $this->tablePrefix . $table;
                    
                    $sql = "SELECT COUNT(*) as count FROM {$fullTable} 
                            WHERE time >= :since 
                            AND uuid IS NOT NULL AND uuid != '#'";
                    
                    $stmt = $this->connection->prepare($sql);
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
    
    public function getDailyActivity(int $days = 7): array
    {
        try {
            $sinceTimestamp = (time() - ($days * 24 * 60 * 60)) * 1000;
            $bansTable = $this->tablePrefix . 'bans';
            
            $sql = "SELECT 
                        DAYOFWEEK(FROM_UNIXTIME(time/1000)) as day_of_week,
                        DAYNAME(FROM_UNIXTIME(time/1000)) as day_name,
                        COUNT(*) as count
                    FROM {$bansTable}
                    WHERE time >= :since
                    AND uuid IS NOT NULL AND uuid != '#'
                    GROUP BY DAYOFWEEK(FROM_UNIXTIME(time/1000)), DAYNAME(FROM_UNIXTIME(time/1000))
                    ORDER BY day_of_week";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':since', $sinceTimestamp, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error getting daily activity: " . $e->getMessage());
            return [];
        }
    }
}
