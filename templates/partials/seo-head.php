<?php $siteExtras = \core\SiteExtras::get(); $allowCrawlers = (bool)$siteExtras['allow_crawlers']; $gaId = \core\SiteExtras::gaId(); ?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['site_lang'] ?? $lang->getCurrentLanguage(), ENT_QUOTES, 'UTF-8') ?>" data-theme="<?= $theme->getCurrentTheme() ?>">
<head>
    <meta charset="<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="<?= htmlspecialchars($config['site_viewport'] ?? 'width=device-width, initial-scale=1.0', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="csrf-token" content="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="base-path" content="<?= htmlspecialchars($config['base_path'], ENT_QUOTES, 'UTF-8') ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="<?= htmlspecialchars($allowCrawlers ? (isset($_GET['lang']) ? 'noindex, follow' : ($config['site_robots'] ?? 'index, follow')) : 'noindex, nofollow', ENT_QUOTES, 'UTF-8') ?>">

    <title><?php
        if (isset($title)) {
            if (!empty($config['site_title_template'])) {
                echo htmlspecialchars(str_replace(['{page}', '{site}'], [$title, $config['site_name']], $config['site_title_template']), ENT_QUOTES, 'UTF-8');
            } else {
                echo htmlspecialchars($title . ' - ' . $config['site_name'], ENT_QUOTES, 'UTF-8');
            }
        } else {
            echo htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8');
        }
    ?></title>
    <meta name="description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">

    <?php
        $canonicalUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
        $canonicalQuery = '';
        if (!empty($_SERVER['QUERY_STRING'])) {
            parse_str($_SERVER['QUERY_STRING'], $qsParams);

            if (isset($qsParams['page']) && ctype_digit((string)$qsParams['page']) && (int)$qsParams['page'] > 1) {
                $canonicalQuery = '?page=' . (int)$qsParams['page'];
            }
        }

        if (!empty($config['site_url'])) {
            $siteUrlBase = rtrim($config['site_url'], '/');
        } else {
            $autoScheme = (($_SERVER['HTTPS'] ?? 'off') === 'on' || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') ? 'https' : 'http';
            $autoHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
            $autoBase = defined('BASE_PATH') ? rtrim(BASE_PATH, '/') : '';
            $siteUrlBase = $autoScheme . '://' . $autoHost . $autoBase;
        }
        $canonicalUrl = $siteUrlBase . $canonicalUri . $canonicalQuery;
    ?>
    <link rel="canonical" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">

    <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($config['site_favicon'] ?? asset('favicon.ico'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($config['site_apple_icon'] ?? asset('apple-touch-icon.png'), ENT_QUOTES, 'UTF-8') ?>">

    <meta property="og:title" content="<?= htmlspecialchars(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <?php
    $ogImage = trim((string)($config['site_og_image'] ?? '')) ?: '/og-image.png';
    if ($ogImage[0] === '/') {
        $ogImage = \core\SiteExtras::siteUrl($config) . $ogImage;
    }
    ?>
    <meta property="og:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['site_twitter_site'])): ?>
    <meta name="twitter:site" content="<?= htmlspecialchars($config['site_twitter_site'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <meta name="theme-color" content="<?= $theme->isDarkMode() ? '#0f1117' : '#ffffff' ?>">

    <?php if (isset($config['site_keywords']) && !empty($config['site_keywords'])): ?>
    <meta name="keywords" content="<?= htmlspecialchars($config['site_keywords'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta name="author" content="<?= htmlspecialchars($config['seo_organization_name'] ?? $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="rating" content="general">
    <meta name="revisit-after" content="7 days">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="distribution" content="global">
    <meta name="language" content="<?= htmlspecialchars($config['site_lang'] ?? $lang->getCurrentLanguage(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="generator" content="LiteBansU 3.0">
    <meta name="coverage" content="Worldwide">
    <meta name="target" content="all">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="application-name" content="<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="msapplication-TileColor" content="<?= $theme->isDarkMode() ? '#0f1117' : '#ffffff' ?>">
    <meta name="msapplication-config" content="none">

    <?php if (isset($config['seo_geo_region'])): ?>
    <meta name="geo.region" content="<?= htmlspecialchars($config['seo_geo_region'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (isset($config['seo_geo_placename'])): ?>
    <meta name="geo.placename" content="<?= htmlspecialchars($config['seo_geo_placename'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (isset($config['seo_geo_position'])): ?>
    <meta name="geo.position" content="<?= htmlspecialchars($config['seo_geo_position'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="ICBM" content="<?= htmlspecialchars($config['seo_geo_position'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <?php if ($allowCrawlers): ?>
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot-news" content="index, follow">
    <?php endif; ?>
    <?php if (!$allowCrawlers || (isset($config['seo_ai_training']) && $config['seo_ai_training'] === false)): ?>
    <meta name="robots" content="noai, noimageai">
    <meta name="GPTBot" content="noindex, nofollow">
    <meta name="ClaudeBot" content="noindex, nofollow">
    <meta name="PerplexityBot" content="noindex, nofollow">
    <meta name="CCBot" content="noindex, nofollow">
    <?php else: ?>
    <meta name="GPTBot" content="index, follow">
    <meta name="ClaudeBot" content="index, follow">
    <meta name="PerplexityBot" content="index, follow">
    <meta name="Google-Extended" content="index, follow">
    <?php endif; ?>

    <link rel="alternate" type="text/plain" title="LLM site description" href="<?= htmlspecialchars($siteUrlBase . '/llms.txt', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="alternate" type="application/json" title="AI agent manifest" href="<?= htmlspecialchars($siteUrlBase . '/agent.json', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="alternate" type="application/json" title="Punishment statistics (JSON)" href="<?= htmlspecialchars($siteUrlBase . '/ai/stats.json', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="sitemap" type="application/xml" title="Sitemap" href="<?= htmlspecialchars($siteUrlBase . '/sitemap.xml', ENT_QUOTES, 'UTF-8') ?>">

    <meta property="og:locale" content="<?= htmlspecialchars($config['seo_locale'] ?? 'en_US', ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['seo_facebook_app_id'])): ?>
    <meta property="fb:app_id" content="<?= htmlspecialchars($config['seo_facebook_app_id'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <meta name="twitter:card" content="summary_large_image">
    <?php if (isset($config['seo_twitter_creator'])): ?>
    <meta name="twitter:creator" content="<?= htmlspecialchars($config['seo_twitter_creator'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') ?>">

    <?php if (isset($config['seo_alternate_languages']) && is_array($config['seo_alternate_languages'])): ?>
    <?php foreach ($config['seo_alternate_languages'] as $langCode => $langUrl): ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($langCode, ENT_QUOTES, 'UTF-8') ?>" href="<?= htmlspecialchars($langUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <?php endif; ?>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>",
        "url": "<?= htmlspecialchars($config['site_url'], ENT_QUOTES, 'UTF-8') ?>",
        "description": "<?= htmlspecialchars($config['site_description'], ENT_QUOTES, 'UTF-8') ?>",
        "potentialAction": {
            "@type": "SearchAction",
            "target": {
                "@type": "EntryPoint",
                "urlTemplate": "<?= htmlspecialchars($config['site_url'], ENT_QUOTES, 'UTF-8') ?>/search?q={search_term_string}"
            },
            "query-input": "required name=search_term_string"
        }
    }
    </script>

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "<?= htmlspecialchars($config['seo_organization_name'] ?? $config['site_name'], ENT_QUOTES, 'UTF-8') ?>",
        "url": "<?= htmlspecialchars($config['site_url'], ENT_QUOTES, 'UTF-8') ?>",
        <?php if (isset($config['seo_organization_logo'])): ?>
        "logo": "<?= htmlspecialchars($config['seo_organization_logo'], ENT_QUOTES, 'UTF-8') ?>",
        <?php endif; ?>
        "sameAs": [
            <?php
            $socialLinks = [];
            if (!empty($config['seo_social_facebook'])) $socialLinks[] = '"' . htmlspecialchars($config['seo_social_facebook'], ENT_QUOTES, 'UTF-8') . '"';
            if (!empty($config['seo_social_twitter'])) $socialLinks[] = '"' . htmlspecialchars($config['seo_social_twitter'], ENT_QUOTES, 'UTF-8') . '"';
            if (!empty($config['seo_social_youtube'])) $socialLinks[] = '"' . htmlspecialchars($config['seo_social_youtube'], ENT_QUOTES, 'UTF-8') . '"';
            echo implode(',', $socialLinks);
            ?>
        ],
        "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "<?= htmlspecialchars($config['seo_contact_type'] ?? 'customer service', ENT_QUOTES, 'UTF-8') ?>",
            <?php if (isset($config['seo_contact_phone'])): ?>
            "telephone": "<?= htmlspecialchars($config['seo_contact_phone'], ENT_QUOTES, 'UTF-8') ?>",
            <?php endif; ?>
            <?php if (isset($config['seo_contact_email'])): ?>
            "email": "<?= htmlspecialchars($config['seo_contact_email'], ENT_QUOTES, 'UTF-8') ?>"
            <?php endif; ?>
        }
    }
    </script>

    <?php if (isset($config['seo_enable_breadcrumbs']) && $config['seo_enable_breadcrumbs'] && isset($breadcrumbs) && is_array($breadcrumbs)): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            <?php
            $breadcrumbItems = [];
            foreach ($breadcrumbs as $index => $crumb) {
                $breadcrumbItems[] = '{
                    "@type": "ListItem",
                    "position": ' . ($index + 1) . ',
                    "name": "' . htmlspecialchars($crumb['name'], ENT_QUOTES, 'UTF-8') . '",
                    "item": "' . htmlspecialchars($crumb['url'], ENT_QUOTES, 'UTF-8') . '"
                }';
            }
            echo implode(',', $breadcrumbItems);
            ?>
        ]
    }
    </script>
    <?php endif; ?>

    <?php if (($currentPage ?? '') === 'home' || !isset($currentPage)): ?>
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "SoftwareApplication",
        "name": "<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>",
        "applicationCategory": "Game",
        "operatingSystem": "Web",
        "offers": {
            "@type": "Offer",
            "price": "0",
            "priceCurrency": "<?= htmlspecialchars($config['seo_price_currency'] ?? 'EUR', ENT_QUOTES, 'UTF-8') ?>"
        }
    }
    </script>
    <?php endif; ?>
    <?php if ($gaId !== '' || !empty($siteExtras['cookie_banner'])): ?>
    <script>window.LB_CONSENT = { ga: <?= json_encode($gaId) ?>, banner: <?= !empty($siteExtras['cookie_banner']) ? 'true' : 'false' ?> };</script>
    <?php endif; ?>
    <?php if (trim((string)$siteExtras['custom_head']) !== ''): ?>
    <?= $siteExtras['custom_head'] ?>

    <?php endif; ?>
