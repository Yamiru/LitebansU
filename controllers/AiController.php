<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   Machine-readable endpoints for AI agents, crawlers, and integrations.
 *  Version:       5.1
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 *
 */

declare(strict_types=1);

class AiController extends BaseController
{
    /**
     * GET /agent.json
     * Returns the AI agent discovery manifest - capabilities, endpoints, data model.
     */
    public function manifest(): void
    {
        $siteUrl = $this->resolveSiteUrl();
        $version = trim(@file_get_contents(BASE_DIR . '/.version') ?: '3.9');
        
        $manifest = [
            'schema_version' => '1.0',
            'name' => $this->config['site_name'] ?? 'LiteBansU',
            'description' => $this->config['site_description'] ?? 'Public punishment history for a LiteBans-powered game server.',
            'version' => $version,
            'product' => 'LiteBansU',
            'product_url' => 'https://github.com/Yamiru/LitebansU',
            'product_license' => 'MIT',
            'base_url' => $siteUrl,
            'canonical_url' => $siteUrl,
            'human_readable' => [
                'home' => $siteUrl . '/',
                'bans' => $siteUrl . '/bans',
                'mutes' => $siteUrl . '/mutes',
                'warnings' => $siteUrl . '/warnings',
                'kicks' => $siteUrl . '/kicks',
                'stats' => $siteUrl . '/stats',
                'search' => $siteUrl . '/search',
                'protest' => $siteUrl . '/protest',
                'privacy' => $siteUrl . '/privacy',
            ],
            'machine_readable' => [
                'stats_json' => $siteUrl . '/ai/stats.json',
                'sitemap_xml' => $siteUrl . '/sitemap.xml',
                'robots_txt' => $siteUrl . '/robots.txt',
                'llms_txt' => $siteUrl . '/llms.txt',
            ],
            'content_policy' => [
                'public' => true,
                'indexing_allowed' => \core\SiteExtras::get()['allow_crawlers'] ?? true,
                'ai_training_allowed' => !(isset($this->config['seo_ai_training']) && $this->config['seo_ai_training'] === false),
                'rate_limit_recommendation_rps' => 2,
                'crawl_delay_seconds' => 1,
            ],
            'data_policy' => [
                'personal_data' => 'Public punishment records only (player name, UUID, reason, staff, dates). See ' . $siteUrl . '/privacy for the full policy.',
                'cookies' => \core\SiteExtras::gaId() !== '' ? 'Necessary cookies always; Google Analytics only after visitor consent.' : 'Necessary cookies only (session, language, theme).',
                'staff_evidence' => 'Server staff may attach an internal report and evidence files to a punishment. These are private by default; a staff member can mark one to show its text and files on that punishment\'s detail page. Staff names and appeal notes are never shown to visitors.',
            ],
            'data_model' => [
                'punishment_types' => ['ban', 'mute', 'warning', 'kick'],
                'timestamp_unit' => 'milliseconds_since_epoch',
                'fields' => [
                    'id' => 'integer, unique within type',
                    'uuid' => 'string, player UUID (v3 for offline, v4 for online)',
                    'name' => 'string, in-game player name',
                    'reason' => 'string, free text staff comment',
                    'staff' => 'string, name of the staff member who issued the punishment',
                    'time' => 'integer, issue time in ms',
                    'until' => 'integer, expiration time in ms (0 = permanent, applies to bans/mutes only)',
                    'active' => 'boolean, effective active state (accounts for expiry, not just DB flag)',
                    'removed_by' => 'string|null, name of staff who lifted the punishment, if any',
                ],
            ],
            'languages' => [
                'supported' => $this->lang->getSupportedLanguages(),
                'default' => $this->config['default_language'] ?? 'en',
                'switch_param' => 'lang',
            ],
            'contact' => [
                'maintainer' => 'Yamiru',
                'homepage' => 'https://yamiru.com',
                'issues' => 'https://github.com/Yamiru/LitebansU/issues',
            ],
            'generated_at' => gmdate('c'),
        ];
        
        $this->sendJson($manifest, 3600);
    }
    
