<?php
/**
 * Enhanced Demo & Video System Installer for LiteBansU
 * Version: 2.1
 * * Project: LiteBansU by Yamiru.com
 * GitHub: https://github.com/Yamiru/LitebansU
 * Issues: https://github.com/Yamiru/LitebansU/issues
 */

session_start();
$version = '2.1';
$installReady = false;
$messages = [];
$errors = [];
$warnings = [];

// Check if we're in the correct location
$requiredFiles = ['index.php', 'config/app.php', 'controllers/AdminController.php'];
$inCorrectLocation = true;

foreach ($requiredFiles as $file) {
    if (!file_exists($file)) {
        $inCorrectLocation = false;
        break;
    }
}

// Handle installation
if (isset($_POST['install']) && $inCorrectLocation) {
    
    // Create directories
    $dirs = [
        'demos' => 0755,
        'demos/data' => 0777,
        'demos/uploads' => 0777,
        'demos/api' => 0755
    ];

    foreach ($dirs as $dir => $perms) {
        if (!file_exists($dir)) {
            if (@mkdir($dir, $perms, true)) {
                $messages[] = "OK Created directory: /{$dir}/";
                @chmod($dir, $perms);
            } else {
                $errors[] = "? Failed to create: /{$dir}/";
            }
        } else {
            $messages[] = "OK Directory exists: /{$dir}/";
        }
    }

    // Main system file with Ban ID based system
    $mainSystem = <<<'PHPSYSTEM'
<?php
/**
 * Enhanced Demo & Video System Installer for LiteBansU
 * Version: 2.1
 * * Project: LiteBansU by Yamiru.com
 * GitHub: https://github.com/Yamiru/LitebansU
 * Issues: https://github.com/Yamiru/LitebansU/issues
 */

session_start();

// Enable error reporting in debug mode
if (isset($_GET['debug'])) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    echo "\n";
}

// Check admin authentication
if (!isset($_SESSION["admin_authenticated"]) || $_SESSION["admin_authenticated"] !== true) {
    header("Location: ../admin");
    exit();
}

// Database connection variables
$pdo = null;
$activeBans = [];
$dbError = null;
$dbConnected = false;
$connectionMethod = null;

