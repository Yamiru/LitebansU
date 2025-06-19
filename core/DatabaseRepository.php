<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       1.0
 *  Author:        Yamiru <yamiru@yamiru.com>
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
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
    
    public function getBans(int $limit = 20, int $offset = 0, bool $activeOnly = true): array
    {
        try {
            $table = $this->tablePrefix . 'bans';
            $historyTable = $this->tablePrefix . 'history';
            
            $where = $activeOnly ? 'WHERE b.active = 1 AND b.uuid IS NOT NULL AND b.uuid != \'#\'' : 'WHERE b.uuid IS NOT NULL AND b.uuid != \'#\'';
            
            $sql = "SELECT b.id, b.uuid, b.reason, b.banned_by_name, b.banned_by_uuid, b.time, b.until, 
                           CAST(b.active AS UNSIGNED) as active, b.removed_by_name, b.removed_by_uuid, 
                           b.removed_by_date, CAST(b.silent AS UNSIGNED) as silent,
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
                    ORDER BY b.time DESC 
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
    
    public function getMutes(int $limit = 20, int $offset = 0, bool $activeOnly = true): array
    {
        try {
            $table = $this->tablePrefix . 'mutes';
            $historyTable = $this->tablePrefix . 'history';
            
            $where = $activeOnly ? 'WHERE m.active = 1 AND m.uuid IS NOT NULL AND m.uuid != \'#\'' : 'WHERE m.uuid IS NOT NULL AND m.uuid != \'#\'';
            
            $sql = "SELECT m.id, m.uuid, m.reason, m.banned_by_name, m.banned_by_uuid, m.time, m.until, 
                           CAST(m.active AS UNSIGNED) as active, m.removed_by_name, m.removed_by_uuid, 
                           m.removed_by_date, CAST(m.silent AS UNSIGNED) as silent,
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
                    ORDER BY m.time DESC 
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
    
    public function getWarnings(int $limit = 20, int $offset = 0): array
    {
        try {
            $table = $this->tablePrefix . 'warnings';
            $historyTable = $this->tablePrefix . 'history';
            
            $sql = "SELECT w.id, w.uuid, w.reason, w.banned_by_name, w.banned_by_uuid, w.time, 
                           CAST(w.warned AS UNSIGNED) as warned, CAST(w.active AS UNSIGNED) as active,
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
                    ORDER BY w.time DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->connection->prepare($sql);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error in getWarnings: " . $e->getMessage());
            return [];
        }
    }
    
    public function getKicks(int $limit = 20, int $offset = 0): array
    {
        try {
            $table = $this->tablePrefix . 'kicks';
            $historyTable = $this->tablePrefix . 'history';
            
            $sql = "SELECT k.id, k.uuid, k.reason, k.banned_by_name, k.banned_by_uuid, k.time,
                           CAST(k.active AS UNSIGNED) as active,
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
                    ORDER BY k.time DESC 
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
            $isUuid = SecurityManager::validateUuid($identifier);
            $historyTable = $this->tablePrefix . 'history';
            
            if ($isUuid) {
                $field = 'uuid';
                $value = $identifier;
            } else {
                // If searching by name, first get UUID from history
                $stmt = $this->connection->prepare("SELECT uuid FROM {$historyTable} WHERE name = :name ORDER BY date DESC LIMIT 1");
                $stmt->bindValue(':name', $identifier);
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
                $stmt->bindValue(':identifier', $value);
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
    
    public function getTotalBans(): int
    {
        return $this->getTotalCount('bans');
    }
    
    public function getTotalMutes(): int
    {
        return $this->getTotalCount('mutes');
    }
    
    public function getTotalWarnings(): int
    {
        return $this->getTotalCount('warnings');
    }
    
    public function getTotalKicks(): int
    {
        return $this->getTotalCount('kicks');
    }
    
    private function getTotalCount(string $table): int
    {
        try {
            $fullTable = $this->tablePrefix . $table;
            $stmt = $this->connection->query("SELECT COUNT(*) as total FROM {$fullTable} WHERE uuid IS NOT NULL AND uuid != '#'");
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
}