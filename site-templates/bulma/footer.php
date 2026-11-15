<?php
$h ??= fn($s): string => htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
$siteExtras = $siteExtras ?? \core\SiteExtras::get();
$gaId = \core\SiteExtras::gaId();
$uiLang = $lang->getCurrentLanguage();
$tx = fn(string $key): string => $h(\core\SiteExtras::t($key, $uiLang));
$consentOn = $gaId !== '' || !empty($siteExtras['cookie_banner']);
?>
</div>
</main>
<footer class="footer py-5">
    <div class="content has-text-centered">
        <p>
            &#169; <?= $h($config['footer_site_name'] ?? '') ?> <?= date('Y') ?>
            - <a href="<?= $h(url('privacy')) ?>"><?= $tx('privacy_link') ?></a>
            <?php if ($consentOn): ?>- <a href="#" data-cookie-settings><?= $tx('cookie_settings') ?></a><?php endif; ?>
            <br>
            Powered by <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer"><strong>LitebansU</strong></a>
            - A project by <a href="https://yamiru.com" target="_blank" rel="noopener noreferrer">Yamiru</a>
        </p>
    </div>
</footer>

<?php if (!empty($siteExtras['cookie_banner'])): ?>
<div id="lb-cookie" class="cookie-banner box" role="dialog" aria-live="polite" aria-label="<?= $tx('cookie_settings') ?>" hidden dir="<?= $uiLang === 'ar' ? 'rtl' : 'ltr' ?>">
    <p class="mb-3"><?= $tx($gaId ? 'cookie_text' : 'cookie_text_basic') ?> <a href="<?= $h(url('privacy')) ?>"><?= $tx('privacy_link') ?></a></p>
    <?php if ($gaId): ?>
    <div class="mb-3">
        <label class="checkbox mr-4"><input type="checkbox" checked disabled> <?= $tx('cookie_necessary') ?></label>
        <label class="checkbox"><input type="checkbox" id="lb-cookie-analytics" checked> <?= $tx('cookie_analytics') ?></label>
    </div>
    <div class="buttons">
        <button type="button" class="button is-primary is-small" data-cookie="accept"><?= $tx('cookie_accept') ?></button>
        <button type="button" class="button is-primary is-light is-small" data-cookie="save"><?= $tx('cookie_save') ?></button>
        <button type="button" class="button is-light is-small" data-cookie="reject"><?= $tx('cookie_reject') ?></button>
    </div>
    <?php else: ?>
    <button type="button" class="button is-primary is-small" data-cookie="reject"><?= $tx('cookie_ok') ?></button>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if ($consentOn): ?>
<script src="<?= $h(asset('assets/js/consent.js')) ?>"></script>
<?php endif; ?>
<script>
document.querySelectorAll('.navbar-burger').forEach(function (b) {
    b.addEventListener('click', function () {
        var open = b.classList.toggle('is-active');
        b.setAttribute('aria-expanded', open ? 'true' : 'false');
        document.getElementById(b.dataset.target).classList.toggle('is-active', open);
    });
});
document.getElementById('themeToggle').addEventListener('click', function () {
    var next = document.documentElement.dataset.theme === 'dark' ? 'light' : 'dark';
    document.documentElement.dataset.theme = next;
    document.cookie = 'selected_theme=' + next + '; path=/; max-age=31536000; samesite=lax' + (location.protocol === 'https:' ? '; secure' : '');
});
document.querySelectorAll('[data-href]').forEach(function (r) {
    r.addEventListener('click', function (e) { if (!e.target.closest('a')) location.href = r.dataset.href; });
});
</script>
<?php if (trim((string)$siteExtras['custom_footer']) !== ''): ?>
<?= $siteExtras['custom_footer'] ?>

<?php endif; ?>
</body>
</html>