// Function to test database connection
function testDatabaseConnection($host, $database, $username, $password, $port = '3306') {
    try {
        $testPdo = new PDO(
            "mysql:host=$host;port=$port;dbname=$database;charset=utf8mb4",
            $username,
            $password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        return $testPdo;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to load .env file manually
function loadEnvFile($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    return true;
}

// Debug output at the start
if (isset($_GET['debug'])) {
    echo "\n";
}

// Method 1: Try .env file from multiple locations
$envPaths = [
    dirname(__DIR__) . '/.env',
    __DIR__ . '/.env',
    dirname(dirname(__DIR__)) . '/.env',
    __DIR__ . '/../.env',
    __DIR__ . '/../../.env',
];

$envLoaded = false;
foreach ($envPaths as $envPath) {
    if (file_exists($envPath)) {
        if (loadEnvFile($envPath)) {
            $envLoaded = true;
            $connectionMethod = ".env file: " . $envPath;
            if (isset($_GET['debug'])) {
                echo "\n";
            }
            break;
        }
    }
}

// Method 2: Try database connection with environment variables
if ($envLoaded && !$pdo) {
    $db_host = getenv('DB_HOST') ?: getenv('MYSQL_HOST') ?: 'localhost';
    $db_name = getenv('DB_DATABASE') ?: getenv('MYSQL_DATABASE') ?: getenv('DB_NAME') ?: null;
    $db_user = getenv('DB_USERNAME') ?: getenv('MYSQL_USER') ?: getenv('DB_USER') ?: null;
    $db_pass = getenv('DB_PASSWORD') ?: getenv('MYSQL_PASSWORD') ?: getenv('DB_PASS') ?: '';
    $db_port = getenv('DB_PORT') ?: getenv('MYSQL_PORT') ?: '3306';
    
    if ($db_name && $db_user !== null) {
        $pdo = testDatabaseConnection($db_host, $db_name, $db_user, $db_pass, $db_port);
        if ($pdo) {
            $dbConnected = true;
            $connectionMethod = "Environment variables from .env";
            if (isset($_GET['debug'])) {
                echo "\n";
            }
        } else {
            $dbError = "Failed to connect with env variables";
        }
    }
}

// Method 3: Try config/database.php
if (!$pdo) {
    $configPaths = [
        dirname(__DIR__) . '/config/database.php',
        __DIR__ . '/../config/database.php',
        __DIR__ . '/config/database.php',
    ];
    
    foreach ($configPaths as $configPath) {
        if (file_exists($configPath)) {
            try {
                $dbConfig = include $configPath;
                if (is_array($dbConfig)) {
                    $pdo = testDatabaseConnection(
                        $dbConfig['host'] ?? 'localhost',
                        $dbConfig['database'] ?? '',
                        $dbConfig['username'] ?? '',
                        $dbConfig['password'] ?? '',
                        $dbConfig['port'] ?? '3306'
                    );
                    if ($pdo) {
                        $dbConnected = true;
                        $connectionMethod = "Config file: " . $configPath;
                        if (isset($_GET['debug'])) {
                            echo "\n";
                        }
                        break;
                    }
                }
            } catch (Exception $e) {
                $dbError = "Config file error: " . $e->getMessage();
            }
        }
    }
}

// Method 4: Manual configuration for testing
if (!$pdo && isset($_GET['manual_db'])) {
    // CHANGE THESE VALUES TO YOUR DATABASE CREDENTIALS
    $manual_config = [
        'host' => 'localhost',
        'database' => 'playserverlist_rer',
        'username' => 'root',           // Change this
        'password' => '',                // Change this
        'port' => '3306'
    ];
    
    $pdo = testDatabaseConnection(
        $manual_config['host'],
        $manual_config['database'],
        $manual_config['username'],
        $manual_config['password'],
        $manual_config['port']
    );
    
    if ($pdo) {
        $dbConnected = true;
        $connectionMethod = "Manual configuration (testing mode)";
        if (isset($_GET['debug'])) {
            echo "\n";
        }
    } else {
        $dbError = "Manual configuration failed - check credentials";
    }
}

// Get active bans if connected
if ($pdo) {
    try {
        // Test the connection
        $testQuery = $pdo->query("SELECT 1");
        
        // Get current time for comparison
        $currentTime = round(microtime(true) * 1000);
        
        // Get active bans
        $query = "
            SELECT 
                CAST(b.id AS CHAR) as ban_id,
                b.uuid,
                b.ip,
                b.reason,
                b.banned_by_name,
                b.banned_by_uuid,
                b.time,
                b.until,
                b.server_scope,
                b.server_origin,
                b.removed_by_name,
                b.removed_by_reason,
                b.removed_by_date
            FROM litebans_bans b
            WHERE b.active = 1 
            AND (b.until = 0 OR b.until = -1 OR b.until > :currentTime)
            ORDER BY b.id DESC
        ";
        
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':currentTime', $currentTime, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($result as $ban) {
            if (!empty($ban['ban_id'])) {
                $banId = (string)$ban['ban_id'];
                $activeBans['ban_' . $banId] = $ban;
            }
        }
        
        if (isset($_GET['debug'])) {
            echo "\n";
        }
        
    } catch (Exception $e) {
        $dbError = "Query error: " . $e->getMessage();
        $dbConnected = false;
        if (isset($_GET['debug'])) {
            echo "\n";
        }
    }
} else {
    if (!$dbError) {
        $dbError = "No database connection available";
    }
}

// Set up basic variables
$username = $_SESSION["admin_user"] ?? "Administrator";
$uploadsDir = "uploads/";
$dataFile = "data/demos.json";

// Supported formats
$supportedFormats = [
    "demos" => ["dem", "demo"],
    "videos" => ["mp4", "mkv", "mov", "avi", "webm", "flv", "wmv", "mpg", "mpeg", "m4v", "3gp", "m4a"]
];
$allFormats = array_merge($supportedFormats["demos"], $supportedFormats["videos"]);

// Create directories
if (!is_dir("data")) @mkdir("data", 0777, true);
if (!is_dir("uploads")) @mkdir("uploads", 0777, true);

// Check if directories are writable
$dataWritable = is_writable("data");
$uploadsWritable = is_writable("uploads");

// Helper functions
function formatBytes($bytes, $precision = 2) {
    $units = ["B", "KB", "MB", "GB"];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . " " . $units[$pow];
}

function timeAgo($timestamp) {
    $time = time() - strtotime($timestamp);
    $units = [
        31536000 => 'year',
        2592000 => 'month', 
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    ];
    
    foreach ($units as $unit => $val) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $val . ($numberOfUnits > 1 ? 's' : '') . ' ago';
    }
    return 'just now';
}

function formatBanDuration($until) {
    if ($until == 0 || $until == -1) {
        return "Permanent";
    }
    $remaining = ($until / 1000) - time();
    if ($remaining <= 0) {
        return "Expired";
    }
    
    $days = floor($remaining / 86400);
    $hours = floor(($remaining % 86400) / 3600);
    $minutes = floor(($remaining % 3600) / 60);
    
    $parts = [];
    if ($days > 0) $parts[] = $days . 'd';
    if ($hours > 0) $parts[] = $hours . 'h';
    if ($minutes > 0) $parts[] = $minutes . 'm';
    
    return implode(' ', $parts) ?: 'Less than 1m';
}

function findBanId($filename, $activeBans) {
    $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);
    $nameWithoutExt = trim($nameWithoutExt);
    
    if (is_numeric($nameWithoutExt)) {
        $banKey = 'ban_' . $nameWithoutExt;
        if (isset($activeBans[$banKey])) {
            return $nameWithoutExt;
        }
        
        // Try without leading zeros
        $banKeyNoZeros = 'ban_' . ltrim($nameWithoutExt, '0');
        if ($banKeyNoZeros !== $banKey && isset($activeBans[$banKeyNoZeros])) {
            return ltrim($nameWithoutExt, '0');
        }
    }
    
    return false;
}

// Load demos
$demos = [];
if (file_exists($dataFile)) {
    $content = file_get_contents($dataFile);
    $demos = json_decode($content, true) ?: [];
}

// Update assignments
foreach ($demos as &$demo) {
    $banId = findBanId($demo["original_name"], $activeBans);
    
    if ($banId !== false) {
        $demo["assigned"] = true;
        $demo["ban_id"] = (string)$banId;
        $demo["ban_info"] = $activeBans['ban_' . $banId] ?? null;
    } else {
        $demo["assigned"] = false;
        $demo["ban_id"] = null;
        $demo["ban_info"] = null;
    }
}

// Save updated data
if (!empty($demos)) {
    file_put_contents($dataFile, json_encode($demos, JSON_PRETTY_PRINT));
}

// Handle file upload
$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES["demo"])) {
    $uploadSuccess = false;
    
    if ($_FILES["demo"]["error"] !== UPLOAD_ERR_OK) {
        $_SESSION["upload_error"] = "Upload failed! Error code: " . $_FILES["demo"]["error"];
    } else {
        $ext = strtolower(pathinfo($_FILES["demo"]["name"], PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allFormats)) {
            $_SESSION["upload_error"] = "Invalid file type!";
        } else {
            $filename = basename($_FILES["demo"]["name"]);
            $targetPath = $uploadsDir . $filename;
            
            if (file_exists($targetPath)) {
                $_SESSION["upload_error"] = "File already exists!";
            } else {
                if (move_uploaded_file($_FILES["demo"]["tmp_name"], $targetPath)) {
                    $banId = findBanId($_FILES["demo"]["name"], $activeBans);
                    
                    $demos[] = [
                        "id" => uniqid(),
                        "filename" => $filename,
                        "original_name" => $_FILES["demo"]["name"],
                        "uploader" => $username,
                        "upload_date" => date("Y-m-d H:i:s"),
                        "size" => $_FILES["demo"]["size"],
                        "type" => in_array($ext, $supportedFormats["videos"]) ? "video" : "demo",
                        "assigned" => $banId !== false,
                        "ban_id" => $banId ?: null,
                        "ban_info" => $banId ? ($activeBans['ban_' . $banId] ?? null) : null
                    ];
                    
                    file_put_contents($dataFile, json_encode($demos, JSON_PRETTY_PRINT));
                    
                    if ($banId !== false) {
                        $_SESSION["upload_message"] = "File uploaded and assigned to Ban #" . $banId;
                    } else {
                        $_SESSION["upload_message"] = "File uploaded! No matching Ban ID found.";
                    }
                    $uploadSuccess = true;
                } else {
                    $_SESSION["upload_error"] = "Failed to save file!";
                }
            }
        }
    }
    
    if ($uploadSuccess) {
        header("Location: " . $_SERVER["PHP_SELF"]);
        exit();
    }
}

