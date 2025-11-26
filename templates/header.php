<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['site_lang'] ?? $lang->getCurrentLanguage(), ENT_QUOTES, 'UTF-8') ?>">
<head>
    <meta charset="<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="<?= htmlspecialchars($config['site_viewport'] ?? 'width=device-width, initial-scale=1.0', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="csrf-token" content="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="base-path" content="<?= htmlspecialchars($config['base_path'], ENT_QUOTES, 'UTF-8') ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="<?= htmlspecialchars($config['site_robots'] ?? 'index, follow', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- SEO Meta Tags -->
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
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= htmlspecialchars($config['site_url'] . $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?= htmlspecialchars($config['site_favicon'] ?? asset('favicon.ico'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="apple-touch-icon" href="<?= htmlspecialchars($config['site_apple_icon'] ?? asset('apple-touch-icon.png'), ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:url" content="<?= htmlspecialchars($config['site_url'] . $_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8') ?>">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['site_og_image'])): ?>
    <meta property="og:image" content="<?= htmlspecialchars($config['site_og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="<?= htmlspecialchars(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars(isset($description) ? $description : $config['site_description'], ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['site_twitter_site'])): ?>
    <meta name="twitter:site" content="<?= htmlspecialchars($config['site_twitter_site'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- Preconnect to external resources -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS with cache busting -->
    <link href="<?= htmlspecialchars(asset('assets/css/main.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="<?= htmlspecialchars($config['site_theme_color'] ?? '#ef4444', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- Additional SEO Meta Tags -->
    <?php if (isset($config['site_keywords']) && !empty($config['site_keywords'])): ?>
    <meta name="keywords" content="<?= htmlspecialchars($config['site_keywords'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <meta name="author" content="<?= htmlspecialchars($config['seo_organization_name'] ?? $config['site_name'], ENT_QUOTES, 'UTF-8') ?>">
    <meta name="rating" content="general">
    <meta name="revisit-after" content="7 days">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- Enhanced SEO Meta Tags -->
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
    
    <!-- Geo Meta Tags (Optional) -->
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
    
    <!-- AI Search Engine Tags -->
    <meta name="bingbot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">
    <meta name="googlebot-news" content="index, follow">
    <?php if (isset($config['seo_ai_training']) && $config['seo_ai_training'] === false): ?>
    <meta name="robots" content="noai, noimageai">
    <?php endif; ?>
    
    <!-- Open Graph Enhanced -->
    <meta property="og:locale" content="<?= htmlspecialchars($config['seo_locale'] ?? 'en_US', ENT_QUOTES, 'UTF-8') ?>">
    <?php if (isset($config['seo_facebook_app_id'])): ?>
    <meta property="fb:app_id" content="<?= htmlspecialchars($config['seo_facebook_app_id'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Enhanced -->
    <meta name="twitter:card" content="summary_large_image">
    <?php if (isset($config['seo_twitter_creator'])): ?>
    <meta name="twitter:creator" content="<?= htmlspecialchars($config['seo_twitter_creator'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    <?php if (isset($config['site_og_image'])): ?>
    <meta name="twitter:image" content="<?= htmlspecialchars($config['site_og_image'], ENT_QUOTES, 'UTF-8') ?>">
    <?php endif; ?>
    
    <!-- DNS Prefetch for Performance -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    <link rel="dns-prefetch" href="//cdnjs.cloudflare.com">
    
    <!-- Alternate Languages (if multilingual) -->
    <?php if (isset($config['seo_alternate_languages']) && is_array($config['seo_alternate_languages'])): ?>
    <?php foreach ($config['seo_alternate_languages'] as $langCode => $langUrl): ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($langCode, ENT_QUOTES, 'UTF-8') ?>" href="<?= htmlspecialchars($langUrl, ENT_QUOTES, 'UTF-8') ?>">
    <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- JSON-LD for SEO -->
    <!-- Schema.org Structured Data (JSON-LD) -->
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
    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-modern" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="navbar-brand-icon">
                    <i class="fas fa-hammer"></i>
                </div>
                <span><?= htmlspecialchars($config['site_name'] ?? 'LiteBans', ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            
            <!-- Mobile Menu Toggle -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Navbar Content -->
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
                    <!-- Language Switcher Dropdown -->
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
'zh' => 'ZH'                            ];
                            ?>
                            <i class="fas fa-globe"></i>
                            <span><?= $langNames[$currentLang] ?? 'EN' ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <?php foreach ($lang->getSupportedLanguages() as $langCode): ?>
                                <li>
                                    <a class="dropdown-item <?= $currentLang === $langCode ? 'active' : '' ?>" 
                                       href="?lang=<?= htmlspecialchars($langCode, ENT_QUOTES, 'UTF-8') ?>">
                                        <?= $langNames[$langCode] ?? strtoupper($langCode) ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <!-- Theme Toggle Switch -->
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
    
    <!-- Hero Gradient Background -->
    <div class="hero-gradient"></div>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Page content will be inserted here -->
