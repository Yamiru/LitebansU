//
// ============================================================================
//  LiteBansU
// ============================================================================
//
//  Plugin Name:   LiteBansU
//  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
//  Version:       1.0
//  Author:        Yamiru <yamiru@yamiru.com>
//  Author URI:    https://yamiru.com
//  License:       MIT
//  License URI:   https://opensource.org/licenses/MIT
//  Repository    https://github.com/Yamiru/LitebansU/
// ============================================================================
//

class LiteBansUI {
    constructor() {
        this.basePath = this.getBasePath();
        this.csrfToken = this.getCsrfToken();
        this.debounceTimer = null;
        this.init();
    }

    init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.initialize());
        } else {
            this.initialize();
        }
    }

    initialize() {
        this.setupThemeSwitcher();
        this.setupLanguageSwitcher();
        this.setupSearch();
        this.setupMobileMenu();
        this.loadUserPreferences();
        this.setupScrollEffects();
        this.setupTooltips();
        
        // Debug mode
        if (window.location.search.includes('debug=theme')) {
            this.debugTheme();
        }
    }

    getBasePath() {
        const metaBasePath = document.querySelector('meta[name="base-path"]');
        if (metaBasePath) {
            return metaBasePath.getAttribute('content') || '';
        }
        
        const path = window.location.pathname;
        const lastSlash = path.lastIndexOf('/');
        const scriptName = lastSlash > 0 ? path.substring(0, lastSlash) : '';
        return scriptName || '';
    }

    getCsrfToken() {
        const meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    setupThemeSwitcher() {
        const switcher = document.getElementById('theme-switcher');
        if (!switcher) {
            console.warn('Theme switcher not found');
            return;
        }

        switcher.addEventListener('change', (e) => {
            e.preventDefault();
            const theme = e.target.value;
            console.log('Theme switcher changed to:', theme);
            if (['light', 'dark', 'auto'].includes(theme)) {
                this.setTheme(theme);
            }
        });
    }

    setupLanguageSwitcher() {
        const switcher = document.getElementById('lang-switcher');
        if (!switcher) return;

        switcher.addEventListener('change', (e) => {
            e.preventDefault();
            const lang = e.target.value;
            if (/^[a-z]{2}$/.test(lang)) {
                this.setLanguage(lang);
            }
        });
    }

    setupSearch() {
        const form = document.getElementById('search-form');
        const input = document.getElementById('search-input');
        const results = document.getElementById('search-results');

        if (!form || !input || !results) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const query = input.value.trim();
            if (!query || query.length < 3) {
                results.innerHTML = '<div class="alert alert-warning">Please enter at least 3 characters</div>';
                return;
            }

            try {
                results.innerHTML = '<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Searching...</span></div></div>';
                
                const response = await this.fetchSearch(query);
                this.displaySearchResults(response, results);
            } catch (error) {
                console.error('Search error:', error);
                results.innerHTML = `<div class="alert alert-danger">Error: ${this.escapeHtml(error.message)}</div>`;
            }
        });

        // Auto-search with debounce
        input.addEventListener('input', () => {
            clearTimeout(this.debounceTimer);
            if (input.value.length >= 3) {
                this.debounceTimer = setTimeout(() => {
                    form.dispatchEvent(new Event('submit'));
                }, 500);
            } else if (input.value.length === 0) {
                results.innerHTML = '';
            }
        });
    }

    async fetchSearch(query) {
        const formData = new FormData();
        formData.append('query', query);
        formData.append('csrf_token', this.csrfToken);

        const searchUrl = this.basePath + '/search';
        
        const response = await fetch(searchUrl, {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Invalid response format');
        }

        const data = await response.json();
        
        if (!data || typeof data !== 'object') {
            throw new Error('Invalid response format');
        }

        return data;
    }

    displaySearchResults(data, container) {
        if (!data.success) {
            container.innerHTML = `<div class="alert alert-danger">${this.escapeHtml(data.error || 'Unknown error')}</div>`;
            return;
        }

        if (!data.punishments || data.punishments.length === 0) {
            container.innerHTML = '<div class="alert alert-info">No punishments found for this player.</div>';
            return;
        }

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
                                <th>Until</th>
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

    renderPunishmentRow(punishment) {
        const statusClass = punishment.active ? 'status-active' : 'status-inactive';
        const statusText = punishment.active ? 'Active' : 'Inactive';
        
        const typeClass = {
            'ban': 'bg-danger',
            'mute': 'bg-warning',
            'warning': 'bg-info',
            'kick': 'bg-secondary'
        }[punishment.type] || 'bg-dark';

        return `
            <tr>
                <td><span class="badge ${typeClass}">${this.escapeHtml(punishment.type.toUpperCase())}</span></td>
                <td class="text-truncate" style="max-width: 200px;" title="${this.escapeHtml(punishment.reason)}">${this.escapeHtml(punishment.reason)}</td>
                <td>${this.escapeHtml(punishment.staff)}</td>
                <td>${this.escapeHtml(punishment.date)}</td>
                <td>${punishment.until ? this.escapeHtml(punishment.until) : '<span class="badge bg-dark">Permanent</span>'}</td>
                <td><span class="status-badge ${statusClass}">${this.escapeHtml(statusText)}</span></td>
            </tr>
        `;
    }

    setupMobileMenu() {
        const toggle = document.querySelector('.mobile-menu-toggle');
        const nav = document.querySelector('.navbar-nav');
        
        if (!toggle || !nav) return;
        
        toggle.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            nav.classList.toggle('show');
            toggle.innerHTML = nav.classList.contains('show') ? '<i class="fas fa-times"></i>' : '<i class="fas fa-bars"></i>';
            toggle.setAttribute('aria-expanded', nav.classList.contains('show'));
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.navbar-modern') && nav.classList.contains('show')) {
                nav.classList.remove('show');
                toggle.innerHTML = '<i class="fas fa-bars"></i>';
                toggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && nav.classList.contains('show')) {
                nav.classList.remove('show');
                toggle.innerHTML = '<i class="fas fa-bars"></i>';
                toggle.setAttribute('aria-expanded', 'false');
                toggle.focus();
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
        const tooltips = document.querySelectorAll('[title]');
        tooltips.forEach(el => {
            el.setAttribute('data-bs-toggle', 'tooltip');
            el.setAttribute('data-bs-placement', 'top');
        });
    }

    setTheme(theme) {
        // Validate theme
        if (!['light', 'dark', 'auto'].includes(theme)) {
            console.error('Invalid theme:', theme);
            return;
        }

        console.log('Setting theme to:', theme);

        // Apply theme immediately to body for instant feedback
        document.body.classList.remove('theme-light', 'theme-dark', 'theme-auto');
        document.body.classList.add(`theme-${theme}`);
        
        // Save to localStorage (if available)
        try {
            localStorage.setItem('litebans_theme', theme);
            console.log('Theme saved to localStorage:', theme);
        } catch (e) {
            console.warn('localStorage not available:', e);
        }

        // Set cookie with proper options
        const path = this.basePath || '/';
        const cookieValue = `selected_theme=${theme}; path=${path}; max-age=2592000; SameSite=Lax`;
        if (window.location.protocol === 'https:') {
            document.cookie = cookieValue + '; Secure';
        } else {
            document.cookie = cookieValue;
        }
        
        console.log('Cookie set:', document.cookie);
        
        // Update theme switcher value
        const switcher = document.getElementById('theme-switcher');
        if (switcher && switcher.value !== theme) {
            switcher.value = theme;
        }
        
        // Reload page to apply server-side changes
        // Small delay to let the cookie be set
        setTimeout(() => {
            const url = new URL(window.location.href);
            url.searchParams.set('theme', theme);
            window.location.href = url.toString();
        }, 100);
    }

    setLanguage(lang) {
        // Validate language code
        if (!/^[a-z]{2}$/.test(lang)) return;

        // Update URL with language parameter
        const url = new URL(window.location.href);
        url.searchParams.set('lang', lang);
        window.location.href = url.toString();
    }

    loadUserPreferences() {
        // Try to get theme from cookie first (server-side set)
        const themeCookie = document.cookie.match(/selected_theme=([^;]+)/);
        let cookieTheme = themeCookie ? themeCookie[1] : null;
        
        // Try localStorage as fallback
        let savedTheme = null;
        try {
            savedTheme = localStorage.getItem('litebans_theme');
        } catch (e) {
            console.warn('localStorage not available');
        }
        
        const theme = cookieTheme || savedTheme || 'auto';
        console.log('Loading theme preference:', theme, {
            cookie: cookieTheme,
            localStorage: savedTheme,
            final: theme
        });
        
        if (['light', 'dark', 'auto'].includes(theme)) {
            // Apply theme class immediately
            document.body.classList.remove('theme-light', 'theme-dark', 'theme-auto');
            document.body.classList.add(`theme-${theme}`);
            
            // Update switcher value
            const switcher = document.getElementById('theme-switcher');
            if (switcher) {
                switcher.value = theme;
                console.log('Theme switcher updated to:', theme);
            }
            
            // Sync localStorage with cookie
            if (cookieTheme && cookieTheme !== savedTheme) {
                try {
                    localStorage.setItem('litebans_theme', cookieTheme);
                } catch (e) {
                    console.warn('Could not sync localStorage');
                }
            }
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = String(text);
        return div.innerHTML;
    }

    // Debug method
    debugTheme() {
        console.log('=== THEME DEBUG INFO ===');
        
        // Check current body classes
        console.log('Body classes:', document.body.className);
        
        // Check theme switcher
        const switcher = document.getElementById('theme-switcher');
        console.log('Theme switcher found:', !!switcher);
        console.log('Theme switcher value:', switcher?.value || 'N/A');
        console.log('Theme switcher options:', switcher ? Array.from(switcher.options).map(o => o.value) : 'N/A');
        
        // Check cookies
        const cookies = document.cookie.split(';').reduce((acc, cookie) => {
            const [name, value] = cookie.trim().split('=');
            acc[name] = value;
            return acc;
        }, {});
        console.log('All cookies:', cookies);
        console.log('Theme cookie:', cookies.selected_theme || 'Not set');
        
        // Check localStorage
        let localTheme = 'Not available';
        try {
            localTheme = localStorage.getItem('litebans_theme') || 'Not set';
        } catch (e) {
            localTheme = 'Error: ' + e.message;
        }
        console.log('LocalStorage theme:', localTheme);
        
        // Check CSS variables
        const computedStyle = getComputedStyle(document.body);
        console.log('CSS Variables:', {
            'bg-primary': computedStyle.getPropertyValue('--bg-primary').trim(),
            'text-primary': computedStyle.getPropertyValue('--text-primary').trim(),
            'card-bg': computedStyle.getPropertyValue('--card-bg').trim(),
            'border-color': computedStyle.getPropertyValue('--border-color').trim()
        });
        
        // Check if LiteBansUI is loaded
        console.log('LiteBansUI loaded:', typeof window.litebansUI);
        console.log('Base path:', this.basePath);
        
        console.log('=== END DEBUG INFO ===');
        
        // Add global test function
        window.testThemeSwitch = (theme) => {
            console.log('Testing theme switch to:', theme);
            this.setTheme(theme);
        };
        
        console.log('Use testThemeSwitch("dark") or testThemeSwitch("light") to test theme switching');
    }

    // Utility methods
    static formatDate(timestamp) {
        const date = new Date(timestamp);
        return date.toLocaleString();
    }

    static formatDuration(seconds) {
        if (seconds <= 0) return 'Permanent';
        
        const days = Math.floor(seconds / 86400);
        const hours = Math.floor((seconds % 86400) / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);

        const parts = [];
        if (days > 0) parts.push(`${days}d`);
        if (hours > 0) parts.push(`${hours}h`);
        if (minutes > 0 && days === 0) parts.push(`${minutes}m`);

        return parts.join(' ') || '< 1m';
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.litebansUI = new LiteBansUI();
    });
} else {
    window.litebansUI = new LiteBansUI();
}

// Global debug function
window.debugTheme = function() {
    if (window.litebansUI) {
        window.litebansUI.debugTheme();
    } else {
        console.error('LiteBansUI not loaded');
    }
};