<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$tiles = [
    ['tone-danger', $stats['bans_active'] ?? 0, $stats['bans'] ?? 0, 'nav.bans'],
    ['tone-warn', $stats['mutes_active'] ?? 0, $stats['mutes'] ?? 0, 'nav.mutes'],
    ['tone-info', $stats['warnings'] ?? 0, null, 'nav.warnings'],
    ['tone-mute', $stats['kicks'] ?? 0, null, 'nav.kicks'],
];
$periods = ['last_24h' => 'Last 24 Hours', 'last_7d' => 'Last 7 Days', 'last_30d' => 'Last 30 Days'];
$kinds = ['bans' => ['nav.bans', 'danger'], 'mutes' => ['nav.mutes', 'warn'], 'warnings' => ['nav.warnings', 'info'], 'kicks' => ['nav.kicks', '']];
?>
<h1><i class="fas fa-chart-bar"></i><?= $h($title ?? $lang->get('nav.statistics')) ?></h1>

<div class="tiles">
    <?php foreach ($tiles as [$tone, $active, $total, $key]): ?>
        <div class="tile <?= $tone ?>">
            <span class="label"><?= $h($lang->get($key)) ?></span>
            <span class="num"><?= number_format((int)$active) ?></span>
            <span class="of"><?= $total !== null ? $h($lang->get('stats.total_of')) . ' ' . number_format((int)$total) : '' ?></span>
        </div>
    <?php endforeach; ?>
</div>

<?php if (!empty($stats['recent_activity'])): ?>
<section class="panel">
    <h2><span><i class="fas fa-clock"></i>Recent activity</span></h2>
    <div class="body cols">
        <?php foreach ($periods as $p => $label): ?>
            <div>
                <p class="small muted"><?= $h($label) ?></p>
                <?php foreach ($kinds as $k => [$kl, $kc]): ?>
                    <span class="tag <?= $kc ?>"><?= $h($lang->get($kl)) ?>: <?= (int)($stats['recent_activity'][$p][$k] ?? 0) ?></span>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<div class="cols">
    <?php if (!empty($stats['top_banned_players'])): ?>
    <section class="panel">
        <h2><span><i class="fas fa-user-slash"></i>Top banned players</span></h2>
        <?php foreach ($stats['top_banned_players'] as $pl): $n = $pl['player_name'] ?? 'Unknown'; ?>
            <div class="row">
                <img class="avatar" src="<?= $h($controller->avatarUrl($pl['uuid'] ?? '', $n)) ?>" alt="" width="40" height="40" loading="lazy">
                <span class="grow"><span class="name"><?= $h($n) ?></span><span class="sub">Last: <?= $h($controller->formatDate((int)$pl['last_ban_time'])) ?></span></span>
                <span><span class="tag danger"><?= (int)$pl['ban_count'] ?> bans</span> <?php if ((int)$pl['active_bans'] > 0): ?><span class="tag warn"><?= (int)$pl['active_bans'] ?> active</span><?php endif; ?></span>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>

    <?php if (!empty($stats['most_active_staff'])): ?>
    <section class="panel">
        <h2><span><i class="fas fa-user-shield"></i>Most active staff</span></h2>
        <?php foreach ($stats['most_active_staff'] as $st): ?>
            <div class="row">
                <span class="grow">
                    <span class="name"><?= $h($st['staff_name']) ?></span>
                    <span class="sub">
                        <?php foreach (['bans' => 'B', 'mutes' => 'M', 'warnings' => 'W', 'kicks' => 'K'] as $k => $l): ?><?= ($st[$k] ?? 0) > 0 ? (int)$st[$k] . $l . ' ' : '' ?><?php endforeach; ?>
                    </span>
                </span>
                <span class="tag info"><?= (int)$st['total_punishments'] ?> total</span>
            </div>
        <?php endforeach; ?>
    </section>
    <?php endif; ?>
</div>

<?php if (!empty($stats['top_ban_reasons'])): ?>
<section class="panel">
    <h2><span><i class="fas fa-list"></i>Top ban reasons</span></h2>
    <?php foreach ($stats['top_ban_reasons'] as $r): ?>
        <div class="row"><span class="grow"><?= $h(mb_strimwidth((string)$r['reason'], 0, 50, '...')) ?></span><span class="tag"><?= (int)$r['count'] ?></span></div>
    <?php endforeach; ?>
</section>
<?php endif; ?>

<?php if (!empty($stats['daily_activity'])): $max = max(1, max(array_column($stats['daily_activity'], 'count'))); ?>
<section class="panel">
    <h2><span><i class="fas fa-calendar"></i>Activity by weekday</span></h2>
    <div class="body chart">
        <?php foreach ($stats['daily_activity'] as $day): ?>
            <div><span><?= (int)$day['count'] ?></span><i style="height:<?= min(100, ($day['count'] / $max) * 100) ?>%"></i><span><?= $h(substr($day['day_name'], 0, 3)) ?></span></div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
