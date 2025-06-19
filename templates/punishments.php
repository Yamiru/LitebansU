<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>
        <?php
        $icon = match($type) {
            'bans' => 'fa-ban text-danger',
            'mutes' => 'fa-volume-mute text-warning', 
            'warnings' => 'fa-exclamation-triangle text-info',
            'kicks' => 'fa-sign-out-alt text-secondary',
            default => 'fa-list'
        };
        ?>
        <i class="fas <?= $icon ?>"></i>
        <?= $title ?>
    </h1>
</div>

<?php if (empty($punishments)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
            <h4 class="text-muted"><?= $lang->get('punishments.no_data') ?></h4>
            <p class="text-muted"><?= $lang->get('punishments.no_data_desc') ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?= $lang->get('table.player') ?></th>
                    <th><?= $lang->get('table.reason') ?></th>
                    <th><?= $lang->get('table.staff') ?></th>
                    <th><?= $lang->get('table.date') ?></th>
                    <?php if ($type !== 'kicks'): ?>
                        <th><?= $lang->get('table.expires') ?></th>
                    <?php endif; ?>
                    <th><?= $lang->get('table.status') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($punishments as $punishment): ?>
                    <tr>
                        <td>
                            <div class="player-info">
                                <img src="<?= $punishment['avatar'] ?>" 
                                     alt="<?= $punishment['name'] ?>" 
                                     class="avatar">
                                <div>
                                    <div class="fw-bold"><?= $punishment['name'] ?></div>
                                    <small class="text-muted font-monospace">
                                        <?= substr($punishment['uuid'], 0, 8) ?>...
                                    </small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="reason-cell" title="<?= $punishment['reason'] ?>">
                                <?= strlen($punishment['reason']) > 50 ? 
                                    substr($punishment['reason'], 0, 50) . '...' : 
                                    $punishment['reason'] ?>
                            </div>
                        </td>
                        <td>
                            <span class="text-primary"><?= $punishment['staff'] ?></span>
                        </td>
                        <td>
                            <small><?= $punishment['date'] ?></small>
                        </td>
                        <?php if ($type !== 'kicks'): ?>
                            <td>
                                <?php if ($punishment['until']): ?>
                                    <span class="badge bg-secondary"><?= $punishment['until'] ?></span>
                                <?php else: ?>
                                    <span class="badge bg-dark"><?= $lang->get('punishment.permanent') ?></span>
                                <?php endif; ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?php if ($type === 'kicks'): ?>
                                <span class="status-badge status-expired"><?= $lang->get('status.completed') ?></span>
                            <?php elseif ($punishment['active']): ?>
                                <span class="status-badge status-active"><?= $lang->get('status.active') ?></span>
                            <?php else: ?>
                                <?php if ($punishment['removed_by']): ?>
                                    <span class="status-badge status-inactive" title="<?= $lang->get('status.removed_by') ?> <?= $punishment['removed_by'] ?>">
                                        <?= $lang->get('status.removed') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-expired"><?= $lang->get('status.expired') ?></span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total'] > 1): ?>
        <nav aria-label="<?= $lang->get('pagination.label') ?>">
            <div class="pagination">
                <?php if ($pagination['has_prev']): ?>
                    <a href="<?= $pagination['prev_url'] ?>" class="btn">
                        <i class="fas fa-chevron-left"></i>
                        <?= $lang->get('pagination.previous') ?>
                    </a>
                <?php else: ?>
                    <span class="btn disabled">
                        <i class="fas fa-chevron-left"></i>
                        <?= $lang->get('pagination.previous') ?>
                    </span>
                <?php endif; ?>
                
                <span class="pagination-info">
                    <?= $lang->get('pagination.page_info', [
                        'current' => $pagination['current'],
                        'total' => $pagination['total']
                    ]) ?>
                </span>
                
                <?php if ($pagination['has_next']): ?>
                    <a href="<?= $pagination['next_url'] ?>" class="btn">
                        <?= $lang->get('pagination.next') ?>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="btn disabled">
                        <?= $lang->get('pagination.next') ?>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>
<?php endif; ?>