// Get messages from session
if (isset($_SESSION["upload_message"])) {
    $message = $_SESSION["upload_message"];
    unset($_SESSION["upload_message"]);
}
if (isset($_SESSION["upload_error"])) {
    $error = $_SESSION["upload_error"];
    unset($_SESSION["upload_error"]);
}

// Handle delete
if (isset($_GET["delete"]) && !empty($_GET["delete"])) {
    $deleteId = $_GET["delete"];
    
    foreach ($demos as $key => $demo) {
        if ($demo["id"] === $deleteId) {
            $filePath = $uploadsDir . $demo["filename"];
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
            
            unset($demos[$key]);
            $demos = array_values($demos);
            file_put_contents($dataFile, json_encode($demos, JSON_PRETTY_PRINT));
            
            $_SESSION["upload_message"] = "File deleted successfully!";
            header("Location: index.php");
            exit();
        }
    }
}

// Sort demos by date
usort($demos, function($a, $b) {
    return strtotime($b["upload_date"]) - strtotime($a["upload_date"]);
});

// Statistics
$totalFiles = count($demos);
$assignedFiles = count(array_filter($demos, function($d) { return $d["assigned"]; }));
$videoFiles = count(array_filter($demos, function($d) { return ($d["type"] ?? "demo") === "video"; }));
$totalSize = array_sum(array_column($demos, "size"));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo & Video Management - LiteBansU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #0a0e27;
            --bg-secondary: #151c3d;
            --bg-card: #1e2749;
            --bg-hover: #252f56;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            min-height: 100vh;
        }
        
        .navbar {
            background: rgba(21, 28, 61, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--border);
            padding: 1rem 0;
        }
        
        .main-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }
        
        .page-title {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border);
        }
        
        .section {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
        }
        
        .alert {
            border-radius: 12px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            font-size: 0.875rem;
            border: 1px solid;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.1);
            color: var(--success);
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border-color: rgba(245, 158, 11, 0.3);
        }
        
        .alert-info {
            background: rgba(99, 102, 241, 0.1);
            color: var(--accent-light);
            border-color: rgba(99, 102, 241, 0.3);
        }
        
        .btn {
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }
        
        .btn-primary {
            background: var(--accent);
            color: white;
        }
        
        .btn-success {
            background: var(--success);
            color: white;
        }
        
        .btn-danger {
            background: var(--danger);
            color: white;
        }
        
        .btn-warning {
            background: var(--warning);
            color: white;
        }
        
        .btn-secondary {
            background: var(--bg-hover);
            color: var(--text-primary);
            border: 1px solid var(--border);
        }
        
        .form-control {
            background: var(--bg-hover);
            border: 2px solid var(--border);
            color: var(--text-primary);
            padding: 0.75rem 1rem;
            border-radius: 12px;
        }
        
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 1.5rem;
        }
        
        .demo-card {
            background: var(--bg-hover);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--border);
        }
        
        .demo-card.assigned {
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .debug-panel {
            background: #000;
            color: #0f0;
            padding: 1rem;
            font-family: monospace;
            margin: 1rem 0;
            border-radius: 8px;
            border: 1px solid #0f0;
            font-size: 0.875rem;
        }
        
        .debug-panel h4 {
            color: #0f0;
            margin-bottom: 1rem;
        }
        
        .debug-panel .success {
            color: #0f0;
        }
        
        .debug-panel .error {
            color: #f00;
        }
        
        .debug-panel .warning {
            color: #ff0;
        }
        
        table {
            width: 100%;
            color: var(--text-primary);
        }
        
        th {
            background: var(--bg-card);
            padding: 0.75rem;
            text-align: left;
            border-bottom: 2px solid var(--border);
        }
        
        td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border);
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="container-fluid">
            <a href="../admin" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Admin
            </a>
            <div class="d-flex align-items-center gap-3">
                <span>Admin: <?php echo htmlspecialchars($username); ?></span>
                <a href="../admin/logout" class="btn btn-danger btn-sm">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </div>
    </nav>
    
    <div class="main-container">
        <h1 class="page-title">Demo & Video Management</h1>
        
        <div class="section">
            <h4><i class="fas fa-database"></i> Database Status</h4>
            <?php if ($dbConnected): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <div>
                        <strong>Database Connected!</strong><br>
                        Connection method: <?php echo htmlspecialchars($connectionMethod); ?><br>
                        Active bans loaded: <?php echo count($activeBans); ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <div>
                        <strong>Database Not Connected</strong><br>
                        <?php if ($dbError): ?>
                            Error: <?php echo htmlspecialchars($dbError); ?><br>
                        <?php endif; ?>
                        <br>
                        <strong>Connection Options:</strong>
                        <ol>
                            <li>Create .env file with database credentials</li>
                            <li>Create config/database.php file</li>
                            <li><a href="?debug=1&manual_db=1" class="btn btn-warning btn-sm">Try Manual Connection</a></li>
                        </ol>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <?php if (isset($_GET['debug'])): ?>
        <div class="debug-panel">
            <h4>??? DEBUG INFORMATION</h4>
            
            <strong>Environment Check:</strong><br>
            <?php
            $checkedPaths = [
                dirname(__DIR__) . '/.env',
                __DIR__ . '/.env',
                dirname(dirname(__DIR__)) . '/.env',
            ];
            foreach ($checkedPaths as $path) {
                if (file_exists($path)) {
                    echo "<span class='success'>OK Found: $path</span><br>";
                } else {
                    echo "<span class='error'>? Not found: $path</span><br>";
                }
            }
            ?>
            
            <br><strong>Database Connection:</strong><br>
            Status: <?php echo $dbConnected ? "<span class='success'>Connected</span>" : "<span class='error'>Not connected</span>"; ?><br>
            <?php if ($connectionMethod): ?>
                Method: <?php echo htmlspecialchars($connectionMethod); ?><br>
            <?php endif; ?>
            <?php if ($dbError): ?>
                Error: <span class='error'><?php echo htmlspecialchars($dbError); ?></span><br>
            <?php endif; ?>
            
            <br><strong>Active Bans:</strong><br>
            Total: <?php echo count($activeBans); ?><br>
            <?php
            $count = 0;
            foreach ($activeBans as $key => $ban) {
                if ($count >= 5) break;
                echo "Ban #" . str_replace('ban_', '', $key) . " - Until: " . formatBanDuration($ban['until']) . "<br>";
                $count++;
            }
            if (count($activeBans) > 5) {
                echo "... and " . (count($activeBans) - 5) . " more<br>";
            }
            ?>
            
            <?php if ($pdo): ?>
                <br><strong>Ban #185 Check:</strong><br>
                <?php
                try {
                    $stmt = $pdo->prepare("SELECT * FROM litebans_bans WHERE id = 185");
                    $stmt->execute();
                    $ban185 = $stmt->fetch(PDO::FETCH_ASSOC);
                    if ($ban185) {
                        echo "Found: Yes<br>";
                        echo "Active: " . $ban185['active'] . "<br>";
                        echo "Until: " . $ban185['until'] . " (" . formatBanDuration($ban185['until']) . ")<br>";
                        echo "In active list: " . (isset($activeBans['ban_185']) ? "<span class='success'>Yes</span>" : "<span class='error'>No</span>") . "<br>";
                    } else {
                        echo "<span class='warning'>Ban #185 not found in database</span><br>";
                    }
                } catch (Exception $e) {
                    echo "<span class='error'>Error: " . $e->getMessage() . "</span><br>";
                }
                ?>
            <?php endif; ?>
            
            <br><strong>Files:</strong><br>
            Total: <?php echo count($demos); ?><br>
            Assigned: <?php echo $assignedFiles; ?><br>
            <?php
            foreach ($demos as $demo) {
                $id = pathinfo($demo['original_name'], PATHINFO_FILENAME);
                if ($id == '185') {
                    echo "File 185.mp4: " . ($demo['assigned'] ? "<span class='success'>Assigned to ban #" . $demo['ban_id'] . "</span>" : "<span class='error'>Not assigned</span>") . "<br>";
                    break;
                }
            }
            ?>
        </div>
        <?php endif; ?>
        
        <div class="stats-grid">
            <div class="stat-card">
                <h3><?php echo $totalFiles; ?></h3>
                <p>Total Files</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $assignedFiles; ?></h3>
                <p>Assigned Files</p>
            </div>
            <div class="stat-card">
                <h3><?php echo $videoFiles; ?></h3>
                <p>Video Files</p>
            </div>
            <div class="stat-card">
                <h3><?php echo formatBytes($totalSize); ?></h3>
                <p>Total Size</p>
            </div>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($activeBans)): ?>
        <div class="section">
            <h4><i class="fas fa-ban"></i> Active Bans (<?php echo count($activeBans); ?>)</h4>
            <p>Name your files with the Ban ID to auto-assign (e.g., 185.mp4)</p>
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Ban ID</th>
                            <th>Player</th>
                            <th>Reason</th>
                            <th>Duration</th>
                            <th>Banned By</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $count = 0;
                        foreach ($activeBans as $ban): 
                            if ($count >= 10) break;
                            $banId = str_replace('ban_', '', array_search($ban, $activeBans));
                            $count++;
                        ?>
                        <tr>
                            <td>#<?php echo $banId; ?></td>
                            <td><?php echo htmlspecialchars($ban['uuid'] ?? 'Unknown'); ?></td>
                            <td><?php echo htmlspecialchars(substr($ban['reason'] ?? 'No reason', 0, 50)); ?></td>
                            <td><?php echo formatBanDuration($ban['until']); ?></td>
                            <td><?php echo htmlspecialchars($ban['banned_by_name'] ?? 'Console'); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if (count($activeBans) > 10): ?>
                    <p style="text-align: center; margin-top: 1rem;">
                        Showing first 10 of <?php echo count($activeBans); ?> bans
                    </p>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="section">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                No active bans found. Check database connection.
            </div>
        </div>
        <?php endif; ?>
        
        <div class="section">
            <h4><i class="fas fa-upload"></i> Upload Evidence</h4>
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <input type="file" class="form-control" name="demo" required 
                           accept=".dem,.demo,.mp4,.mkv,.mov,.avi,.webm,.flv,.wmv,.mpg,.mpeg,.m4v,.3gp,.m4a">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-upload"></i> Upload File
                </button>
            </form>
        </div>
        
        <div class="section">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fas fa-folder-open"></i> Uploaded Files</h4>
                <div>
                    <?php if (!isset($_GET['debug'])): ?>
                        <a href="?debug=1" class="btn btn-secondary btn-sm">
                            <i class="fas fa-bug"></i> Debug Mode
                        </a>
                    <?php else: ?>
                        <a href="index.php" class="btn btn-secondary btn-sm">
                            <i class="fas fa-times"></i> Hide Debug
                        </a>
                    <?php endif; ?>
                    <?php if (!$dbConnected): ?>
                        <a href="?debug=1&manual_db=1" class="btn btn-warning btn-sm">
                            <i class="fas fa-database"></i> Try Manual DB
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if (!empty($demos)): ?>
                <div class="demo-grid">
                    <?php foreach ($demos as $demo): ?>
                    <div class="demo-card <?php echo $demo["assigned"] ? "assigned" : ""; ?>">
                        <h6><?php echo htmlspecialchars($demo["original_name"]); ?></h6>
                        
                        <?php if ($demo["assigned"] && $demo["ban_id"]): ?>
                            <div class="alert alert-success">
                                <i class="fas fa-link"></i> Assigned to Ban #<?php echo $demo["ban_id"]; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-unlink"></i> Not assigned
                            </div>
                        <?php endif; ?>
                        
                        <p>Uploaded by: <?php echo htmlspecialchars($demo["uploader"]); ?></p>
                        <p>Date: <?php echo timeAgo($demo["upload_date"]); ?></p>
                        <p>Size: <?php echo formatBytes($demo["size"]); ?></p>
                        
                        <div class="d-flex gap-2">
                            <a href="uploads/<?php echo $demo["filename"]; ?>" 
                               download="<?php echo $demo["original_name"]; ?>" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-download"></i> Download
                            </a>
                            <a href="?delete=<?php echo $demo["id"]; ?>" 
                               onclick="return confirm('Delete this file?')" 
                               class="btn btn-danger btn-sm">
                                <i class="fas fa-trash"></i> Delete
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    No files uploaded yet.
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
PHPSYSTEM;

    // Write main file
    if (@file_put_contents('demos/index.php', $mainSystem)) {
        $messages[] = 'OK Created main system file with Ban ID based assignment';
    } else {
        $errors[] = '? Failed to create main system file';
    }

    // Create fixed .htaccess files
    $htaccessMain = '# Demo System Security
