<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
include __DIR__ . '/../../templates/partials/seo-head.php';
$navStats = $GLOBALS['stats'] ?? [];
$nav = [
    ['home', '', 'nav.home', 0],
    ['bans', 'bans', 'nav.bans', $navStats['bans_active'] ?? 0],
    ['mutes', 'mutes', 'nav.mutes', $navStats['mutes_active'] ?? 0],
    ['warnings', 'warnings', 'nav.warnings', $navStats['warnings'] ?? 0],
    ['kicks', 'kicks', 'nav.kicks', $navStats['kicks'] ?? 0],
];
if ($config['show_menu_stats'] ?? true) {
    $nav[] = ['stats', 'stats', 'nav.statistics', 0];
}
if ($config['show_menu_protest'] ?? true) {
    $nav[] = ['protest', 'protest', 'nav.protest', 0];
}
if (($config['show_menu_admin'] ?? true) && ($config['admin_enabled'] ?? false)) {
    $nav[] = ['admin', 'admin', 'nav.admin', 0];
}
$homeLink = \core\SiteExtras::homeLink($config);
?>
    <link rel="stylesheet" href="<?= $h(asset('assets/css/icons.css')) ?>">
    <link rel="stylesheet" href="<?= $h(\core\TemplateManager::assetUrl('frost', 'frost.css')) ?>">
</head>
<body>
<div class="bar"></div>
<header class="head">
    <div class="wrap">
        <a class="brand" href="<?= $h(url()) ?>"><?= $h($config['site_name'] ?? 'LiteBans') ?></a>
        <button type="button" class="btn burger" id="burger" aria-label="Menu" aria-expanded="false"><i class="fas fa-bars"></i></button>
        <ul class="menu" id="menu">
            <?php foreach ($nav as [$page, $path, $key, $count]): ?>
                <li><a<?= ($currentPage ?? '') === $page ? ' class="on"' : '' ?> href="<?= $h(url($path)) ?>"><?= $h($lang->get($key)) ?><?php if ($count): ?><span class="count"><?= (int)$count ?></span><?php endif; ?></a></li>
            <?php endforeach; ?>
            <?php if ($homeLink): ?>
                <li><a href="<?= $h($homeLink['url']) ?>"<?= $homeLink['newtab'] ? ' target="_blank" rel="noopener"' : '' ?>><i class="fas fa-globe"></i><?= $h($homeLink['label']) ?></a></li>
            <?php endif; ?>
        </ul>
        <div class="tools">
            <button type="button" id="themeToggle" aria-label="Dark / light" title="Dark / light"><i class="fas fa-sun sun"></i><i class="fas fa-moon moon"></i></button>
            <details class="lang">
                <summary><i class="fas fa-globe"></i><?= $h(strtoupper($lang->getCurrentLanguage())) ?></summary>
                <ul>
                    <?php foreach ($lang->getSupportedLanguages() as $code): ?>
                        <li><a<?= $lang->getCurrentLanguage() === $code ? ' class="on"' : '' ?> href="?<?= $h(http_build_query(array_merge($_GET, ['lang' => $code]))) ?>" hreflang="<?= $h($code === 'cn' ? 'zh' : $code) ?>"><?= $h($lang->getLanguageName($code)) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </details>
        </div>
    </div>
</header>
<main class="wrap">
