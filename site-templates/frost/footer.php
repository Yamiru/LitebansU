<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$siteExtras = $siteExtras ?? \core\SiteExtras::get();
$gaId = \core\SiteExtras::gaId();
$uiLang = $lang->getCurrentLanguage();
$tx = fn(string $key): string => $h(\core\SiteExtras::t($key, $uiLang));
$consentOn = $gaId !== '' || !empty($siteExtras['cookie_banner']);
?>
</main>
<footer class="foot">
    <div class="wrap">
        <p>&#169; <?= $h($config['footer_site_name'] ?? '') ?> <?= date('Y') ?> &middot; <a href="<?= $h(url('privacy')) ?>"><?= $tx('privacy_link') ?></a><?php if ($consentOn): ?> &middot; <a href="#" data-cookie-settings><?= $tx('cookie_settings') ?></a><?php endif; ?></p>
        <p>Powered by <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer">LitebansU</a> &middot; by <a href="https://yamiru.com" target="_blank" rel="noopener noreferrer">yamiru.com</a></p>
    </div>
</footer>

<?php if (!empty($siteExtras['cookie_banner'])): ?>
<div id="lb-cookie" class="cookie-banner" role="dialog" aria-live="polite" aria-label="<?= $tx('cookie_settings') ?>" hidden dir="<?= $uiLang === 'ar' ? 'rtl' : 'ltr' ?>">
    <p><?= $tx($gaId ? 'cookie_text' : 'cookie_text_basic') ?> <a href="<?= $h(url('privacy')) ?>"><?= $tx('privacy_link') ?></a></p>
    <?php if ($gaId): ?>
    <p class="small" style="margin-top:.6rem">
        <label><input type="checkbox" checked disabled> <?= $tx('cookie_necessary') ?></label>
        <label><input type="checkbox" id="lb-cookie-analytics" checked> <?= $tx('cookie_analytics') ?></label>
    </p>
    <div class="acts">
        <button type="button" data-cookie="accept"><?= $tx('cookie_accept') ?></button>
        <button type="button" data-cookie="save"><?= $tx('cookie_save') ?></button>
        <button type="button" data-cookie="reject"><?= $tx('cookie_reject') ?></button>
    </div>
    <?php else: ?>
    <div class="acts"><button type="button" data-cookie="reject"><?= $tx('cookie_ok') ?></button></div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($consentOn): ?>
<script src="<?= $h(asset('assets/js/consent.js')) ?>"></script>
<?php endif; ?>
<script>
(function () {
    var burger = document.getElementById('burger'), menu = document.getElementById('menu');
    burger.addEventListener('click', function () {
        var open = menu.classList.toggle('open');
        burger.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    document.getElementById('themeToggle').addEventListener('click', function () {
        var next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
        document.documentElement.dataset.theme = next;
        document.cookie = 'selected_theme=' + next + '; path=/; max-age=31536000; samesite=lax' + (location.protocol === 'https:' ? '; secure' : '');
    });
    document.querySelectorAll('[data-href]').forEach(function (r) {
        r.addEventListener('click', function (e) { if (!e.target.closest('a')) location.href = r.dataset.href; });
    });
})();
</script>
<?php if (trim((string)$siteExtras['custom_footer']) !== ''): ?>
<?= $siteExtras['custom_footer'] ?>

<?php endif; ?>
</body>
</html>
