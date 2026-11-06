/**
 * ============================================================================
 * LiteBansU
 * ============================================================================
 *
 * Plugin Name:   LiteBansU
 * Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 * Version:       5.0
 * Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 * Author URI:    https://yamiru.com
 * License:       MIT
 * License URI:   https://opensource.org/licenses/MIT
 * Repository:    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */
(function () {
    var cfg = window.LB_CONSENT;
    if (!cfg) { return; }

    var COOKIE = 'lb_consent';

    function banner() { return document.getElementById('lb-cookie'); }

    function readChoice() {
        var match = document.cookie.match(new RegExp('(?:^|; )' + COOKIE + '=([^;]*)'));
        if (!match) { return null; }
        return decodeURIComponent(match[1]) === 'analytics' ? 'analytics' : 'necessary';
    }

    function writeChoice(value) {
        var secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = COOKIE + '=' + value + '; Max-Age=' + (180 * 86400) + '; Path=/; SameSite=Lax' + secure;
    }

    function loadAnalytics() {
        if (!cfg.ga || window.__lbGaLoaded) { return; }
        window.__lbGaLoaded = true;
        window['ga-disable-' + cfg.ga] = false;
        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag('js', new Date());
        window.gtag('config', cfg.ga, { anonymize_ip: true });
        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(cfg.ga);
        document.head.appendChild(script);
    }

    function removeAnalytics() {
        if (!cfg.ga) { return; }
        window['ga-disable-' + cfg.ga] = true;
        document.cookie.split('; ').forEach(function (entry) {
            var name = entry.split('=')[0];
            if (name === '_ga' || name.indexOf('_ga_') === 0 || name === '_gid') {
                document.cookie = name + '=; Max-Age=0; Path=/';
                document.cookie = name + '=; Max-Age=0; Path=/; Domain=' + location.hostname;
            }
        });
    }

    function apply(choice) {
        if (choice === 'analytics') { loadAnalytics(); } else { removeAnalytics(); }
    }

    // The choices are visible from the start. Analytics is pre-selected the first time, and shows
    // the saved choice when the visitor reopens the notice from the footer.
    function show() {
        var box = banner();
        if (!box) { return; }
        box.hidden = false;
        var analytics = document.getElementById('lb-cookie-analytics');
        var saved = readChoice();
        if (analytics) { analytics.checked = saved === null || saved === 'analytics'; }
    }

    function decide(choice) {
        writeChoice(choice);
        apply(choice);
        var box = banner();
        if (box) { box.hidden = true; }
    }

    function start() {
        var saved = readChoice();
        if (saved) { apply(saved); } else if (cfg.banner) { show(); }
    }

    document.addEventListener('click', function (event) {
        var target = event.target;
        var action = target.closest && target.closest('[data-cookie]');
        if (action) {
            var kind = action.getAttribute('data-cookie');
            if (kind === 'accept') { decide('analytics'); }
            if (kind === 'reject') { decide('necessary'); }
            if (kind === 'save') {
                var analytics = document.getElementById('lb-cookie-analytics');
                decide(analytics && analytics.checked ? 'analytics' : 'necessary');
            }
            return;
        }
        if (target.closest && target.closest('[data-cookie-settings]')) {
            event.preventDefault();
            show();
        }
    });

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', start);
    } else {
        start();
    }
})();
