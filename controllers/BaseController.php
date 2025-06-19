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
        echo json_encode($data);
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
        $timezone = new DateTimeZone($this->config['timezone'] ?? 'UTC');
        $date = new DateTime('@' . intval($timestamp / 1000));
        $date->setTimezone($timezone);
        
        return $date->format($this->config['date_format'] ?? 'Y-m-d H:i:s');
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
        
        if ($days > 0) {
            return $this->lang->get('time.days', ['count' => $days]);
        }
        
        return $this->lang->get('time.hours', ['count' => $hours]);
    }
    
    protected function getAvatarUrl(string $uuid, string $name): string
    {
        $baseUrl = $this->config['avatar_url'] ?? 'https://crafatar.com/avatars/{uuid}?size=32&overlay=true';
        
        if (strlen($uuid) === 36 && $uuid[14] === '3') {
            $baseUrl = $this->config['avatar_url_offline'] ?? 'https://minotar.net/avatar/{name}/32';
        }
        
        return str_replace(['{uuid}', '{name}'], [$uuid, $name], $baseUrl);
    }
}