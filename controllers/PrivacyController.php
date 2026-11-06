<?php
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
