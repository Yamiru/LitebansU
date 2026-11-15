<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$icon = match($type) {
    'bans' => 'fa-ban has-text-danger',
    'mutes' => 'fa-volume-mute has-text-warning',
    'warnings' => 'fa-exclamation-triangle has-text-info',
    'kicks' => 'fa-sign-out-alt has-text-grey',
    default => 'fa-list',
};
$plain = fn($s): string => html_entity_decode(strip_tags((string)$s), ENT_QUOTES | ENT_HTML5, 'UTF-8');
$sortLink = function (string $col, string $label) use ($sortParams): string {
    $current = $sortParams['sort'] === $col;
    $order = ($current && $sortParams['order'] === 'ASC') ? 'DESC' : 'ASC';
    $icon = !$current ? '<i class="fas fa-sort"></i>' : '<i class="fas fa-arrow-up"' . ($sortParams['order'] === 'DESC' ? ' style="transform:rotate(180deg)"' : '') . '></i>';
    return '<a href="?sort=' . $col . '&amp;order=' . $order . '">' . $label . ' ' . $icon . '</a>';
};
$singular = rtrim($type, 's');
?>
<h1 class="title is-2"><i class="fas <?= $icon ?> mr-2"></i><?= $title ?></h1>

<?php if (empty($punishments)): ?>
    <div class="notification has-text-centered py-6">
        <p class="title is-5"><?= $lang->get('punishments.no_data') ?></p>
        <p><?= $lang->get('punishments.no_data_desc') ?></p>
    </div>
<?php else: ?>
    <div class="table-container">
        <table class="table is-fullwidth is-hoverable is-striped">
            <thead>
                <tr>
                    <th><?= $sortLink('name', $lang->get('table.player')) ?></th>
                    <th class="is-hidden-mobile"><?= $sortLink('id', 'ID') ?></th>
                    <th class="is-hidden-mobile"><?= $sortLink('server', $lang->get('table.server')) ?></th>
                    <th><?= $sortLink('reason', $lang->get('table.reason')) ?></th>
                    <th class="is-hidden-touch"><?= $sortLink('banned_by_name', $lang->get('table.staff')) ?></th>
                    <th class="is-hidden-mobile"><?= $sortLink('time', $lang->get('table.date')) ?></th>
                    <?php if ($type !== 'kicks'): ?><th class="is-hidden-touch"><?= $sortLink('until', $lang->get('table.expires')) ?></th><?php endif; ?>
                    <th><?= $sortLink('active', $lang->get('table.status')) ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($punishments as $p): ?>
                <tr data-href="<?= $h(url('detail?type=' . $singular . '&id=' . $p['id'])) ?>" class="is-clickable">
                    <td>
                        <div class="media is-align-items-center">
                            <figure class="media-left mb-0"><p class="image is-32x32"><img class="avatar" src="<?= $p['avatar'] ?>" alt="" width="32" height="32" loading="lazy"></p></figure>
                            <div class="media-content">
                                <strong><?= $p['name'] ?></strong>
                                <?php if ($controller->shouldShowUuid()): ?><br><small class="has-text-grey is-family-monospace is-hidden-mobile"><?= $h(substr($p['uuid'], 0, 36)) ?></small><?php endif; ?>
                            </div>
                        </div>
                    </td>
                    <td class="is-hidden-mobile"><small class="is-family-monospace has-text-grey"><?= (int)$p['id'] ?></small></td>
                    <td class="is-hidden-mobile"><span class="tag"><?= $h($p['server'] ?? 'Global') ?></span></td>
                    <td title="<?= $h($plain($p['reason'])) ?>"><?= $h(mb_strimwidth($plain($p['reason']), 0, 40, '...')) ?></td>
                    <td class="is-hidden-touch has-text-link"><?= $p['staff'] ?></td>
                    <td class="is-hidden-mobile"><small><?= $p['date'] ?></small></td>
                    <?php if ($type !== 'kicks'): ?>
                        <td class="is-hidden-touch">
                            <?php if ($p['removed_by']): ?>-
                            <?php elseif ($p['until']): ?>
                                <?php $perm = strpos($p['until'], $lang->get('punishment.permanent')) !== false; $exp = strpos($p['until'], $lang->get('punishment.expired')) !== false; ?>
                                <span class="tag <?= $perm ? 'is-danger' : ($exp ? 'is-success' : 'is-warning') ?>"><?= $p['until'] ?></span>
                            <?php else: ?><span class="tag is-danger"><?= $lang->get('punishment.permanent') ?></span><?php endif; ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?php if ($type === 'kicks'): ?><span class="tag is-dark"><?= $lang->get('status.completed') ?></span>
                        <?php elseif ($p['active']): ?><span class="tag is-danger"><?= $lang->get('status.active') ?></span>
                        <?php elseif ($p['removed_by']): ?><span class="tag is-dark" title="<?= $h($lang->get('status.removed_by') . ' ' . $plain($p['removed_by'])) ?>"><?= $lang->get('status.removed') ?></span>
                        <?php else: ?><span class="tag is-success"><?= $lang->get('status.expired') ?></span><?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <?php if ($pagination['total'] > 1): ?>
        <nav class="pagination is-centered" role="navigation" aria-label="pagination">
            <?php if ($pagination['has_prev']): ?><a class="pagination-previous" href="<?= $pagination['prev_url'] ?>" aria-label="Previous"><i class="fas fa-chevron-left"></i></a><?php endif; ?>
            <?php if ($pagination['has_next']): ?><a class="pagination-next" href="<?= $pagination['next_url'] ?>" aria-label="Next"><i class="fas fa-chevron-right"></i></a><?php endif; ?>
            <ul class="pagination-list"><li><span class="pagination-link is-current"><?= (int)$pagination['current'] ?> / <?= (int)$pagination['total'] ?></span></li></ul>
        </nav>
    <?php endif; ?>
<?php endif; ?>
