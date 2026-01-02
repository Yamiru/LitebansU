<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.7
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

// Ensure EnvLoader is loaded
if (!class_exists('core\\EnvLoader')) {
    require_once dirname(__DIR__) . '/core/EnvLoader.php';
}

use core\EnvLoader;

class DatabaseConfig
{
    private const REQUIRED_EXTENSIONS = ['pdo_mysql', 'intl', 'mbstring'];
    
    private string $host;
    private int $port;
    private string $database;
    private string $username;
    private string $password;
    private string $driver;
    private string $tablePrefix;
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
                throw new \RuntimeException("Required extension not loaded: {$ext}");
            }
        }
    }
    
    private function loadConfig(): void
    {
        $this->host = EnvLoader::get('DB_HOST', 'localhost');
        $this->port = (int)EnvLoader::get('DB_PORT', 3306);
        $this->database = EnvLoader::get('DB_NAME', 'litebans');
        $this->username = EnvLoader::get('DB_USER', 'root');
        $this->password = EnvLoader::get('DB_PASS', '');
        $this->driver = EnvLoader::get('DB_DRIVER', 'mysql');
        $this->tablePrefix = EnvLoader::get('TABLE_PREFIX', 'litebans_');
        
        // Validate port range
        if ($this->port < 1 || $this->port > 65535) {
            throw new \InvalidArgumentException('Invalid database port');
        }
        
        // Validate required fields
        if (empty($this->database)) {
            throw new \InvalidArgumentException('Database name is required');
        }
        
        if (empty($this->username)) {
            throw new \InvalidArgumentException('Database username is required');
        }
    }
    
    /**
     * Get MySQL init command attribute constant
     * PHP 8.5+ deprecated PDO::MYSQL_ATTR_INIT_COMMAND in favor of Pdo\Mysql::ATTR_INIT_COMMAND
     */
    private function getMysqlInitCommandAttribute(): int
    {
        // Check if we're on PHP 8.5+ where Pdo\Mysql class exists
        if (class_exists('Pdo\\Mysql') && defined('Pdo\\Mysql::ATTR_INIT_COMMAND')) {
            return \Pdo\Mysql::ATTR_INIT_COMMAND;
        }
        
        // Fallback for PHP < 8.5
        return \PDO::MYSQL_ATTR_INIT_COMMAND;
    }
    
    private function setOptions(): void
    {
        $this->options = [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
            $this->getMysqlInitCommandAttribute() => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci",
            \PDO::ATTR_TIMEOUT => 30,
            \PDO::ATTR_PERSISTENT => false,
        ];
    }
    
    public function createConnection(): \PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $this->host,
            $this->port,
            $this->database
        );
        
        try {
            $pdo = new \PDO($dsn, $this->username, $this->password, $this->options);
            
            // Test connection
            $pdo->query('SELECT 1');
            
            return $pdo;
        } catch (\PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Failed to connect to database: ' . $e->getMessage());
        }
    }
    
    public function getTablePrefix(): string
    {
        return $this->tablePrefix;
    }
    
    public function getHost(): string
    {
        return $this->host;
    }
    
    public function getPort(): int
    {
        return $this->port;
    }
    
    public function getDatabase(): string
    {
        return $this->database;
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    
    public function getDriver(): string
    {
        return $this->driver;
    }
}
