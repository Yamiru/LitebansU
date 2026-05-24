<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   Machine-readable endpoints for AI agents, crawlers, and integrations.
 *  Version:       3.9
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 *
 * Provides:
 *  - /ai/stats.json  -> aggregate punishment counts as JSON
 *  - /sitemap.xml    -> XML sitemap of all canonical pages
 *
 * All endpoints are public, cacheable, and CORS-open. They never expose data
 * that isn't already visible in the regular HTML pages.
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
            ],
            'machine_readable' => [
                'stats_json' => $siteUrl . '/ai/stats.json',
                'sitemap_xml' => $siteUrl . '/sitemap.xml',
                'robots_txt' => $siteUrl . '/robots.txt',
                'llms_txt' => $siteUrl . '/llms.txt',
            ],
            'content_policy' => [
                'public' => true,
                'indexing_allowed' => true,
                'ai_training_allowed' => !(isset($this->config['seo_ai_training']) && $this->config['seo_ai_training'] === false),
                'rate_limit_recommendation_rps' => 2,
                'crawl_delay_seconds' => 1,
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
            ['loc' => $siteUrl . '/stats',     'priority' => '0.7', 'changefreq' => 'daily'],
            ['loc' => $siteUrl . '/search',    'priority' => '0.5', 'changefreq' => 'weekly'],
        ];
        
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
            $xml .= "    <lastmod>" . $now . "</lastmod>\n";
            $xml .= "    <changefreq>" . $url['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $url['priority'] . "</priority>\n";
            // Add hreflang alternates for each canonical page
            foreach ($supportedLangs as $altCode) {
                $hreflang = $altCode === 'cn' ? 'zh' : $altCode;
                $sep = strpos($url['loc'], '?') === false ? '?' : '&';
                $altUrl = $url['loc'] . $sep . 'lang=' . $altCode;
                $xml .= '    <xhtml:link rel="alternate" hreflang="' . htmlspecialchars($hreflang, ENT_QUOTES | ENT_XML1, 'UTF-8') . '" href="' . htmlspecialchars($altUrl, ENT_QUOTES | ENT_XML1, 'UTF-8') . '"/>' . "\n";
            }
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
        $lines[] = '# robots.txt for ' . ($this->config['site_name'] ?? 'LiteBansU');
        $lines[] = '# Dynamically generated. Auto-detects deployment URL and AI opt-in/opt-out.';
        $lines[] = '';
        $lines[] = '# ---------------------------------------------------------------------------';
        $lines[] = '# Default policy: allow public listings, block internal paths';
        $lines[] = '# ---------------------------------------------------------------------------';
        $lines[] = 'User-agent: *';
        foreach (['/', '/bans', '/mutes', '/warnings', '/kicks', '/stats', '/protest', '/search', '/detail', '/assets/', '/llms.txt', '/agent.json', '/ai/stats.json', '/sitemap.xml'] as $allow) {
            $lines[] = 'Allow: ' . $allow;
        }
        foreach (['/admin', '/config/', '/core/', '/controllers/', '/lang/', '/templates/', '/data/', '/logs/', '/.env', '/hash.php', '/install.php', '/install-demos.php'] as $disallow) {
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
     * Dynamic llms.txt - the LLM-friendly site description. Auto-fills site name,
     * description, supported languages, and absolute URLs so the file does not
     * need manual editing on different deployments.
     */
    public function llms(): void
    {
        $siteUrl = $this->resolveSiteUrl();
        $siteName = $this->config['site_name'] ?? 'LiteBansU';
        $siteDesc = $this->config['site_description'] ?? 'A self-hosted, multilingual web interface for the LiteBans Minecraft punishment management system.';
        $supported = implode(', ', $this->lang->getSupportedLanguages());
        $aiOptOut = isset($this->config['seo_ai_training']) && $this->config['seo_ai_training'] === false;
        $policyLine = $aiOptOut
            ? 'The operator has opted out of AI training. AI/LLM crawlers will see noindex/nofollow signals; please respect that opt-out.'
            : 'This site is intentionally public and welcomes indexing by both traditional search engines and modern LLM/AI crawlers (Googlebot, Bingbot, GPTBot, ClaudeBot, PerplexityBot, CCBot, Google-Extended, and others).';
        
        $body = <<<TXT
# {$siteName}

> {$siteDesc}

## About this site

This is a public punishment history viewer for a Minecraft (or compatible) game server running the LiteBans plugin. The site reads directly from the LiteBans database in read-only mode and exposes the data in human-readable HTML pages and machine-readable JSON endpoints.

The information shown is intended to be public and indexable: it lets players see who was punished, for what reason, by which staff member, when, and whether the punishment is still active. Player data displayed is limited to in-game name, UUID, optional avatar, and the punishment record itself.

## Available pages

- {$siteUrl}/ - Home page with site statistics and recent punishments.
- {$siteUrl}/bans - Paginated list of all bans (active and historical).
- {$siteUrl}/mutes - Paginated list of all mutes.
- {$siteUrl}/warnings - Paginated list of all warnings.
- {$siteUrl}/kicks - Paginated list of all kicks.
- {$siteUrl}/stats - Aggregate statistics across all punishment types.
- {$siteUrl}/detail?type={ban|mute|warning|kick}&id={id} - Detailed view of a single punishment.
- {$siteUrl}/search - Full-text search by player name across all punishment types.
- {$siteUrl}/protest - Form for players to appeal a punishment (optional).

## Machine-readable endpoints

- {$siteUrl}/agent.json - Discovery manifest for AI agents and crawlers.
- {$siteUrl}/ai/stats.json - Current aggregate counts as JSON (60s cache).
- {$siteUrl}/sitemap.xml - XML sitemap of all canonical pages.
- {$siteUrl}/robots.txt - Crawl policy.

## Crawling policy

{$policyLine}

## Supported languages

{$supported}

Language switch parameter: ?lang=XX

## Source code

LiteBansU is open source under the MIT license. Plugin/source: https://github.com/Yamiru/LitebansU

## Notes for AI agents

- Punishment status: an "active" punishment may still be expired if its `until` timestamp has passed and LiteBans has not yet cleared the active flag. The web UI accounts for this; raw database flags should not be trusted alone.
- Timestamps in JSON endpoints are Unix milliseconds (LiteBans native format).
- Player avatars are fetched from third-party services (Crafatar/Cravatar by default); they are not stored on this server.
- This site does not collect personal data beyond the LiteBans punishment records themselves.

TXT;
        
        if (!headers_sent()) {
            header_remove('Content-Type');
            header('Content-Type: text/plain; charset=UTF-8');
            header('Access-Control-Allow-Origin: *');
            header('Cache-Control: public, max-age=3600');
            header('X-Content-Type-Options: nosniff');
        }
        echo $body;
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
