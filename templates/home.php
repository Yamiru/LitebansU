<div class="hero-section mb-5">
    <div class="card">
        <div class="card-body text-center">
            <h1 class="display-4 mb-3">
                <i class="fas fa-shield-alt text-primary"></i>
                <?= htmlspecialchars($lang->get('home.welcome'), ENT_QUOTES, 'UTF-8') ?>
            </h1>
            <p class="lead text-muted">
                <?= htmlspecialchars($lang->get('home.description'), ENT_QUOTES, 'UTF-8') ?>
            </p>
        </div>
    </div>
</div>

<!-- Search Section -->
<div class="search-container mb-5">
    <h3 class="mb-3">
        <i class="fas fa-search text-primary"></i>
        <?= htmlspecialchars($lang->get('search.title'), ENT_QUOTES, 'UTF-8') ?>
    </h3>
    
    <form id="search-form" class="search-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
        <div class="row g-3">
            <div class="col-md-9">
                <div class="input-group input-group-lg">
                    <span class="input-group-text">
                        <i class="fas fa-user"></i>
                    </span>
                    <input 
                        type="text" 
                        id="search-input"
                        class="form-control" 
                        placeholder="<?= htmlspecialchars($lang->get('search.placeholder'), ENT_QUOTES, 'UTF-8') ?>"
                        autocomplete="off"
                        maxlength="36"
                    >
                </div>
                <small class="form-text text-muted">
                    <?= htmlspecialchars($lang->get('search.help'), ENT_QUOTES, 'UTF-8') ?>
                </small>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100">
                    <i class="fas fa-search"></i>
                    <?= htmlspecialchars($lang->get('search.button'), ENT_QUOTES, 'UTF-8') ?>
                </button>
            </div>
        </div>
    </form>
    
    <div id="search-results" class="mt-4"></div>
</div>

