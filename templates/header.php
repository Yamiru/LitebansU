<!DOCTYPE html>
<html lang="<?= htmlspecialchars($config['site_lang'] ?? $lang->getCurrentLanguage(), ENT_QUOTES, 'UTF-8') ?>">
<!--
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
-->
<head>
    <meta charset="<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="viewport" content="<?= htmlspecialchars($config['site_viewport'] ?? 'width=device-width, initial-scale=1.0', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="csrf-token" content="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
    <meta name="base-path" content="<?= htmlspecialchars($config['base_path'], ENT_QUOTES, 'UTF-8') ?>">
    <meta http-equiv="Content-Type" content="text/html; charset=<?= htmlspecialchars($config['site_charset'] ?? 'UTF-8', ENT_QUOTES, 'UTF-8') ?>">
    <meta name="robots" content="<?= htmlspecialchars($config['site_robots'] ?? 'index, follow', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- SEO Meta Tags -->
    <title><?= isset($title) ? htmlspecialchars($config['site_title_template'] ? str_replace(['{page}', '{site}'], [$title, $config['site_name']], $config['site_title_template']) : $title . ' - ' . $config['site_name'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?></title>
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
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Custom CSS with cache busting -->
    <link href="<?= htmlspecialchars(asset('assets/css/main.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="<?= htmlspecialchars($config['site_theme_color'] ?? '#6366f1', ENT_QUOTES, 'UTF-8') ?>">
    
    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "WebSite",
        "name": "<?= htmlspecialchars($config['site_name'], ENT_QUOTES, 'UTF-8') ?>",
        "description": "<?= htmlspecialchars($config['site_description'], ENT_QUOTES, 'UTF-8') ?>",
        "url": "<?= htmlspecialchars($config['site_url'], ENT_QUOTES, 'UTF-8') ?>"
    }
    </script>
    
    <!-- Debug Info (only in debug mode) -->
    <?php if ($config['debug'] ?? false): ?>
    <script>
        console.log('SEO Debug:', {
            title: '<?= addslashes(isset($title) ? $title . ' - ' . $config['site_name'] : $config['site_name']) ?>',
            description: '<?= addslashes(isset($description) ? $description : $config['site_description']) ?>',
            canonical: '<?= addslashes($config['site_url'] . $_SERVER['REQUEST_URI']) ?>',
            currentTheme: '<?= $theme->getCurrentTheme() ?>',
            bodyClass: '<?= $theme->getThemeClasses()['body'] ?>',
            basePath: '<?= BASE_PATH ?>'
        });
    </script>
    <?php endif; ?>
</head>
<body class="<?= htmlspecialchars($theme->getThemeClasses()['body'], ENT_QUOTES, 'UTF-8') ?>">
    <!-- Modern Navbar -->
    <nav class="navbar-modern" id="mainNavbar">
        <div class="container">
            <a class="navbar-brand" href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                <div class="navbar-brand-icon">
                    <i class="fas fa-hammer"></i>
                </div>
                <span><?= htmlspecialchars($config['site_name'] ?? 'LiteBans', ENT_QUOTES, 'UTF-8') ?></span>
            </a>
            
            <ul class="navbar-nav" id="navbarNav">
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
                        <?php if (isset($stats['bans_active']) && $stats['bans_active'] > 0): ?>
                            <span class="badge"><?= htmlspecialchars((string)$stats['bans_active'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'mutes' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('mutes'), ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fas fa-volume-mute"></i>
                        <span><?= htmlspecialchars($lang->get('nav.mutes'), ENT_QUOTES, 'UTF-8') ?></span>
                        <?php if (isset($stats['mutes_active']) && $stats['mutes_active'] > 0): ?>
                            <span class="badge"><?= htmlspecialchars((string)$stats['mutes_active'], ENT_QUOTES, 'UTF-8') ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'warnings' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('warnings'), ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fas fa-exclamation-triangle"></i>
                        <span><?= htmlspecialchars($lang->get('nav.warnings'), ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($currentPage ?? '') === 'kicks' ? 'active' : '' ?>" href="<?= htmlspecialchars(url('kicks'), ENT_QUOTES, 'UTF-8') ?>">
                        <i class="fas fa-sign-out-alt"></i>
                        <span><?= htmlspecialchars($lang->get('nav.kicks'), ENT_QUOTES, 'UTF-8') ?></span>
                    </a>
                </li>
            </ul>
            
            <div class="navbar-controls">
                <!-- Language Switcher -->
                <div class="lang-select">
                    <select id="lang-switcher" class="form-select form-select-sm" aria-label="<?= htmlspecialchars($lang->get('nav.language'), ENT_QUOTES, 'UTF-8') ?>">
                        <?php foreach ($lang->getSupportedLanguages() as $langCode): ?>
                            <option value="<?= htmlspecialchars($langCode, ENT_QUOTES, 'UTF-8') ?>" 
                                    <?= $lang->getCurrentLanguage() === $langCode ? 'selected' : '' ?>>
                                <?= htmlspecialchars(strtoupper($langCode), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Theme Switcher -->
                <div class="theme-select">
                    <select id="theme-switcher" class="form-select form-select-sm" aria-label="<?= htmlspecialchars($lang->get('nav.theme'), ENT_QUOTES, 'UTF-8') ?>">
                        <?php foreach ($theme->getAvailableThemes() as $themeOption): ?>
                            <option value="<?= htmlspecialchars($themeOption, ENT_QUOTES, 'UTF-8') ?>" 
                                    <?= $theme->getCurrentTheme() === $themeOption ? 'selected' : '' ?>>
                                <?= htmlspecialchars(ucfirst($themeOption), ENT_QUOTES, 'UTF-8') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <!-- Mobile Menu Toggle -->
                <button class="mobile-menu-toggle" type="button" aria-label="Toggle navigation" aria-expanded="false">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </nav>
    
    <!-- Hero Gradient Background -->
    <div class="hero-gradient"></div>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="container">
            <!-- Breadcrumb (optional) -->
            <?php if (($currentPage ?? '') !== 'home' && isset($title)): ?>
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a href="<?= htmlspecialchars(url(), ENT_QUOTES, 'UTF-8') ?>">
                            <i class="fas fa-home"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item active" aria-current="page">
                        <?= htmlspecialchars($title, ENT_QUOTES, 'UTF-8') ?>
                    </li>
                </ol>
            </nav>
            <?php endif; ?>
            
            <!-- Page content will be inserted here -->