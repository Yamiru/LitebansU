<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$tiles = [
    ['fa-ban', 'is-danger', $stats['bans_active'] ?? 0, $stats['bans'] ?? 0, 'nav.bans'],
    ['fa-volume-mute', 'is-warning', $stats['mutes_active'] ?? 0, $stats['mutes'] ?? 0, 'nav.mutes'],
    ['fa-exclamation-triangle', 'is-info', $stats['warnings'] ?? 0, null, 'nav.warnings'],
    ['fa-sign-out-alt', 'is-dark', $stats['kicks'] ?? 0, null, 'nav.kicks'],
];
$periods = ['last_24h' => 'Last 24 Hours', 'last_7d' => 'Last 7 Days', 'last_30d' => 'Last 30 Days'];
$kinds = ['bans' => ['nav.bans', 'is-danger'], 'mutes' => ['nav.mutes', 'is-warning'], 'warnings' => ['nav.warnings', 'is-info'], 'kicks' => ['nav.kicks', 'is-dark']];
?>
<h1 class="title is-2"><i class="fas fa-chart-bar has-text-primary mr-2"></i><?= $h($title ?? $lang->get('nav.statistics')) ?></h1>

<div class="columns is-multiline mb-4">
    <?php foreach ($tiles as [$icon, $color, $active, $total, $key]): ?>
        <div class="column is-half-tablet is-one-quarter-desktop">
            <div class="stat-tile notification <?= $color ?>">
                <i class="fas <?= $icon ?> stat-tile-icon" aria-hidden="true"></i>
                <p class="heading"><?= $h($lang->get($key)) ?></p>
                <p class="title is-1"><?= number_format((int)$active) ?></p>
                <p class="is-size-7"><?= $total !== null ? $h($lang->get('stats.total_of')) . ' ' . number_format((int)$total) : '&nbsp;' ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (!empty($stats['recent_activity'])): ?>
<div class="box">
    <h2 class="title is-5"><i class="fas fa-clock mr-2"></i>Recent activity</h2>
    <div class="columns">
        <?php foreach ($periods as $p => $label): ?>
            <div class="column">
                <p class="heading"><?= $h($label) ?></p>
                <div class="tags">
                    <?php foreach ($kinds as $k => [$kl, $kc]): ?>
                        <span class="tag <?= $kc ?>"><?= $h($lang->get($kl)) ?>: <?= (int)($stats['recent_activity'][$p][$k] ?? 0) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<div class="columns">
    <?php if (!empty($stats['top_banned_players'])): ?>
    <div class="column">
        <div class="card">
            <header class="card-header"><p class="card-header-title"><i class="fas fa-user-slash mr-2"></i>Top banned players</p></header>
            <div class="card-content p-0">
                <?php foreach ($stats['top_banned_players'] as $pl): $n = $pl['player_name'] ?? 'Unknown'; ?>
                    <div class="media p-3 m-0">
                        <figure class="media-left mb-0"><p class="image is-32x32"><img class="avatar" src="<?= $h($controller->avatarUrl($pl['uuid'] ?? '', $n)) ?>" alt="" width="32" height="32" loading="lazy"></p></figure>
                        <div class="media-content">
                            <strong><?= $h($n) ?></strong>
                            <p class="is-size-7 has-text-grey">Last: <?= $h($controller->formatDate((int)$pl['last_ban_time'])) ?></p>
                        </div>
                        <div class="media-right">
                            <span class="tag is-danger"><?= (int)$pl['ban_count'] ?> bans</span>
                            <?php if ((int)$pl['active_bans'] > 0): ?><span class="tag is-warning"><?= (int)$pl['active_bans'] ?> active</span><?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($stats['most_active_staff'])): ?>
    <div class="column">
        <div class="card">
            <header class="card-header"><p class="card-header-title"><i class="fas fa-user-shield mr-2"></i>Most active staff</p></header>
            <div class="card-content p-0">
                <?php foreach ($stats['most_active_staff'] as $st): ?>
                    <div class="media p-3 m-0">
                        <div class="media-content">
                            <strong><?= $h($st['staff_name']) ?></strong>
                            <p class="is-size-7">
                                <?php foreach (['bans' => ['B', 'has-text-danger'], 'mutes' => ['M', 'has-text-warning'], 'warnings' => ['W', 'has-text-info'], 'kicks' => ['K', 'has-text-grey']] as $k => [$l, $c]): ?>
                                    <?php if (($st[$k] ?? 0) > 0): ?><span class="<?= $c ?> mr-2"><?= (int)$st[$k] ?><?= $l ?></span><?php endif; ?>
                                <?php endforeach; ?>
                            </p>
                        </div>
                        <div class="media-right"><span class="tag is-primary"><?= (int)$st['total_punishments'] ?> total</span></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($stats['top_ban_reasons'])): ?>
<div class="card mt-5">
    <header class="card-header"><p class="card-header-title"><i class="fas fa-list mr-2"></i>Top ban reasons</p></header>
    <div class="card-content">
        <?php foreach ($stats['top_ban_reasons'] as $r): ?>
            <div class="is-flex is-justify-content-space-between py-1">
                <span><?= $h(mb_strimwidth((string)$r['reason'], 0, 50, '...')) ?></span>
                <span class="tag"><?= (int)$r['count'] ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($stats['daily_activity'])): $max = max(1, max(array_column($stats['daily_activity'], 'count'))); ?>
<div class="card mt-5">
    <header class="card-header"><p class="card-header-title"><i class="fas fa-calendar mr-2"></i>Activity by weekday</p></header>
    <div class="card-content">
        <div class="day-chart">
            <?php foreach ($stats['daily_activity'] as $day): ?>
                <div class="day-col">
                    <span class="day-count"><?= (int)$day['count'] ?></span>
                    <div class="day-bar" style="height:<?= min(100, ($day['count'] / $max) * 100) ?>%"></div>
                    <span class="day-label"><?= $h(substr($day['day_name'], 0, 3)) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>
