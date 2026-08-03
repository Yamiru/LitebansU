<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['site_lang'] ?? $lang->getCurrentLanguage(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="<?= htmlspecialchars($config['site_viewport'] ?? 'width=device-width, initial-scale=1.0', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="csrf-token" content="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="base-path" content="<?= htmlspecialchars($config['base_path'], ENT_QUOTES, 'UTF-8') ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="<?= htmlspecialchars($config['site_robots'] ?? 'index, follow', ENT_QUOTES, 'UTF-8') ?>">

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
    <?php if (isset($config['site_og_image'])): ?>
    <meta property="og:image" content="<?= htmlspecialchars($config['site_og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['site_twitter_site'])): ?>
    <meta name="twitter:site" content="<?= htmlspecialchars($config['site_twitter_site'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <link href="<?= htmlspecialchars(asset('assets/css/main.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    <style>
        .navbar-modern .container{max-width:100%}
        .navbar-collapse{min-width:0;gap:var(--space-sm)}
        .navbar-nav{flex-wrap:wrap;justify-content:center;min-width:0}
        .navbar-controls{flex-shrink:0;margin-left:auto}
        @media (min-width:1400px) and (max-width:1699.98px){
            .navbar-nav{gap:.125rem}
            .nav-link{font-size:.875rem;padding:var(--space-xs) var(--space-sm);padding-right:.875rem;gap:.25rem}
            .nav-link i{display:none}
            .navbar-brand{font-size:1.05rem}
        }
        @media (max-width:1399.98px){
            .navbar-toggler{display:block;margin-left:auto}
            .navbar-collapse{position:absolute;top:100%;left:0;right:0;z-index:1030;background:var(--bg-primary);border-top:1px solid var(--border-color);box-shadow:var(--shadow-lg);padding:var(--space-md);display:none;flex-direction:column;gap:var(--space-md);max-height:calc(100vh - 80px);overflow-y:auto;-webkit-overflow-scrolling:touch}
            .navbar-collapse.show{display:flex}
            .navbar-nav{flex-direction:column;width:100%;gap:var(--space-xs)}
            .nav-item{width:100%}
            .nav-link{width:100%;padding:var(--space-md);padding-right:var(--space-xl);justify-content:space-between}
            .nav-link .badge{position:static;transform:none;margin-left:auto}
            .navbar-controls{width:100%;justify-content:space-between;padding-top:var(--space-md);border-top:1px solid var(--border-color);flex-wrap:wrap;gap:var(--space-sm);margin-left:0}
            .navbar-controls .dropdown{flex:1}
            .navbar-controls .btn-navbar{width:100%}
            .theme-toggle-wrapper{margin-left:auto}
        }
        @media (min-width:992px) and (max-width:1279.98px){
            .table-responsive .table thead th,.table-responsive .table tbody td{padding:.375rem .5rem;font-size:.8125rem}
            .table-responsive .player-info{gap:.375rem}
            .table-responsive .avatar{width:30px;height:30px}
            .table-responsive .reason-cell{max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
            .table-responsive .badge,.table-responsive .status-badge{font-size:.6875rem;padding:.2rem .4rem}
            .table-responsive .btn-sm{padding:.25rem .5rem;font-size:.75rem}
            .table-responsive .btn-sm i{display:none}
            .table-responsive .font-monospace{font-size:.75rem}
            .table-responsive .sort-link{gap:.25rem}
            .table-responsive .sort-link i{font-size:.75rem}
            .table-responsive.d-none.d-lg-block .table thead th:nth-child(2),
            .table-responsive.d-none.d-lg-block .table tbody td:nth-child(2){display:none}
        }
    </style>

    <meta name="theme-color" content="<?= htmlspecialchars($config['site_theme_color'] ?? '#ef4444', ENT_QUOTES, 'UTF-8') ?>">

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
    <meta name="msapplication-TileColor" content="<?= htmlspecialchars($config['site_theme_color'] ?? '#ef4444', ENT_QUOTES, 'UTF-8') ?>">
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

    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot-news" content="index, follow">
    <?php if (isset($config['seo_ai_training']) && $config['seo_ai_training'] === false): ?>
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
    <?php if (isset($config['site_og_image'])): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($config['site_og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>

    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">

    <?php if (isset($config['seo_alternate_languages']) && is_array($config['seo_alternate_languages'])): ?>
    <?php foreach ($config['seo_alternate_languages'] as $langCode => $langUrl): ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($langCode, ENT_QUOTES, 'UTF-8') ?>" href="<?= htmlspecialchars($langUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <?php else: ?>
    <?php

        $sep = $canonicalQuery === '' ? '?' : '&';
        foreach ($lang->getSupportedLanguages() as $altCode):

            $hreflangCode = $altCode === 'cn' ? 'zh' : $altCode;
            $altUrl = $canonicalUrl . $sep . 'lang=' . $altCode;
    ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($hreflangCode, ENT_QUOTES, 'UTF-8') ?>" href="<?= htmlspecialchars($altUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <link rel="alternate" hreflang="x-default" href="<?= htmlspecialchars($canonicalUrl, ENT_QUOTES, 'UTF-8') ?>">
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
</head>
<body class="<?= htmlspecialchars($theme->getThemeClasses()['body'], ENT_QUOTES, 'UTF-8') ?>">
    <nav class="navbar navbar-expand-xxl navbar-modern" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="navbar-brand-icon">
                    <i class="fas fa-hammer"></i>
                </div>
                <span><?= htmlspecialchars($config['site_name'] ?? 'LiteBans', ENT_QUOTES, 'UTF-8') ?></span>
            </a>

            <button class="navbar-toggler" type="button" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'home' ? 'active' : '' ?>" href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-home"></i>
                            <span><?= htmlspecialchars($lang->get('nav.home'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'bans' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('bans'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-ban"></i>
                            <span><?= htmlspecialchars($lang->get('nav.bans'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['bans_active']) && $GLOBALS['stats']['bans_active'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['bans_active'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'mutes' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('mutes'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-volume-mute"></i>
                            <span><?= htmlspecialchars($lang->get('nav.mutes'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['mutes_active']) && $GLOBALS['stats']['mutes_active'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['mutes_active'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'warnings' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('warnings'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span><?= htmlspecialchars($lang->get('nav.warnings'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['warnings']) && $GLOBALS['stats']['warnings'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['warnings'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'kicks' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('kicks'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-sign-out-alt"></i>
                            <span><?= htmlspecialchars($lang->get('nav.kicks'), ENT_QUOTES, 'UTF-8') ?></span>
                            <?php if (isset($GLOBALS['stats']['kicks']) && $GLOBALS['stats']['kicks'] > 0): ?>
                                <span class="badge"><?= htmlspecialchars((string)$GLOBALS['stats']['kicks'], ENT_QUOTES, 'UTF-8') ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <?php if ($config['show_menu_stats'] ?? true): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'stats' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('stats'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-chart-bar"></i>
                            <span><?= htmlspecialchars($lang->get('nav.statistics'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if ($config['show_menu_protest'] ?? true): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'protest' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('protest'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-gavel"></i>
                            <span><?= htmlspecialchars($lang->get('nav.protest'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                    <?php if (($config['show_menu_admin'] ?? true) && ($config['admin_enabled'] ?? false)): ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($currentPage ?? '') === 'admin' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('admin'), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-cog"></i>
                            <span><?= htmlspecialchars($lang->get('nav.admin'), ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>

                <div class="navbar-controls d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-navbar dropdown-toggle" type="button" id="langDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php
                            $currentLang = $lang->getCurrentLanguage();
                            $langNames = [
                                'ar' => 'AR',
                                'cs' => 'CS',
                                'de' => 'DE',
                                'gr' => 'GR',
                                'en' => 'EN',
                                'es' => 'ES',
                                'fr' => 'FR',
                                'hu' => 'HU',
                                'it' => 'IT',
                                'ja' => 'JA',
                                'pl' => 'PL',
                                'ro' => 'RO',
                                'ru' => 'RU',
                                'sk' => 'SK',
                                'sr' => 'SR',
                                'tr' => 'TR',
                                'cn' => 'CN',
                            ];
                            ?>
                            <i class="fas fa-globe"></i>
                            <span><?= htmlspecialchars($langNames[$currentLang] ?? 'EN', ENT_QUOTES, 'UTF-8') ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php foreach ($lang->getSupportedLanguages() as $langCode): ?>
                                <li>
                                    <a class="dropdown-item <?= $currentLang === $langCode ? 'active' : '' ?>"
                                       href="?lang=<?= htmlspecialchars($langCode, ENT_QUOTES, 'UTF-8') ?>"
                                       hreflang="<?= htmlspecialchars($langCode === 'cn' ? 'zh' : $langCode, ENT_QUOTES, 'UTF-8') ?>"
                                       title="<?= htmlspecialchars($lang->getLanguageName($langCode), ENT_QUOTES, 'UTF-8') ?>">
                                        <strong><?= htmlspecialchars($langNames[$langCode] ?? strtoupper($langCode), ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span class="text-muted small ms-1"><?= htmlspecialchars($lang->getLanguageName($langCode), ENT_QUOTES, 'UTF-8') ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="theme-toggle-wrapper">
                        <input type="checkbox" id="theme-toggle" class="theme-toggle-checkbox"
                               <?= $theme->getCurrentTheme() === 'dark' ? 'checked' : '' ?>>
                        <label for="theme-toggle" class="theme-toggle-label">
                            <i class="fas fa-sun"></i>
                            <i class="fas fa-moon"></i>
                            <span class="theme-toggle-ball"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="hero-gradient"></div>

    <main class="main-content">
        <div class="container">
