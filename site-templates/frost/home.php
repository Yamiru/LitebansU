<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$tiles = [
    ['bans', 'tone-danger', $stats['bans_active'] ?? 0, $stats['bans'] ?? 0, 'nav.bans'],
    ['mutes', 'tone-warn', $stats['mutes_active'] ?? 0, $stats['mutes'] ?? 0, 'nav.mutes'],
    ['warnings', 'tone-info', $stats['warnings'] ?? 0, null, 'nav.warnings'],
    ['kicks', 'tone-mute', $stats['kicks'] ?? 0, null, 'nav.kicks'],
];
$lists = [['ban', $recentBans, 'nav.bans', 'fa-ban'], ['mute', $recentMutes, 'nav.mutes', 'fa-volume-mute']];
?>
<section class="hero">
    <h1><?= $h($config['site_name'] ?? '') ?></h1>
    <p><?= $h($config['site_description'] ?? '') ?></p>
    <div class="btns">
        <a class="btn primary" href="<?= $h(url('search')) ?>"><i class="fas fa-search"></i><?= $h($lang->get('search.title')) ?></a>
        <a class="btn" href="<?= $h(url('bans')) ?>"><i class="fas fa-ban"></i><?= $h($lang->get('nav.bans')) ?></a>
    </div>
</section>

<div class="tiles">
    <?php foreach ($tiles as [$page, $tone, $active, $total, $key]): ?>
        <a class="tile <?= $tone ?>" href="<?= $h(url($page)) ?>">
            <span class="label"><?= $h($lang->get($key)) ?></span>
            <span class="num"><?= number_format((int)$active) ?></span>
            <span class="of"><?= $total !== null ? $h($lang->get('stats.total_of')) . ' ' . number_format((int)$total) : '' ?></span>
        </a>
    <?php endforeach; ?>
</div>

<div class="cols">
    <?php foreach ($lists as [$singular, $rows, $key, $icon]): ?>
        <section class="panel">
            <h2><span><i class="fas <?= $icon ?>"></i><?= $h($lang->get($key)) ?></span><span class="tag"><?= count($rows) ?></span></h2>
            <?php if (empty($rows)): ?>
                <p class="body muted" style="margin:0"><?= $h($lang->get('punishments.no_data')) ?></p>
            <?php else: foreach ($rows as $r): $name = $r['player_name'] ?? $r['name'] ?? 'Unknown'; ?>
                <a class="row" href="<?= $h(url('detail?type=' . $singular . '&id=' . $r['id'])) ?>">
                    <img class="avatar" src="<?= $h($controller->avatarUrl($r['uuid'] ?? '', $name)) ?>" alt="" width="40" height="40" loading="lazy">
                    <span class="grow"><span class="name"><?= $h($name) ?></span><span class="sub"><?= $h(mb_strimwidth((string)($r['reason'] ?? ''), 0, 70, '...')) ?></span></span>
                    <span class="when"><?= $h($controller->formatDate((int)$r['time'])) ?></span>
                </a>
            <?php endforeach; endif; ?>
            <a class="more" href="<?= $h(url($singular . 's')) ?>"><?= $h($lang->get('table.view')) ?> <i class="fas fa-chevron-right"></i></a>
        </section>
    <?php endforeach; ?>
</div>
