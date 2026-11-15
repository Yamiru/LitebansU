<?php include __DIR__ . '/partials/seo-head.php'; ?>

    <link href="<?= htmlspecialchars(asset('assets/vendor/bootstrap/bootstrap.min.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    <link href="<?= htmlspecialchars(asset('assets/fonts/fonts.css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
    <link rel="preload" href="<?= htmlspecialchars(asset('assets/fonts/public-latin.woff2'), ENT_QUOTES, 'UTF-8') ?>" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="<?= htmlspecialchars(asset('assets/fonts/bricolage-latin.woff2'), ENT_QUOTES, 'UTF-8') ?>" as="font" type="font/woff2" crossorigin>

    <link href="<?= htmlspecialchars(bundle('css'), ENT_QUOTES, 'UTF-8') ?>" rel="stylesheet">
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
            .navbar-toggler{display:block}
            .navbar-collapse{position:absolute;top:100%;left:0;right:0;z-index:1030;background:var(--bg-primary);border-top:1px solid var(--border-color);box-shadow:var(--shadow-lg);padding:var(--space-md);display:none;flex-direction:column;gap:var(--space-md);max-height:calc(100vh - 80px);max-height:calc(100dvh - 80px);overflow-y:auto;-webkit-overflow-scrolling:touch;overscroll-behavior:contain}
            .navbar-modern{padding-left:env(safe-area-inset-left);padding-right:env(safe-area-inset-right)}
            .nav-link,.navbar-toggler,.btn-navbar{min-height:44px;touch-action:manipulation;-webkit-tap-highlight-color:transparent}
            .navbar-collapse.show{display:flex}
            .navbar-nav{flex-direction:column;width:100%;gap:var(--space-xs)}
            .nav-item{width:100%}
            .nav-link{width:100%;padding:var(--space-md);padding-right:var(--space-xl);justify-content:space-between}
            .nav-link .badge{position:static;transform:none;margin-left:auto}
            .navbar-modern .container{flex-wrap:nowrap}
            .navbar-brand{order:0;flex:1 1 auto;min-width:0}
            .navbar-brand span{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
            .navbar-controls{order:1;margin-left:var(--space-sm);flex-wrap:nowrap}
            .navbar-toggler{order:2;margin-left:var(--space-sm)}
            .navbar-collapse{order:3}
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
                    <?php if ($homeLink = \core\SiteExtras::homeLink($config)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= htmlspecialchars($homeLink['url'], ENT_QUOTES, 'UTF-8') ?>"<?= $homeLink['newtab'] ? ' target="_blank" rel="noopener"' : '' ?>>
                            <i class="fas fa-globe"></i>
                            <span><?= htmlspecialchars($homeLink['label'], ENT_QUOTES, 'UTF-8') ?></span>
                        </a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>

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
                                   href="?<?= htmlspecialchars(http_build_query(array_merge($_GET, ['lang' => $langCode])), ENT_QUOTES, 'UTF-8') ?>"
                                   hreflang="<?= htmlspecialchars($langCode === 'cn' ? 'zh' : $langCode, ENT_QUOTES, 'UTF-8') ?>"
                                   title="<?= htmlspecialchars($lang->getLanguageName($langCode), ENT_QUOTES, 'UTF-8') ?>">
                                    <?= htmlspecialchars($lang->getLanguageName($langCode), ENT_QUOTES, 'UTF-8') ?>
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
    </nav>

    <div class="hero-gradient" aria-hidden="true"><i></i><i></i><i></i><i></i><i></i><i></i></div>

    <main class="main-content">
        <div class="container">
