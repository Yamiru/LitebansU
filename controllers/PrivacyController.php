<?php
/**
 * Public privacy page. The text is edited in the admin panel (SEO & Tracking tab).
 */

declare(strict_types=1);

class PrivacyController extends BaseController
{
    public function index(): void
    {
        $language = $this->lang->getCurrentLanguage();
        $this->render('privacy', [
            'title' => \core\SiteExtras::t('privacy_link', $language),
            'currentPage' => 'privacy',
            'privacyHtml' => \core\SiteExtras::privacyHtml($language, (string)($this->config['site_name'] ?? 'this site')),
        ]);
    }
}
