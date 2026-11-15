<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$icon = match($type) {
    'bans' => 'fa-ban', 'mutes' => 'fa-volume-mute', 'warnings' => 'fa-exclamation-triangle', 'kicks' => 'fa-sign-out-alt', default => 'fa-list',
};
$plain = fn($s): string => html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$sortLink = function (string $col, string $label) use ($sortParams): string {
    $current = $sortParams['sort'] === $col;
    $order = ($current && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC';
    $mark = !$current ? '<i class="fas fa-sort"></i>' : '<i class="fas fa-arrow-up"' . ($sortParams['order'] === 'DESC' ? ' style="transform:rotate(180deg)"' : '') . '></i>';
    return '<a href="?sort=' . $col . '&amp;order=' . $order . '">' . $label . ' ' . $mark . '</a>';
};
$singular = rtrim($type, 's');
?>
<h1><i class="fas <?= $icon ?>"></i><?= $title ?></h1>

<?php if (empty($punishments)): ?>
    <div class="notice"><strong><?= $lang->get('punishments.no_data') ?></strong><br><?= $lang->get('punishments.no_data_desc') ?></div>
<?php else: ?>
    <div class="tablewrap">
        <table>
            <thead>
                <tr>
                    <th><?= $sortLink('name', $lang->get('table.player')) ?></th>
                    <th class="hide-m"><?= $sortLink('id', 'ID') ?></th>
                    <th class="hide-m"><?= $sortLink('server', $lang->get('table.server')) ?></th>
                    <th><?= $sortLink('reason', $lang->get('table.reason')) ?></th>
                    <th class="hide-m"><?= $sortLink('banned_by_name', $lang->get('table.staff')) ?></th>
                    <th class="hide-s"><?= $sortLink('time', $lang->get('table.date')) ?></th>
                    <?php if ($type !== 'kicks'): ?><th class="hide-m"><?= $sortLink('until', $lang->get('table.expires')) ?></th><?php endif; ?>
                    <th><?= $sortLink('active', $lang->get('table.status')) ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($punishments as $p): ?>
                <tr data-href="<?= $h(url('detail?type=' . $singular . '&id=' . $p['id'])) ?>">
                    <td>
                        <div class="who">
                            <img class="avatar" src="<?= $p['avatar'] ?>" alt="" width="32" height="32" loading="lazy">
                            <div><strong><?= $p['name'] ?></strong><?php if ($controller->shouldShowUuid()): ?><small class="mono hide-m"><?= $h(substr($p['uuid'], 0, 36)) ?></small><?php endif; ?></div>
                        </div>
                    </td>
                    <td class="hide-m mono muted"><?= (int)$p['id'] ?></td>
                    <td class="hide-m"><span class="tag"><?= $h($p['server'] ?? 'Global') ?></span></td>
                    <td title="<?= $h($plain($p['reason'])) ?>"><?= $h(mb_strimwidth($plain($p['reason']), 0, 40, '...')) ?></td>
                    <td class="hide-m"><?= $p['staff'] ?></td>
                    <td class="hide-s small"><?= $p['date'] ?></td>
                    <?php if ($type !== 'kicks'): ?>
                        <td class="hide-m">
                            <?php if ($p['removed_by']): ?>-
                            <?php elseif ($p['until']): ?>
                                <?php $perm = strpos($p['until'], $lang->get('punishment.permanent')) !== false; $exp = strpos($p['until'], $lang->get('punishment.expired')) !== false; ?>
                                <span class="tag <?= $perm ? 'danger' : ($exp ? 'ok' : 'warn') ?>"><?= $p['until'] ?></span>
                            <?php else: ?><span class="tag danger"><?= $lang->get('punishment.permanent') ?></span><?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if ($type === 'kicks'): ?><span class="tag"><?= $lang->get('status.completed') ?></span>
                        <?php elseif ($p['active']): ?><span class="tag danger"><?= $lang->get('status.active') ?></span>
                        <?php elseif ($p['removed_by']): ?><span class="tag" title="<?= $h($lang->get('status.removed_by') . ' ' . $plain($p['removed_by'])) ?>"><?= $lang->get('status.removed') ?></span>
                        <?php else: ?><span class="tag ok"><?= $lang->get('status.expired') ?></span><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination['total'] > 1): ?>
        <nav class="pager" aria-label="pagination">
            <?php if ($pagination['has_prev']): ?><a href="<?= $pagination['prev_url'] ?>" aria-label="Previous"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
            <span class="muted"><?= (int)$pagination['current'] ?> / <?= (int)$pagination['total'] ?></span>
            <?php if ($pagination['has_next']): ?><a href="<?= $pagination['next_url'] ?>" aria-label="Next"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
        </nav>
    <?php endif; ?>
<?php endif; ?>