    /**
     * GET /ai/stats.json
     * Returns aggregate punishment counts and metadata in a stable JSON shape.
     */
    public function stats(): void
    {
        try {
            $stats = $this->repository->getStats();
        } catch (Exception $e) {
            error_log('AiController::stats error: ' . $e->getMessage());
            $stats = [];
        }
        
        $siteUrl = $this->resolveSiteUrl();
        $version = trim(@file_get_contents(BASE_DIR . '/.version') ?: '3.9');
        
        $payload = [
            'schema_version' => '1.0',
            'product' => 'LiteBansU',
            'product_version' => $version,
            'site' => [
                'name' => $this->config['site_name'] ?? 'LiteBansU',
                'url' => $siteUrl,
            ],
            'generated_at' => gmdate('c'),
            'cache_ttl_seconds' => 60,
            'counts' => [
                'bans' => [
                    'total' => (int)($stats['bans'] ?? 0),
                    'active' => (int)($stats['bans_active'] ?? 0),
                ],
                'mutes' => [
                    'total' => (int)($stats['mutes'] ?? 0),
                    'active' => (int)($stats['mutes_active'] ?? 0),
                ],
                'warnings' => [
                    'total' => (int)($stats['warnings'] ?? 0),
                ],
                'kicks' => [
                    'total' => (int)($stats['kicks'] ?? 0),
                ],
            ],
            'links' => [
                'human' => [
                    'bans' => $siteUrl . '/bans',
                    'mutes' => $siteUrl . '/mutes',
                    'warnings' => $siteUrl . '/warnings',
                    'kicks' => $siteUrl . '/kicks',
                    'stats' => $siteUrl . '/stats',
                ],
                'agent_manifest' => $siteUrl . '/agent.json',
                'sitemap' => $siteUrl . '/sitemap.xml',
                'llms_txt' => $siteUrl . '/llms.txt',
            ],
        ];
        
        $this->sendJson($payload, 60);
    }
    
    /**
     * GET /sitemap.xml
     * Dynamic sitemap of canonical pages. Does not list individual punishments
     * (high churn, easily reaches the 50k limit) - those are reachable via /bans?page=N.
     */
    public function sitemap(): void
    {
        $siteUrl = $this->resolveSiteUrl();
        $now = date('c');
        
        $urls = [
            ['loc' => $siteUrl . '/',          'priority' => '1.0', 'changefreq' => 'hourly'],
            ['loc' => $siteUrl . '/bans',      'priority' => '0.9', 'changefreq' => 'hourly'],
            ['loc' => $siteUrl . '/mutes',     'priority' => '0.9', 'changefreq' => 'hourly'],
            ['loc' => $siteUrl . '/warnings',  'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => $siteUrl . '/kicks',     'priority' => '0.6', 'changefreq' => 'daily'],
            ['loc' => $siteUrl . '/search',    'priority' => '0.5', 'changefreq' => 'weekly'],
            ['loc' => $siteUrl . '/privacy',   'priority' => '0.3', 'changefreq' => 'monthly'],
        ];
        
        // Pages that can be switched off in the settings
        if ($this->config['show_menu_stats'] ?? true) {
            $urls[] = ['loc' => $siteUrl . '/stats', 'priority' => '0.7', 'changefreq' => 'daily'];
        }
        if ($this->config['show_menu_protest'] ?? true) {
            $urls[] = ['loc' => $siteUrl . '/protest', 'priority' => '0.5', 'changefreq' => 'monthly'];
        }

        // Add paginated listings for the first few pages of each high-volume type
        try {
            $perPage = max(1, (int)($this->config['items_per_page'] ?? 20));
            foreach (['bans', 'mutes'] as $type) {
                $totalMethod = 'getTotal' . ucfirst($type);
                if (method_exists($this->repository, $totalMethod)) {
                    $total = (int)$this->repository->$totalMethod(false);
                    $pages = min(50, (int)ceil($total / $perPage)); // cap at 50 pages per type
                    for ($p = 2; $p <= $pages; $p++) {
                        $urls[] = [
                            'loc' => $siteUrl . '/' . $type . '?page=' . $p,
                            'priority' => '0.5',
                            'changefreq' => 'daily',
                        ];
                    }
                }
            }
        } catch (Exception $e) {
            // Ignore - sitemap with core pages is still valid
            error_log('AiController::sitemap pagination error: ' . $e->getMessage());
        }
        
        $supportedLangs = $this->lang->getSupportedLanguages();
        $xmlDecl = '<' . '?xml version="1.0" encoding="UTF-8"?' . '>'; // split to avoid any short_open_tag edge cases
        $xml = $xmlDecl . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">' . "\n";
        
        foreach ($urls as $url) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($url['loc'], ENT_QUOTES | ENT_XML1, 'UTF-8') . "</loc>\n";
            $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }
        
        $xml .= '</urlset>' . "\n";
        
        // Drop any earlier Content-Type that index.php may have set, then send XML.
        // Some Apache + mod_php combinations otherwise leave text/html on dynamically-routed .xml URLs.
        if (!headers_sent()) {
            header_remove('Content-Type');
            header('Content-Type: application/xml; charset=UTF-8');
            header('Access-Control-Allow-Origin: *');
            header('Cache-Control: public, max-age=3600');
            header('X-Robots-Tag: noindex, follow');
            // Belt and suspenders: prevent any framework/server from re-sniffing the body
            header('X-Content-Type-Options: nosniff');
        }
        echo $xml;
    }
    
