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

class DatabaseConfig
{
    private const REQUIRED_EXTENSIONS = ['pdo_mysql', 'intl', 'mbstring'];
    
    private string $host;
    private int $port;
    private string $database;
    private string $username;
    private string $password;
    private string $driver;
    private array $options;
    
    public function __construct()
    {
        $this->validateExtensions();
        $this->loadConfig();
        $this->setOptions();
    }
    
    private function validateExtensions(): void
    {
        foreach (self::REQUIRED_EXTENSIONS as $ext) {
            if (!extension_loaded($ext)) {
                throw new RuntimeException("Required extension not loaded: {$ext}");
            }
        }
    }
    
    private function loadConfig(): void
    {
        // My Database
        $this->host = $_ENV['DB_HOST'] ?? 'localhost';
        $this->port = (int)($_ENV['DB_PORT'] ?? 3306);
        $this->database = $_ENV['DB_NAME'] ?? '';
        $this->username = $_ENV['DB_USER'] ?? '';
        $this->password = $_ENV['DB_PASS'] ?? '';
        $this->driver = $_ENV['DB_DRIVER'] ?? 'mysql';
        
        // Validate port range
        if ($this->port < 1 || $this->port > 65535) {
            throw new InvalidArgumentException('Invalid database port');
        }
    }
    
    private function setOptions(): void
    {
        $this->options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 5,
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci, sql_mode = 'TRADITIONAL'"
        ];
    }
    
    public function createConnection(): PDO
    {
        $dsn = $this->buildDsn();
        
        try {
            $pdo = new PDO($dsn, $this->username, $this->password, $this->options);
            
            // Additional security settings
            $pdo->exec("SET SESSION sql_mode = 'TRADITIONAL'");
            $pdo->exec("SET SESSION time_zone = '+00:00'");
            
            return $pdo;
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            throw new RuntimeException('Database connection failed', 0, $e);
        }
    }
    
    private function buildDsn(): string
    {
        $driver = strtolower($this->driver);
        if ($driver === 'mariadb') {
            $driver = 'mysql';
        }
        
        // Validate driver
        if (!in_array($driver, ['mysql', 'pgsql', 'sqlite'], true)) {
            throw new InvalidArgumentException('Unsupported database driver');
        }
        
        $dsn = "{$driver}:host={$this->host};port={$this->port};dbname={$this->database}";
        
        if ($driver === 'mysql') {
            $dsn .= ';charset=utf8mb4';
        }
        
        return $dsn;
    }
    
    public function getTablePrefix(): string
    {
        $prefix = $_ENV['TABLE_PREFIX'] ?? 'litebans_';
        
        // Validate table prefix
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $prefix)) {
            throw new InvalidArgumentException('Invalid table prefix');
        }
        
        return $prefix;
    }
}