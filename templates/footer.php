</div>
</main>
<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JS -->
<script src="<?= htmlspecialchars(bundle('js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php
$siteExtras = $siteExtras ?? \core\SiteExtras::get();
$gaId = \core\SiteExtras::gaId();
$uiLang = $lang->getCurrentLanguage();
$tx = fn(string $key): string => htmlspecialchars(\core\SiteExtras::t($key, $uiLang), ENT_QUOTES, 'UTF-8');
?>
<?php $consentOn = $gaId !== '' || !empty($siteExtras['cookie_banner']); ?>
<?php if ($consentOn): ?>
<script src="<?= htmlspecialchars(asset('assets/js/consent.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
<?php endif; ?>

<footer class="footer mt-auto">
    <div class="container">
        <div class="row text-center text-md-start align-items-center py-3">
            <!-- Left side -->
            <div class="col-md-6 mb-2 mb-md-0">
                <p class="mb-0">
                    &#169; <?= htmlspecialchars($config['footer_site_name'], ENT_QUOTES, 'UTF-8') ?> <?= date('Y') ?>
                    <span class="text-muted">-</span>
                    <a href="<?= htmlspecialchars(url('privacy'), ENT_QUOTES, 'UTF-8') ?>"><?= $tx('privacy_link') ?></a>
                    <?php if ($consentOn): ?>
                        <span class="text-muted">-</span>
                        <a href="#" data-cookie-settings><?= $tx('cookie_settings') ?></a>
                    <?php endif; ?>
                </p>
            </div>
            <!-- Right side -->
            <div class="col-md-6 text-md-end">
                <p class="mb-0">
                    Powered by 
                    <a href="https://github.com/Yamiru/LitebansU" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                        <strong>LitebansU</strong>
                    </a> 
                    <span class="text-muted">-</span> 
                    A project by <a href="https://yamiru.com" target="_blank" rel="noopener noreferrer" class="text-decoration-none">Yamiru</a>
                </p>
            </div>
        </div>
    </div>
</footer>

<?php if (!empty($siteExtras['cookie_banner'])): ?>
<div id="lb-cookie" class="cookie-banner" role="dialog" aria-live="polite" aria-label="<?= $tx('cookie_settings') ?>" hidden dir="<?= $uiLang === 'ar' ? 'rtl' : 'ltr' ?>">
    <p><?= $tx($gaId ? 'cookie_text' : 'cookie_text_basic') ?> <a href="<?= htmlspecialchars(url('privacy'), ENT_QUOTES, 'UTF-8') ?>"><?= $tx('privacy_link') ?></a></p>
    <?php if ($gaId): ?>
    <div class="cookie-choices">
        <label><input type="checkbox" checked disabled> <?= $tx('cookie_necessary') ?></label>
        <label><input type="checkbox" id="lb-cookie-analytics" checked> <?= $tx('cookie_analytics') ?></label>
    </div>
    <div class="cookie-actions">
        <button type="button" class="btn btn-primary btn-sm" data-cookie="accept"><?= $tx('cookie_accept') ?></button>
        <button type="button" class="btn btn-outline-primary btn-sm" data-cookie="save"><?= $tx('cookie_save') ?></button>
        <button type="button" class="btn btn-outline-secondary btn-sm" data-cookie="reject"><?= $tx('cookie_reject') ?></button>
    </div>
    <?php else: ?>
    <div class="cookie-actions">
        <button type="button" class="btn btn-primary btn-sm" data-cookie="reject"><?= $tx('cookie_ok') ?></button>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php if (trim((string)$siteExtras['custom_footer']) !== ''): ?>
<?= $siteExtras['custom_footer'] ?>

<?php endif; ?>


</body>
</html>
