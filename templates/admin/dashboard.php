<?php
/**
 * ============================================================================
 *  LiteBansU - Administration
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.2
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
        <?php if (($config['google_auth_enabled'] ?? false) && ($currentUser['role'] ?? '') === 'admin'): ?>
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
      <li class="nav-item" role="presentation"> <a class="nav-link" href="demos/"> <i class="fas fa-video"></i> Demo Management </a> </li>
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
                                    <td class="text-muted">OS Version</td>
                                    <td class="admin-table-text"><?= PHP_OS ?></td>
                                </tr>
                                <tr>
                                    <td class="text-muted">Web Server</td>
                                    <td class="admin-table-text"><i class="fas fa-server text-success"></i> Web Server</td>
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
                        
                        <!-- Authentication Settings -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fas fa-key"></i> Authentication Settings</h6>
                        <div class="alert alert-info mb-3">
                            <i class="fas fa-info-circle"></i>
                            <strong>Google OAuth:</strong> When enabled, admins sign in with their Google account. 
                            The first user to sign in becomes administrator. You can manage users in the "Users" tab.
                            <br><small>Get credentials from: <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a></small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="google_auth_enabled" 
                                           id="google_auth_enabled" <?= ($config['google_auth_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="google_auth_enabled">
                                        Enable Google Authentication
                                    </label>
                                    <small class="form-text text-muted d-block">When disabled, password login is used</small>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="google-auth-fields">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Google Client ID</label>
                                    <input type="text" class="form-control" name="google_client_id" 
                                           value="<?= htmlspecialchars($config['google_client_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="xxxx.apps.googleusercontent.com">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Google Client Secret</label>
                                    <input type="password" class="form-control" name="google_client_secret" 
                                           value="<?= htmlspecialchars($config['google_client_secret'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="GOCSPX-xxxx">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="allow_password_login" 
                                           id="allow_password_login" <?= ($config['allow_password_login'] ?? true) ? 'checked' : '' ?>>
                                    <label class="form-check-label text-danger fw-bold" for="allow_password_login">
                                        <i class="fas fa-exclamation-triangle"></i> Allow Password Login (Fallback)
                                    </label>
                                    <small class="form-text text-danger d-block">
                                        <strong>Warning:</strong> When disabled, only Google authentication will work. 
                                        Make sure Google OAuth is properly configured before disabling!
                                    </small>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-secondary">
                                    <strong>Redirect URI:</strong> 
                                    <code><?= htmlspecialchars(rtrim($config['site_url'] ?? '', '/') . ($config['base_path'] ?? '') . '/admin/oauth-callback', ENT_QUOTES, 'UTF-8') ?></code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent)">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Discord OAuth Settings -->
                        <hr class="my-4">
                        <h6 class="mb-3"><i class="fab fa-discord"></i> Discord OAuth Settings</h6>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>Discord OAuth:</strong> Alternative to Google OAuth. Admins can sign in with their Discord account.
                            <br><small>Get credentials from: <a href="https://discord.com/developers/applications" target="_blank">Discord Developer Portal</a></small>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" name="discord_auth_enabled" 
                                           id="discord_auth_enabled" <?= ($config['discord_auth_enabled'] ?? false) ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="discord_auth_enabled">
                                        Enable Discord Authentication
                                    </label>
                                    <small class="form-text text-muted d-block">Can be used alongside Google OAuth</small>
                                </div>
                            </div>
                        </div>
                        <div class="row" id="discord-auth-fields">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discord Client ID</label>
                                    <input type="text" class="form-control" name="discord_client_id" 
                                           value="<?= htmlspecialchars($config['discord_client_id'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="1234567890123456789">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Discord Client Secret</label>
                                    <input type="password" class="form-control" name="discord_client_secret" 
                                           value="<?= htmlspecialchars($config['discord_client_secret'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="xxxx-xxxx-xxxx">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="alert alert-secondary">
                                    <strong>Redirect URI:</strong> 
                                    <code><?= htmlspecialchars(rtrim($config['site_url'] ?? '', '/') . ($config['base_path'] ?? '') . '/admin/oauth-callback?provider=discord', ENT_QUOTES, 'UTF-8') ?></code>
                                    <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="navigator.clipboard.writeText(this.previousElementSibling.textContent)">
                                        <i class="fas fa-copy"></i>
                                    </button>
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
                                    <label class="form-label">Avatar Provider</label>
                                    <select class="form-control" name="avatar_provider">
                                        <option value="crafatar" <?= ($config['avatar_provider'] ?? 'crafatar') === 'crafatar' ? 'selected' : '' ?>>Crafatar</option>
                                        <option value="cravatar" <?= ($config['avatar_provider'] ?? 'crafatar') === 'cravatar' ? 'selected' : '' ?>>Cravatar</option>
                                        <option value="custom" <?= ($config['avatar_provider'] ?? 'crafatar') === 'custom' ? 'selected' : '' ?>>Custom</option>
                                    </select>
                                    <small class="form-text text-muted">Avatar service provider</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Avatar URL (Online)</label>
                                    <input type="text" class="form-control" name="avatar_url" 
                                           value="<?= htmlspecialchars($config['avatar_url'] ?? 'https://crafatar.com/avatars/{uuid}?size=64&overlay=true', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="https://crafatar.com/avatars/{uuid}?size=64&overlay=true">
                                    <small class="form-text text-muted">URL for online mode avatars. Use {uuid} placeholder</small>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Avatar URL (Offline)</label>
                                    <input type="text" class="form-control" name="avatar_url_offline" 
                                           value="<?= htmlspecialchars($config['avatar_url_offline'] ?? 'https://crafatar.com/avatars/{uuid}?size=64', ENT_QUOTES, 'UTF-8') ?>"
                                           placeholder="https://crafatar.com/avatars/{uuid}?size=64">
                                    <small class="form-text text-muted">URL for offline mode avatars. Use {uuid} or {name} placeholder</small>
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
                </div>
            </div>
        </div>

        <?php if (($config['google_auth_enabled'] ?? false) && ($currentUser['role'] ?? '') === 'admin'): ?>
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
                        Users must sign in with Google to activate their account. Add their email address here to grant access.
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

<!-- Modify Reason Modal -->
<div class="modal fade" id="modifyReasonModal" tabindex="-1" aria-labelledby="modifyReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modifyReasonModalLabel">
                    <i class="fas fa-edit text-warning"></i> Modify Reason
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modify-type">
                <input type="hidden" id="modify-id">
                <div class="mb-3">
                    <label class="form-label">Player</label>
                    <div class="form-control bg-secondary" id="modify-player-name"></div>
                </div>
                <div class="mb-3">
                    <label class="form-label">New Reason</label>
                    <textarea class="form-control" id="modify-reason-input" rows="3" placeholder="Enter new reason..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
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
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="add-user-email" placeholder="user@example.com" required>
                    <small class="text-muted">User must sign in with this Google email</small>
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
    
    // Toggle Discord OAuth fields visibility
    const discordAuthEnabled = document.getElementById('discord_auth_enabled');
    const discordAuthFields = document.getElementById('discord-auth-fields');
    if (discordAuthEnabled && discordAuthFields) {
        function toggleDiscordFields() {
            discordAuthFields.style.display = discordAuthEnabled.checked ? 'block' : 'none';
        }
        toggleDiscordFields();
        discordAuthEnabled.addEventListener('change', toggleDiscordFields);
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
    
    // Open Modify Reason Modal
    function openModifyReasonModal(e) {
        const btn = e.currentTarget;
        const type = btn.dataset.type;
        const id = btn.dataset.id;
        const playerName = btn.dataset.player;
        const currentReason = btn.dataset.reason;
        
        document.getElementById('modify-type').value = type;
        document.getElementById('modify-id').value = id;
        document.getElementById('modify-player-name').textContent = playerName;
        document.getElementById('modify-reason-input').value = currentReason;
        
        const modal = new bootstrap.Modal(document.getElementById('modifyReasonModal'));
        modal.show();
    }
    
    // Save Modified Reason
    document.getElementById('save-modified-reason')?.addEventListener('click', async function() {
        const type = document.getElementById('modify-type').value;
        const id = document.getElementById('modify-id').value;
        const newReason = document.getElementById('modify-reason-input').value.trim();
        
        if (!newReason) {
            alert('Please enter a reason');
            return;
        }
        
        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Saving...';
        
        try {
            const response = await fetch('<?= url('admin/modify-reason') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ type, id: parseInt(id), reason: newReason })
            });
            
            const result = await response.json();
            
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('modifyReasonModal')).hide();
                alert('Reason updated successfully!');
                // Refresh search results
                document.getElementById('admin-search-form').dispatchEvent(new Event('submit'));
            } else {
                throw new Error(result.error || 'Failed to update reason');
            }
        } catch (error) {
            console.error('Modify reason error:', error);
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
        html += '<thead><tr><th>User</th><th>Email</th><th>Role</th><th>Status</th><th>Last Login</th><th>Actions</th></tr></thead><tbody>';
        
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
                        <span>${escapeHtml(user.name || user.email)}</span>
                    </div>
                </td>
                <td>${escapeHtml(user.email)}</td>
                <td>${roleBadge}</td>
                <td>${statusBadge}</td>
                <td><small class="text-muted">${lastLogin}</small></td>
                <td>
                    <button class="btn btn-sm btn-outline-warning edit-user-btn" 
                            data-id="${escapeHtml(user.id)}"
                            data-email="${escapeAttr(user.email)}"
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
        const name = document.getElementById('add-user-name').value.trim();
        const role = document.getElementById('add-user-role').value;
        
        if (!email) {
            alert('Email is required');
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
                body: JSON.stringify({ email, name, role })
            });
            
            const result = await response.json();
            
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('addUserModal')).hide();
                document.getElementById('add-user-email').value = '';
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
