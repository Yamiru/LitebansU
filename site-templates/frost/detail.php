<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$p = $punishment;
$permanent = $lang->get('punishment.permanent');
$fact = fn(string $icon, string $label, string $html): string => '<div><dt><i class="fas ' . $icon . '"></i>' . $label . '</dt><dd>' . $html . '</dd></div>';
$flags = ($p['silent'] ? '<span class="tag">Silent</span> ' : '') . ($p['ipban'] ? '<span class="tag danger">IP Ban</span> ' : '') . ($p['warned'] ? '<span class="tag warn">Warned</span> ' : '');
?>
<p class="small muted">
    <a href="<?= $h(url()) ?>"><?= $h($lang->get('nav.home')) ?></a> /
    <a href="<?= $h(url($type . 's')) ?>"><?= $h($lang->get('nav.' . $type . 's')) ?></a> / #<?= (int)$p['id'] ?>
</p>

<h1>
    <?= $h(ucfirst($type)) ?> #<?= (int)$p['id'] ?>
    <?php if ($p['active']): ?><span class="tag danger"><?= $h($lang->get('status.active')) ?></span>
    <?php elseif ($p['removed']): ?><span class="tag ok"><?= $h($lang->get('status.removed')) ?></span>
    <?php else: ?><span class="tag"><?= $h($lang->get('status.expired')) ?></span><?php endif; ?>
</h1>

<div class="split">
    <div class="center">
        <img class="avatar big" src="<?= $p['avatar'] ?>" alt="" width="96" height="96" style="margin:0 auto">
        <h2 style="border:0;margin:1rem 0 0.2rem"><?= $p['name'] ?></h2>
        <?php if ($controller->shouldShowUuid()): ?><p class="small muted mono" style="word-break:break-all"><?= $h($p['uuid']) ?></p><?php endif; ?>
    </div>
    <div>
        <dl class="facts">
            <?= $fact('fa-comment-alt', $h($lang->get('table.reason')), $p['reason']) ?>
            <?= $fact('fa-user-shield', $h($lang->get('table.staff')), $p['staff']) ?>
            <?= $fact('fa-calendar', $h($lang->get('table.date')), $h($p['date'])) ?>
            <?= $fact('fa-server', $h($lang->get('table.server')), '<span class="tag">' . $h($p['server'] ?? 'Global') . '</span>') ?>
            <?php if (($config['show_server_origin'] ?? true) && !empty($p['server_origin']) && $p['server_origin'] !== '*'): ?>
                <?= $fact('fa-sign-in-alt', 'Server Origin', '<span class="tag info">' . $h($p['server_origin']) . '</span>') ?>
            <?php endif; ?>
            <?php if (($config['show_server_scope'] ?? true) && !empty($p['server_scope']) && $p['server_scope'] !== '*'): ?>
                <?= $fact('fa-globe', 'Server Scope', '<span class="tag info">' . $h($p['server_scope']) . '</span>') ?>
            <?php endif; ?>
            <?php if (!empty($p['ip'])): ?>
                <?= $fact('fa-network-wired', 'IP', '<span class="mono">' . $p['ip'] . '</span>') ?>
            <?php endif; ?>
            <?php if (in_array($type, ['ban', 'mute'], true) && ($p['duration'] || $p['until'])): ?>
                <?= $fact('fa-hourglass-half', $h($lang->get('detail.duration')), '<span class="tag ' . ($p['until_timestamp'] == 0 ? 'danger' : 'warn') . '">' . $h($p['duration'] ?: $permanent) . '</span>') ?>
                <?php if ($p['active'] && $p['timeLeft']): ?>
                    <?= $fact('fa-clock', $h($lang->get('detail.time_left')), '<span class="tag ' . ($p['until_timestamp'] == 0 ? 'danger' : 'warn') . '">' . $h($p['until_timestamp'] == 0 ? $permanent : $p['timeLeft']) . '</span>') ?>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($p['until'] && in_array($type, ['ban', 'mute'], true)): ?>
                <?= $fact('fa-calendar-times', $h($lang->get('table.expires')), $h($p['until'])) ?>
            <?php endif; ?>
            <?php if ($p['removed']): ?>
                <?= $fact('fa-user-times', $h($lang->get('detail.removed_by')), (string)$p['removed_by']) ?>
                <?php if ($p['removed_date']): ?><?= $fact('fa-calendar-check', $h($lang->get('detail.removed_date')), $h($p['removed_date'])) ?><?php endif; ?>
            <?php endif; ?>
            <?= $fact('fa-tags', $h($lang->get('detail.flags')), $flags ?: '<span class="muted">None</span>') ?>
        </dl>
        <?php if ($p['active'] && $p['until_timestamp'] > 0): ?>
            <p class="small muted" style="margin:1.5rem 0 0"><i class="fas fa-tasks"></i><?= $h($lang->get('detail.progress')) ?> &middot; <?= round($p['progress'], 1) ?>%</p>
            <div class="progress"><i style="width:<?= min(100, max(0, (float)$p['progress'])) ?>%"></i></div>
        <?php endif; ?>
    </div>
</div>

<?php try { $snFile = __DIR__ . '/../../demos/case-evidence.php'; if (is_file($snFile)) { require_once $snFile; echo sn_panel_html($type, (int)$p['id']); } } catch (Throwable $e) {} ?>

<?php if (!empty($relatedPunishments)): ?>
    <section class="panel" style="margin-top:2rem">
        <h2><span><i class="fas fa-history"></i><?= $h($lang->get('detail.other_punishments')) ?></span></h2>
        <?php foreach ($relatedPunishments as $r): ?>
            <a class="row" href="<?= $h(url('detail?type=' . rtrim($r['type'], 's') . '&id=' . $r['id'])) ?>">
                <img class="avatar" src="<?= $r['avatar'] ?>" alt="" width="40" height="40" loading="lazy">
                <span class="grow">
                    <span class="name"><?= $h(ucfirst(rtrim($r['type'], 's'))) ?> #<?= (int)$r['id'] ?> <span class="tag <?= $r['active'] ? 'danger' : '' ?>"><?= $h($lang->get($r['active'] ? 'status.active' : 'status.expired')) ?></span></span>
                    <span class="sub"><?= strip_tags((string)$r['reason']) ?></span>
                </span>
                <span class="when"><?= $h($r['date']) ?></span>
            </a>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