    /**
     * GET /robots.txt
     * Dynamic robots.txt with absolute Sitemap URL that auto-detects BASE_PATH.
     * Works on any deployment (root domain, subdomain, subdirectory) with no manual editing.
     */
    public function robots(): void
    {
        $siteUrl = $this->resolveSiteUrl();
        $aiOptOut = isset($this->config['seo_ai_training']) && $this->config['seo_ai_training'] === false;
        
        $lines = [];
        if (!(\core\SiteExtras::get()['allow_crawlers'] ?? true)) {
            // Crawlers switched off in the admin panel (SEO & Tracking)
            if (!headers_sent()) {
                header('Content-Type: text/plain; charset=UTF-8');
            }
            echo "User-agent: *\nDisallow: /\n";
            return;
        }
        $lines[] = '# robots.txt for ' . ($this->config['site_name'] ?? 'LiteBansU');
        $lines[] = '# Dynamically generated. Auto-detects deployment URL and AI opt-in/opt-out.';
        $lines[] = '';
        $lines[] = '# ---------------------------------------------------------------------------';
        $lines[] = '# Default policy: allow public listings, block internal paths';
        $lines[] = '# ---------------------------------------------------------------------------';
        $lines[] = 'User-agent: *';
        foreach (['/', '/bans', '/mutes', '/warnings', '/kicks', '/stats', '/protest', '/privacy', '/search', '/detail', '/assets/', '/llms.txt', '/agent.json', '/ai/stats.json', '/sitemap.xml'] as $allow) {
            $lines[] = 'Allow: ' . $allow;
        }
        foreach (['/admin', '/config/', '/core/', '/controllers/', '/lang/', '/templates/', '/data/', '/logs/', '/.env', '/hash.php', '/install.php', '/demos/'] as $disallow) {
            $lines[] = 'Disallow: ' . $disallow;
        }
        $lines[] = '';
        $lines[] = '# Avoid wasting crawl budget on transient query parameters';
        foreach (['/*?lang=', '/*?theme=', '/*&lang=', '/*&theme='] as $disallow) {
            $lines[] = 'Disallow: ' . $disallow;
        }
        $lines[] = '';
        
        $traditionalBots = ['Googlebot', 'Bingbot', 'DuckDuckBot', 'YandexBot'];
        $aiBots = [
            'GPTBot', 'ChatGPT-User', 'OAI-SearchBot',
            'ClaudeBot', 'Claude-Web', 'anthropic-ai',
            'PerplexityBot', 'Perplexity-User',
            'Google-Extended', 'CCBot', 'Bytespider',
            'Applebot', 'Applebot-Extended',
            'meta-externalagent', 'FacebookBot',
            'cohere-ai', 'cohere-training-data-crawler',
            'Diffbot', 'ImagesiftBot', 'Omgilibot', 'PetalBot',
        ];
        
        $lines[] = '# ---------------------------------------------------------------------------';
        $lines[] = '# Traditional search engines';
        $lines[] = '# ---------------------------------------------------------------------------';
        foreach ($traditionalBots as $bot) {
            $lines[] = 'User-agent: ' . $bot;
            $lines[] = 'Allow: /';
            $lines[] = '';
        }
        
        $lines[] = '# ---------------------------------------------------------------------------';
        if ($aiOptOut) {
            $lines[] = '# AI / LLM crawlers - opt-out (seo_ai_training=false)';
            $lines[] = '# ---------------------------------------------------------------------------';
            foreach ($aiBots as $bot) {
                $lines[] = 'User-agent: ' . $bot;
                $lines[] = 'Disallow: /';
                $lines[] = '';
            }
        } else {
            $lines[] = '# AI / LLM crawlers - public ban lists are intended to be indexable';
            $lines[] = '# ---------------------------------------------------------------------------';
            foreach ($aiBots as $bot) {
                $lines[] = 'User-agent: ' . $bot;
                $lines[] = 'Allow: /';
                $lines[] = '';
            }
        }
        
        $lines[] = '# ---------------------------------------------------------------------------';
        $lines[] = '# Sitemap & crawl delay';
        $lines[] = '# ---------------------------------------------------------------------------';
        $lines[] = 'Sitemap: ' . $siteUrl . '/sitemap.xml';
        $lines[] = '';
        $lines[] = 'Crawl-delay: 1';
        
        $body = implode("\n", $lines) . "\n";
        
        if (!headers_sent()) {
            header_remove('Content-Type');
            header('Content-Type: text/plain; charset=UTF-8');
            header('Access-Control-Allow-Origin: *');
            header('Cache-Control: public, max-age=3600');
            header('X-Content-Type-Options: nosniff');
        }
        echo $body;
    }
    
