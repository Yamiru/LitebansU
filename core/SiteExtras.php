<?php
/**
 * ============================================================================
 * LiteBansU
 * ============================================================================
 *
 * Plugin Name:   LiteBansU
 * Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 * Version:       5.0
 * Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 * Author URI:    https://yamiru.com
 * License:       MIT
 * License URI:   https://opensource.org/licenses/MIT
 * Repository:    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */
declare(strict_types=1);

namespace core;

class SiteExtras
{
    public const FILE = __DIR__ . '/../data/site_extras.php';
    private const LEGACY = __DIR__ . '/../data/site_extras.json';
    private const GUARD = "<?php http_response_code(404); exit; ?>\n";

    private const DEFAULTS = [
        'allow_crawlers' => true,
        'ga_id' => '',
        'cookie_banner' => true,
        'custom_head' => '',
        'custom_footer' => '',
        'privacy' => [],
        'indexnow_key' => '',
    ];

    private const ALLOWED_TAGS = '<p><br><strong><b><em><i><u><ul><ol><li><h2><h3><h4><a><blockquote><code><hr>';

    private static ?array $cache = null;

    public static function get(): array
    {
        if (self::$cache === null) {
            // Stored as a PHP file that exits at once, so no web server can hand the JSON out
            $raw = @file_get_contents(is_file(self::FILE) ? self::FILE : self::LEGACY);
            $data = $raw ? json_decode((string)preg_replace('/^<\?php[^\n]*\?>\r?\n/', '', $raw), true) : null;
            self::$cache = array_merge(self::DEFAULTS, is_array($data) ? $data : []);
            if (!is_array(self::$cache['privacy'])) {
                self::$cache['privacy'] = [];
            }
        }
        return self::$cache;
    }

