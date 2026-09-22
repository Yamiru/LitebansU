<?php
/**
 * ============================================================================
 *  LiteBansU - Administration
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       5.0
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *
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
        <?php if (($currentUser['role'] ?? 'admin') === 'admin'): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="export-tab" data-bs-toggle="tab" data-bs-target="#export" type="button">
                <i class="fas fa-file-export"></i> Export/Import
            </button>
        </li>
        <?php endif; ?>
        <?php if ((($config['google_auth_enabled'] ?? false) || ($config['discord_auth_enabled'] ?? false)) && ($currentUser['role'] ?? '') === 'admin'): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button">
                <i class="fas fa-users"></i> Users
            </button>
        </li>
        <?php endif; ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings" type="button">
                <i class="fas fa-cog"></i> Settings
            </button>
        </li>
       <?php if (($currentUser['role'] ?? 'admin') === 'admin'): ?>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="info-tab" data-bs-toggle="tab" data-bs-target="#info" type="button">
                <i class="fas fa-info-circle"></i> System Info
            </button>
        </li>
       <?php endif; ?>
      <li class="nav-item" role="presentation"><button class="nav-link" id="case-evidence-tab" data-bs-toggle="tab" data-bs-target="#case-evidence" type="button"><i class="fas fa-folder-open"></i> <?php /* case-evidence:tab */ try { require_once __DIR__ . '/../../demos/case-evidence.php'; echo sn_e(sn_t('tab')); } catch (Throwable $e) { echo 'Evidence'; } ?></button></li>
      <?php if (($currentUser['role'] ?? 'admin') === 'admin'): ?>
      <li class="nav-item" role="presentation"><button class="nav-link" id="seo-tracking-tab" data-bs-toggle="tab" data-bs-target="#seo-tracking" type="button"><i class="fas fa-bullseye"></i> SEO &amp; Tracking</button></li>
      <?php endif; ?>
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
                                    <td class="text-muted">LiteBansU Version</td>
                                    <td class="admin-table-text">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-code-branch"></i> 
                                            <?php 
                                            $version = file_exists(BASE_DIR . '/.version') ? trim(file_get_contents(BASE_DIR . '/.version')) : '3.3';
                                            echo htmlspecialchars($version, ENT_QUOTES, 'UTF-8');
                                            ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">GitHub Version</td>
                                    <td class="admin-table-text">
                                        <span id="github-version-badge">
                                            <span class="spinner-border spinner-border-sm" role="status"></span>
                                            <small class="text-muted ms-2">Checking...</small>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="text-muted">OS Version</td>
                                    <td class="admin-table-text"><?= PHP_OS ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Web Server</td>
                                    <td class="admin-table-text">
                                        <?php
                                        $server = $_SERVER['SERVER_SOFTWARE'] ?? '';
                                        if (stripos($server, 'nginx') !== false) {
                                            echo '<i class="fas fa-server text-success"></i> Nginx';
                                        } elseif (stripos($server, 'apache') !== false) {
                                            echo '<i class="fas fa-server text-success"></i> Apache';
                                        } else {
                                            echo '<i class="fas fa-server text-warning"></i> ' . htmlspecialchars($server ?: 'Unknown', ENT_QUOTES, 'UTF-8');
                                        }
                                        ?>
                                    </td>
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

        <?php if (($currentUser['role'] ?? 'admin') === 'admin'): ?>
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
        <?php endif; ?>

        <!-- Settings Tab -->
        <div class="tab-pane fade" id="settings" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <?php if (($currentUser['role'] ?? 'admin') === 'admin'): ?>
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
                        
                        <!-- Protest Settings -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-gavel"></i> Protest Settings</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discord Invite URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fab fa-discord"></i></span>
                                        <input type="url" class="form-control" name="protest_discord" 
                                               value="<?= htmlspecialchars($config['protest_discord'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                               placeholder="https://discord.gg/...">
                                    </div>
                                    <small class="form-text text-muted">Discord server invite link for protests</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Protest Email</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                        <input type="email" class="form-control" name="protest_email" 
                                               value="<?= htmlspecialchars($config['protest_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                               placeholder="support@yourserver.com">
                                    </div>
                                    <small class="form-text text-muted">Email address for ban protests</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Forum URL</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-comments"></i></span>
                                        <input type="url" class="form-control" name="protest_forum" 
                                               value="<?= htmlspecialchars($config['protest_forum'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                               placeholder="https://forum.yourserver.com/ban-protests">
                                    </div>
                                    <small class="form-text text-muted">Forum link for ban protests</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Display Options -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-eye"></i> Display Options</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_silent_punishments" 
                                           id="show_silent" <?= ($config['show_silent_punishments'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_silent">
                                        Show Silent Punishments
                                    </label>
                                    <small class="form-text text-muted d-block">Display silent bans and mutes</small>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_server_origin" 
                                           id="show_server_origin" <?= ($config['show_server_origin'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_server_origin">
                                        Show Server Origin
                                    </label>
                                    <small class="form-text text-muted d-block">Display which server issued the punishment</small>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_server_scope" 
                                           id="show_server_scope" <?= ($config['show_server_scope'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_server_scope">
                                        Show Server Scope
                                    </label>
                                    <small class="form-text text-muted d-block">Display which servers the punishment applies to</small>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_contact_discord" 
                                           id="show_discord" <?= ($config['show_contact_discord'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_discord">
                                        Show Discord Contact
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_contact_email" 
                                           id="show_email" <?= ($config['show_contact_email'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_email">
                                        Show Email Contact
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_contact_forum" 
                                           id="show_forum" <?= ($config['show_contact_forum'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_forum">
                                        Show Forum Contact
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_menu_protest" 
                                           id="show_menu_protest" <?= ($config['show_menu_protest'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_menu_protest">
                                        Show Protest in Menu
                                    </label>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="show_menu_stats" 
                                           id="show_menu_stats" <?= ($config['show_menu_stats'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="show_menu_stats">
                                        Show Statistics in Menu
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Access Control Settings -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-lock"></i> Access Control</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="require_login" 
                                           id="require_login" <?= ($config['require_login'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="require_login">
                                        <i class="fas fa-user-lock"></i> Require Login for All Pages
                                    </label>
                                    <small class="form-text text-muted d-block">
                                        When enabled, visitors must log in through the admin panel to view any page. 
                                        Useful for private servers or internal use.
                                    </small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="alert alert-warning mb-0">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Note:</strong> Make sure you have admin authentication configured before enabling this option, 
                                    otherwise you may lock yourself out!
                                </div>
                            </div>
                        </div>
                        
                        <!-- SEO Settings -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-search"></i> SEO Settings</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="seo_enable_schema" 
                                           id="seo_schema" <?= ($config['seo_enable_schema'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="seo_schema">
                                        Enable Schema.org Markup
                                    </label>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Organization Name</label>
                                    <input type="text" class="form-control" name="seo_organization_name" 
                                           value="<?= htmlspecialchars($config['seo_organization_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Organization Logo URL</label>
                                    <input type="text" class="form-control" name="seo_organization_logo" 
                                           value="<?= htmlspecialchars($config['seo_organization_logo'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Facebook Page URL</label>
                                    <input type="text" class="form-control" name="seo_social_facebook" 
                                           value="<?= htmlspecialchars($config['seo_social_facebook'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Twitter Handle</label>
                                    <input type="text" class="form-control" name="seo_social_twitter" 
                                           value="<?= htmlspecialchars($config['seo_social_twitter'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted">e.g., @yourhandle</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">YouTube Channel URL</label>
                                    <input type="text" class="form-control" name="seo_social_youtube" 
                                           value="<?= htmlspecialchars($config['seo_social_youtube'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Contact Email</label>
                                    <input type="email" class="form-control" name="seo_contact_email" 
                                           value="<?= htmlspecialchars($config['seo_contact_email'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Contact Phone</label>
                                    <input type="text" class="form-control" name="seo_contact_phone" 
                                           value="<?= htmlspecialchars($config['seo_contact_phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted">e.g., +421123456789</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">SEO Locale</label>
                                    <input type="text" class="form-control" name="seo_locale" 
                                           value="<?= htmlspecialchars($config['seo_locale'] ?? 'en_US', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted">e.g., en_US, sk_SK</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Geo Region (Optional)</label>
                                    <input type="text" class="form-control" name="seo_geo_region" 
                                           value="<?= htmlspecialchars($config['seo_geo_region'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted">e.g., SK-KI</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Geo Place Name (Optional)</label>
                                    <input type="text" class="form-control" name="seo_geo_placename" 
                                           value="<?= htmlspecialchars($config['seo_geo_placename'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted">e.g., KoÅ¡ice</small>
                                </div>
                                
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="seo_ai_training" 
                                           id="seo_ai" <?= ($config['seo_ai_training'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="seo_ai">
                                        Allow AI Training
                                    </label>
                                    <small class="form-text text-muted d-block">Allow search engines to use content for AI</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Advanced Site Settings -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-globe"></i> Advanced Site Settings</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Site URL</label>
                                    <input type="url" class="form-control" name="site_url" 
                                           value="<?= htmlspecialchars($config['site_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="https://yourdomain.com">
                                    <small class="form-text text-muted">Full site URL with https:// (used for SEO and canonical links)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Site Description</label>
                                    <textarea class="form-control" name="site_description" rows="3"
                                              placeholder="View and search player punishments on our Minecraft server"><?= htmlspecialchars($config['site_description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                                    <small class="form-text text-muted">Meta description for search engines (150-160 characters)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Page Title Template</label>
                                    <input type="text" class="form-control" name="site_title_template" 
                                           value="<?= htmlspecialchars($config['site_title_template'] ?? '{page} - {site}', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="{page} - {site}">
                                    <small class="form-text text-muted">Template for page titles. Use {page} for page name and {site} for site name</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" name="site_keywords" 
                                           value="<?= htmlspecialchars($config['site_keywords'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="minecraft, litebans, punishments, bans, server">
                                    <small class="form-text text-muted">Comma-separated keywords for SEO</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Robots Meta Tag</label>
                                    <select class="form-control" name="site_robots">
                                        <option value="index, follow" <?= ($config['site_robots'] ?? 'index, follow') === 'index, follow' ? 'selected' : '' ?>>Index, Follow (Recommended)</option>
                                        <option value="noindex, follow" <?= ($config['site_robots'] ?? 'index, follow') === 'noindex, follow' ? 'selected' : '' ?>>No Index, Follow</option>
                                        <option value="index, nofollow" <?= ($config['site_robots'] ?? 'index, follow') === 'index, nofollow' ? 'selected' : '' ?>>Index, No Follow</option>
                                        <option value="noindex, nofollow" <?= ($config['site_robots'] ?? 'index, follow') === 'noindex, nofollow' ? 'selected' : '' ?>>No Index, No Follow</option>
                                    </select>
                                    <small class="form-text text-muted">Control search engine indexing</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Site Language Code</label>
                                    <input type="text" class="form-control" name="site_lang" 
                                           value="<?= htmlspecialchars($config['site_lang'] ?? 'en', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="en" maxlength="5">
                                    <small class="form-text text-muted">HTML language attribute (e.g., en, sk, de, cs)</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Default Language</label>
                                    <select class="form-control" name="default_language">
                                        <option value="en" <?= ($config['default_language'] ?? 'en') === 'en' ? 'selected' : '' ?>>English</option>
                                        <option value="sk" <?= ($config['default_language'] ?? 'en') === 'sk' ? 'selected' : '' ?>>Slovak</option>
                                        <option value="cs" <?= ($config['default_language'] ?? 'en') === 'cs' ? 'selected' : '' ?>>Czech</option>
                                        <option value="de" <?= ($config['default_language'] ?? 'en') === 'de' ? 'selected' : '' ?>>German</option>
                                        <option value="es" <?= ($config['default_language'] ?? 'en') === 'es' ? 'selected' : '' ?>>Spanish</option>
                                        <option value="fr" <?= ($config['default_language'] ?? 'en') === 'fr' ? 'selected' : '' ?>>French</option>
                                        <option value="pl" <?= ($config['default_language'] ?? 'en') === 'pl' ? 'selected' : '' ?>>Polish</option>
                                        <option value="ru" <?= ($config['default_language'] ?? 'en') === 'ru' ? 'selected' : '' ?>>Russian</option>
                                    </select>
                                    <small class="form-text text-muted">Default language for new visitors</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Theme Color</label>
                                    <input type="color" class="form-control form-control-color" name="site_theme_color" 
                                           value="<?= htmlspecialchars($config['site_theme_color'] ?? '#ef4444', ENT_QUOTES, 'UTF-8') ?>">
                                    <small class="form-text text-muted">Browser theme color for mobile devices</small>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Avatar Configuration -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-user-circle"></i> Avatar Configuration</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Avatar URL (Online)</label>
                                    <input type="text" class="form-control" name="avatar_url" 
                                           value="<?= htmlspecialchars($config['avatar_url'] ?? 'https://mineskin.eu/helm/{name}', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="https://mineskin.eu/helm/{name}">
                                    <small class="form-text text-muted">URL for online mode avatars. Use {name} or {uuid} placeholder</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Avatar URL (Offline)</label>
                                    <input type="text" class="form-control" name="avatar_url_offline" 
                                           value="<?= htmlspecialchars($config['avatar_url_offline'] ?? 'https://mineskin.eu/helm/{name}', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="https://mineskin.eu/helm/{name}">
                                    <small class="form-text text-muted">URL for offline mode avatars. Use {name} or {uuid} placeholder</small>
                                </div>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Save Settings
                        </button>
                    </form>
                    <?php else: ?>
                    <h5 class="mb-4"><i class="fas fa-cog"></i> Settings</h5>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> As a moderator, you can only manage cache. Other settings are available to administrators only.
                    </div>
                    <?php endif; ?>
                    
                    <!-- Cache Management Section -->
                    <hr class="my-4">
                    <div class="cache-management">
                        <h5 class="mb-3">
                            <i class="fas fa-database"></i> Cache Management
                        </h5>
                        <p class="text-muted">Clear cached statistics and data to refresh information.</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card admin-cache-card">
                                    <div class="card-body">
                                        <h6><i class="fas fa-chart-bar text-warning"></i> Statistics Cache</h6>
                                        <p class="small text-muted mb-3">Cached punishment statistics and counters</p>
                                        <button type="button" id="clear-stats-cache" class="btn btn-warning">
                                            <i class="fas fa-sync-alt"></i> Clear Stats Cache
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="card admin-cache-card">
                                    <div class="card-body">
                                        <h6><i class="fas fa-trash-alt text-danger"></i> Full Cache Clear</h6>
                                        <p class="small text-muted mb-3">Clear all cached data and reset</p>
                                        <button type="button" id="clear-all-cache" class="btn btn-danger">
                                            <i class="fas fa-trash"></i> Clear All Cache
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="cache-status" class="mt-3"></div>
                    </div>
                    
                    <!-- Database Diagnostic Section -->
                    <hr class="my-4">
                    <div class="database-diagnostic">
                        <h5 class="mb-3">
                            <i class="fas fa-stethoscope"></i> Database Diagnostic
                        </h5>
                        <p class="text-muted">Test database structure and timestamp handling</p>
                        
                        <div class="card admin-cache-card">
                            <div class="card-body">
                                <h6><i class="fas fa-database text-info"></i> Database Structure Test</h6>
                                <p class="small text-muted mb-3">Check tables, columns, and timestamp validity</p>
                                <button type="button" id="test-database" class="btn btn-info">
                                    <i class="fas fa-play"></i> Run Database Test
                                </button>
                                <button type="button" id="clear-opcache" class="btn btn-warning ms-2">
                                    <i class="fas fa-broom"></i> Clear OPcache & Reload Config
                                </button>
                            </div>
                        </div>
                        
                        <div id="database-test-results" class="mt-3"></div>
                    </div>
                    
                </div>
            </div>
        </div>

        <?php if ((($config['google_auth_enabled'] ?? false) || ($config['discord_auth_enabled'] ?? false)) && ($currentUser['role'] ?? '') === 'admin'): ?>
        <!-- Users Tab -->
        <div class="tab-pane fade" id="users" role="tabpanel">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0"><i class="fas fa-users text-primary"></i> User Management</h5>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                            <i class="fas fa-plus"></i> Add User
                        </button>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Add an admin by email (Google sign-in) or by Discord ID (Discord sign-in). They get access the first time they sign in with that account.
                    </div>
                    
                    <div id="users-list">
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (($currentUser['role'] ?? 'admin') === 'admin'): ?>
        <!-- SEO & Tracking Tab -->
        <?php
        $seoExtras = \core\SiteExtras::get();
        $seoLangs = $lang->getSupportedLanguages();
        $seoSiteUrl = \core\SiteExtras::siteUrl($config);
        $seoPrivacyShown = [];
        foreach ($seoLangs as $code) {
            $seoPrivacyShown[$code] = \core\SiteExtras::privacyHtml($code, (string)($config['site_name'] ?? 'this site'));
        }
        ?>
        <div class="tab-pane fade" id="seo-tracking" role="tabpanel">
            <form id="seo-form" onsubmit="return false">
                <div class="alert alert-danger d-none" id="seo-error" role="alert"></div>
                <div class="alert alert-success d-none" id="seo-saved" role="status"></div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-search text-primary"></i> Search engines and crawlers</h5>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" id="seo-allow-crawlers" <?= !empty($seoExtras['allow_crawlers']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="seo-allow-crawlers">Allow search engines and other crawlers</label>
                        </div>
                        <p class="text-muted small">When on, <a href="<?= htmlspecialchars(url('robots.txt'), ENT_QUOTES, 'UTF-8') ?>" target="_blank">robots.txt</a> allows the public pages and lists the <a href="<?= htmlspecialchars(url('sitemap.xml'), ENT_QUOTES, 'UTF-8') ?>" target="_blank">sitemap</a>. When off, the whole site is closed to crawlers (robots.txt and noindex). Blocking AI crawlers only is still <code>SEO_AI_TRAINING=false</code> in .env.</p>
                        <hr>
                        <h6 class="mb-2">IndexNow</h6>
                        <p class="text-muted small mb-2">Tells Bing, Yandex and other IndexNow search engines about your pages right away. It sends the home page, ban, mute, warning and kick lists, statistics, protest, privacy and the list pages. Individual punishments (a player and an ID) are never sent. Site URL used: <code><?= htmlspecialchars($seoSiteUrl, ENT_QUOTES, 'UTF-8') ?></code></p>
                        <button type="button" class="btn btn-outline-primary" id="indexnow-submit"><i class="fas fa-paper-plane"></i> Submit all pages to IndexNow</button>
                        <div id="indexnow-result" class="mt-3"></div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-chart-line text-primary"></i> Google Analytics and cookies</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label" for="seo-ga-id">Measurement ID</label>
                                <input type="text" class="form-control" id="seo-ga-id" placeholder="G-XXXXXXXXXX" value="<?= htmlspecialchars((string)$seoExtras['ga_id'], ENT_QUOTES, 'UTF-8') ?>">
                                <small class="text-muted">Leave empty to switch analytics off. The tag loads on every page, but only after the visitor accepts analytics cookies. The notice itself does not need this.</small>
                            </div>
                            <div class="col-md-6 d-flex align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="seo-cookie-banner" <?= !empty($seoExtras['cookie_banner']) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="seo-cookie-banner">Show the cookie notice in the visitor's language (accept, decline or choose when Google Analytics is set, a plain notice otherwise)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex justify-content-between flex-wrap gap-2 mb-2">
                            <h5 class="mb-0"><i class="fas fa-user-shield text-primary"></i> Privacy page</h5>
                            <a href="<?= htmlspecialchars(url('privacy'), ENT_QUOTES, 'UTF-8') ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="fas fa-external-link-alt"></i> Open /privacy</a>
                        </div>
                        <p class="text-muted small">The cookie notice and the footer link to this page. Each language starts with the text visitors see now. Change it and save; languages you do not touch keep showing the English text, or the built-in default, which you should read and adapt. Allowed formatting: p, br, strong, em, u, h2 to h4, lists, links, blockquote.</p>
                        <div class="mb-2">
                            <label class="form-label" for="seo-privacy-lang">Language</label>
                            <select class="form-control w-auto" id="seo-privacy-lang">
                                <?php foreach ($seoLangs as $code): ?>
                                    <option value="<?= htmlspecialchars($code, ENT_QUOTES, 'UTF-8') ?>" <?= $code === 'en' ? 'selected' : '' ?>><?= htmlspecialchars(strtoupper($code) . ' - ' . $lang->getLanguageName($code), ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <textarea class="form-control font-monospace" id="seo-privacy-text" rows="12" placeholder="<p>Your privacy policy...</p>"></textarea>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-code text-primary"></i> Custom code</h5>
                        <div class="alert alert-warning small">This code is added to every public page exactly as you write it. Only paste code you trust. It is not held back by the cookie notice, so do not add trackers here that need consent. Scripts from other domains also need that domain allowed in the Content-Security-Policy in <code>.htaccess</code>.</div>
                        <div class="mb-3">
                            <label class="form-label" for="seo-custom-head">Head code (before &lt;/head&gt;)</label>
                            <textarea class="form-control font-monospace" id="seo-custom-head" rows="5" spellcheck="false"><?= htmlspecialchars((string)$seoExtras['custom_head'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                        <div>
                            <label class="form-label" for="seo-custom-footer">Footer code (before &lt;/body&gt;)</label>
                            <textarea class="form-control font-monospace" id="seo-custom-footer" rows="5" spellcheck="false"><?= htmlspecialchars((string)$seoExtras['custom_footer'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary" id="seo-save"><i class="fas fa-save"></i> Save SEO &amp; Tracking</button>
            </form>
        </div>
        <?php endif; ?>

        <?php /* case-evidence:pane */ try { require_once __DIR__ . '/../../demos/case-evidence.php'; echo sn_admin_pane_html(); } catch (Throwable $e) {} ?>
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

.sitemap-container {
    max-height: 400px;
    overflow-y: auto;
    background: var(--bg-secondary);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 1rem;
}

.sitemap-container pre {
    margin: 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.sitemap-container code {
    color: var(--text-primary);
    font-size: 0.875rem;
    font-family: 'Courier New', monospace;
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

.admin-cache-card {
    background: var(--bg-secondary) !important;
    border: 1px solid var(--border-color) !important;
}

.admin-cache-card .card-body h6 {
    color: var(--text-primary) !important;
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

/* Modal fixes */
.modal {
    z-index: 1055 !important;
}

.modal-backdrop {
    z-index: 1050 !important;
}

.modal-dialog {
    pointer-events: none;
}

.modal-content {
    pointer-events: auto;
}

.modal.show .modal-dialog {
    transform: none;
}
</style>

<!-- Modify Case Modal: reason (LiteBans database) plus case evidence (internal storage) -->
<?php $snT = function (string $key): string { return function_exists('sn_t') ? htmlspecialchars(sn_t($key), ENT_QUOTES, 'UTF-8') : $key; }; ?>
<div class="modal fade" id="modifyReasonModal" tabindex="-1" aria-labelledby="modifyReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifyReasonModalLabel">
                    <i class="fas fa-edit text-warning"></i> Modify Case
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="modify-case-form" onsubmit="return false">
                    <input type="hidden" id="modify-type">
                    <input type="hidden" id="modify-id">
                    <div class="mb-3">
                        <label class="form-label">Player</label>
                        <div class="form-control bg-secondary" id="modify-player-name"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Reason</label>
                        <textarea class="form-control" id="modify-reason-input" rows="2" placeholder="Enter new reason..."></textarea>
                    </div>

                    <hr>
                    <h6 class="mb-3"><i class="fas fa-folder-open"></i> <?= $snT('tab') ?></h6>
                    <div id="modify-evidence-existing" class="mb-3 small"></div>
                    <div id="modify-evidence-add">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4">
                                <label class="form-label" for="modify-appeal-status"><?= $snT('ap_title') ?></label>
                                <select class="form-control" id="modify-appeal-status">
                                    <?php foreach (['none', 'pending', 'accepted', 'rejected'] as $option): ?>
                                        <option value="<?= $option ?>"><?= $snT('ap_' . $option) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label" for="modify-appeal-line">&nbsp;</label>
                                <input type="text" class="form-control" id="modify-appeal-line" maxlength="160" placeholder="<?= $snT('ap_line_ph') ?>">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="modify-report"><?= $snT('report') ?></label>
                            <textarea class="form-control" id="modify-report" rows="3" maxlength="5000" placeholder="<?= $snT('report_ph') ?>"></textarea>
                        </div>
                        <div class="mb-1">
                            <label class="form-label" for="modify-files"><?= $snT('shots') ?></label>
                            <input type="file" class="form-control" id="modify-files" accept="image/png,image/jpeg,image/gif,image/webp,video/*,.mkv,.dem,.demo,.m4a" multiple
                                   data-sn-captions data-placeholder="<?= $snT('caption_ph') ?>">
                            <div class="sn-captions" data-sn-caption-list></div>
                            <div class="form-text"><?= $snT('shots_hint') ?></div>
                        </div>
                        <div class="form-check mt-3">
                            <input class="form-check-input" type="checkbox" id="modify-public">
                            <label class="form-check-label" for="modify-public"><?= $snT('public_label') ?></label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <a class="btn btn-outline-secondary me-auto" id="modify-open-case" href="#"><i class="fas fa-external-link-alt"></i> <?= $snT('c_open') ?></a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="save-modified-reason">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="fas fa-user-plus text-success"></i> Add User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label" for="add-user-email">Email</label>
                    <input type="email" class="form-control" id="add-user-email" placeholder="user@example.com">
                    <small class="text-muted">For Google sign-in, or Discord sign-in with the same email</small>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="add-user-discord">Discord ID</label>
                    <input type="text" class="form-control" id="add-user-discord" inputmode="numeric" placeholder="123456789012345678">
                    <small class="text-muted">Numeric user ID (Discord: Settings, Advanced, Developer Mode, then right-click the user and Copy User ID). Enter an email, a Discord ID, or both.</small>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" id="add-user-name" placeholder="Display name (optional)">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" id="add-user-role">
                        <option value="viewer">Viewer - Can only view and search</option>
                        <option value="moderator">Moderator - Can remove and modify punishments</option>
                        <option value="admin">Administrator - Full access</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="save-new-user">
                    <i class="fas fa-plus"></i> Add User
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">
                    <i class="fas fa-user-edit text-warning"></i> Edit User
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="edit-user-id">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" id="edit-user-email" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" id="edit-user-name" placeholder="Display name">
                </div>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select class="form-select" id="edit-user-role">
                        <option value="viewer">Viewer - Can only view and search</option>
                        <option value="moderator">Moderator - Can remove and modify punishments</option>
                        <option value="admin">Administrator - Full access</option>
                    </select>
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="edit-user-active" checked>
                        <label class="form-check-label" for="edit-user-active">Account Active</label>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger me-auto" id="delete-user-btn">
                    <i class="fas fa-trash"></i> Delete
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="save-edit-user">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Admin Dashboard JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const userRole = '<?= htmlspecialchars($currentUser['role'] ?? 'admin', ENT_QUOTES, 'UTF-8') ?>';
    const canModify = userRole === 'admin' || userRole === 'moderator';
    
    // Check GitHub version
    const githubVersionBadge = document.getElementById('github-version-badge');
    if (githubVersionBadge) {
        fetch('<?= url('admin/check-github-version') ?>')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let html = '<span class="badge bg-secondary"><i class="fab fa-github"></i> ' + escapeHtml(data.github_version) + '</span>';
                    
                    if (data.update_available) {
                        html += ' <span class="badge bg-success ms-2"><i class="fas fa-arrow-up"></i> Update Available!</span>';
                    } else {
                        html += ' <small class="text-muted ms-2"><i class="fas fa-check"></i> Up to date</small>';
                    }
                    
                    githubVersionBadge.innerHTML = html;
                } else {
                    githubVersionBadge.innerHTML = '<small class="text-muted"><i class="fas fa-times"></i> Unable to check</small>';
                }
            })
            .catch(error => {
                console.error('GitHub version check failed:', error);
                githubVersionBadge.innerHTML = '<small class="text-muted"><i class="fas fa-times"></i> Check failed</small>';
            });
    }
    
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
                    alert(result.message || 'Settings saved successfully!');
                    // Reloading is manual: offer a button instead of refreshing on our own
                    if (result.reload_recommended) {
                        showReloadNotice('Some changes (such as avatars) apply after the page is reloaded.');
                    }
                } else {
                    alert('Failed to save settings: ' + (result.error || 'Unknown error'));
                }
            } catch (error) {
                alert('Error saving settings: ' + error.message);
            }
        });
    }
    
    // SEO & Tracking tab: crawlers, Google Analytics, privacy page, custom code, IndexNow
    (function () {
        const form = document.getElementById('seo-form');
        if (!form) return;
        const csrf = () => document.querySelector('meta[name="csrf-token"]')?.content || '';
        // Text of each language as visitors see it now (saved text, else English, else the built-in default)
        const privacy = <?= json_encode((object)$seoPrivacyShown, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) ?>;
        const privacyShown = { ...privacy };
        const privacySaved = <?= json_encode(array_keys((array)($seoExtras['privacy'] ?? [])), JSON_HEX_TAG) ?>;
        const langSelect = document.getElementById('seo-privacy-lang');
        const privacyText = document.getElementById('seo-privacy-text');
        let currentLang = langSelect.value;
        privacyText.value = privacy[currentLang] || '';

        langSelect.addEventListener('change', () => {
            privacy[currentLang] = privacyText.value;
            currentLang = langSelect.value;
            privacyText.value = privacy[currentLang] || '';
        });

        const errorBox = document.getElementById('seo-error');
        const savedBox = document.getElementById('seo-saved');
        const show = (box, text) => {
            errorBox.classList.add('d-none');
            savedBox.classList.add('d-none');
            box.textContent = text;
            box.classList.remove('d-none');
        };

        async function postJson(url, body) {
            const response = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ ...body, csrf_token: csrf() })
            });
            const text = await response.text();
            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                throw new Error('HTTP ' + response.status + ': ' + text.replace(/<[^>]*>/g, ' ').trim().substring(0, 160));
            }
            if (!response.ok || result.error) throw new Error(result.error || result.message || 'Request failed');
            return result;
        }

        document.getElementById('seo-save').addEventListener('click', async function () {
            privacy[currentLang] = privacyText.value;
            // Only send languages that were saved before or edited now, not copies of the default text
            const changed = {};
            Object.keys(privacy).forEach(code => {
                if (privacySaved.includes(code) || (privacy[code] || '').trim() !== (privacyShown[code] || '').trim()) changed[code] = privacy[code];
            });
            const btn = this;
            btn.disabled = true;
            try {
                await postJson('<?= url('admin/site-extras') ?>', {
                    allow_crawlers: document.getElementById('seo-allow-crawlers').checked,
                    ga_id: document.getElementById('seo-ga-id').value.trim(),
                    cookie_banner: document.getElementById('seo-cookie-banner').checked,
                    custom_head: document.getElementById('seo-custom-head').value,
                    custom_footer: document.getElementById('seo-custom-footer').value,
                    privacy: changed
                });
                show(savedBox, 'Saved. Changes are live on the public pages.');
            } catch (error) {
                show(errorBox, error.message);
            } finally {
                btn.disabled = false;
            }
        });

        document.getElementById('indexnow-submit').addEventListener('click', async function () {
            const btn = this;
            const out = document.getElementById('indexnow-result');
            btn.disabled = true;
            out.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Submitting...';
            try {
                const result = await postJson('<?= url('admin/indexnow') ?>', {});
                out.innerHTML = '<div class="alert alert-success mb-2"><i class="fas fa-check"></i> ' + escapeHtml(result.message) + '</div>' +
                    '<details><summary class="small">URLs</summary><ul class="small mb-0">' + result.urls.map(u => '<li>' + escapeHtml(u) + '</li>').join('') + '</ul></details>';
            } catch (error) {
                out.innerHTML = '<div class="alert alert-danger mb-0"><i class="fas fa-exclamation-triangle"></i> ' + escapeHtml(error.message) + '</div>';
            } finally {
                btn.disabled = false;
            }
        });
    })();

    // Manual reload: a small notice with a button, never an automatic refresh
    function showReloadNotice(message) {
        document.getElementById('reload-notice')?.remove();
        const notice = document.createElement('div');
        notice.id = 'reload-notice';
        notice.className = 'alert alert-info shadow d-flex align-items-center gap-3';
        notice.style.cssText = 'position: fixed; bottom: 1.5rem; left: 1.5rem; z-index: 1080; max-width: 420px; margin: 0;';
        notice.innerHTML = '<span></span><button type="button" class="btn btn-sm btn-primary text-nowrap"><i class="fas fa-sync-alt"></i> Reload page</button>' +
            '<button type="button" class="btn-close" aria-label="Close"></button>';
        notice.querySelector('span').textContent = message;
        notice.querySelector('.btn-primary').addEventListener('click', () => location.reload());
        notice.querySelector('.btn-close').addEventListener('click', () => notice.remove());
        document.body.appendChild(notice);
    }

    // Case evidence badges for search results (labels come from the site language)
    const SN_LABELS = <?= json_encode(function_exists('sn_t') ? [
        'summary' => sn_t('c_sum', 0, 0),
        'pending' => sn_t('ap_pending'), 'accepted' => sn_t('ap_accepted'), 'rejected' => sn_t('ap_rejected'), 'title' => sn_t('ap_title'),
        'empty' => sn_t('c_none'),
    ] : ['summary' => '0 / 0', 'title' => 'Appeal', 'empty' => ''], JSON_UNESCAPED_UNICODE) ?>;
    function evidenceBadges(p) {
        const e = p.evidence;
        if (!e || (!e.reports && !e.files && e.appeal === 'none')) return '';
        const summary = SN_LABELS.summary.replace(/\d+/, e.reports).replace(/\d+/, e.files);
        const appeal = e.appeal !== 'none' ? ` <span class="badge bg-secondary">${escapeHtml(SN_LABELS.title)}: ${escapeHtml(SN_LABELS[e.appeal] || e.appeal)}</span>` : '';
        const match = p.evidence_match ? ' <span class="badge bg-warning text-dark"><i class="fas fa-search"></i></span>' : '';
        return `<small class="d-block mt-1"><span class="badge bg-info text-dark"><i class="fas fa-folder-open"></i> ${escapeHtml(summary)}</span>${appeal}${match}</small>`;
    }

    // Enhanced Admin search with better error handling
    const adminSearchForm = document.getElementById('admin-search-form');
    if (adminSearchForm) {
        adminSearchForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const query = document.getElementById('admin-search-input').value.trim();
            const type = document.getElementById('admin-search-type').value;
            const resultsDiv = document.getElementById('admin-search-results');
            
            if (!query || query.length < 1) {
                resultsDiv.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Please enter at least 1 character</div>';
                return;
            }
            
            resultsDiv.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Searching...</span></div></div>';
            
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
                    let html = `<h6 class="mb-3"><i class="fas fa-search text-primary"></i> Found ${result.punishments.length} results for "${escapeHtml(query)}"</h6>`;
                    html += '<div class="punishment-list">';
                    
                    result.punishments.forEach(p => {
                        const statusClass = p.active ? 'bg-danger' : 'bg-success';
                        const statusText = p.active ? 'Active' : 'Inactive';
                        const showRemoveBtn = p.active && ['ban', 'mute'].includes(p.type);
                        const typeColor = getTypeColor(p.type);
                        
                        html += `
                            <div class="punishment-item admin-search-result" style="cursor: pointer;" onclick="window.location.href='<?= url('detail') ?>?type=${p.type}&id=${p.id}'">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">
                                            ${escapeHtml(p.player_name)}
                                            <span class="badge bg-${typeColor} ms-2">${escapeHtml(p.type.toUpperCase())}</span>
                                            <span class="badge ${statusClass} ms-1">${statusText}</span>
                                        </div>
                                        <small class="text-muted d-block">${escapeHtml(p.reason.length > 60 ? p.reason.substring(0, 60) + '...' : p.reason)}</small>
                                        ${evidenceBadges(p)}
                                        <small class="text-muted">
                                            <i class="fas fa-user-shield"></i> ${escapeHtml(p.staff)} 
                                            <i class="fas fa-clock ms-2"></i> ${escapeHtml(p.date)}
                                            ${p.until ? ' <i class="fas fa-hourglass-end ms-2"></i> ' + escapeHtml(p.until) : ''}
                                            ${p.server !== 'Global' ? ' <i class="fas fa-server ms-2"></i> ' + escapeHtml(p.server) : ''}
                                        </small>
                                    </div>
                                    <div class="text-end" onclick="event.stopPropagation()">
                                        ${canModify ? `
                                        <button class="btn btn-sm btn-warning modify-reason-btn me-1" 
                                                data-type="${p.type}" data-id="${p.id}" data-player="${escapeHtml(p.player_name)}" data-reason="${escapeAttr(p.reason)}">
                                            <i class="fas fa-edit"></i> Modify
                                        </button>
                                        ${showRemoveBtn ? 
                                            `<button class="btn btn-sm btn-danger remove-punishment-btn" 
                                                    data-type="${p.type}" data-id="${p.id}" data-player="${escapeHtml(p.player_name)}">
                                                <i class="fas fa-times"></i> Remove
                                            </button>` : 
                                            `<a href="<?= url('detail') ?>?type=${p.type}&id=${p.id}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> View
                                            </a>`}
                                        ` : `
                                        <a href="<?= url('detail') ?>?type=${p.type}&id=${p.id}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                        `}
                                    </div>
                                </div>
                            </div>
                        `;
                    });
                    html += '</div>';
                    
                    resultsDiv.innerHTML = html;
                    
                    // Add remove punishment handlers
                    document.querySelectorAll('.remove-punishment-btn').forEach(btn => {
                        btn.addEventListener('click', removePunishment);
                    });
                    
                    // Add modify reason handlers
                    document.querySelectorAll('.modify-reason-btn').forEach(btn => {
                        btn.addEventListener('click', openModifyReasonModal);
                    });
                } else {
                    resultsDiv.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No punishments found for your search</div>';
                }
            } catch (error) {
                console.error('Admin search error:', error);
                resultsDiv.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Search error: ' + escapeHtml(error.message) + '</div>';
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
    
    // Open the Modify Case modal and load the case evidence
    const CASE_EVIDENCE_URL = '<?= url('admin/case-evidence') ?>';
    let caseCsrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    let caseLoaded = { appeal: { status: 'none', line: '' }, canAdd: false };

    async function openModifyReasonModal(e) {
        const btn = e.currentTarget;
        const type = btn.dataset.type;
        const id = btn.dataset.id;

        document.getElementById('modify-type').value = type;
        document.getElementById('modify-id').value = id;
        document.getElementById('modify-player-name').textContent = btn.dataset.player;
        const reasonInput = document.getElementById('modify-reason-input');
        reasonInput.value = btn.dataset.reason;
        reasonInput.dataset.original = btn.dataset.reason;
        document.getElementById('modify-report').value = '';
        document.getElementById('modify-public').checked = false;
        document.getElementById('modify-files').value = '';
        document.querySelector('#modify-case-form [data-sn-caption-list]').textContent = '';
        document.getElementById('modify-open-case').href = `<?= url('detail') ?>?type=${type}&id=${id}#case-evidence`;

        const existing = document.getElementById('modify-evidence-existing');
        existing.innerHTML = '<span class="text-muted"><span class="spinner-border spinner-border-sm"></span></span>';
        new bootstrap.Modal(document.getElementById('modifyReasonModal')).show();

        try {
            const response = await fetch(`${CASE_EVIDENCE_URL}?type=${encodeURIComponent(type)}&id=${encodeURIComponent(id)}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await response.json();
            if (!data.success) throw new Error(data.error || 'Failed to load');
            caseCsrf = data.csrf || caseCsrf;
            caseLoaded = { appeal: data.appeal || { status: 'none', line: '' }, canAdd: !!data.can_add };
            document.getElementById('modify-appeal-status').value = caseLoaded.appeal.status || 'none';
            document.getElementById('modify-appeal-line').value = caseLoaded.appeal.line || '';
            document.getElementById('modify-evidence-add').style.display = data.can_add ? '' : 'none';
            existing.innerHTML = data.notes.length ? data.notes.map(n => `
                <div class="border rounded p-2 mb-2">
                    <div class="text-muted"><i class="fas fa-user-shield"></i> ${escapeHtml(n.author)}, ${escapeHtml(n.created)}</div>
                    <div style="white-space: pre-wrap">${escapeHtml(n.text.length > 240 ? n.text.substring(0, 240) + '...' : n.text)}</div>
                    ${n.attachments.map(a => `<span class="badge bg-secondary me-1"><i class="fas ${a.kind === 'image' ? 'fa-image' : 'fa-film'}"></i> ${escapeHtml(a.caption || a.name)}</span>`).join('')}
                </div>`).join('') : `<span class="text-muted">${escapeHtml(SN_LABELS.empty || '')}</span>`;
        } catch (error) {
            existing.innerHTML = `<span class="text-danger">${escapeHtml(error.message)}</span>`;
        }
    }

    async function postCaseEvidence(fields, files) {
        const body = new FormData();
        Object.entries(fields).forEach(([key, value]) => body.append(key, value));
        files.forEach(file => body.append('evidence[]', file));
        document.querySelectorAll('#modify-case-form [name="caption[]"]').forEach(input => body.append('caption[]', input.value));
        const response = await fetch(CASE_EVIDENCE_URL, { method: 'POST', body, headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const text = await response.text();
        let result;
        try {
            result = JSON.parse(text);
        } catch (e) {
            throw new Error('HTTP ' + response.status + ': ' + text.replace(/<[^>]*>/g, ' ').trim().substring(0, 160));
        }
        if (!result.ok) throw new Error(result.message || result.error || 'Failed to save');
    }

    // Save the reason (LiteBans database) and the case evidence (internal storage)
    document.getElementById('save-modified-reason')?.addEventListener('click', async function() {
        const type = document.getElementById('modify-type').value;
        const id = document.getElementById('modify-id').value;
        const reasonInput = document.getElementById('modify-reason-input');
        const newReason = reasonInput.value.trim();
        const report = document.getElementById('modify-report').value.trim();
        const files = Array.from(document.getElementById('modify-files').files);
        const appealStatus = document.getElementById('modify-appeal-status').value;
        const appealLine = document.getElementById('modify-appeal-line').value.trim();

        if (!type || !['ban', 'mute', 'warning', 'kick'].includes(type)) {
            alert('Invalid punishment type');
            return;
        }
        if (!id || isNaN(parseInt(id))) {
            alert('Invalid punishment ID');
            return;
        }
        if (!newReason) {
            alert('Please enter a reason');
            return;
        }
        if (files.length && !report) {
            document.getElementById('modify-report').focus();
            alert(<?= json_encode(function_exists('sn_t') ? sn_t('e_text', 5000) : 'Write what happened.', JSON_UNESCAPED_UNICODE) ?>);
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';

        try {
            if (newReason !== (reasonInput.dataset.original || '')) {
                const response = await fetch('<?= url('admin/modify-reason') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: JSON.stringify({ type, id: parseInt(id), reason: newReason })
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.error || 'Failed to update reason');
            }

            if (caseLoaded.canAdd) {
                const base = { type, id, csrf_token: caseCsrf };
                if (appealStatus !== (caseLoaded.appeal.status || 'none') || appealLine !== (caseLoaded.appeal.line || '')) {
                    await postCaseEvidence({ ...base, action: 'appeal', status: appealStatus, line: appealLine }, []);
                }
                if (report) {
                    await postCaseEvidence({ ...base, action: 'add', text: report, public: document.getElementById('modify-public').checked ? '1' : '0' }, files);
                }
            }

            bootstrap.Modal.getInstance(document.getElementById('modifyReasonModal')).hide();
            // Refresh search results
            document.getElementById('admin-search-form').dispatchEvent(new Event('submit'));
        } catch (error) {
            console.error('Modify case error:', error);
            alert('Error: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

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
    
    function escapeAttr(text) {
        return String(text)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\n/g, '&#10;')
            .replace(/\r/g, '&#13;');
    }
    
    // Cache Management
    const clearStatsCacheBtn = document.getElementById('clear-stats-cache');
    const clearAllCacheBtn = document.getElementById('clear-all-cache');
    const cacheStatus = document.getElementById('cache-status');
    
    if (clearStatsCacheBtn) {
        clearStatsCacheBtn.addEventListener('click', async function() {
            if (!confirm('Clear statistics cache? This will refresh all stats data.')) return;
            
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Clearing...';
            
            try {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch('<?= url('stats/clear-cache') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    cacheStatus.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> Statistics cache cleared successfully!</div>';
                    setTimeout(() => cacheStatus.innerHTML = '', 3000);
                } else {
                    throw new Error(result.message || 'Failed to clear cache');
                }
            } catch (error) {
                cacheStatus.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: ' + error.message + '</div>';
            } finally {
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }
    
    if (clearAllCacheBtn) {
        clearAllCacheBtn.addEventListener('click', async function() {
            if (!confirm('Clear ALL cache? This will reset all cached data and may temporarily slow down the site.')) return;
            
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Clearing...';
            
            try {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                formData.append('clear_all', '1');
                
                const response = await fetch('<?= url('stats/clear-cache') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    cacheStatus.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> All cache cleared successfully!</div>';
                    setTimeout(() => cacheStatus.innerHTML = '', 5000);
                } else {
                    throw new Error(result.message || 'Failed to clear cache');
                }
            } catch (error) {
                cacheStatus.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: ' + error.message + '</div>';
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }

    // Database Diagnostic
    const testDatabaseBtn = document.getElementById('test-database');
    const clearOpcacheBtn = document.getElementById('clear-opcache');
    const databaseTestResults = document.getElementById('database-test-results');
    
    if (testDatabaseBtn) {
        testDatabaseBtn.addEventListener('click', async function() {
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Testing...';
            
            try {
                const response = await fetch('<?= url('admin/test-database') ?>');
                const result = await response.json();
                
                if (result.success) {
                    let html = '<div class="card mt-3"><div class="card-body">';
                    html += '<h6 class="text-success"><i class="fas fa-check-circle"></i> Database Test Results</h6>';
                    
                    // Tables status
                    html += '<h6 class="mt-3">Tables Status:</h6>';
                    html += '<table class="table table-sm table-bordered">';
                    html += '<thead><tr><th>Table</th><th>Status</th><th>Time Column</th><th>Until Column</th><th>Columns</th></tr></thead><tbody>';
                    
                    for (const [tableName, tableInfo] of Object.entries(result.tables)) {
                        const statusBadge = tableInfo.status === 'ok' ? 'success' : 'danger';
                        html += '<tr>';
                        html += '<td>' + escapeHtml(tableName) + '</td>';
                        html += '<td><span class="badge bg-' + statusBadge + '">' + escapeHtml(tableInfo.status) + '</span></td>';
                        html += '<td>' + (tableInfo.has_time ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') + '</td>';
                        html += '<td>' + (tableInfo.has_until ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-danger"></i>') + '</td>';
                        html += '<td>' + (tableInfo.columns || 'N/A') + '</td>';
                        html += '</tr>';
                    }
                    html += '</tbody></table>';
                    
                    // Timestamp tests
                    if (Object.keys(result.timestamp_test).length > 0) {
                        html += '<h6 class="mt-3">Timestamp Tests (Latest Records):</h6>';
                        html += '<table class="table table-sm table-bordered">';
                        html += '<thead><tr><th>Table</th><th>Raw Time</th><th>Time (Seconds)</th><th>Date</th><th>Until Date</th><th>Valid</th></tr></thead><tbody>';
                        
                        for (const [tableName, timestampInfo] of Object.entries(result.timestamp_test)) {
                            const validBadge = timestampInfo.is_valid ? 'success' : 'danger';
                            html += '<tr>';
                            html += '<td>' + escapeHtml(tableName) + '</td>';
                            html += '<td><code>' + timestampInfo.raw_time + '</code></td>';
                            html += '<td><code>' + timestampInfo.time_seconds + '</code></td>';
                            html += '<td>' + escapeHtml(timestampInfo.time_date) + '</td>';
                            html += '<td>' + escapeHtml(timestampInfo.until_date) + '</td>';
                            html += '<td><span class="badge bg-' + validBadge + '">' + (timestampInfo.is_valid ? 'Valid' : 'Invalid') + '</span></td>';
                            html += '</tr>';
                        }
                        html += '</tbody></table>';
                    }
                    
                    // Server info
                    if (result.server_info) {
                        html += '<h6 class="mt-3">Server Time Info:</h6>';
                        html += '<table class="table table-sm table-bordered">';
                        html += '<tbody>';
                        html += '<tr><td><strong>PHP Time (seconds)</strong></td><td>' + result.server_info.php_time + '</td></tr>';
                        html += '<tr><td><strong>PHP Time (milliseconds)</strong></td><td>' + result.server_info.php_time_ms + '</td></tr>';
                        html += '<tr><td><strong>Current Date</strong></td><td>' + escapeHtml(result.server_info.php_date) + '</td></tr>';
                        html += '<tr><td><strong>Timezone</strong></td><td>' + escapeHtml(result.server_info.timezone) + '</td></tr>';
                        html += '</tbody></table>';
                    }
                    
                    // Warnings
                    if (result.warnings && result.warnings.length > 0) {
                        html += '<div class="alert alert-warning mt-3"><h6><i class="fas fa-exclamation-triangle"></i> Warnings:</h6><ul class="mb-0">';
                        result.warnings.forEach(warning => {
                            html += '<li>' + escapeHtml(warning) + '</li>';
                        });
                        html += '</ul></div>';
                    }
                    
                    html += '</div></div>';
                    databaseTestResults.innerHTML = html;
                } else {
                    throw new Error(result.error || 'Test failed');
                }
            } catch (error) {
                databaseTestResults.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: ' + escapeHtml(error.message) + '</div>';
            } finally {
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }
    
    if (clearOpcacheBtn) {
        clearOpcacheBtn.addEventListener('click', async function() {
            if (!confirm('Clear OPcache and reload configuration? This will apply any changes made to .env file.')) return;
            
            const originalText = this.innerHTML;
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Clearing...';
            
            try {
                const formData = new FormData();
                formData.append('csrf_token', csrfToken);
                
                const response = await fetch('<?= url('admin/clear-all-cache') ?>', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    databaseTestResults.innerHTML = '<div class="alert alert-success"><i class="fas fa-check"></i> ' + 
                        escapeHtml(result.message) + '<br><small>Cleared: ' + result.cleared.join(', ') + '</small></div>';
                    showReloadNotice('Cache cleared. Reload the page to see fresh data.');
                } else {
                    throw new Error(result.error || 'Failed to clear cache');
                }
            } catch (error) {
                databaseTestResults.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Error: ' + escapeHtml(error.message) + '</div>';
            } finally {
                this.disabled = false;
                this.innerHTML = originalText;
            }
        });
    }

    // ========================
    // USER MANAGEMENT
    // ========================
    
    const usersList = document.getElementById('users-list');
    
    // Load users when Users tab is shown
    const usersTab = document.getElementById('users-tab');
    if (usersTab) {
        usersTab.addEventListener('shown.bs.tab', loadUsers);
        // Also load if tab is already active
        if (usersTab.classList.contains('active')) {
            loadUsers();
        }
    }
    
    async function loadUsers() {
        if (!usersList) return;
        
        usersList.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary"></div></div>';
        
        try {
            const response = await fetch('<?= url('admin/users') ?>', {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const result = await response.json();
            
            if (result.success) {
                renderUsers(result.users);
            } else {
                throw new Error(result.error || 'Failed to load users');
            }
        } catch (error) {
            usersList.innerHTML = `<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> ${escapeHtml(error.message)}</div>`;
        }
    }
    
    function renderUsers(users) {
        if (!users || users.length === 0) {
            usersList.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No users found. Add users to grant access to the admin panel.</div>';
            return;
        }
        
        let html = '<div class="table-responsive"><table class="table table-hover">';
        html += '<thead><tr><th>User</th><th>Sign-in</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead><tbody>';
        
        users.forEach(user => {
            const roleColors = { admin: 'danger', moderator: 'warning', viewer: 'info' };
            const roleBadge = `<span class="badge bg-${roleColors[user.role] || 'secondary'}">${escapeHtml(user.role)}</span>`;
            const statusBadge = user.active !== false 
                ? '<span class="badge bg-success">Active</span>' 
                : '<span class="badge bg-secondary">Inactive</span>';
            const lastLogin = user.last_login ? new Date(user.last_login * 1000).toLocaleString() : 'Never';
            const avatar = user.picture 
                ? `<img src="${escapeHtml(user.picture)}" class="rounded-circle me-2" style="width: 32px; height: 32px;">` 
                : '<i class="fas fa-user-circle fa-2x me-2 text-muted"></i>';
            
            html += `<tr>
                <td>
                    <div class="d-flex align-items-center">
                        ${avatar}
                        <span>${escapeHtml(user.name || user.email || user.discord_id || '')}</span>
                    </div>
                </td>
                <td>${escapeHtml(user.email || '')}${user.email && user.discord_id ? '<br>' : ''}${user.discord_id ? '<small class="text-muted"><i class="fab fa-discord"></i> ' + escapeHtml(user.discord_id) + '</small>' : ''}</td>
                <td>${roleBadge}</td>
                <td>${statusBadge}</td>
                <td><small class="text-muted">${lastLogin}</small></td>
                <td>
                    <button class="btn btn-sm btn-outline-warning edit-user-btn" 
                            data-id="${escapeHtml(user.id)}"
                            data-email="${escapeAttr(user.email || user.discord_id || '')}"
                            data-name="${escapeAttr(user.name || '')}"
                            data-role="${escapeAttr(user.role)}"
                            data-active="${user.active !== false ? '1' : '0'}">
                        <i class="fas fa-edit"></i>
                    </button>
                </td>
            </tr>`;
        });
        
        html += '</tbody></table></div>';
        usersList.innerHTML = html;
        
        // Add edit handlers
        document.querySelectorAll('.edit-user-btn').forEach(btn => {
            btn.addEventListener('click', openEditUserModal);
        });
    }
    
    function openEditUserModal(e) {
        const btn = e.currentTarget;
        document.getElementById('edit-user-id').value = btn.dataset.id;
        document.getElementById('edit-user-email').value = btn.dataset.email;
        document.getElementById('edit-user-name').value = btn.dataset.name;
        document.getElementById('edit-user-role').value = btn.dataset.role;
        document.getElementById('edit-user-active').checked = btn.dataset.active === '1';
        
        const modal = new bootstrap.Modal(document.getElementById('editUserModal'));
        modal.show();
    }
    
    // Add new user
    document.getElementById('save-new-user')?.addEventListener('click', async function() {
        const email = document.getElementById('add-user-email').value.trim();
        const discordId = document.getElementById('add-user-discord').value.trim();
        const name = document.getElementById('add-user-name').value.trim();
        const role = document.getElementById('add-user-role').value;
        
        if (!email && !discordId) {
            alert('Enter an email or a Discord ID');
            return;
        }
        
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Adding...';
        
        try {
            const response = await fetch('<?= url('admin/users/add') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ email, discord_id: discordId, name, role })
            });
            
            const result = await response.json();
            
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                document.getElementById('add-user-email').value = '';
                document.getElementById('add-user-discord').value = '';
                document.getElementById('add-user-name').value = '';
                document.getElementById('add-user-role').value = 'viewer';
                loadUsers();
            } else {
                throw new Error(result.error || 'Failed to add user');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    
    // Save edited user
    document.getElementById('save-edit-user')?.addEventListener('click', async function() {
        const id = document.getElementById('edit-user-id').value;
        const name = document.getElementById('edit-user-name').value.trim();
        const role = document.getElementById('edit-user-role').value;
        const active = document.getElementById('edit-user-active').checked;
        
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
        
        try {
            const response = await fetch('<?= url('admin/users/update') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id, name, role, active })
            });
            
            const result = await response.json();
            
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                loadUsers();
            } else {
                throw new Error(result.error || 'Failed to update user');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });
    
    // Delete user
    document.getElementById('delete-user-btn')?.addEventListener('click', async function() {
        const id = document.getElementById('edit-user-id').value;
        const email = document.getElementById('edit-user-email').value;
        
        if (!confirm(`Are you sure you want to delete user ${email}? This cannot be undone.`)) return;
        
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Deleting...';
        
        try {
            const response = await fetch('<?= url('admin/users/delete') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ id })
            });
            
            const result = await response.json();
            
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('editUserModal')).hide();
                loadUsers();
            } else {
                throw new Error(result.error || 'Failed to delete user');
            }
        } catch (error) {
            alert('Error: ' + error.message);
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    });

    // Session keep-alive mechanism
    // Ping server every 5 minutes to keep session alive
    setInterval(async () => {
        try {
            const response = await fetch('<?= url('admin/keep-alive') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            const result = await response.json();
            
            // If not authenticated, redirect to admin login
            if (!result.authenticated) {
                console.log('Session expired, redirecting to login...');
                window.location.href = '<?= url('admin') ?>';
            }
        } catch (error) {
            console.error('Keep-alive error:', error);
            // Don't reload on network errors, just log them
        }
    }, 5 * 60 * 1000); // Run every 5 minutes

});
</script>