Options -Indexes

# Protect JSON files
<FilesMatch "\.json$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# PHP settings for video uploads (if allowed by host)
<IfModule mod_php.c>
    php_value upload_max_filesize 500M
    php_value post_max_size 500M
    php_value max_execution_time 600
    php_value max_input_time 600
    php_value memory_limit 512M
</IfModule>';

    @file_put_contents('demos/.htaccess', $htaccessMain);
    
    $htaccessData = 'Order Deny,Allow
Deny from all';
    
    @file_put_contents('demos/data/.htaccess', $htaccessData);
    
    $htaccessUploads = '# Allow demo and video files
<FilesMatch "\.(dem|demo|mp4|mkv|mov|avi|webm|flv|wmv|mpg|mpeg|m4v|3gp|m4a)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Block PHP execution
<FilesMatch "\.(php|phtml|php3|php4|php5|php7)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Disable PHP engine
<IfModule mod_php.c>
    php_flag engine off
</IfModule>

RemoveHandler .php .phtml .php3 .php4 .php5 .php7';

    @file_put_contents('demos/uploads/.htaccess', $htaccessUploads);

    // Create .user.ini
    $userIni = '; PHP settings for shared hosting
upload_max_filesize = 500M
post_max_size = 500M
max_file_uploads = 20
max_execution_time = 600
max_input_time = 600
memory_limit = 512M';

    @file_put_contents('demos/.user.ini', $userIni);

    // Create API stats
    $apiStats = '<?php
