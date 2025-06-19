<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       1.0
 *  Author:        Yamiru <yamiru@yamiru.com>
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

declare(strict_types=1);

class LanguageManager
{
    private const DEFAULT_LANG = 'en';
    private const SUPPORTED_LANGS = ['en', 'sk', 'ru', 'de', 'es', 'fr'];
    
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
    
    public function get(string $key, array $params = []): string
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
        
        // Ensure value is string
        if (!is_string($value)) {
            return "[{$key}]";
        }
        
        // Replace parameters
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
            'en' => 'English',
            'sk' => 'Slovenèina',
            'ru' => 'Russian',
            'de' => 'Deutsch',
            'es' => 'Espanol',
            'fr' => 'Français'
        ];
        
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
        
        return self::DEFAULT_LANG;
    }
}