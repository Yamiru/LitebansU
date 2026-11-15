<?php
/**
 * ============================================================================
 * LiteBansU
 * ============================================================================
 *
 * Plugin Name:   LiteBansU
 * Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 * Author URI:    https://yamiru.com
 * License:       MIT
 * ============================================================================
 */
declare(strict_types=1);

namespace core;

class AvatarCache
{
    private const DIR = __DIR__ . '/../data/avatars';
    private const MAX_AGE = 30 * 86400;
    private const MAX_BYTES = 524288;

    public static function available(): bool
    {
        return function_exists('imagewebp') && function_exists('imagecreatefromstring');
    }

    public static function enabled(array $config): bool
    {
        $setting = SiteExtras::get()['avatar_cache'] ?? ($config['avatar_cache'] ?? false);
        return (bool)$setting && self::available();
    }

    public static function idFor(string $uuid, string $name): ?string
    {
        if (preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/i', $uuid)) {
            return strtolower($uuid);
        }
        return preg_match('/^[A-Za-z0-9_]{1,16}$/', $name) ? 'n_' . $name : null;
    }

    public static function serveCached(string $path): void
    {
        if (preg_match('#/avatar/([a-f0-9-]{36}|n_[A-Za-z0-9_]{1,16})\.webp$#', $path, $m)) {
            $file = self::DIR . '/' . $m[1] . '.webp';
            if (is_file($file) && time() - (int)filemtime($file) < self::MAX_AGE) {
                self::send($file);
            }
        }
    }

    public static function serve(string $id, \PDO $db, string $prefix, array $config): void
    {
        $isUuid = (bool)preg_match('/^[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12}$/', $id);
        $isName = (bool)preg_match('/^n_[A-Za-z0-9_]{1,16}$/', $id);
        if (!$isUuid && !$isName) {
            http_response_code(404);
            exit;
        }

        $file = self::DIR . '/' . $id . '.webp';
        $fresh = is_file($file) && time() - (int)filemtime($file) < self::MAX_AGE;

        if (!$fresh) {
            if (self::isKnownPlayer($db, $prefix, $id, $isUuid)
                && \SecurityManager::rateLimitCheck('avatar_dl_' . \SecurityManager::getClientIp(), 60, 60)) {
                $name = $isUuid && $id[14] === '3' ? self::nameOf($db, $prefix, $id) : null;
                self::download($name !== null ? 'n_' . $name : $id, $name !== null ? false : $isUuid, $file, $config);
            }
        }

        if (is_file($file)) {
            self::send($file);
        }
        self::placeholder();
    }

    private static function isKnownPlayer(\PDO $db, string $prefix, string $id, bool $isUuid): bool
    {
        try {
            $history = $prefix . 'history';
            $stmt = $db->prepare($isUuid
                ? "SELECT 1 FROM {$history} WHERE uuid = :v LIMIT 1"
                : "SELECT 1 FROM {$history} WHERE name = :v LIMIT 1");
            $stmt->execute([':v' => $isUuid ? $id : substr($id, 2)]);
            return (bool)$stmt->fetchColumn();
        } catch (\Throwable $e) {
            error_log('AvatarCache lookup: ' . $e->getMessage());
            return false;
        }
    }

    private static function nameOf(\PDO $db, string $prefix, string $uuid): ?string
    {
        try {
            $stmt = $db->prepare("SELECT name FROM {$prefix}history WHERE uuid = :u ORDER BY date DESC LIMIT 1");
            $stmt->execute([':u' => $uuid]);
            $name = (string)$stmt->fetchColumn();
            return preg_match('/^[A-Za-z0-9_]{1,16}$/', $name) ? $name : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function download(string $id, bool $isUuid, string $file, array $config): void
    {
        $template = $isUuid
            ? (string)($config['avatar_url'] ?? 'https://crafatar.com/avatars/{uuid}?size=64&overlay=true')
            : (string)($config['avatar_url_offline'] ?? 'https://cravatar.eu/avatar/{name}/64');
        $url = str_replace(['{uuid}', '{name}'], [$isUuid ? $id : '', $isUuid ? '' : substr($id, 2)], $template);
        if (!preg_match('#^https?://#i', $url)) {
            return;
        }

        $body = null;
        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 6, CURLOPT_CONNECTTIMEOUT => 3,
                CURLOPT_FOLLOWLOCATION => true, CURLOPT_MAXREDIRS => 3, CURLOPT_PROTOCOLS => CURLPROTO_HTTP | CURLPROTO_HTTPS,
                CURLOPT_MAXFILESIZE => self::MAX_BYTES, CURLOPT_USERAGENT => 'LiteBansU-avatar-cache',
            ]);
            $data = curl_exec($ch);
            $ok = $data !== false && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200;
            curl_close($ch);
            $body = $ok ? $data : null;
        } else {
            $ctx = stream_context_create(['http' => ['timeout' => 6, 'user_agent' => 'LiteBansU-avatar-cache']]);
            $data = @file_get_contents($url, false, $ctx, 0, self::MAX_BYTES);
            $body = $data === false ? null : $data;
        }
        if ($body === null || strlen($body) > self::MAX_BYTES || !@getimagesizefromstring($body)) {
            return;
        }

        $image = @imagecreatefromstring($body);
        if (!$image) {
            return;
        }
        imagepalettetotruecolor($image);
        imagealphablending($image, true);
        imagesavealpha($image, true);
        if (!is_dir(self::DIR)) {
            @mkdir(self::DIR, 0750, true);
        }
        $tmp = $file . '.' . bin2hex(random_bytes(4)) . '.tmp';
        $saved = @imagewebp($image, $tmp, defined('IMG_WEBP_LOSSLESS') ? IMG_WEBP_LOSSLESS : 100);
        if ($saved && filesize($tmp) > 0) {
            @rename($tmp, $file);
        } else {
            @unlink($tmp);
        }
    }

    private static function send(string $file): void
    {
        header('Content-Type: image/webp');
        header('Content-Length: ' . filesize($file));
        header('Cache-Control: public, max-age=2592000');
        header('X-Content-Type-Options: nosniff');
        readfile($file);
        exit;
    }

    private static function placeholder(): void
    {
        header('Content-Type: image/svg+xml');
        header('Cache-Control: public, max-age=300');
        header('X-Content-Type-Options: nosniff');
        echo '<svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"><rect width="64" height="64" rx="8" fill="#666"/>'
            . '<path d="M32 16c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 20c-8.84 0-16 7.16-16 16v4h4v-4c0-6.62 5.37-12 12-12s12 5.38 12 12v4h4v-4c0-8.84-7.16-16-16-16z" fill="#fff"/></svg>';
        exit;
    }
}