    /** Merges $changes into the stored settings. Returns false when data/ cannot be written. */
    public static function save(array $changes): bool
    {
        $data = array_merge(self::get(), array_intersect_key($changes, self::DEFAULTS));
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json === false || @file_put_contents(self::FILE, self::GUARD . $json, LOCK_EX) === false) {
            return false;
        }
        @chmod(self::FILE, 0600);
        @unlink(self::LEGACY);
        self::$cache = $data;
        return true;
    }

    /** A valid Google tag ID (G-, GT-, AW- or UA-), or an empty string. */
    public static function gaId(): string
    {
        $id = strtoupper(trim((string)self::get()['ga_id']));
        return preg_match('/^(G|GT|AW|UA)-[A-Z0-9-]{4,20}$/', $id) ? $id : '';
    }

    public static function normalizeGaId(string $value): string
    {
        $id = strtoupper(trim($value));
        return preg_match('/^(G|GT|AW|UA)-[A-Z0-9-]{4,20}$/', $id) ? $id : '';
    }

    /** The IndexNow key. With $create it is generated on first use; returns null if it cannot be stored. */
    public static function indexNowKey(bool $create = true): ?string
    {
        $key = (string)self::get()['indexnow_key'];
        if (preg_match('/^[a-f0-9]{32}$/', $key)) {
            return $key;
        }
        if (!$create) {
            return null;
        }
        $key = bin2hex(random_bytes(16));
        return self::save(['indexnow_key' => $key]) ? $key : null;
    }

    public static function siteUrl(array $config): string
    {
        if (!empty($config['site_url'])) {
            return rtrim((string)$config['site_url'], '/');
        }
        $scheme = (($_SERVER['HTTPS'] ?? 'off') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
        return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . (defined('BASE_PATH') ? rtrim(BASE_PATH, '/') : '');
    }

    public static function t(string $key, string $lang): string
    {
        static $all = null;
        $all ??= (function () {
            $file = __DIR__ . '/../lang/extras.php';
            return is_file($file) ? (require $file) : [];
        })();
        return $all[$lang][$key] ?? $all['en'][$key] ?? $key;
    }

    /** Sanitized privacy page HTML for a language: the admin's text, else English, else the built-in default. */
    public static function privacyHtml(string $lang, string $siteName): string
    {
        $privacy = self::get()['privacy'];
        $text = trim((string)($privacy[$lang] ?? ''));
        if ($text === '') {
            $text = trim((string)($privacy['en'] ?? ''));
        }
        if ($text === '') {
            return self::defaultPrivacyHtml($siteName);
        }
        return self::sanitizeHtml($text);
    }

    public static function defaultPrivacyHtml(string $siteName): string
    {
        return "<p>This page explains what this website does with data when you visit it. The site operator can change this text in the admin panel.</p>"
            . "<h2>What we show</h2><p>The site publicly lists punishments (bans, mutes, warnings and kicks) issued on the game server: player name, reason, staff member, date and duration.</p>"
            . "<h2>Cookies</h2><ul>"
            . "<li><strong>Necessary:</strong> a session cookie, your language and theme choice, and your cookie choice. The site cannot work without them.</li>"
            . "<li><strong>Analytics:</strong> only if you accept them, Google Analytics sets cookies to measure how the site is used. If you decline, it is not loaded.</li></ul>"
            . "<p>You can change your choice at any time with the cookie settings link at the bottom of every page.</p>"
            . "<h2>Your rights</h2><p>You can ask the site operator to correct or remove personal data that concerns you. Contact the operator using the details on the protest page.</p>";
    }

    /** Keeps a small set of formatting tags, drops every attribute except a safe href on links. */
    public static function sanitizeHtml(string $html): string
    {
        $html = strip_tags($html, self::ALLOWED_TAGS);
        if (trim($html) === '') {
            return '';
        }
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8"><div id="root">' . $html . '</div>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('//*[@id="root"]//*') as $node) {
            $href = $node->nodeName === 'a' ? trim($node->getAttribute('href')) : '';
            while ($node->attributes->length) {
                $node->removeAttributeNode($node->attributes->item(0));
            }
            if ($node->nodeName === 'a' && preg_match('#^(https?://|mailto:|/)#i', $href)) {
                $node->setAttribute('href', $href);
                $node->setAttribute('rel', 'noopener nofollow');
            }
        }
        $root = $dom->getElementById('root') ?? $dom->documentElement;
        $out = '';
        foreach ($root->childNodes as $child) {
            $out .= $dom->saveHTML($child);
        }
        return $out;
    }

    /**
     * Public pages worth indexing. Individual punishments (they contain a player and an ID) are left out.
     */
    public static function indexableUrls(array $config, $repository = null): array
    {
        $site = self::siteUrl($config);
        $paths = ['/', '/bans', '/mutes', '/warnings', '/kicks'];
        if ($config['show_menu_stats'] ?? true) {
            $paths[] = '/stats';
        }
        if ($config['show_menu_protest'] ?? true) {
            $paths[] = '/protest';
        }
        $paths[] = '/privacy';
        $urls = array_map(fn($p) => $site . $p, $paths);

        if ($repository) {
            try {
                $perPage = max(1, (int)($config['items_per_page'] ?? 20));
                foreach (['bans', 'mutes'] as $type) {
                    $method = 'getTotal' . ucfirst($type);
                    if (method_exists($repository, $method)) {
                        $pages = min(50, (int)ceil(((int)$repository->$method(false)) / $perPage));
                        for ($page = 2; $page <= $pages; $page++) {
                            $urls[] = $site . '/' . $type . '?page=' . $page;
                        }
                    }
                }
            } catch (\Throwable $e) {
                error_log('SiteExtras::indexableUrls ' . $e->getMessage());
            }
        }
        return $urls;
    }

    /** POSTs a URL list to IndexNow. Returns [succeeded, HTTP status, message]. */
    public static function submitIndexNow(array $config, array $urls): array
    {
        $key = self::indexNowKey();
        if ($key === null) {
            return [false, 0, 'Cannot store the IndexNow key (data/ is not writable).'];
        }
        $site = self::siteUrl($config);
        $host = parse_url($site, PHP_URL_HOST);
        if (!$host || in_array($host, ['localhost', '127.0.0.1'], true)) {
            return [false, 0, 'IndexNow needs a public site URL. Set SITE_URL in .env (currently ' . $site . ').'];
        }
        $payload = json_encode(['host' => $host, 'key' => $key, 'keyLocation' => $site . '/' . $key . '.txt', 'urlList' => array_values($urls)], JSON_UNESCAPED_SLASHES);
        $endpoint = 'https://api.indexnow.org/indexnow';

        if (function_exists('curl_init')) {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_POST => true, CURLOPT_POSTFIELDS => $payload, CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 15,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json; charset=utf-8'],
            ]);
            $body = curl_exec($ch);
            $status = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
        } else {
            $context = stream_context_create(['http' => [
                'method' => 'POST', 'header' => "Content-Type: application/json; charset=utf-8\r\n", 'content' => $payload, 'timeout' => 15, 'ignore_errors' => true,
            ]]);
            $body = @file_get_contents($endpoint, false, $context);
            $status = 0;
            foreach ($http_response_header ?? [] as $line) {
                if (preg_match('#^HTTP/\S+\s+(\d{3})#', $line, $m)) {
                    $status = (int)$m[1];
                }
            }
            $error = $body === false ? 'Request failed' : '';
        }

        if ($status === 200 || $status === 202) {
            return [true, $status, count($urls) . ' URLs submitted.'];
        }
        $hints = [400 => 'Bad request.', 403 => 'The key file was not found at ' . $site . '/' . $key . '.txt.', 422 => 'The URLs do not belong to the host.', 429 => 'Too many requests, try again later.'];
        return [false, $status, ($hints[$status] ?? ($error ?: 'Unexpected response.')) . ($status ? ' (HTTP ' . $status . ')' : '')];
    }
}
