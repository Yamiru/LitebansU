<?php
/**
 * ============================================================================
 *  LiteBansU - Logger
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   Centralized logging system with file-based logging support
 *  Version:       3.8
 *  License:       MIT
 * ============================================================================
 */

declare(strict_types=1);

namespace core;

class Logger
{
    private static ?Logger $instance = null;
    private bool $enabled;
    private string $logPath;
    private bool $debug;
    
    private const LOG_LEVELS = [
        'DEBUG' => 0,
        'INFO' => 1,
        'WARNING' => 2,
        'ERROR' => 3,
        'CRITICAL' => 4
    ];
    
    private function __construct()
    {
        $this->enabled = EnvLoader::get('LOG_ERRORS', 'false') === 'true';
        $this->debug = EnvLoader::get('DEBUG', 'false') === 'true';
        $this->logPath = $this->resolveLogPath();
        
        if ($this->enabled) {
            $this->ensureLogDirectory();
        }
    }
    
    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function resolveLogPath(): string
    {
        $configPath = EnvLoader::get('ERROR_LOG_PATH', 'logs/error.log');
        
        if (str_starts_with($configPath, '/')) {
            return $configPath;
        }
        
        $baseDir = defined('BASE_DIR') ? BASE_DIR : dirname(__DIR__);
        return rtrim($baseDir, '/') . '/' . ltrim($configPath, '/');
    }
    
    private function ensureLogDirectory(): void
    {
        $logDir = dirname($this->logPath);
        
        if (!is_dir($logDir)) {
            if (!@mkdir($logDir, 0755, true)) {
                $this->logPath = sys_get_temp_dir() . '/litebans_error.log';
                return;
            }
        }
        
        $htaccess = $logDir . '/.htaccess';
        if (!file_exists($htaccess)) {
            @file_put_contents($htaccess, "Order deny,allow\nDeny from all\n");
        }
        
        $index = $logDir . '/index.php';
        if (!file_exists($index)) {
            @file_put_contents($index, "<?php http_response_code(403); exit('Forbidden');");
        }
    }
    
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
    
    public function isDebug(): bool
    {
        return $this->debug;
    }
    
    public function getLogPath(): string
    {
        return $this->logPath;
    }
    
