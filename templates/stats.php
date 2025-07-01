<div class="stats-page">
    <!-- Header - OPRAVENÉ -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-chart-bar text-primary"></i>
            <?= htmlspecialchars($lang->get('nav.statistics'), ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <!-- ZMENENÉ ID z "stats-clear-cache-btn" na "clear-cache-btn" -->
        <button id="clear-cache-btn" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-sync-alt"></i>
            <span class="d-none d-sm-inline">Clear Cache</span>
        </button>
    </div>


    <!-- Basic Statistics Grid - Fixed with consistent styling -->
    <div class="stats-grid-main mb-5">
        <div class="stat-card-main bg-danger-gradient">
            <div class="stat-icon-main">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stat-content-main">
                <div class="stat-number-main"><?= number_format($stats['bans_active'] ?? 0) ?></div>
                <div class="stat-label-main"><?= htmlspecialchars($lang->get('stats.active_bans'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-main">
                    <?= htmlspecialchars($lang->get('stats.total_of'), ENT_QUOTES, 'UTF-8') ?> <?= number_format($stats['bans'] ?? 0) ?>
                </div>
            </div>
        </div>
        
        <div class="stat-card-main bg-warning-gradient">
            <div class="stat-icon-main">
                <i class="fas fa-volume-mute"></i>
            </div>
            <div class="stat-content-main">
                <div class="stat-number-main"><?= number_format($stats['mutes_active'] ?? 0) ?></div>
                <div class="stat-label-main"><?= htmlspecialchars($lang->get('stats.active_mutes'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-main">
                    <?= htmlspecialchars($lang->get('stats.total_of'), ENT_QUOTES, 'UTF-8') ?> <?= number_format($stats['mutes'] ?? 0) ?>
                </div>
            </div>
        </div>
        
        <div class="stat-card-main bg-info-gradient">
            <div class="stat-icon-main">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content-main">
                <div class="stat-number-main"><?= number_format($stats['warnings'] ?? 0) ?></div>
                <div class="stat-label-main"><?= htmlspecialchars($lang->get('stats.total_warnings'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-main">
                    <?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>
        
        <div class="stat-card-main bg-secondary-gradient">
            <div class="stat-icon-main">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="stat-content-main">
                <div class="stat-number-main"><?= number_format($stats['kicks'] ?? 0) ?></div>
                <div class="stat-label-main"><?= htmlspecialchars($lang->get('stats.total_kicks'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-main">
                    <?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <?php if (!empty($stats['recent_activity'])): ?>
    <div class="row mb-5">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock text-primary"></i>
                        Recent Activity Overview
                    </h5>
                </div>
                <div class="card-body">
                    <div class="activity-timeline">
                        <div class="timeline-item">
                            <div class="timeline-header">
                                <h6 class="mb-2">Last 24 Hours</h6>
                            </div>
                            <div class="activity-stats">
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-danger"><?= $stats['recent_activity']['last_24h']['bans'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Bans</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-warning"><?= $stats['recent_activity']['last_24h']['mutes'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Mutes</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-info"><?= $stats['recent_activity']['last_24h']['warnings'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Warnings</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-secondary"><?= $stats['recent_activity']['last_24h']['kicks'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Kicks</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-header">
                                <h6 class="mb-2">Last 7 Days</h6>
                            </div>
                            <div class="activity-stats">
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-danger"><?= $stats['recent_activity']['last_7d']['bans'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Bans</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-warning"><?= $stats['recent_activity']['last_7d']['mutes'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Mutes</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-info"><?= $stats['recent_activity']['last_7d']['warnings'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Warnings</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-secondary"><?= $stats['recent_activity']['last_7d']['kicks'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Kicks</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="timeline-item">
                            <div class="timeline-header">
                                <h6 class="mb-2">Last 30 Days</h6>
                            </div>
                            <div class="activity-stats">
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-danger"><?= $stats['recent_activity']['last_30d']['bans'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Bans</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-warning"><?= $stats['recent_activity']['last_30d']['mutes'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Mutes</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-info"><?= $stats['recent_activity']['last_30d']['warnings'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Warnings</span>
                                </div>
                                <div class="activity-stat-item">
                                    <span class="activity-stat-count text-secondary"><?= $stats['recent_activity']['last_30d']['kicks'] ?? 0 ?></span>
                                    <span class="activity-stat-label">Kicks</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="row">
        <!-- Top Banned Players -->
        <?php if (!empty($stats['top_banned_players'])): ?>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-slash text-danger"></i>
                        Most Banned Players
                    </h5>
                </div>
                <div class="card-body">
                    <div class="top-players-list">
                        <?php $rank = 1; ?>
                        <?php foreach ($stats['top_banned_players'] as $player): ?>
                        <div class="player-rank-item">
                            <div class="rank-number"><?= $rank++ ?></div>
                            <div class="player-details">
                                <img src="<?= htmlspecialchars($this->getAvatarUrl($player['uuid'], $player['player_name'] ?? 'Unknown'), ENT_QUOTES, 'UTF-8') ?>" 
                                     alt="<?= htmlspecialchars($player['player_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?>" 
                                     class="player-avatar-small">
                                <div class="player-name">
                                    <span class="fw-bold"><?= htmlspecialchars($player['player_name'] ?? 'Unknown', ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="last-ban-time">Last: <?= htmlspecialchars($this->formatDate((int)$player['last_ban_time']), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                            </div>
                            <div class="ban-stats">
                                <span class="badge bg-danger"><?= (int)$player['ban_count'] ?> bans</span>
                                <?php if ((int)$player['active_bans'] > 0): ?>
                                    <span class="badge bg-warning"><?= (int)$player['active_bans'] ?> active</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Most Active Staff -->
        <?php if (!empty($stats['most_active_staff'])): ?>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-user-shield text-success"></i>
                        Most Active Staff
                    </h5>
                </div>
                <div class="card-body">
                    <div class="staff-list">
                        <?php $rank = 1; ?>
                        <?php foreach ($stats['most_active_staff'] as $staff): ?>
                        <div class="staff-item">
                            <div class="rank-number"><?= $rank++ ?></div>
                            <div class="staff-details">
                                <i class="fas fa-user-shield text-primary"></i>
                                <span class="fw-bold"><?= htmlspecialchars($staff['staff_name'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="punishment-breakdown">
                                <span class="badge bg-primary"><?= $staff['total_punishments'] ?> total</span>
                                <div class="breakdown-details">
                                    <?php if ($staff['bans'] > 0): ?>
                                        <span class="text-danger"><?= $staff['bans'] ?>B</span>
                                    <?php endif; ?>
                                    <?php if ($staff['mutes'] > 0): ?>
                                        <span class="text-warning"><?= $staff['mutes'] ?>M</span>
                                    <?php endif; ?>
                                    <?php if ($staff['warnings'] > 0): ?>
                                        <span class="text-info"><?= $staff['warnings'] ?>W</span>
                                    <?php endif; ?>
                                    <?php if ($staff['kicks'] > 0): ?>
                                        <span class="text-secondary"><?= $staff['kicks'] ?>K</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Top Ban Reasons -->
    <?php if (!empty($stats['top_ban_reasons'])): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-list-alt text-info"></i>
                        Most Common Ban Reasons
                    </h5>
                </div>
                <div class="card-body">
                    <div class="reasons-grid">
                        <?php foreach ($stats['top_ban_reasons'] as $reason): ?>
                        <div class="reason-card">
                            <div class="reason-text">
                                <?= htmlspecialchars(mb_substr($reason['reason'], 0, 50), ENT_QUOTES, 'UTF-8') ?>
                                <?php if (mb_strlen($reason['reason']) > 50): ?>...<?php endif; ?>
                            </div>
                            <div class="reason-count">
                                <span class="badge bg-secondary"><?= $reason['count'] ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Daily Activity Chart -->
    <?php if (!empty($stats['daily_activity'])): ?>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar text-warning"></i>
                        Activity by Day of Week (Last 7 Days)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="daily-chart-container">
                        <div class="daily-activity-chart">
                            <?php 
                            $maxCount = max(array_column($stats['daily_activity'], 'count'));
                            foreach ($stats['daily_activity'] as $day): 
                            ?>
                            <div class="day-column">
                                <div class="day-bar" style="height: <?= min(100, ($day['count'] / max(1, $maxCount)) * 100) ?>%">
                                    <span class="day-count"><?= $day['count'] ?></span>
                                </div>
                                <div class="day-label"><?= substr($day['day_name'], 0, 3) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <!-- Clear Cache Modal - NOVÝ -->
    <div class="modal fade" id="cacheModal" tabindex="-1" aria-labelledby="cacheModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cacheModalLabel">
                        <i class="fas fa-sync-alt text-primary me-2"></i>
                        Clear Statistics Cache
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-triangle text-warning fa-2x"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-1">Are you sure?</h6>
                            <p class="mb-0 text-muted">This will clear all cached statistics data and force a refresh. This action cannot be undone.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Cancel
                    </button>
                    <button type="button" id="confirm-clear-cache" class="btn btn-danger">
                        <i class="fas fa-sync-alt me-1"></i>
                        Clear Cache
                    </button>
                </div>
            </div>
        </div>
    </div>
    
</div>