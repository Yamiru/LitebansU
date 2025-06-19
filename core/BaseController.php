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
        // Handle null or empty values
        if (empty($uuid) || empty($name)) {
            // Return default avatar
            return 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHZpZXdCb3g9IjAgMCAzMiAzMiIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMzIiIGhlaWdodD0iMzIiIHJ4PSI0IiBmaWxsPSIjNjY2Ii8+PHBhdGggZD0iTTE2IDhjLTIuMjEgMC00IDEuNzktNCA0czEuNzkgNCA0IDQgNC0xLjc5IDQtNC0xLjc5LTQtNC00em0wIDEwYy00LjQyIDAtOCAzLjU4LTggOHYyaDJ2LTJjMC0zLjMxIDIuNjktNiA2LTZzNiAyLjY5IDYgNnYyaDJ2LTJjMC00LjQyLTMuNTgtOC04LTh6IiBmaWxsPSIjZmZmIi8+PC9zdmc+';
        }
        
        // Use safe defaults
        $uuid = preg_replace('/[^a-f0-9-]/i', '', $uuid);
        $name = preg_replace('/[^a-zA-Z0-9_]/i', '', $name);
        
        // Determine if offline mode UUID
        $baseUrl = $this->config['avatar_url'] ?? 'https://crafatar.com/avatars/{uuid}?size=32&overlay=true';
        
        if (strlen($uuid) === 36 && substr($uuid, 14, 1) === '3') {
            $baseUrl = $this->config['avatar_url_offline'] ?? 'https://minotar.net/avatar/{name}/32';
        }
        
        return str_replace(['{uuid}', '{name}'], [$uuid, $name], $baseUrl);
    }
}