<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
include __DIR__ . '/../../templates/partials/seo-head.php';
$tplCss = fn(string $f): string => $h(\core\TemplateManager::assetUrl('bulma', $f));
$navStats = $GLOBALS['stats'] ?? [];
$nav = [
    ['home', '', 'fa-home', 'nav.home', null],
    ['bans', 'bans', 'fa-ban', 'nav.bans', $navStats['bans_active'] ?? 0],
    ['mutes', 'mutes', 'fa-volume-mute', 'nav.mutes', $navStats['mutes_active'] ?? 0],
    ['warnings', 'warnings', 'fa-exclamation-triangle', 'nav.warnings', $navStats['warnings'] ?? 0],
    ['kicks', 'kicks', 'fa-sign-out-alt', 'nav.kicks', $navStats['kicks'] ?? 0],
];
if ($config['show_menu_stats'] ?? true) {
    $nav[] = ['stats', 'stats', 'fa-chart-bar', 'nav.statistics', null];
}
if ($config['show_menu_protest'] ?? true) {
    $nav[] = ['protest', 'protest', 'fa-gavel', 'nav.protest', null];
}
if (($config['show_menu_admin'] ?? true) && ($config['admin_enabled'] ?? false)) {
    $nav[] = ['admin', 'admin', 'fa-cog', 'nav.admin', null];
}
$homeLink = \core\SiteExtras::homeLink($config);
?>
    <link rel="stylesheet" href="<?= $h(asset('assets/vendor/bulma/bulma.min.css')) ?>">
    <link rel="stylesheet" href="<?= $h(asset('assets/css/icons.css')) ?>">
    <link rel="stylesheet" href="<?= $tplCss('template.css') ?>">
</head>
<body>
<nav class="navbar is-fixed-top has-shadow" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item has-text-weight-bold" href="<?= $h(url()) ?>">
            <i class="fas fa-hammer has-text-danger mr-2"></i><?= $h($config['site_name'] ?? 'LiteBans') ?>
        </a>
        <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="mainMenu">
            <span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span><span aria-hidden="true"></span>
        </a>
    </div>
    <div id="mainMenu" class="navbar-menu">
        <div class="navbar-start">
            <?php foreach ($nav as [$page, $path, $icon, $key, $count]): ?>
                <a class="navbar-item<?= ($currentPage ?? '') === $page ? ' is-active' : '' ?>" href="<?= $h(url($path)) ?>">
                    <i class="fas <?= $icon ?> mr-2"></i><?= $h($lang->get($key)) ?>
                    <?php if ($count): ?><span class="tag is-danger is-rounded ml-2"><?= $h((string)$count) ?></span><?php endif; ?>
                </a>
            <?php endforeach; ?>
            <?php if ($homeLink): ?>
                <a class="navbar-item" href="<?= $h($homeLink['url']) ?>"<?= $homeLink['newtab'] ? ' target="_blank" rel="noopener"' : '' ?>>
                    <i class="fas fa-globe mr-2"></i><?= $h($homeLink['label']) ?>
                </a>
            <?php endif; ?>
        </div>
        <div class="navbar-end">
            <a class="navbar-item" id="themeToggle" role="button" aria-label="Dark / light" title="Dark / light">
                <i class="fas fa-sun th-sun"></i><i class="fas fa-moon th-moon"></i>
            </a>
            <div class="navbar-item has-dropdown is-hoverable">
                <a class="navbar-link"><i class="fas fa-globe mr-2"></i><?= $h(strtoupper($lang->getCurrentLanguage())) ?></a>
                <div class="navbar-dropdown is-right">
                    <?php foreach ($lang->getSupportedLanguages() as $code): ?>
                        <a class="navbar-item<?= $lang->getCurrentLanguage() === $code ? ' is-active' : '' ?>"
                           href="?<?= $h(http_build_query(array_merge($_GET, ['lang' => $code]))) ?>"
                           hreflang="<?= $h($code === 'cn' ? 'zh' : $code) ?>"><?= $h($lang->getLanguageName($code)) ?></a>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</nav>
<main class="section">
<div class="container">
