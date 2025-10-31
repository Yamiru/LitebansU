/**
 * ============================================================================
 * LiteBansU
 * ============================================================================
 *
 * Plugin Name: LiteBansU
 * Description: A modern, secure, and responsive web interface for LiteBans punishment management system.
 * Version: 2.3
 * Market URI: https://builtbybit.com/resources/litebansu-litebans-website.69448/
 * Author URI: https://yamiru.com
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 * Repository https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */
class LiteBansUI {
    constructor() {
        this.basePath = this.getBasePath();
        this.csrfToken = this.getCsrfToken();
        this.debounceTimer = null;
        this.searchCache = new Map();
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initialize());
        } else {
            this.initialize();
        }
    }

    initialize() {
        // Core functionality
        this.setupThemeSwitcher();
        this.setupLanguageSwitcher();
        this.setupSearch();
        this.setupMobileMenu();
        this.setupScrollEffects();
        this.setupTooltips();
        this.setupDetailPageFeatures();
        this.setupClearCache();
        this.setupModals();
        this.setupTables();
        
        // Initialize Bootstrap components
        this.initializeBootstrapComponents();
    }

    getBasePath() {
        const metaBasePath = document.querySelector('meta[name="base-path"]');
        if (metaBasePath) {
            let path = metaBasePath.getAttribute('content') || '';
            return path.replace(/\/$/, '');
        }
        return '';
    }

    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    setupThemeSwitcher() {
        const toggle = document.getElementById('theme-toggle');
        if (!toggle) return;

        toggle.addEventListener('change', (e) => {
            e.preventDefault();
            const theme = e.target.checked ? 'dark' : 'light';
            const url = new URL(window.location.href);
            url.searchParams.set('theme', theme);
            window.location.href = url.toString();
        });
    }

    setupLanguageSwitcher() {
        const dropdownItems = document.querySelectorAll('#langDropdown + .dropdown-menu .dropdown-item');
        if (dropdownItems.length === 0) return;

        dropdownItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const lang = e.currentTarget.getAttribute('href').match(/lang=(\w+)/);
                if (lang && lang[1]) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('lang', lang[1]);
                    window.location.href = url.toString();
                }
            });
        });
    }

    // =====================================================================
    // ZMENA 1: PÃ´vodnÃ¡ logika z form.addEventListener('submit') je presunutÃ¡ 
    // do tejto novej metÃ³dy, aby ju bolo moÅ¾nÃ© volaÅ¥ priamo.
    // =====================================================================
    async performSearch() {
        const input = document.getElementById('search-input');
        const results = document.getElementById('search-results');

        if (!input || !results) return;

        const query = input.value.trim();
        if (!query || query.length < 2) {
            results.innerHTML = '<div class="alert alert-warning"><i class="fas fa-exclamation-triangle"></i> Please enter at least 2 characters</div>';
            return;
        }

        if (this.searchCache.has(query)) {
            this.displaySearchResults(this.searchCache.get(query), results);
            return;
        }

        try {
            results.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Searching...</span></div></div>';
            
            const response = await this.fetchSearch(query);
            
            if (response.success) {
                this.searchCache.set(query, response);
                setTimeout(() => this.searchCache.delete(query), 300000);
            }
            
            this.displaySearchResults(response, results);
        } catch (error) {
            console.error('Search error:', error);
            results.innerHTML = `<div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle"></i> 
                    ${this.escapeHtml(error.message || 'An error occurred while searching. Please try again.')}
                </div>`;
        }
    }


    setupSearch() {
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
        const results = document.getElementById('search-results');

        if (!form || !input || !results) return;

        // Prevent zoom on iOS when focusing input
        input.addEventListener('focus', (e) => {
            if (window.innerWidth <= 768) {
                e.target.setAttribute('style', 'font-size: 16px !important');
            }
        });

        input.addEventListener('blur', (e) => {
            e.target.removeAttribute('style');
        });

        // =====================================================================
        // ZMENA 2: Nahradenie celej pÃ´vodnej logiky volanÃ­m novej metÃ³dy performSearch()
        // =====================================================================
        form.addEventListener('submit', (e) => {
            e.preventDefault(); // ZastavÃ­ Å¡tandardnÃ© odoslanie formulÃ¡ra
            this.performSearch();
        });


        // Auto-search with debounce
        input.addEventListener('input', () => {
            clearTimeout(this.debounceTimer);
            if (input.value.length >= 2) {
                this.debounceTimer = setTimeout(() => {
                    // PÃ´vodnÃ©: form.dispatchEvent(new Event('submit'));
                    // NovÃ©: Priame volanie, ktorÃ© Firefox nezablokuje
                    this.performSearch(); 
                }, 500);
            } else if (input.value.length === 0) {
                results.innerHTML = '';
            }
        });
        // =====================================================================

        // Clear search on ESC
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                input.value = '';
                results.innerHTML = '';
                input.blur(); // Remove focus on mobile
            }
        });
    }

    async fetchSearch(query) {
        const formData = new FormData();
        formData.append('query', query);
        formData.append('csrf_token', this.csrfToken);

        const searchUrl = this.basePath + '/search';
        
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 10000);

        try {
            const response = await fetch(searchUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                signal: controller.signal
            });

            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error(`Server error: ${response.status}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Invalid response format from server');
            }

            const data = await response.json();
            
            if (!data || typeof data !== 'object') {
                throw new Error('Invalid response data');
            }

            if (data.error) {
                throw new Error(data.error);
            }

            return data;
        } catch (error) {
            if (error.name === 'AbortError') {
                throw new Error('Request timeout. Please check your connection and try again.');
            }
            throw error;
        }
    }

    displaySearchResults(data, container) {
        if (!data.success) {
            container.innerHTML = `<div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i> 
                ${this.escapeHtml(data.error || 'Search failed. Please try again.')}
            </div>`;
            return;
        }

        if (!data.punishments || data.punishments.length === 0) {
            container.innerHTML = '<div class="alert alert-info"><i class="fas fa-info-circle"></i> No punishments found for this player.</div>';
            return;
        }

        // Mobile-friendly display for phones
        if (window.innerWidth <= 768) {
            const html = `
                <div class="search-results fade-in">
                    <h4 class="mb-3">Results for: <strong>${this.escapeHtml(data.player)}</strong></h4>
                    <div class="mobile-search-results">
                        ${data.punishments.map(p => this.renderMobilePunishmentCard(p)).join('')}
                    </div>
                </div>
            `;
            container.innerHTML = html;
        } else {
            const html = `
                <div class="search-results fade-in">
                    <h4 class="mb-3">Results for: <strong>${this.escapeHtml(data.player)}</strong></h4>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Reason</th>
                                    <th>Staff</th>
                                    <th>Date</th>
                                    <th>Expires</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.punishments.map(p => this.renderPunishmentRow(p)).join('')}
                            </tbody>
                        </table>
                    </div>
                </div>
            `;
            container.innerHTML = html;
        }
        
        this.fadeIn(container.querySelector('.search-results'));
        
        // Add click handlers for punishment rows
        this.setupPunishmentRowClicks();
    }

    renderMobilePunishmentCard(punishment) {
        const statusClass = punishment.active ? 'status-active' : 'status-inactive';
        const statusText = punishment.active ? 'Active' : 'Inactive';
        
        const typeClass = {
            'ban': 'bg-danger',
            'bans': 'bg-danger',
            'mute': 'bg-warning',
            'mutes': 'bg-warning',
            'warning': 'bg-info',
            'warnings': 'bg-info',
            'kick': 'bg-secondary',
            'kicks': 'bg-secondary'
        }[punishment.type] || 'bg-dark';

        const typeText = punishment.type.replace(/s$/, '').toUpperCase();
        const typeSingular = punishment.type.replace(/s$/, '');

        let untilText = '';
        if (punishment.until) {
            if (punishment.until.toLowerCase().includes('permanent')) {
                untilText = '<span class="badge bg-danger">Permanent</span>';
            } else if (punishment.until.toLowerCase().includes('expired')) {
                untilText = '<span class="badge bg-success">Expired</span>';
            } else {
                untilText = `<span class="badge bg-warning">${this.escapeHtml(punishment.until)}</span>`;
            }
        } else if (punishment.type === 'ban' || punishment.type === 'mute') {
            untilText = '<span class="badge bg-danger">Permanent</span>';
        }

        return `
            <div class="search-result-card mb-3 punishment-row" data-type="${typeSingular}" data-id="${punishment.id || ''}" style="cursor: pointer;">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge ${typeClass}">${this.escapeHtml(typeText)}</span>
                            <span class="status-badge ${statusClass}">${this.escapeHtml(statusText)}</span>
                        </div>
                        <div class="mb-2">
                            <strong class="text-muted small">Reason:</strong>
                            <div class="mt-1">${this.escapeHtml(punishment.reason)}</div>
                        </div>
                        <div class="row g-2 small">
                            <div class="col-6">
                                <strong class="text-muted">Staff:</strong><br>
                                <span class="text-primary">${this.escapeHtml(punishment.staff)}</span>
                            </div>
                            <div class="col-6">
                                <strong class="text-muted">Date:</strong><br>
                                ${this.escapeHtml(punishment.date)}
                            </div>
                            ${untilText ? `
                                <div class="col-12 mt-2">
                                    <strong class="text-muted">Expires:</strong> ${untilText}
                                </div>
                            ` : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    renderPunishmentRow(punishment) {
        const statusClass = punishment.active ? 'status-active' : 'status-inactive';
        const statusText = punishment.active ? 'Active' : 'Inactive';
        
        const typeClass = {
            'ban': 'bg-danger',
            'bans': 'bg-danger',
            'mute': 'bg-warning',
            'mutes': 'bg-warning',
            'warning': 'bg-info',
            'warnings': 'bg-info',
            'kick': 'bg-secondary',
            'kicks': 'bg-secondary'
        }[punishment.type] || 'bg-dark';

        const typeText = punishment.type.replace(/s$/, '').toUpperCase();
        const typeSingular = punishment.type.replace(/s$/, '');

        let untilBadge = '';
        if (punishment.until) {
            if (punishment.until.toLowerCase().includes('permanent')) {
                untilBadge = '<span class="badge bg-danger">Permanent</span>';
            } else if (punishment.until.toLowerCase().includes('expired')) {
                untilBadge = '<span class="badge bg-success">Expired</span>';
            } else {
                untilBadge = `<span class="badge bg-warning">${this.escapeHtml(punishment.until)}</span>`;
            }
        } else if (punishment.type === 'ban' || punishment.type === 'mute') {
            untilBadge = '<span class="badge bg-danger">Permanent</span>';
        } else {
            untilBadge = '<span class="text-muted">N/A</span>';
        }

        return `
            <tr class="punishment-row" data-type="${typeSingular}" data-id="${punishment.id || ''}" style="cursor: pointer;">
                <td><span class="badge ${typeClass}">${this.escapeHtml(typeText)}</span></td>
                <td class="text-truncate" style="max-width: 200px;" title="${this.escapeHtml(punishment.reason)}">${this.escapeHtml(punishment.reason)}</td>
                <td>${this.escapeHtml(punishment.staff)}</td>
                <td>${this.escapeHtml(punishment.date)}</td>
                <td>${untilBadge}</td>
                <td><span class="status-badge ${statusClass}">${this.escapeHtml(statusText)}</span></td>
            </tr>
        `;
    }

    setupPunishmentRowClicks() {
        // Use event delegation for dynamically added rows
        document.addEventListener('click', (e) => {
            const row = e.target.closest('.punishment-row');
            if (row) {
                const type = row.dataset.type;
                const id = row.dataset.id;
                
                if (type && id) {
                    const detailUrl = `${this.basePath}/detail?type=${type}&id=${id}`;
                    window.location.href = detailUrl;
                }
            }
        });
    }

    setupMobileMenu() {
        const toggler = document.querySelector('.navbar-toggler');
        const collapse = document.querySelector('.navbar-collapse');
        
        if (!toggler || !collapse) return;
        
        toggler.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            collapse.classList.toggle('show');
            toggler.setAttribute('aria-expanded', collapse.classList.contains('show'));
        });
        
        // Close on outside click
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.navbar-modern') && collapse.classList.contains('show')) {
                collapse.classList.remove('show');
                toggler.setAttribute('aria-expanded', 'false');
            }
        });

        // Close on ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && collapse.classList.contains('show')) {
                collapse.classList.remove('show');
                toggler.setAttribute('aria-expanded', 'false');
            }
        });
    }

    setupScrollEffects() {
        const navbar = document.getElementById('mainNavbar');
        if (!navbar) return;
        
        let lastScroll = 0;
        let ticking = false;

        const updateNavbar = () => {
            const currentScroll = window.pageYOffset;
            
            if (currentScroll > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
            
            lastScroll = currentScroll;
            ticking = false;
        };

        window.addEventListener('scroll', () => {
            if (!ticking) {
                window.requestAnimationFrame(updateNavbar);
                ticking = true;
            }
        });
    }

    setupTooltips() {
        const tooltips = document.querySelectorAll('[title]:not([data-bs-toggle="tooltip"])');
        tooltips.forEach(el => {
            if (!el.closest('.navbar-nav')) { // Skip nav items
                el.setAttribute('data-bs-toggle', 'tooltip');
                el.setAttribute('data-bs-placement', 'top');
            }
        });
    }

    setupDetailPageFeatures() {
        // Auto-refresh for active punishments
        const progressBar = document.querySelector('.progress-bar');
        if (progressBar) {
            // Update progress every minute
            setInterval(() => {
                if (document.visibilityState === 'visible') {
                    const progress = parseFloat(progressBar.style.width);
                    if (progress < 100) {
                        // Could implement real-time progress update here
                        // For now, just reload the page
                        location.reload();
                    }
                }
            }, 60000);
        }

        // Copy UUID on click
        const uuidElements = document.querySelectorAll('.font-monospace');
        uuidElements.forEach(el => {
            if (el.textContent.match(/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i)) {
                el.style.cursor = 'pointer';
                el.addEventListener('click', () => {
                    this.copyToClipboard(el.textContent);
                });
            }
        });
    }

    setupClearCache() {
        const clearCacheBtn = document.getElementById('clear-cache-btn');
        const confirmBtn = document.getElementById('confirm-clear-cache');
        
        if (!clearCacheBtn || !confirmBtn) return;
        
        clearCacheBtn.addEventListener('click', () => {
            const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('cacheModal'));
            modal.show();
        });
        
        confirmBtn.addEventListener('click', async () => {
            const modal = bootstrap.Modal.getInstance(document.getElementById('cacheModal'));
            
            try {
                const formData = new FormData();
                formData.append('csrf_token', this.csrfToken);
                
                const response = await fetch(this.basePath + '/stats/clear-cache', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.success) {
                    modal.hide();
                    this.showNotification('success', data.message || 'Cache cleared successfully');
                    setTimeout(() => location.reload(), 2000);
                } else {
                    throw new Error(data.message || 'Failed to clear cache');
                }
            } catch (error) {
                console.error('Cache clear error:', error);
                this.showNotification('danger', error.message);
                modal.hide();
            }
        });
    }

    setupModals() {
        // Handle all modal close buttons
        document.querySelectorAll('.modal .btn-close, [data-bs-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal');
                if (modal) {
                    const bsModal = bootstrap.Modal.getInstance(modal);
                    if (bsModal) bsModal.hide();
                }
            });
        });
    }

    setupTables() {
        // Make table rows clickable
        document.querySelectorAll('.table tbody tr[onclick]').forEach(row => {
            row.style.cursor = 'pointer';
            
            // Prevent navigation when clicking on buttons/links
            row.querySelectorAll('a, button').forEach(el => {
                el.addEventListener('click', (e) => {
                    e.stopPropagation();
                });
            });
        });

        // Sort tables (if needed in future)
        this.setupTableSort();
    }

    setupTableSort() {
        // Placeholder for table sorting functionality
        // Can be implemented later if needed
    }

    initializeBootstrapComponents() {
        // Initialize all Bootstrap tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Initialize all Bootstrap popovers
        const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
        popoverTriggerList.map(function (popoverTriggerEl) {
            return new bootstrap.Popover(popoverTriggerEl);
        });
    }

    // Utility functions
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    fadeIn(element) {
        if (!element) return;
        element.style.opacity = '0';
        element.style.transition = 'opacity 300ms ease-in-out';
        
        setTimeout(() => {
            element.style.opacity = '1';
        }, 10);
    }

    copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                this.showNotification('success', 'Copied to clipboard!');
            }).catch(() => {
                this.fallbackCopy(text);
            });
        } else {
            this.fallbackCopy(text);
        }
    }

    fallbackCopy(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.opacity = '0';
        document.body.appendChild(textArea);
        textArea.select();
        
        try {
            document.execCommand('copy');
            this.showNotification('success', 'Copied to clipboard!');
        } catch (err) {
            this.showNotification('danger', 'Failed to copy');
        }
        
        document.body.removeChild(textArea);
    }

    showNotification(type, message) {
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = '9999';
        alert.style.minWidth = '250px';
        
        const icon = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        
        alert.innerHTML = `
            <i class="fas fa-${icon}"></i> ${this.escapeHtml(message)}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        document.body.appendChild(alert);
        
        // Auto dismiss after 3 seconds
        setTimeout(() => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 150);
        }, 3000);
    }

    // Public API methods
    refresh() {
        location.reload();
    }

    navigateTo(url) {
        window.location.href = this.basePath + url;
    }
}

// Initialize the UI when DOM is ready
const LiteBansU = new LiteBansUI();

// Expose to global scope for external access
window.LiteBansU = LiteBansU;
