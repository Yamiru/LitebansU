<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.0
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

class LanguageManager
{
    private const DEFAULT_LANG = 'en';
    private const SUPPORTED_LANGS = ['en', 'ar', 'cs', 'de', 'gr', 'es', 'fr', 'hu', 'it', 'ja', 'pl', 'ro', 'ru', 'sk', 'sr', 'tr', 'cn'];
    
    private array $translations = [];
    private string $currentLang;
    
    public function __construct(string $lang = self::DEFAULT_LANG)
    {
        $this->currentLang = $this->validateLanguage($lang);
        $this->loadTranslations();
    }
    
    private function validateLanguage(string $lang): string
    {
        return in_array($lang, self::SUPPORTED_LANGS, true) ? $lang : self::DEFAULT_LANG;
    }
    
    private function loadTranslations(): void
    {
        // Always load default language first
        $defaultPath = __DIR__ . "/../lang/en.php";
        if (file_exists($defaultPath)) {
            $this->translations = include $defaultPath;
        } else {
            $this->translations = [];
        }
        
        // Load selected language if different from default
        if ($this->currentLang !== 'en') {
            $langPath = __DIR__ . "/../lang/{$this->currentLang}.php";
            if (file_exists($langPath)) {
                $langTranslations = include $langPath;
                if (is_array($langTranslations)) {
                    $this->translations = array_replace_recursive($this->translations, $langTranslations);
                }
            }
        }
    }
    
    /**
     * Get translation value for a key
     * Returns string for text values, array for array values, or fallback
     */
    public function get(string $key, array $params = [])
    {
        $keys = explode('.', $key);
        $value = $this->translations;
        
        // Navigate through nested array
        foreach ($keys as $k) {
            if (!is_array($value) || !isset($value[$k])) {
                return "[{$key}]";
            }
            $value = $value[$k];
        }
        
        // If value is array, return it as-is (for foreach loops)
        if (is_array($value)) {
            return $value;
        }
        
        // Ensure value is string for text values
        if (!is_string($value)) {
            return "[{$key}]";
        }
        
        // Replace parameters for string values
        if (!empty($params)) {
            foreach ($params as $param => $replacement) {
                // Ensure replacement is string
                $replacement = (string)$replacement;
                $value = str_replace("{{$param}}", $replacement, $value);
            }
        }
        
        return $value;
    }
    
    public function getCurrentLanguage(): string
    {
        return $this->currentLang;
    }
    
    public function getSupportedLanguages(): array
    {
        return self::SUPPORTED_LANGS;
    }
    
    public function getLanguageName(string $code): string
    {
        $names = [
'ar' => '???????',
'cs' => 'Èetina',
'de' => 'Deutsch',
'gr' => '????????',
'en' => 'English',
'es' => 'Espanol',
'fr' => 'Français',
'hu' => 'Magyar',
'it' => 'Italiano',
'ja' => '???',
'pl' => 'Polski',
'ro' => 'Românã',
'ru' => '???????',
'sk' => 'Slovenèina',
'sr' => 'Srpski',
'tr' => 'Türkçe',
'cn' => '?? (??)'        ];
        
        return $names[$code] ?? strtoupper($code);
    }
    
    public static function detectLanguage(): string
    {
        // Check session first
        if (isset($_SESSION['selected_lang']) && in_array($_SESSION['selected_lang'], self::SUPPORTED_LANGS, true)) {
            return $_SESSION['selected_lang'];
        }
        
        // Check cookie
        if (isset($_COOKIE['selected_lang']) && in_array($_COOKIE['selected_lang'], self::SUPPORTED_LANGS, true)) {
            $_SESSION['selected_lang'] = $_COOKIE['selected_lang'];
            return $_COOKIE['selected_lang'];
        }
        
        // Check browser language
        if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $browserLang = strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2));
            if (in_array($browserLang, self::SUPPORTED_LANGS, true)) {
                return $browserLang;
            }
        }
        
        // Use default from config if available
        if (class_exists('core\\EnvLoader')) {
            $defaultLang = \core\EnvLoader::get('DEFAULT_LANGUAGE', self::DEFAULT_LANG);
            return in_array($defaultLang, self::SUPPORTED_LANGS, true) ? $defaultLang : self::DEFAULT_LANG;
        }
        
        return self::DEFAULT_LANG;
    }
}