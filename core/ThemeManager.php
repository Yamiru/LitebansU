<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       2.5
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

declare(strict_types=1);

class ThemeManager
{
    private const DEFAULT_THEME = 'dark';
    private const AVAILABLE_THEMES = ['light', 'dark'];
    
    private string $currentTheme;
    
    public function __construct()
    {
        $this->currentTheme = $this->detectTheme();
    }
    
    private function detectTheme(): string
    {
        // Check cookie first (from theme switcher)
        if (isset($_COOKIE['selected_theme']) && in_array($_COOKIE['selected_theme'], self::AVAILABLE_THEMES)) {
            return $_COOKIE['selected_theme'];
        }
        
        // Use default from config if available
        if (class_exists('core\\EnvLoader')) {
            $defaultTheme = \core\EnvLoader::get('DEFAULT_THEME', self::DEFAULT_THEME);
            return in_array($defaultTheme, self::AVAILABLE_THEMES) ? $defaultTheme : self::DEFAULT_THEME;
        }
        
        return self::DEFAULT_THEME;
    }
    
    public function getCurrentTheme(): string
    {
        return $this->currentTheme;
    }
    
    public function getAvailableThemes(): array
    {
        return self::AVAILABLE_THEMES;
    }
    
    public function getThemeClasses(): array
    {
        // Return simple theme class for body
        return [
            'body' => 'theme-' . $this->currentTheme
        ];
    }
    
    public function getBodyClass(): string
    {
        return 'theme-' . $this->currentTheme;
    }
    
    public function getCssVariables(): string
    {
        // CSS variables are now handled by CSS classes, not inline styles
        return '';
    }
    
    /**
     * Get theme-specific button classes for backwards compatibility
     */
    public function getButtonClasses(): array
    {
        return [
            'primary' => 'btn-primary',
            'secondary' => 'btn-outline-secondary',
            'danger' => 'btn-danger',
            'warning' => 'btn-warning',
            'info' => 'btn-info',
            'success' => 'btn-success'
        ];
    }
    
    /**
     * Check if current theme is dark mode
     */
    public function isDarkMode(): bool
    {
        return $this->currentTheme === 'dark';
    }
    
    /**
     * Check if current theme is light mode
     */
    public function isLightMode(): bool
    {
        return $this->currentTheme === 'light';
    }
    
    /**
     * Get theme name for display
     */
    public function getThemeName(?string $theme = null): string
    {
        $theme = $theme ?? $this->currentTheme;
        
        return match($theme) {
            'light' => 'Light',
            'dark' => 'Dark',
            default => 'Unknown'
        };
    }
}
