<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       2.3
 *  Author:        Yamiru <yamiru@yamiru.com>
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

declare(strict_types=1);

abstract class BaseController
{
    protected DatabaseRepository $repository;
    protected LanguageManager $lang;
    protected ThemeManager $theme;
    protected array $config;
    
    public function __construct(DatabaseRepository $repository, LanguageManager $lang, ThemeManager $theme, array $config = [])
    {
        $this->repository = $repository;
        $this->lang = $lang;
        $this->theme = $theme;
        $this->config = $config;
    }
    
    protected function render(string $template, array $data = []): void
    {
        // Make controller instance available in templates
        $data['controller'] = $this;
        $data['lang'] = $this->lang;
        $data['theme'] = $this->theme;
        $data['config'] = $this->config;
        
        extract($data);
        
        include __DIR__ . "/../templates/header.php";
        include __DIR__ . "/../templates/{$template}.php";
        include __DIR__ . "/../templates/footer.php";
    }
    
    protected function renderPartial(string $template, array $data = []): string
    {
        ob_start();
        extract($data);
        include __DIR__ . "/../templates/partials/{$template}.php";
        return ob_get_clean();
    }
    
    protected function redirect(string $url, int $code = 302): void
    {
        header("Location: {$url}", true, $code);
        exit;
    }
    
    protected function jsonResponse(array $data, int $code = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }
    
    protected function getPage(): int
    {
        $page = $_GET['page'] ?? 1;
        return max(1, SecurityManager::validateInteger($page, 1, 1000));
    }
    
    protected function getLimit(): int
    {
        return (int)($this->config['items_per_page'] ?? 20);
    }
    
    protected function getOffset(): int
    {
        return ($this->getPage() - 1) * $this->getLimit();
    }
    
    protected function formatDate(int $timestamp): string
    {
        try {
            $timezone = new DateTimeZone($this->config['timezone'] ?? 'UTC');
            // Handle millisecond timestamps
            $seconds = intval($timestamp / 1000);
            $date = new DateTime('@' . $seconds);
            $date->setTimezone($timezone);
            
            return $date->format($this->config['date_format'] ?? 'Y-m-d H:i:s');
        } catch (Exception $e) {
            return date('Y-m-d H:i:s', intval($timestamp / 1000));
        }
    }
    
    protected function formatDuration(int $until): string
    {
        if ($until <= 0) {
            return $this->lang->get('punishment.permanent');
        }
        
        $now = time() * 1000;
        if ($until <= $now) {
            return $this->lang->get('punishment.expired');
        }
        
        $diff = intval(($until - $now) / 1000);
        $days = intval($diff / 86400);
        $hours = intval(($diff % 86400) / 3600);
        $minutes = intval(($diff % 3600) / 60);
        
        if ($days > 0) {
            return $this->lang->get('time.days', ['count' => (string)$days]);
        } elseif ($hours > 0) {
            return $this->lang->get('time.hours', ['count' => (string)$hours]);
        } else {
            return $this->lang->get('time.minutes', ['count' => (string)max(1, $minutes)]);
        }
    }
    
    protected function getAvatarUrl(?string $uuid, ?string $name): string
    {
        // Sanitize inputs
        $uuid = !empty($uuid) ? preg_replace('/[^a-f0-9-]/i', '', $uuid) : '';
        $name = !empty($name) ? preg_replace('/[^a-zA-Z0-9_]/i', '', $name) : '';
        
        // Default avatar SVG
        $default = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHZpZXdCb3g9IjAgMCA2NCA2NCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iNjQiIGhlaWdodD0iNjQiIHJ4PSI4IiBmaWxsPSIjNjY2Ii8+PHBhdGggZD0iTTMyIDE2Yy00LjQyIDAtOCAzLjU4LTggOHMzLjU4IDggOCA4IDgtMy41OCA4LTgtMy41OC04LTgtOHptMCAyMGMtOC44NCAwLTE2IDcuMTYtMTYgMTZ2NGg0di00YzAtNi42MiA1LjM3LTEyIDEyLTEyczEyIDUuMzggMTIgMTJ2NGg0di00YzAtOC44NC03LjE2LTE2LTE2LTE2eiIgZmlsbD0iI2ZmZiIvPjwvc3ZnPg==';
        
        // Detect if offline UUID (UUID v3 - offline players)
        // Offline UUID has '3' at position 14 (xxxxxxxx-xxxx-3xxx-xxxx-xxxxxxxxxxxx)
        $isOffline = !empty($uuid) && strlen($uuid) === 36 && substr($uuid, 14, 1) === '3';
        
        // OFFLINE PLAYER - Use name-based avatar
        if ($isOffline && !empty($name)) {
            // Use Minotar for offline players (works with player name)
            return "https://cravatar.eu/avatar/{$name}/64";
        }
        
        // ONLINE PLAYER - Use UUID-based avatar
        if (!empty($uuid) && !$isOffline) {
            // Use Crafatar for online players (works with UUID)
            return "https://crafatar.com/avatars/{$uuid}?size=64&overlay=true";
        }
        
        // FALLBACK - Return default SVG
        return $default;
    }
    
    public function shouldShowUuid(): bool
    {
        return (bool)($this->config['show_uuid'] ?? true);
    }
}