    /**
     * GET /llms.txt
     */
    public function llms(): void
    {
        if (!headers_sent()) {
            header_remove('Content-Type');
            header('Content-Type: text/plain; charset=UTF-8');
            header('Access-Control-Allow-Origin: *');
            header('Cache-Control: public, max-age=3600');
            header('X-Content-Type-Options: nosniff');
        }
        echo $this->buildLlms();
    }

    /** The llms.txt text. Also used by the admin check, so both always see the same thing. */
    public function buildLlms(): string
    {
        $siteUrl = $this->resolveSiteUrl();
        $siteName = trim((string)($this->config['site_name'] ?? '')) ?: 'LiteBansU';

        if (!(\core\SiteExtras::get()['allow_crawlers'] ?? true)) {
            // Crawlers switched off in the admin panel (SEO & Tracking) - same signal as robots.txt
            return "# {$siteName}\n\nThe operator has closed this site to crawlers and AI agents. See [robots.txt]({$siteUrl}/robots.txt).\n";
        }

        $siteDesc = trim((string)($this->config['site_description'] ?? '')) ?: 'Public punishment history of a game server running the LiteBans plugin.';
        $siteDesc = trim(preg_replace('/\s+/', ' ', $siteDesc));
        $supported = implode(', ', $this->lang->getSupportedLanguages());
        $aiOptOut = isset($this->config['seo_ai_training']) && $this->config['seo_ai_training'] === false;
        $policyLine = $aiOptOut
            ? 'The operator has opted out of AI training. AI/LLM crawlers will see noindex/nofollow signals; please respect that opt-out.'
            : 'This site is public and welcomes indexing by search engines and LLM/AI crawlers. The live policy is in [robots.txt](' . $siteUrl . '/robots.txt) and overrides anything stated here.';
        $cookieLine = \core\SiteExtras::gaId() !== ''
            ? 'Necessary cookies (session, language, theme, cookie choice) are always set. Google Analytics is loaded only after a visitor accepts it in the cookie notice.'
            : 'Only necessary cookies are set (session, language, theme, cookie choice). No analytics are configured.';

        $pages = [
            ['/', 'Home', 'Site statistics and recent punishments.'],
            ['/bans', 'Bans', 'Paginated list of all bans (active and historical).'],
            ['/mutes', 'Mutes', 'Paginated list of all mutes.'],
            ['/warnings', 'Warnings', 'Paginated list of all warnings.'],
            ['/kicks', 'Kicks', 'Paginated list of all kicks.'],
        ];
        if ($this->config['show_menu_stats'] ?? true) {
            $pages[] = ['/stats', 'Statistics', 'Aggregate statistics across all punishment types.'];
        }
        $pages[] = ['/search', 'Search', 'Search by player name across all punishment types.'];
        if ($this->config['show_menu_protest'] ?? true) {
            $pages[] = ['/protest', 'Protest', 'Form for players to appeal a punishment.'];
        }
        $pages[] = ['/privacy', 'Privacy', 'What data is shown and which cookies are used.'];

        $machine = [
            ['/agent.json', 'Agent manifest', 'Discovery manifest for AI agents and crawlers.'],
            ['/ai/stats.json', 'Stats JSON', 'Current aggregate counts as JSON (60s cache).'],
            ['/sitemap.xml', 'Sitemap', 'XML sitemap of all canonical pages.'],
            ['/robots.txt', 'Robots', 'Crawl policy.'],
        ];

        $list = static function (array $items) use ($siteUrl): string {
            $out = '';
            foreach ($items as [$path, $label, $desc]) {
                $out .= "- [{$label}]({$siteUrl}{$path}): {$desc}\n";
            }
            return $out;
        };

        return "# {$siteName}\n\n> {$siteDesc}\n\n"
            . "This is a public punishment history viewer for a game server running the LiteBans plugin. It reads the LiteBans database read-only and shows in-game name, UUID, optional avatar, reason, staff member, dates and whether the punishment is still active. Single punishments are at `{$siteUrl}/detail?type={ban|mute|warning|kick}&id={id}`.\n\n"
            . "## Pages\n\n" . $list($pages)
            . "\n## Machine-readable endpoints\n\n" . $list($machine)
            . "\n## Crawling policy\n\n{$policyLine}\n\n"
            . "## Supported languages\n\n{$supported}. Switch with `?lang=XX`.\n\n"
            . "## Cookies and personal data\n\n{$cookieLine} Full policy: [Privacy]({$siteUrl}/privacy)\n\n"
            . "Server staff may attach an internal report and evidence files to a punishment. This is private by default; only report text and files a staff member marked public are shown. Staff names and appeal notes are never shown to visitors.\n\n"
            . "## Optional\n\n- [LiteBansU source code](https://github.com/Yamiru/LitebansU): Open source (MIT) software this site runs on.\n\n"
            . "## Notes for AI agents\n\n"
            . "- An \"active\" punishment may already be expired if its `until` timestamp has passed; the web UI accounts for this.\n"
            . "- Timestamps in JSON endpoints are Unix milliseconds.\n"
            . "- Avatars come from third-party services and are not stored on this server.\n";
    }

