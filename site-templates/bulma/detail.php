<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$p = $punishment;
$icon = match($type) {
    'ban' => 'fa-ban has-text-danger', 'mute' => 'fa-volume-mute has-text-warning',
    'warning' => 'fa-exclamation-triangle has-text-info', 'kick' => 'fa-sign-out-alt has-text-grey', default => 'fa-list',
};
$row = function (string $icon, string $label, string $html): string {
    return '<div class="column is-half"><p class="heading"><i class="fas ' . $icon . ' mr-1"></i>' . $label . '</p><div>' . $html . '</div></div>';
};
$permanent = $lang->get('punishment.permanent');
?>
<nav class="breadcrumb" aria-label="breadcrumbs">
    <ul>
        <li><a href="<?= $h(url()) ?>"><i class="fas fa-home mr-1"></i><?= $h($lang->get('nav.home')) ?></a></li>
        <li><a href="<?= $h(url($type . 's')) ?>"><?= $h($lang->get('nav.' . $type . 's')) ?></a></li>
        <li class="is-active"><a href="#" aria-current="page">#<?= (int)$p['id'] ?></a></li>
    </ul>
</nav>

<div class="card mb-5">
    <header class="card-header">
        <p class="card-header-title"><i class="fas <?= $icon ?> mr-2"></i><?= $h(ucfirst($type)) ?> #<?= (int)$p['id'] ?></p>
        <span class="card-header-icon">
            <?php if ($p['active']): ?><span class="tag is-danger"><?= $h($lang->get('status.active')) ?></span>
            <?php elseif ($p['removed']): ?><span class="tag is-success"><?= $h($lang->get('status.removed')) ?></span>
            <?php else: ?><span class="tag is-dark"><?= $h($lang->get('status.expired')) ?></span><?php endif; ?>
        </span>
    </header>
    <div class="card-content">
        <div class="columns">
            <div class="column is-one-third has-text-centered">
                <figure class="image is-96x96 is-inline-block"><img class="avatar" src="<?= $p['avatar'] ?>" alt="" width="96" height="96"></figure>
                <h2 class="title is-4 mt-3 mb-1"><?= $p['name'] ?></h2>
                <?php if ($controller->shouldShowUuid()): ?><p class="is-size-7 has-text-grey is-family-monospace"><?= $h($p['uuid']) ?></p><?php endif; ?>
            </div>
            <div class="column">
                <div class="columns is-multiline">
                    <?= $row('fa-comment-alt', $h($lang->get('table.reason')), '<p>' . $p['reason'] . '</p>') ?>
                    <?= $row('fa-user-shield', $h($lang->get('table.staff')), $p['staff']) ?>
                    <?= $row('fa-calendar', $h($lang->get('table.date')), $h($p['date'])) ?>
                    <?= $row('fa-server', $h($lang->get('table.server')), '<span class="tag">' . $h($p['server'] ?? 'Global') . '</span>') ?>
                    <?php if (($config['show_server_origin'] ?? true) && !empty($p['server_origin']) && $p['server_origin'] !== '*'): ?>
                        <?= $row('fa-sign-in-alt', 'Server Origin', '<span class="tag is-info">' . $h($p['server_origin']) . '</span>') ?>
                    <?php endif; ?>
                    <?php if (($config['show_server_scope'] ?? true) && !empty($p['server_scope']) && $p['server_scope'] !== '*'): ?>
                        <?= $row('fa-globe', 'Server Scope', '<span class="tag is-primary">' . $h($p['server_scope']) . '</span>') ?>
                    <?php endif; ?>
                    <?php if (!empty($p['ip'])): ?>
                        <?= $row('fa-network-wired', 'IP', '<span class="is-family-monospace">' . $p['ip'] . '</span>') ?>
                    <?php endif; ?>
                    <?php if (in_array($type, ['ban', 'mute'], true) && ($p['duration'] || $p['until'])): ?>
                        <?= $row('fa-hourglass-half', $h($lang->get('detail.duration')), '<span class="tag ' . ($p['until_timestamp'] == 0 ? 'is-danger' : 'is-warning') . '">' . $h($p['duration'] ?: $permanent) . '</span>') ?>
                        <?php if ($p['active'] && $p['timeLeft']): ?>
                            <?= $row('fa-clock', $h($lang->get('detail.time_left')), '<span class="tag ' . ($p['until_timestamp'] == 0 ? 'is-danger' : 'is-warning') . '">' . $h($p['until_timestamp'] == 0 ? $permanent : $p['timeLeft']) . '</span>') ?>
                        <?php endif; ?>
                    <?php endif; ?>
                    <?php if ($p['until'] && in_array($type, ['ban', 'mute'], true)): ?>
                        <?= $row('fa-calendar-times', $h($lang->get('table.expires')), $h($p['until'])) ?>
                    <?php endif; ?>
                    <?php if ($p['removed']): ?>
                        <?= $row('fa-user-times', $h($lang->get('detail.removed_by')), (string)$p['removed_by']) ?>
                        <?php if ($p['removed_date']): ?><?= $row('fa-calendar-check', $h($lang->get('detail.removed_date')), $h($p['removed_date'])) ?><?php endif; ?>
                    <?php endif; ?>
                    <?php
                    $flags = ($p['silent'] ? '<span class="tag mr-1">Silent</span>' : '') . ($p['ipban'] ? '<span class="tag is-danger mr-1">IP Ban</span>' : '') . ($p['warned'] ? '<span class="tag is-warning mr-1">Warned</span>' : '');
                    ?>
                    <?= $row('fa-tags', $h($lang->get('detail.flags')), $flags ?: '<span class="has-text-grey">None</span>') ?>
                </div>
                <?php if ($p['active'] && $p['until_timestamp'] > 0): ?>
                    <p class="heading mt-2"><i class="fas fa-tasks mr-1"></i><?= $h($lang->get('detail.progress')) ?></p>
                    <progress class="progress <?= $p['progress'] >= 100 ? 'is-success' : 'is-warning' ?>" value="<?= round($p['progress'], 1) ?>" max="100"><?= round($p['progress'], 1) ?>%</progress>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php try { $snFile = __DIR__ . '/../../demos/case-evidence.php'; if (is_file($snFile)) { require_once $snFile; echo sn_panel_html($type, (int)$p['id']); } } catch (Throwable $e) {} ?>

<?php if (!empty($relatedPunishments)): ?>
    <div class="card mt-5">
        <header class="card-header"><p class="card-header-title"><i class="fas fa-history mr-2"></i><?= $h($lang->get('detail.other_punishments')) ?></p></header>
        <div class="card-content p-0">
            <?php foreach ($relatedPunishments as $r): ?>
                <a class="media p-3 m-0 is-clickable" href="<?= $h(url('detail?type=' . rtrim($r['type'], 's') . '&id=' . $r['id'])) ?>">
                    <figure class="media-left mb-0"><p class="image is-32x32"><img class="avatar" src="<?= $r['avatar'] ?>" alt="" width="32" height="32" loading="lazy"></p></figure>
                    <div class="media-content">
                        <strong><?= $h(ucfirst(rtrim($r['type'], 's'))) ?> #<?= (int)$r['id'] ?></strong>
                        <span class="tag <?= $r['active'] ? 'is-danger' : 'is-dark' ?> ml-2"><?= $h($lang->get($r['active'] ? 'status.active' : 'status.expired')) ?></span>
                        <p class="is-size-7 has-text-grey"><?= $r['reason'] ?> &middot; <?= $h($r['date']) ?></p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