    public function log(string $level, string $message, array $context = []): void
    {
        if (!$this->enabled) {
            return;
        }
        
        $level = strtoupper($level);
        if (!isset(self::LOG_LEVELS[$level])) {
            $level = 'INFO';
        }
        
        $timestamp = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' | ' . json_encode($context, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';
        
        $requestInfo = '';
        if (isset($_SERVER['REQUEST_URI'])) {
            $requestInfo = ' | URI: ' . ($_SERVER['REQUEST_URI'] ?? 'CLI');
        }
        if (isset($_SERVER['REMOTE_ADDR'])) {
            $requestInfo .= ' | IP: ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        }
        
        $logEntry = "[{$timestamp}] [{$level}] {$message}{$contextStr}{$requestInfo}" . PHP_EOL;
        
        $result = @file_put_contents($this->logPath, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($result === false) {
            error_log("[LiteBansU] {$level}: {$message}");
        }
        
        if (self::LOG_LEVELS[$level] >= self::LOG_LEVELS['ERROR']) {
            error_log("[LiteBansU] {$level}: {$message}");
        }
    }
    
    public function debug(string $message, array $context = []): void
    {
        if ($this->debug) {
            $this->log('DEBUG', $message, $context);
        }
    }
    
    public function info(string $message, array $context = []): void
    {
        $this->log('INFO', $message, $context);
    }
    
    public function warning(string $message, array $context = []): void
    {
        $this->log('WARNING', $message, $context);
    }
    
    public function error(string $message, array $context = []): void
    {
        $this->log('ERROR', $message, $context);
    }
    
    public function critical(string $message, array $context = []): void
    {
        $this->log('CRITICAL', $message, $context);
    }
    
    public function exception(\Throwable $e, string $additionalMessage = ''): void
    {
        $message = $additionalMessage ? "{$additionalMessage}: " : '';
        $message .= $e->getMessage();
        
        $context = [
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'code' => $e->getCode()
        ];
        
        if ($this->debug) {
            $context['trace'] = $e->getTraceAsString();
        }
        
        $this->log('ERROR', $message, $context);
    }
    
    public function databaseError(\PDOException $e, string $operation = ''): void
    {
        $errorCode = $e->getCode();
        $errorMessage = $e->getMessage();
        
        $detailedMessage = $this->parseDatabaseError($errorCode, $errorMessage, $operation);
        
        $context = [
            'pdo_code' => $errorCode,
            'operation' => $operation,
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ];
        
        if ($this->debug) {
            $context['trace'] = $e->getTraceAsString();
        }
        
        $this->log('ERROR', $detailedMessage, $context);
    }
    
    private function parseDatabaseError($code, string $message, string $operation): string
    {
        $errorTypes = [
            1045 => 'Database authentication failed - invalid username or password',
            1049 => 'Database does not exist - check DB_NAME in .env',
            1044 => 'Access denied to database - user lacks privileges',
            1146 => 'Table does not exist - LiteBans tables may not be created',
            1054 => 'Unknown column in query - database schema mismatch',
            2002 => 'Cannot connect to database server - check DB_HOST and DB_PORT',
            2003 => 'Database server not responding - server may be down',
            2006 => 'Database server has gone away - connection lost',
            2013 => 'Lost connection during query - timeout or server issue',
            1040 => 'Too many database connections - server limit reached',
            1205 => 'Lock wait timeout exceeded - database is busy',
            1062 => 'Duplicate entry - data integrity violation',
            1064 => 'SQL syntax error in query',
        ];
        
        $prefix = $operation ? "[{$operation}] " : '';
        
        if (isset($errorTypes[$code])) {
            return $prefix . $errorTypes[$code] . " (Code: {$code})";
        }
        
        if (stripos($message, 'Unknown database') !== false) {
            return $prefix . "Database not found - verify DB_NAME setting. Original: {$message}";
        }
        if (stripos($message, 'Access denied') !== false) {
            return $prefix . "Access denied - check DB_USER and DB_PASS. Original: {$message}";
        }
        if (stripos($message, 'Connection refused') !== false) {
            return $prefix . "Connection refused - database server not accepting connections. Original: {$message}";
        }
        if (stripos($message, "doesn't exist") !== false || stripos($message, 'does not exist') !== false) {
            return $prefix . "Database object not found - check table prefix and LiteBans installation. Original: {$message}";
        }
        if (stripos($message, 'timeout') !== false) {
            return $prefix . "Database connection timeout - server may be overloaded. Original: {$message}";
        }
        
        return $prefix . "Database error: {$message} (Code: {$code})";
    }
    
    public function rotateIfNeeded(int $maxSize = 5242880): void
    {
        if (!file_exists($this->logPath)) {
            return;
        }
        
        if (filesize($this->logPath) > $maxSize) {
            $backupPath = $this->logPath . '.' . date('Y-m-d-His') . '.bak';
            @rename($this->logPath, $backupPath);
            
            $backups = glob($this->logPath . '.*.bak');
            if (count($backups) > 5) {
                usort($backups, function($a, $b) {
                    return filemtime($a) - filemtime($b);
                });
                
                $toDelete = array_slice($backups, 0, count($backups) - 5);
                foreach ($toDelete as $file) {
                    @unlink($file);
                }
            }
        }
    }
    
    public function getRecentLogs(int $lines = 100): array
    {
        if (!file_exists($this->logPath)) {
            return [];
        }
        
        $content = @file_get_contents($this->logPath);
        if ($content === false) {
            return [];
        }
        
        $allLines = explode("\n", trim($content));
        return array_slice($allLines, -$lines);
    }
    
    public function clearLogs(): bool
    {
        if (!file_exists($this->logPath)) {
            return true;
        }
        
        return @file_put_contents($this->logPath, '') !== false;
    }
}