    /**
     * Checks an llms.txt body against the llms.txt recommendations and this site's own settings.
     * Returns a list of [level, message] where level is ok|warn|error.
     */
    public static function checkLlms(string $body, string $siteUrl, string $siteName): array
    {
        $r = [];
        $add = static function (string $level, string $msg) use (&$r): void {
            $r[] = [$level, $msg];
        };

        if (trim($body) === '') {
            return [['error', 'The file is empty.']];
        }
        preg_match_all('/^# \S.*$/m', $body, $h1);
        count($h1[0]) >= 1 ? $add('ok', 'Has an H1 heading: ' . trim($h1[0][0], '# ')) : $add('error', 'No H1 heading (a line starting with "# ").');
        if (count($h1[0]) > 1) {
            $add('warn', 'More than one H1 heading.');
        }
        if ($h1[0] && trim($h1[0][0], '# ') !== $siteName) {
            $add('warn', "The H1 is not the configured site name \"{$siteName}\".");
        }
        preg_match('/^> \S.*$/m', $body) ? $add('ok', 'Has a summary line (blockquote).') : $add('warn', 'No summary line ("> ...") under the H1.');

        preg_match_all('/\[[^\]]+\]\((https?:\/\/[^)\s]+)\)/', $body, $links);
        $count = count($links[1]);
        $count > 0 ? $add('ok', "Contains {$count} Markdown links.") : $add('error', 'No Markdown links. Links must look like [text](https://...).');
        preg_match('/^## \S/m', $body) ? $add('ok', 'Has H2 sections.') : $add('warn', 'No H2 sections to group the links.');

        if ($count > 0) {
            $own = array_filter($links[1], fn($u) => str_starts_with($u, $siteUrl));
            $own ? $add('ok', count($own) . ' links point to this site (' . $siteUrl . ').') : $add('error', 'No link points to this site (' . $siteUrl . ').');
        }
        if (preg_match('#https?://(localhost|127\.0\.0\.1)#', $body) && !preg_match('#^https?://(localhost|127\.0\.0\.1)#', $siteUrl)) {
            $add('error', 'The file contains localhost links, but the site URL is ' . $siteUrl . '.');
        }
        preg_match('/<\/?(html|body|div|script)\b/i', $body) ? $add('error', 'Looks like HTML, not Markdown.') : $add('ok', 'Plain Markdown, no HTML.');
        return $r;
    }

    private function sendJson(array $payload, int $cacheSeconds = 0): void
    {
        if (!headers_sent()) {
            header_remove('Content-Type');
            header('Content-Type: application/json; charset=UTF-8');
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, OPTIONS');
            if ($cacheSeconds > 0) {
                header('Cache-Control: public, max-age=' . $cacheSeconds);
            } else {
                header('Cache-Control: no-store');
            }
            header('X-Robots-Tag: noindex, follow');
            header('X-Content-Type-Options: nosniff');
        }
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
    }
    
    /**
     * Resolve the canonical base URL of this deployment.
     *
     * Priority:
     *   1. $config['site_url'] (operator-defined absolute URL, including any subdir like /litebansU)
     *   2. Scheme + host + BASE_PATH (auto-detected from the current request)
     *
     * This makes the AI/SEO endpoints work on any hosting layout - root domain,
     * subdomain, or subdirectory - without forcing the operator to configure it.
     */
    private function resolveSiteUrl(): string
    {
        $configured = $this->config['site_url'] ?? '';
        if (is_string($configured) && $configured !== '') {
            return rtrim($configured, '/');
        }
        
        $scheme = (($_SERVER['HTTPS'] ?? 'off') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $basePath = defined('BASE_PATH') ? BASE_PATH : '';
        
        return $scheme . '://' . $host . rtrim($basePath, '/');
    }
}
