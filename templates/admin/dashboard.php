<?php
/**
 * ============================================================================
 *  LiteBansU - Secure Password Hash Generator
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       2.0
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository:    https://github.com/Yamiru/LitebansU/
 *
 *  This tool generates a secure BCRYPT password hash for use in your .env file.
 *  ?? WARNING: Remove this file after generating your hash to prevent misuse.
 * ============================================================================
 */

// Ensure user is authenticated
if (!$controller->isAuthenticated()) {
    header('Location: ' . url('admin'));
    exit;
}
?>

<div class="admin-dashboard">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>
            <i class="fas fa-tachometer-alt"></i>
            <?= htmlspecialchars($lang->get('admin.dashboard'), ENT_QUOTES, 'UTF-8') ?>
        </h1>
        <a href="<?= htmlspecialchars(url('admin/logout'), ENT_QUOTES, 'UTF-8') ?>" class="btn btn-outline-danger">
            <i class="fas fa-sign-out-alt"></i>
            <?= htmlspecialchars($lang->get('admin.logout'), ENT_QUOTES, 'UTF-8') ?>
        </a>
    </div>

    <!-- Admin Navigation Tabs -->
    <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
                <i class="fas fa-chart-line"></i> Overview
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="search-tab" data-bs-toggle="tab" data-bs-target="#search" type="button">
                <i class="fas fa-search"></i> Search & Manage
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="export-tab" data-bs-toggle="tab" data-bs-target="#export" type="button">
                <i class="fas fa-file-export"></i> Export/Import
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">
                <i class="fas fa-cog"></i> Settings
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                <i class="fas fa-info-circle"></i> System Info
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="adminTabContent">
        <!-- Overview Tab -->
        <div class="tab-pane fade show active" id="overview" role="tabpanel">
            <div class="row">
                <!-- Stats Cards -->
                <div class="col-md-3 mb-4">
                    <div class="card admin-stat-card bg-danger">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Active Bans</h6>
                                    <h2 class="mb-0 text-white"><?= number_format($stats['bans_active'] ?? 0) ?></h2>
                                </div>
                                <i class="fas fa-ban fa-2x opacity-50 text-white"></i>
                            </div>
                            <small class="text-white">Total: <?= number_format($stats['bans'] ?? 0) ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card admin-stat-card bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Active Mutes</h6>
                                    <h2 class="mb-0 text-white"><?= number_format($stats['mutes_active'] ?? 0) ?></h2>
                                </div>
                                <i class="fas fa-volume-mute fa-2x opacity-50 text-white"></i>
                            </div>
                            <small class="text-white">Total: <?= number_format($stats['mutes'] ?? 0) ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card admin-stat-card bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Warnings</h6>
                                    <h2 class="mb-0 text-white"><?= number_format($stats['warnings'] ?? 0) ?></h2>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x opacity-50 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>                <div class="col-md-3 mb-4">
                    <div class="card admin-stat-card bg-warning">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Active Mutes</h6>
                                    <h2 class="mb-0 text-white"><?= number_format($stats['mutes_active'] ?? 0) ?></h2>
                                </div>
                                <i class="fas fa-volume-mute fa-2x opacity-50 text-white"></i>
                            </div>
                            <small class="text-white">Total: <?= number_format($stats['mutes'] ?? 0) ?></small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card admin-stat-card bg-info">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Warnings</h6>
                                    <h2 class="mb-0 text-white"><?= number_format($stats['warnings'] ?? 0) ?></h2>
                                </div>
                                <i class="fas fa-exclamation-triangle fa-2x opacity-50 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-4">
                    <div class="card admin-stat-card bg-secondary">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-0 text-white">Kicks</h6>
                                    <h2 class="mb-0 text-white"><?= number_format($stats['kicks'] ?? 0) ?></h2>
                                </div>
                                <i class="fas fa-sign-out-alt fa-2x opacity-50 text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- System Info Cards -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-server"></i> Server Information
                            </h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <td class="text-muted">OS Version</td>
                                    <td class="admin-table-text"><?= PHP_OS ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">PHP Version</td>
                                    <td class="admin-table-text"><?= PHP_VERSION ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Database</td>
                                    <td class="admin-table-text"><?= htmlspecialchars($config['db_driver'] ?? 'mysql', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Database Size</td>
                                    <td class="admin-table-text"><?= htmlspecialchars($controller->getDatabaseSize(), ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Timezone</td>
                                    <td class="admin-table-text"><?= htmlspecialchars($config['timezone'] ?? 'UTC', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Theme</td>
                                    <td class="admin-table-text"><?= htmlspecialchars($config['default_theme'] ?? 'dark', ENT_QUOTES, 'UTF-8') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-chart-bar"></i> Quick Stats
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="admin-quick-stats-text">Active Bans</small>
                                    <small class="admin-quick-stats-text"><?= number_format($stats['bans_active'] ?? 0) ?> / <?= number_format($stats['bans'] ?? 0) ?></small>
                                </div>
                                <div class="progress">
                                    <?php $banPercent = $stats['bans'] > 0 ? ($stats['bans_active'] / $stats['bans']) * 100 : 0; ?>
                                    <div class="progress-bar bg-danger" style="width: <?= $banPercent ?>%"></div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <small class="admin-quick-stats-text">Active Mutes</small>
                                    <small class="admin-quick-stats-text"><?= number_format($stats['mutes_active'] ?? 0) ?> / <?= number_format($stats['mutes'] ?? 0) ?></small>
                                </div>
                                <div class="progress">
                                    <?php $mutePercent = $stats['mutes'] > 0 ? ($stats['mutes_active'] / $stats['mutes']) * 100 : 0; ?>
                                    <div class="progress-bar bg-warning" style="width: <?= $mutePercent ?>%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search & Manage Tab -->
        <div class="tab-pane fade" id="search" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">Search Punishments</h5>
                    <form id="admin-search-form">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="admin-search-input" 
                                       placeholder="Search by player name, UUID, reason, or staff...">
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="admin-search-type">
                                    <option value="">All Types</option>
                                    <option value="bans">Bans</option>
                                    <option value="mutes">Mutes</option>
                                    <option value="warnings">Warnings</option>
                                    <option value="kicks">Kicks</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <div id="admin-search-results" class="mt-4"></div>
                </div>
            </div>
        </div>

        <!-- Export/Import Tab -->
        <div class="tab-pane fade" id="export" role="tabpanel">
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-download"></i> <?= htmlspecialchars($lang->get('admin.export_data'), ENT_QUOTES, 'UTF-8') ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><?= htmlspecialchars($lang->get('admin.export_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                            <form id="export-form">
                                <div class="mb-3">
                                    <label class="form-label"><?= htmlspecialchars($lang->get('admin.data_type'), ENT_QUOTES, 'UTF-8') ?></label>
                                    <select class="form-control" name="type" id="export-type">
                                        <option value="all"><?= htmlspecialchars($lang->get('admin.all_punishments'), ENT_QUOTES, 'UTF-8') ?></option>
                                        <option value="bans">Bans Only</option>
                                        <option value="mutes">Mutes Only</option>
                                        <option value="warnings">Warnings Only</option>
                                        <option value="kicks">Kicks Only</option>
                                    </select>
                                </div>
                                <div class="mb-3" id="filter-options">
                                    <label class="form-label">Filter</label>
                                    <select class="form-control" name="filter">
                                        <option value="all">All Records</option>
                                        <option value="active">Active Only</option>
                                    </select>
                                    <small class="form-text text-muted">Active filter only applies to Bans and Mutes</small>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Format</label>
                                    <select class="form-control" name="format">
                                        <option value="json">JSON</option>
                                        <option value="csv">CSV</option>
                                        <option value="xml">XML</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-download"></i> Export
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="fas fa-upload"></i> <?= htmlspecialchars($lang->get('admin.import_data'), ENT_QUOTES, 'UTF-8') ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <p><?= htmlspecialchars($lang->get('admin.import_desc'), ENT_QUOTES, 'UTF-8') ?></p>
                            <form id="import-form" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label class="form-label"><?= htmlspecialchars($lang->get('admin.select_file'), ENT_QUOTES, 'UTF-8') ?></label>
                                    <input type="file" class="form-control" name="import_file" accept=".json,.xml" required>
                                </div>
                                <button type="submit" class="btn btn-warning">
                                    <i class="fas fa-upload"></i> <?= htmlspecialchars($lang->get('admin.import'), ENT_QUOTES, 'UTF-8') ?>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<div style="text-align: center;">
    <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer">
        <i class="fab fa-github"></i> Github project
    </a>
</div>
        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-4"><?= htmlspecialchars($lang->get('admin.settings'), ENT_QUOTES, 'UTF-8') ?></h5>
                    <form id="settings-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(SecurityManager::generateCsrfToken(), ENT_QUOTES, 'UTF-8') ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Site Name</label>
                                    <input type="text" class="form-control" name="site_name" 
                                           value="<?= htmlspecialchars($config['site_name'] ?? 'LiteBans', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label"><?= htmlspecialchars($lang->get('admin.footer_site_name'), ENT_QUOTES, 'UTF-8') ?></label>
                                    <input type="text" class="form-control" name="footer_site_name" 
                                           value="<?= htmlspecialchars($config['footer_site_name'] ?? 'YourSite', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted"><?= htmlspecialchars($lang->get('admin.footer_site_name_desc'), ENT_QUOTES, 'UTF-8') ?> (© Footer Site Name <?= date('Y') ?>)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Items Per Page</label>
                                    <input type="number" class="form-control" name="items_per_page" 
                                           value="<?= (int)($config['items_per_page'] ?? 20) ?>" min="5" max="100">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Default Theme</label>
                                    <select class="form-control" name="default_theme">
                                        <option value="light" <?= ($config['default_theme'] ?? 'dark') === 'light' ? 'selected' : '' ?>>Light</option>
                                        <option value="dark" <?= ($config['default_theme'] ?? 'dark') === 'dark' ? 'selected' : '' ?>>Dark</option>
                                        <option value="auto" <?= ($config['default_theme'] ?? 'dark') === 'auto' ? 'selected' : '' ?>>Auto</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Timezone</label>
                                    <select class="form-control" name="timezone">
                                        <?php
                                        $timezones = timezone_identifiers_list();
                                        $currentTz = $config['timezone'] ?? 'UTC';
                                        foreach ($timezones as $tz):
                                        ?>
                                            <option value="<?= $tz ?>" <?= $tz === $currentTz ? 'selected' : '' ?>><?= $tz ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Date Format</label>
                                    <input type="text" class="form-control" name="date_format" 
                                           value="<?= htmlspecialchars($config['date_format'] ?? 'Y-m-d H:i:s', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted">PHP date format</small>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_player_uuid" 
                                           id="show_uuid" <?= ($config['show_uuid'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_uuid">
                                        <?= htmlspecialchars($lang->get('admin.show_player_uuid'), ENT_QUOTES, 'UTF-8') ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Info Tab -->
        <div class="tab-pane fade" id="info" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <h5 class="mb-3">PHP Information</h5>
                    <div class="btn-group mb-3" role="group">
                        <button type="button" class="btn btn-outline-primary phpinfo-btn" data-section="general">General</button>
                        <button type="button" class="btn btn-outline-primary phpinfo-btn" data-section="configuration">Configuration</button>
                        <button type="button" class="btn btn-outline-primary phpinfo-btn" data-section="modules">Modules</button>
                        <button type="button" class="btn btn-outline-primary phpinfo-btn" data-section="environment">Environment</button>
                        <button type="button" class="btn btn-outline-primary phpinfo-btn" data-section="variables">Variables</button>
                    </div>
                    <div id="phpinfo-content" class="phpinfo-container"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Admin Dashboard CSS -->
<style>
.admin-dashboard {
    animation: fadeIn 0.3s ease-out;
}

.nav-tabs .nav-link {
    color: var(--text-secondary);
    border: none;
    border-bottom: 2px solid transparent;
    background: transparent;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

.nav-tabs .nav-link:hover {
    color: var(--primary);
    border-color: transparent;
    background: var(--hover-bg);
}

.nav-tabs .nav-link.active {
    color: var(--primary);
    background: transparent;
    border-color: var(--primary);
}

/* Fixed admin stat card colors */
.admin-stat-card h6,
.admin-stat-card h2,
.admin-stat-card small {
    color: white !important;
}

.admin-stat-card .text-white {
    color: white !important;
}

/* Fixed admin table text colors */
.admin-table-text {
    color: var(--text-primary) !important;
}

.admin-quick-stats-text {
    color: var(--text-primary) !important;
}

.phpinfo-container {
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 1rem;
}

.phpinfo-container table {
    width: 100%;
    margin-bottom: 1rem;
}

.phpinfo-container h3,
.phpinfo-container h4 {
    color: var(--primary);
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}

.admin-search-result {
    padding: 1rem;
    background: var(--bg-secondary);
    border-radius: var(--radius-md);
    margin-bottom: 0.5rem;
    transition: all var(--transition-fast);
}

.admin-search-result:hover {
    background: var(--hover-bg);
}

.admin-search-result .fw-bold {
    color: var(--text-primary) !important;
}

.admin-search-result .text-muted {
    color: var(--text-secondary) !important;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<!-- Admin Dashboard JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    
    // Export form
    const exportForm = document.getElementById('export-form');
    if (exportForm) {
        exportForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const params = new URLSearchParams(formData);
            window.location.href = '<?= url('admin/export') ?>?' + params.toString();
        });
    }
    
    // Show/hide filter options based on type selection
    const exportType = document.getElementById('export-type');
    const filterOptions = document.getElementById('filter-options');
    if (exportType && filterOptions) {
        exportType.addEventListener('change', function() {
            const showFilter = ['all', 'bans', 'mutes'].includes(this.value);
            filterOptions.style.display = showFilter ? 'block' : 'none';
        });
    }
    
    // Import form
    const importForm = document.getElementById('import-form');
    if (importForm) {
        importForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('csrf_token', csrfToken);
            
            try {
                const response = await fetch('<?= url('admin/import') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Import successful! Imported ' + result.imported + ' records.');
                    this.reset();
                } else {
                    alert('Import failed: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                alert('Import error: ' + error.message);
            }
        });
    }
    
    // Settings form
    const settingsForm = document.getElementById('settings-form');
    if (settingsForm) {
        settingsForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            try {
                const response = await fetch('<?= url('admin/save-settings') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Settings saved successfully!');
                } else {
                    alert('Failed to save settings: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                alert('Error saving settings: ' + error.message);
            }
        });
    }
    
    // Enhanced Admin search with better error handling
    const adminSearchForm = document.getElementById('admin-search-form');
    if (adminSearchForm) {
        adminSearchForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const query = document.getElementById('admin-search-input').value.trim();
            const type = document.getElementById('admin-search-type').value;
            const resultsDiv = document.getElementById('admin-search-results');
            
            if (!query || query.length < 2) {
                resultsDiv.innerHTML = '<div class="alert alert-warning">Please enter at least 2 characters</div>';
                return;
            }
            
            resultsDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary"></div><p class="mt-2">Searching...</p></div>';
            
            try {
                const response = await fetch('<?= url('admin/search-punishments') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ query, type })
                });
                
                const result = await response.json();
                
                if (result.success && result.punishments.length > 0) {
                    let html = `<h6 class="mb-3">Found ${result.punishments.length} results for "${query}"</h6>`;
                    
                    result.punishments.forEach(p => {
                        const statusClass = p.active ? 'bg-danger' : 'bg-success';
                        const statusText = p.active ? 'Active' : 'Inactive';
                        const showRemoveBtn = p.active && ['ban', 'mute'].includes(p.type);
                        
                        html += `
                            <div class="admin-search-result">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong class="fw-bold">${escapeHtml(p.player_name)}</strong>
                                        <span class="badge bg-${getTypeColor(p.type)} ms-2">${escapeHtml(p.type.toUpperCase())}</span>
                                        <span class="badge ${statusClass} ms-1">${statusText}</span>
                                        <div class="text-muted small mt-1">${escapeHtml(p.reason)}</div>
                                        <div class="text-muted small">
                                            By ${escapeHtml(p.staff)} on ${escapeHtml(p.date)}
                                            ${p.until ? ' • Expires: ' + escapeHtml(p.until) : ''}
                                            ${p.server !== 'Global' ? ' • Server: ' + escapeHtml(p.server) : ''}
                                        </div>
                                    </div>
                                    ${showRemoveBtn ? 
                                        `<button class="btn btn-sm btn-danger remove-punishment-btn" 
                                                data-type="${p.type}" data-id="${p.id}" data-player="${escapeHtml(p.player_name)}">
                                            <i class="fas fa-times"></i> Remove
                                        </button>` : ''}
                                </div>
                            </div>
                        `;
                    });
                    
                    resultsDiv.innerHTML = html;
                    
                    // Add remove punishment handlers
                    document.querySelectorAll('.remove-punishment-btn').forEach(btn => {
                        btn.addEventListener('click', removePunishment);
                    });
                } else {
                    resultsDiv.innerHTML = '<div class="alert alert-info">No punishments found for your search</div>';
                }
            } catch (error) {
                console.error('Admin search error:', error);
                resultsDiv.innerHTML = '<div class="alert alert-danger">Search error: ' + error.message + '</div>';
            }
        });
    }
    
    // Enhanced Remove punishment handler
    async function removePunishment(e) {
        const btn = e.currentTarget;
        const type = btn.dataset.type;
        const id = btn.dataset.id;
        const playerName = btn.dataset.player;
        
        if (!confirm(`Are you sure you want to remove this ${type} for ${playerName}?`)) return;
        
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Removing...';
        
        try {
            const response = await fetch('<?= url('admin/remove-punishment') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ type, id: parseInt(id) })
            });
            
            const result = await response.json();
            
            if (result.success) {
                btn.closest('.admin-search-result').style.opacity = '0.5';
                btn.innerHTML = '<i class="fas fa-check"></i> Removed';
                btn.classList.remove('btn-danger');
                btn.classList.add('btn-success');
                
                // Update status badge
                const statusBadge = btn.closest('.admin-search-result').querySelector('.badge.bg-danger');
                if (statusBadge && statusBadge.textContent === 'Active') {
                    statusBadge.className = 'badge bg-success ms-1';
                    statusBadge.textContent = 'Removed';
                }
            } else {
                throw new Error(result.error || 'Failed to remove punishment');
            }
        } catch (error) {
            console.error('Remove punishment error:', error);
            alert('Error: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }
    
    // PHP Info loader
    document.querySelectorAll('.phpinfo-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const section = this.dataset.section;
            const contentDiv = document.getElementById('phpinfo-content');
            
            // Update active button
            document.querySelectorAll('.phpinfo-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            contentDiv.innerHTML = '<div class="text-center"><div class="spinner-border"></div></div>';
            
            try {
                const response = await fetch('<?= url('admin/phpinfo') ?>?section=' + section);
                const html = await response.text();
                contentDiv.innerHTML = html;
            } catch (error) {
                contentDiv.innerHTML = '<div class="alert alert-danger">Failed to load PHP info</div>';
            }
        });
    });
    
    // Helper functions
    function getTypeColor(type) {
        const colors = {
            'ban': 'danger',
            'mute': 'warning',
            'warning': 'info',
            'kick': 'secondary'
        };
        return colors[type] || 'dark';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

});
</script>