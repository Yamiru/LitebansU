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
    <!-- Desktop Table -->
    <div class="table-responsive d-none d-lg-block">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><?= $lang->get('table.player') ?></th>
                    <th>ID</th>
                    <th><?= $lang->get('table.server') ?></th>
                    <th><?= $lang->get('table.reason') ?></th>
                    <th><?= $lang->get('table.staff') ?></th>
                    <th><?= $lang->get('table.date') ?></th>
                    <?php if ($type !== 'kicks'): ?>
                        <th><?= $lang->get('table.expires') ?></th>
                    <?php endif; ?>
                    <th><?= $lang->get('table.status') ?></th>
                    <th><?= $lang->get('table.actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($punishments as $punishment): ?>
                    <tr onclick="window.location.href='<?= htmlspecialchars(url('detail?type=' . rtrim($type, 's') . '&id=' . $punishment['id']), ENT_QUOTES, 'UTF-8') ?>'" style="cursor: pointer;">
                        <td>
                            <div class="player-info">
                                <img src="<?= $punishment['avatar'] ?>" 
                                     alt="<?= $punishment['name'] ?>" 
                                     class="avatar">
                                <div>
                                    <div class="fw-bold"><?= $punishment['name'] ?></div>
                                    <?php if ($controller->shouldShowUuid()): ?>
                                    <small class="text-muted font-monospace">
                                        <?= substr($punishment['uuid'], 0, 8) ?>...
                                    </small>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td>
                            <small class="font-monospace text-muted">
                                <?= $punishment['id'] ?>
                                <?php if ($config['show_uuid'] === true): ?>
                                <br><strong>UUID:</strong> <?= substr($punishment['uuid'], 0, 8) ?>...
                                <?php endif; ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                <?= htmlspecialchars($punishment['server'] ?? 'Global', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </td>
                        <td>
                            <div class="reason-cell" title="<?= $punishment['reason'] ?>">
                                <?= strlen($punishment['reason']) > 15 ? 
                                    substr($punishment['reason'], 0, 15) . '...' : 
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
                                    <?php 
                                    if (strpos($punishment['until'], $lang->get('punishment.permanent')) !== false): ?>
                                        <span class="badge bg-danger"><?= $punishment['until'] ?></span>
                                    <?php elseif (strpos($punishment['until'], $lang->get('punishment.expired')) !== false): ?>
                                        <span class="badge bg-success"><?= $punishment['until'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning"><?= $punishment['until'] ?></span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <span class="badge bg-danger"><?= $lang->get('punishment.permanent') ?></span>
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
                        <td onclick="event.stopPropagation();">
                            <a href="<?= htmlspecialchars(url('detail?type=' . rtrim($type, 's') . '&id=' . $punishment['id']), ENT_QUOTES, 'UTF-8') ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> <?= $lang->get('table.view') ?>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Mobile Card Layout -->
    <div class="mobile-punishment-list d-lg-none">
        <?php foreach ($punishments as $punishment): ?>
            <div class="mobile-punishment-card" onclick="window.location.href='<?= htmlspecialchars(url('detail?type=' . rtrim($type, 's') . '&id=' . $punishment['id']), ENT_QUOTES, 'UTF-8') ?>'">
                <div class="card mb-3">
                    <div class="card-body">
                        <!-- Player Header -->
                        <div class="d-flex align-items-center mb-3">
                            <img src="<?= $punishment['avatar'] ?>" 
                                 alt="<?= $punishment['name'] ?>" 
                                 class="avatar me-3">
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold"><?= $punishment['name'] ?></h6>
                                <small class="text-muted font-monospace">
                                    <?= $type === 'bans' ? 'Ban' : ($type === 'mutes' ? 'Mute' : ($type === 'warnings' ? 'Warn' : 'Kick')) ?> #<?= $punishment['id'] ?>
                                </small>
                            </div>
                            <div class="text-end">
                                <?php if ($type === 'kicks'): ?>
                                    <span class="badge bg-secondary"><?= $lang->get('status.completed') ?></span>
                                <?php elseif ($punishment['active']): ?>
                                    <span class="badge bg-danger"><?= $lang->get('status.active') ?></span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $lang->get('status.inactive') ?></span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- UUID Display (conditionally shown) -->
                        <?php if ($config['show_uuid'] === true): ?>
                        <div class="mb-2">
                            <small class="text-muted font-monospace"><strong>UUID:</strong> <?= $punishment['uuid'] ?></small>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Server Badge -->
                        <div class="mb-2">
                            <span class="badge bg-secondary">
                                <i class="fas fa-server"></i> <?= htmlspecialchars($punishment['server'] ?? 'Global', ENT_QUOTES, 'UTF-8') ?>
                            </span>
                        </div>
                        
                        <!-- Reason -->
                        <div class="mb-2">
                            <strong class="text-muted small"><?= $lang->get('table.reason') ?>:</strong>
                            <div class="mt-1">
                                <?= strlen($punishment['reason']) > 15 ? 
                                    substr($punishment['reason'], 0, 15) . '...' : 
                                    $punishment['reason'] ?>
                            </div>
                        </div>
                        
                        <!-- Meta Information -->
                        <div class="row g-2 small text-muted">
                            <div class="col-6">
                                <strong><?= $lang->get('table.staff') ?>:</strong><br>
                                <span class="text-primary"><?= $punishment['staff'] ?></span>
                            </div>
                            <div class="col-6">
                                <strong><?= $lang->get('table.date') ?>:</strong><br>
                                <?= date('M j, Y', strtotime($punishment['date'])) ?>
                            </div>
                            <?php if ($type !== 'kicks' && $punishment['until']): ?>
                                <div class="col-12 mt-2">
                                    <strong><?= $lang->get('table.expires') ?>:</strong>
                                    <?php 
                                    if (strpos($punishment['until'], $lang->get('punishment.permanent')) !== false): ?>
                                        <span class="badge bg-danger ms-1"><?= $punishment['until'] ?></span>
                                    <?php elseif (strpos($punishment['until'], $lang->get('punishment.expired')) !== false): ?>
                                        <span class="badge bg-success ms-1"><?= $punishment['until'] ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-warning ms-1"><?= $punishment['until'] ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Action Button -->
                        <div class="text-end mt-3" onclick="event.stopPropagation();">
                            <a href="<?= htmlspecialchars(url('detail?type=' . rtrim($type, 's') . '&id=' . $punishment['id']), ENT_QUOTES, 'UTF-8') ?>" 
                               class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> <?= $lang->get('table.view') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($pagination['total'] > 1): ?>
        <nav aria-label="<?= $lang->get('pagination.label') ?>">
            <div class="pagination">
                <?php if ($pagination['has_prev']): ?>
                    <a href="<?= $pagination['prev_url'] ?>" class="btn">
                        <i class="fas fa-chevron-left"></i>
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.previous') ?></span>
                    </a>
                <?php else: ?>
                    <span class="btn disabled">
                        <i class="fas fa-chevron-left"></i>
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.previous') ?></span>
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
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.next') ?></span>
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php else: ?>
                    <span class="btn disabled">
                        <span class="d-none d-sm-inline"><?= $lang->get('pagination.next') ?></span>
                        <i class="fas fa-chevron-right"></i>
                    </span>
                <?php endif; ?>
            </div>
        </nav>
    <?php endif; ?>
<?php endif; ?>
