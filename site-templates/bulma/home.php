<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$tiles = [
    ['bans', 'fa-ban', 'is-danger', $stats['bans_active'] ?? 0, $stats['bans'] ?? 0, 'nav.bans'],
    ['mutes', 'fa-volume-mute', 'is-warning', $stats['mutes_active'] ?? 0, $stats['mutes'] ?? 0, 'nav.mutes'],
    ['warnings', 'fa-exclamation-triangle', 'is-info', $stats['warnings'] ?? 0, null, 'nav.warnings'],
    ['kicks', 'fa-sign-out-alt', 'is-dark', $stats['kicks'] ?? 0, null, 'nav.kicks'],
];
$lists = [['ban', $recentBans, 'nav.bans', 'is-danger', 'fa-ban'], ['mute', $recentMutes, 'nav.mutes', 'is-warning', 'fa-volume-mute']];
?>
<section class="home-hero mb-6">
    <span class="home-hero-icon" aria-hidden="true"><i class="fas fa-hammer"></i></span>
    <h1 class="title is-1 mb-3"><?= $h($config['site_name'] ?? '') ?></h1>
    <p class="subtitle is-5 mb-5"><?= $h($config['site_description'] ?? '') ?></p>
    <div class="buttons">
        <a class="button is-primary is-medium" href="<?= $h(url('search')) ?>"><span class="icon"><i class="fas fa-search"></i></span><span><?= $h($lang->get('search.title')) ?></span></a>
        <a class="button is-medium" href="<?= $h(url('bans')) ?>"><span class="icon"><i class="fas fa-ban"></i></span><span><?= $h($lang->get('nav.bans')) ?></span></a>
    </div>
</section>

<div class="columns is-multiline mb-5">
    <?php foreach ($tiles as [$page, $icon, $color, $active, $total, $key]): ?>
        <div class="column is-half-tablet is-one-quarter-desktop">
            <a class="stat-tile notification <?= $color ?>" href="<?= $h(url($page)) ?>">
                <i class="fas <?= $icon ?> stat-tile-icon" aria-hidden="true"></i>
                <p class="heading"><?= $h($lang->get($key)) ?></p>
                <p class="title is-1"><?= number_format((int)$active) ?></p>
                <p class="is-size-7"><?= $total !== null ? $h($lang->get('stats.total_of')) . ' ' . number_format((int)$total) : '&nbsp;' ?></p>
            </a>
        </div>
    <?php endforeach; ?>
</div>

<div class="columns">
    <?php foreach ($lists as [$singular, $rows, $key, $color, $icon]): ?>
        <div class="column">
            <div class="card recent-card">
                <header class="card-header">
                    <p class="card-header-title"><i class="fas <?= $icon ?> mr-2"></i><?= $h($lang->get($key)) ?></p>
                    <span class="card-header-icon"><span class="tag <?= $color ?> is-rounded"><?= count($rows) ?></span></span>
                </header>
                <div class="card-content p-0">
                    <?php if (empty($rows)): ?>
                        <p class="p-4 has-text-grey"><?= $h($lang->get('punishments.no_data')) ?></p>
                    <?php else: foreach ($rows as $r): $name = $r['player_name'] ?? $r['name'] ?? 'Unknown'; ?>
                        <a class="recent-row media is-align-items-center" href="<?= $h(url('detail?type=' . $singular . '&id=' . $r['id'])) ?>">
                            <figure class="media-left mb-0"><p class="image is-48x48"><img class="avatar" src="<?= $h($controller->avatarUrl($r['uuid'] ?? '', $name)) ?>" alt="" width="48" height="48" loading="lazy"></p></figure>
                            <div class="media-content">
                                <strong><?= $h($name) ?></strong>
                                <p class="is-size-7 has-text-grey"><?= $h(mb_strimwidth((string)($r['reason'] ?? ''), 0, 60, '...')) ?></p>
                            </div>
                            <div class="media-right is-size-7 has-text-grey has-text-right"><?= $h($controller->formatDate((int)$r['time'])) ?></div>
                        </a>
                    <?php endforeach; endif; ?>
                </div>
                <footer class="card-footer"><a class="card-footer-item" href="<?= $h(url($singular . 's')) ?>"><?= $h($lang->get('table.view')) ?><i class="fas fa-chevron-right ml-2"></i></a></footer>
            </div>
        </div>
    <?php endforeach; ?>
</div>
