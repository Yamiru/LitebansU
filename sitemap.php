<?php
/**
 * Dynamic Sitemap Generator for LiteBansU
 */

require_once __DIR__ . '/core/EnvLoader.php';

$config = EnvLoader::load(__DIR__ . '/.env');

header('Content-Type: application/xml; charset=utf-8');

$baseUrl = rtrim($config['site_url'] ?? 'https://yoursite.com', '/');

$urls = [
    ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily'],
    ['loc' => $baseUrl . '/bans', 'priority' => '0.9', 'changefreq' => 'hourly'],
    ['loc' => $baseUrl . '/mutes', 'priority' => '0.9', 'changefreq' => 'hourly'],
    ['loc' => $baseUrl . '/warnings', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['loc' => $baseUrl . '/kicks', 'priority' => '0.7', 'changefreq' => 'daily'],
    ['loc' => $baseUrl . '/stats', 'priority' => '0.8', 'changefreq' => 'daily'],
    ['loc' => $baseUrl . '/protest', 'priority' => '0.6', 'changefreq' => 'weekly'],
    ['loc' => $baseUrl . '/search', 'priority' => '0.7', 'changefreq' => 'daily'],
];

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
        xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($urls as $url): ?>
    <url>
        <loc><?= htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= date('Y-m-d\TH:i:s+00:00') ?></lastmod>
        <changefreq><?= htmlspecialchars($url['changefreq'], ENT_XML1, 'UTF-8') ?></changefreq>
        <priority><?= htmlspecialchars($url['priority'], ENT_XML1, 'UTF-8') ?></priority>
    </url>
<?php endforeach; ?>
</urlset>