session_start();
if (!isset($_SESSION["admin_authenticated"])) {
    http_response_code(401);
    exit(json_encode(["error" => "Unauthorized"]));
}

$dataFile = "../data/demos.json";
$demos = file_exists($dataFile) ? json_decode(file_get_contents($dataFile), true) : [];

$stats = [
    "total" => count($demos),
    "assigned" => count(array_filter($demos, function($d) { return $d["assigned"] ?? false; })),
    "videos" => count(array_filter($demos, function($d) { return ($d["type"] ?? "demo") === "video"; })),
    "size" => round(array_sum(array_column($demos, "size")) / 1024 / 1024, 2)
];

header("Content-Type: application/json");
echo json_encode($stats);';

    @file_put_contents('demos/api/stats.php', $apiStats);

    // Create Nginx config
    $nginxConfig = '# Nginx configuration for Demo System
# Add this to your server block

location /demos {
    index index.php;
    try_files $uri $uri/ /demos/index.php?$query_string;
    
    # Upload limits
    client_max_body_size 500M;
    client_body_timeout 300s;
    
    # PHP processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_param PHP_VALUE "upload_max_filesize=500M \n post_max_size=500M";
        fastcgi_read_timeout 300s;
    }
    
    # Protect data
    location ~ \.json$ {
        deny all;
    }
    
    location /demos/data {
        deny all;
    }
    
    # Allow uploads
    location /demos/uploads {
        location ~ \.(dem|demo|mp4|mkv|mov|avi|webm|flv|wmv|mpg|mpeg|m4v|3gp|m4a)$ {
            add_header Content-Disposition attachment;
            add_header X-Content-Type-Options nosniff;
        }
        
        location ~ \.php$ {
            deny all;
        }
    }
}';

    @file_put_contents('demos/nginx.conf', $nginxConfig);
    $messages[] = 'OK Created nginx configuration';

    $installReady = true;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Demo & Video System Installer - LiteBansU</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-primary: #0a0e27;
            --bg-secondary: #151c3d;
            --bg-card: #1e2749;
            --bg-hover: #252f56;
            --accent: #6366f1;
            --accent-light: #818cf8;
            --accent-dark: #4f46e5;
            --success: #22c55e;
            --warning: #f59e0b;
            --danger: #ef4444;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-muted: #64748b;
            --border: rgba(255, 255, 255, 0.1);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(99, 102, 241, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(236, 72, 153, 0.05) 0%, transparent 50%);
        }
        
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #ec4899 100%);
            padding: 3rem 2rem;
            border-radius: 20px;
            text-align: center;
            margin-bottom: 3rem;
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: shimmer 3s ease-in-out infinite;
        }
        
        @keyframes shimmer {
            0%, 100% { transform: translate(-50%, -50%) rotate(0deg); }
            50% { transform: translate(-50%, -50%) rotate(180deg); }
        }
        
        h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            font-weight: 800;
            position: relative;
            z-index: 1;
            text-shadow: 0 2px 20px rgba(0,0,0,0.2);
        }
        
        .version {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            display: inline-block;
            padding: 0.5rem 1.5rem;
            border-radius: 30px;
            font-size: 0.875rem;
            margin-bottom: 1rem;
            font-weight: 600;
            position: relative;
            z-index: 1;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
            position: relative;
            z-index: 1;
        }
        
        .info-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            font-size: 0.875rem;
        }
        
        .info-item a {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .info-item a:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }
        
        .section {
            background: var(--bg-card);
            border-radius: 20px;
            padding: 2.5rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }
        
        .status-box {
            background: var(--bg-secondary);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--border);
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        .status-box::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }
        
        .status-box.error {
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .status-box.error::before {
            background: linear-gradient(90deg, var(--danger), #f87171);
        }
        
        .status-box.success {
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .status-box.success::before {
            background: linear-gradient(90deg, var(--success), #4ade80);
        }
        
        .status-icon {
            font-size: 4rem;
            margin-bottom: 1rem;
            display: inline-block;
            animation: bounce 2s infinite;
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }
        
        .feature-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }
        
        .feature {
            background: var(--bg-secondary);
            padding: 1.75rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .feature::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(99, 102, 241, 0.1) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.2);
            border-color: var(--accent);
        }
        
        .feature:hover::before {
            opacity: 1;
        }
        
        .feature h4 {
            color: var(--accent-light);
            margin-bottom: 0.75rem;
            font-size: 1.125rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 1;
        }
        
        .feature p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.6;
            position: relative;
            z-index: 1;
        }
        
        .install-btn {
            background: linear-gradient(135deg, var(--accent) 0%, var(--accent-light) 100%);
            color: white;
            border: none;
            padding: 1rem 3rem;
            font-size: 1.125rem;
            font-weight: 700;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
            position: relative;
            overflow: hidden;
        }
        
        .install-btn::before {
            content: "";
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }
        
        .install-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 40px rgba(99, 102, 241, 0.5);
        }
        
        .install-btn:hover::before {
            width: 300px;
            height: 300px;
        }
        
        .install-btn:disabled {
            background: var(--bg-hover);
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .message {
            padding: 0.75rem 0;
            margin: 0.5rem 0;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        
        .message.success {
            color: var(--success);
        }
        
        .message.error {
            color: var(--danger);
        }
        
        .code-block {
            background: var(--bg-primary);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 1.5rem;
            font-family: 'Consolas', 'Monaco', monospace;
            font-size: 0.875rem;
            overflow-x: auto;
            margin: 1.5rem 0;
            line-height: 1.6;
            color: var(--text-secondary);
        }
        
        .alert {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(239, 68, 68, 0.05) 100%);
            border: 1px solid rgba(239, 68, 68, 0.3);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 2rem;
            text-align: center;
        }
        
        .alert h3 {
            color: var(--danger);
            margin-bottom: 0.75rem;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .check-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin: 0.5rem 0;
            font-size: 0.875rem;
            padding: 0.5rem;
            background: var(--bg-secondary);
            border-radius: 8px;
        }
        
        .check-item .icon {
            font-size: 1.25rem;
        }
        
        /* New feature highlight */
        .new-feature {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0.05) 100%);
            border: 1px solid rgba(99, 102, 241, 0.3);
            border-radius: 16px;
            padding: 1.5rem;
            margin: 2rem 0;
            text-align: center;
        }
        
        .new-feature h3 {
            color: var(--accent-light);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
        }
        
        .new-feature ul {
            text-align: left;
            max-width: 600px;
            margin: 0 auto;
            color: var(--text-secondary);
            list-style-type: none;
            padding-left: 0;
        }
        
        .new-feature li {
            margin: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
        }

        .new-feature li::before {
            content: '?';
            position: absolute;
            left: 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            h1 {
                font-size: 2rem;
            }
            
            .header {
                padding: 2rem 1.5rem;
            }
            
            .info-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .feature-grid {
                grid-template-columns: 1fr;
            }
            
            .section {
                padding: 1.5rem;
            }
        }
        
        /* Loading animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .section, .status-box, .alert {
            animation: fadeIn 0.5s ease forwards;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="version">Version <?php echo $version; ?></div>
            <h1>Demo & Video Management</h1>
            <p style="font-size: 1.25rem; margin-bottom: 0; position: relative; z-index: 1;">
                Advanced Evidence Management for LiteBansU
            </p>
            
            <div class="info-grid">
                <div class="info-item">
                    <a href="https://github.com/Yamiru/LitebansU" target="_blank">
                        <i class="fab fa-github"></i>
                        GitHub
                    </a>
                </div>
                <div class="info-item">
                    <a href="https://github.com/Yamiru/LitebansU/issues" target="_blank">
                        <i class="fas fa-bug"></i>
                        Issues
                    </a>
                </div>
                <div class="info-item">
                    <a href="https://yamiru.com" target="_blank">
                        <i class="fas fa-globe"></i>
                        Yamiru.com
                    </a>
                </div>
            </div>
        </div>
        
        <div class="new-feature">
            <h3><i class="fas fa-sparkles"></i> New in Version <?php echo $version; ?></h3>
            <ul>
                <li><strong>Complete DB Rework:</strong> Automatically finds your database from <code>.env</code> or <code>config/database.php</code>.</li>
                <li><strong>Permanent Ban Support:</strong> Correctly identifies permanent bans (where `until` is 0 or -1).</li>
                <li><strong>Enhanced Debug Mode:</strong> New debug panel to troubleshoot connections and assignments.</li>
                <li><strong>Modern UI:</strong> A completely redesigned, responsive user interface.</li>
                <li><strong>Robust & Secure:</strong> Improved security and file handling.</li>
            </ul>
        </div>
        
        <?php if (!$inCorrectLocation): ?>
            <div class="status-box error">
                <div class="status-icon">ERROR</div>
                <h3>Installation Error</h3>
                <p>This installer must be placed in your LiteBansU root directory!</p>
                <div class="code-block" style="text-align: left;">
/your-website/
+¦ index.php
+¦ config/
+¦ controllers/
L¦ install-demos.php  &lt;-- Place here
                </div>
                <p style="margin-top: 1rem;">Required files not found:</p>
                <?php foreach ($requiredFiles as $file): ?>
                    <?php if (!file_exists($file)): ?>
                        <div class="check-item" style="justify-content: center;">
                            <span class="icon" style="color: var(--danger);">?</span>
                            <span><?php echo $file; ?></span>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <?php if (!$installReady): ?>
                <div class="status-box success">
                    <div class="status-icon">OK</div>
                    <h3>Ready to Install</h3>
                    <p>The installer is in the correct location and ready to proceed.</p>
                    
                    <div style="margin-top: 1.5rem; display: inline-block; text-align: left;">
                        <div class="check-item">
                            <span class="icon" style="color: var(--success);">OK</span>
                            <span>LiteBansU installation detected</span>
                        </div>
                        <div class="check-item">
                            <span class="icon" style="color: var(--success);">OK</span>
                            <span>All required files found</span>
                        </div>
                        <div class="check-item">
                            <span class="icon" style="color: var(--success);">OK</span>
                            <span>Ready to create demo system</span>
                        </div>
                    </div>
                </div>
                
                <div class="section">
                    <h2 style="margin-bottom: 2rem; text-align: center; font-size: 2rem;">
                        ??? What Will Be Installed
                    </h2>
                    
                    <div class="feature-grid">
                        <div class="feature">
                            <h4><i class="fas fa-database"></i> Smart DB Connection</h4>
                            <p>Automatically connects to your database via <code>.env</code> or config files.</p>
                        </div>
                        <div class="feature">
                            <h4><i class="fas fa-hashtag"></i> Simple Ban ID</h4>
                            <p>Name files using Ban ID only (e.g., <code>1234.mp4</code>) for automatic matching.</p>
                        </div>
                        <div class="feature">
                            <h4><i class="fas fa-table"></i> Active Bans Table</h4>
                            <p>View all active bans with their IDs for easy reference.</p>
                        </div>
                        <div class="feature">
                            <h4><i class="fas fa-film"></i> Multi-Format Support</h4>
                            <p>Support for demos (<code>.dem</code>) and all major video formats.</p>
                        </div>
                        <div class="feature">
                            <h4><i class="fas fa-shield-alt"></i> Secure Storage</h4>
                            <p>Protected directories with proper permissions and <code>.htaccess</code> rules.</p>
                        </div>
                        <div class="feature">
                            <h4><i class="fas fa-bug"></i> Debug Mode</h4>
                            <p>An integrated debugger to easily solve potential issues.</p>
                        </div>
                    </div>
                    
                    <form method="POST" style="text-align: center; margin-top: 2rem;">
                        <button type="submit" name="install" class="install-btn">
                            <i class="fas fa-rocket"></i>
                            Install Demo System
                        </button>
                    </form>
                </div>
            <?php else: ?>
                <div class="section">
                    <h2 style="margin-bottom: 1.5rem;">Installation Results</h2>
                    
                    <?php foreach ($messages as $msg): ?>
                        <div class="message success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $msg; ?>
                        </div>
                    <?php endforeach; ?>
                    
                    <?php foreach ($errors as $err): ?>
                        <div class="message error">
                            <i class="fas fa-times-circle"></i>
                            <?php echo $err; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <?php if (empty($errors)): ?>
                    <div class="status-box success">
                        <div class="status-icon">OK</div>
                        <h3>Installation Complete!</h3>
                        <p>The Demo & Video Management System has been successfully installed.</p>
                        
                        <div style="margin-top: 2rem;">
                            <a href="demos/" class="install-btn">
                                <i class="fas fa-external-link-alt"></i>
                                Open Demo System
                            </a>
                        </div>
                    </div>
                    
                    <div class="section">
                        <h3 style="margin-bottom: 1.5rem;">?? Next Steps</h3>
                        
                        <ol style="padding-left: 1.5rem; line-height: 1.8;">
                            <li style="margin-bottom: 1.5rem;">
<strong>Add to Admin Panel (Optional):</strong> 
<p style="color: var(--text-secondary); margin-top: 0.5rem;">
    For easy access, add a link to the admin dashboard. Open the file:<br>
    <code>templates/admin/dashboard.php</code>.<br>
    Find the <code>&lt;/ul&gt;</code> and its <code>&lt;/li&gt;</code> elements at the end of the <strong>Admin Navigation Tabs</strong> section, just before the start of the <strong>Tab Content</strong>.  
    Insert the following code between them:
</p>
<div class="code-block" style="text-align: left;">
&lt;li class="nav-item" role="presentation"&gt;
    &lt;a class="nav-link" href="demos/"&gt;
        &lt;i class="fas fa-video"&gt;&lt;/i&gt; Demo Management
    &lt;/a&gt;
&lt;/li&gt;
</div>
                          </li>
                            
                            <li style="margin-bottom: 1.5rem;">
                                <strong>How to Use:</strong>
                                <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                                    When uploading files, name them using only the Ban ID. For example: <code style="background: var(--bg-hover); padding: 0.25rem 0.5rem; border-radius: 4px;">1234.mp4</code> will automatically be assigned to Ban #1234.
                                </p>
                            </li>
                            
                            <li style="margin-bottom: 1.5rem;">
                                <strong>For Nginx Users:</strong>
                                <p style="color: var(--text-secondary); margin-top: 0.5rem;">
                                    Add the configuration from <code style="background: var(--bg-hover); padding: 0.25rem 0.5rem; border-radius: 4px;">/demos/nginx.conf</code> to your server block if you are using Nginx instead of Apache.
                                </p>
                            </li>
                        </ol>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>
        
        <div class="alert">
            <h3><i class="fas fa-exclamation-triangle"></i> Security Warning</h3>
            <p><strong>DELETE THIS INSTALLER FILE</strong> after installation to prevent unauthorized access!</p>
            <p style="margin-top: 0.5rem; font-size: 0.875rem; opacity: 0.8;">
                File to delete: <code style="background: rgba(255,255,255,0.1); padding: 0.25rem 0.75rem; border-radius: 4px;">
                    <?php echo basename(__FILE__); ?>
                </code>
            </p>
        </div>
    </div>
</body>
</html>