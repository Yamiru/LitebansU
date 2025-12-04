<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.3
 *  Author:        Yamiru <yamiru@yamiru.com>
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
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
        
        // CRITICAL FIX: Use EnvLoader to read avatar URLs from .env
        // For offline players (cracked servers), use NAME to fetch original skin from Mojang
        // For online players (premium), use UUID
        
        // Detect if offline UUID (UUID v3 - offline/cracked servers)
        // Offline UUID has '3' at position 14: xxxxxxxx-xxxx-3xxx-xxxx-xxxxxxxxxxxx
        // Online UUID has '4' at position 14:  xxxxxxxx-xxxx-4xxx-xxxx-xxxxxxxxxxxx
        $isOffline = !empty($uuid) && strlen($uuid) === 36 && substr($uuid, 14, 1) === '3';
        
        // OFFLINE PLAYER (UUID v3) - Use name to fetch original Mojang skin
        if ($isOffline && !empty($name)) {
            // Crafatar/Mineatar can fetch original skin from Mojang using player name
            // This works for premium players on cracked servers
            $offlineUrl = \core\EnvLoader::get('AVATAR_URL_OFFLINE', 'https://cravatar.eu/avatar/{name}/64');
            return str_replace(['{uuid}', '{name}'], [$uuid, $name], $offlineUrl);
        }
        
        // ONLINE PLAYER (UUID v4) - Use UUID directly
        if (!empty($uuid) && !$isOffline) {
            $onlineUrl = \core\EnvLoader::get('AVATAR_URL', 'https://crafatar.com/avatars/{uuid}?size=64&overlay=true');
            return str_replace(['{uuid}', '{name}'], [$uuid, $name], $onlineUrl);
        }
        
        // FALLBACK - No valid UUID or name
        return $default;
    }
    
    public function shouldShowUuid(): bool
    {
        return (bool)($this->config['show_uuid'] ?? true);
    }
}