<?php if (isset($searchQuery) && !empty($searchQuery)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-populate search field and trigger search
    const searchInput = document.getElementById('search-input');
    const searchForm = document.getElementById('search-form');
    
    if (searchInput && searchForm) {
        searchInput.value = <?= json_encode($searchQuery) ?>;
        // Trigger search after a small delay
        setTimeout(() => {
            searchForm.dispatchEvent(new Event('submit'));
        }, 100);
    }
});
</script>
<?php endif; ?>
<!-- Statistics Grid - Improved 2x2 Layout -->
<div class="stats-overview mb-5">
    <h3 class="mb-4 text-center">
        <i class="fas fa-chart-bar text-primary"></i>
        <?= htmlspecialchars($lang->get('stats.title'), ENT_QUOTES, 'UTF-8') ?>
    </h3>
    <div class="stats-grid-2x2">
        <div class="stat-card-compact bg-danger">
            <div class="stat-icon-compact">
                <i class="fas fa-ban"></i>
            </div>
            <div class="stat-content-compact">
                <div class="stat-number-compact"><?= number_format($stats['bans_active'] ?? 0) ?></div>
                <div class="stat-label-compact"><?= htmlspecialchars($lang->get('stats.active_bans'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-compact">
                    <?= htmlspecialchars($lang->get('stats.total_of'), ENT_QUOTES, 'UTF-8') ?> <?= number_format($stats['bans'] ?? 0) ?>
                </div>
            </div>
        </div>
        
        <div class="stat-card-compact bg-warning">
            <div class="stat-icon-compact">
                <i class="fas fa-volume-mute"></i>
            </div>
            <div class="stat-content-compact">
                <div class="stat-number-compact"><?= number_format($stats['mutes_active'] ?? 0) ?></div>
                <div class="stat-label-compact"><?= htmlspecialchars($lang->get('stats.active_mutes'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-compact">
                    <?= htmlspecialchars($lang->get('stats.total_of'), ENT_QUOTES, 'UTF-8') ?> <?= number_format($stats['mutes'] ?? 0) ?>
                </div>
            </div>
        </div>
        
        <div class="stat-card-compact bg-info">
            <div class="stat-icon-compact">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="stat-content-compact">
                <div class="stat-number-compact"><?= number_format($stats['warnings'] ?? 0) ?></div>
                <div class="stat-label-compact"><?= htmlspecialchars($lang->get('stats.total_warnings'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-compact">
                    <?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>
        
        <div class="stat-card-compact bg-secondary">
            <div class="stat-icon-compact">
                <i class="fas fa-sign-out-alt"></i>
            </div>
            <div class="stat-content-compact">
                <div class="stat-number-compact"><?= number_format($stats['kicks'] ?? 0) ?></div>
                <div class="stat-label-compact"><?= htmlspecialchars($lang->get('stats.total_kicks'), ENT_QUOTES, 'UTF-8') ?></div>
                <div class="stat-total-compact">
                    <?= htmlspecialchars($lang->get('stats.all_time'), ENT_QUOTES, 'UTF-8') ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Link to Full Statistics -->
    <div class="text-center mt-4">
        <a href="<?= htmlspecialchars(url('stats'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-primary">
            <i class="fas fa-chart-line"></i>
            View Detailed Statistics
        </a>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <h3 class="mb-4">
            <i class="fas fa-clock text-primary"></i>
            <?= htmlspecialchars($lang->get('home.recent_activity'), ENT_QUOTES, 'UTF-8') ?>
        </h3>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-ban text-danger"></i>
                    <?= htmlspecialchars($lang->get('home.recent_bans'), ENT_QUOTES, 'UTF-8') ?>
                </h5>
                <span class="badge bg-danger"><?= count($recentBans) ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($recentBans)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0"><?= htmlspecialchars($lang->get('home.no_recent_bans'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php else: ?>
                    <div class="punishment-list">
                        <?php foreach ($recentBans as $ban): ?>
                            <?php 
                            $playerName = $ban['player_name'] ?? $ban['name'] ?? 'Unknown';
                            $uuid = $ban['uuid'] ?? '';
                            ?>
                            <div class="punishment-item" style="cursor: pointer;" onclick="window.location.href='<?= htmlspecialchars(url('detail?type=ban&id=' . $ban['id']), ENT_QUOTES, 'UTF-8') ?>'">
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($controller->getAvatarUrl($uuid, $playerName), ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="<?= htmlspecialchars($playerName, ENT_QUOTES, 'UTF-8') ?>" 
                                         class="avatar me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?= htmlspecialchars($playerName, ENT_QUOTES, 'UTF-8') ?></div>
                                        <small class="text-muted">
                                            <?php 
                                            $reason = $ban['reason'] ?? 'No reason provided';
                                            echo htmlspecialchars(mb_substr($reason, 0, 40), ENT_QUOTES, 'UTF-8');
                                            if (mb_strlen($reason) > 40) echo '...';
                                            ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">
                                            <?= htmlspecialchars($controller->formatDate((int)$ban['time']), ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                        <?php if ($ban['active'] ?? false): ?>
                                            <span class="badge bg-danger">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= htmlspecialchars(url('bans'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-list"></i>
                            <?= htmlspecialchars($lang->get('home.view_all_bans'), ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-lg-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-volume-mute text-warning"></i>
                    <?= htmlspecialchars($lang->get('home.recent_mutes'), ENT_QUOTES, 'UTF-8') ?>
                </h5>
                <span class="badge bg-warning"><?= count($recentMutes) ?></span>
            </div>
            <div class="card-body">
                <?php if (empty($recentMutes)): ?>
                    <div class="text-center py-4">
                        <i class="fas fa-volume-up fa-2x text-success mb-2"></i>
                        <p class="text-muted mb-0"><?= htmlspecialchars($lang->get('home.no_recent_mutes'), ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                <?php else: ?>
                    <div class="punishment-list">
                        <?php foreach ($recentMutes as $mute): ?>
                            <?php 
                            $playerName = $mute['player_name'] ?? $mute['name'] ?? 'Unknown';
                            $uuid = $mute['uuid'] ?? '';
                            ?>
                            <div class="punishment-item" style="cursor: pointer;" onclick="window.location.href='<?= htmlspecialchars(url('detail?type=mute&id=' . $mute['id']), ENT_QUOTES, 'UTF-8') ?>'">
                                <div class="d-flex align-items-center">
                                    <img src="<?= htmlspecialchars($controller->getAvatarUrl($uuid, $playerName), ENT_QUOTES, 'UTF-8') ?>" 
                                         alt="<?= htmlspecialchars($playerName, ENT_QUOTES, 'UTF-8') ?>" 
                                         class="avatar me-3">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold"><?= htmlspecialchars($playerName, ENT_QUOTES, 'UTF-8') ?></div>
                                        <small class="text-muted">
                                            <?php 
                                            $reason = $mute['reason'] ?? 'No reason provided';
                                            echo htmlspecialchars(mb_substr($reason, 0, 40), ENT_QUOTES, 'UTF-8');
                                            if (mb_strlen($reason) > 40) echo '...';
                                            ?>
                                        </small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted d-block">
                                            <?= htmlspecialchars($controller->formatDate((int)$mute['time']), ENT_QUOTES, 'UTF-8') ?>
                                        </small>
                                        <?php if ($mute['active'] ?? false): ?>
                                            <span class="badge bg-warning">Active</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Inactive</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="text-center mt-3">
                        <a href="<?= htmlspecialchars(url('mutes'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-list"></i>
                            <?= htmlspecialchars($lang->get('home.view_all_mutes'), ENT_QUOTES, 'UTF-8') ?>
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>