<?php
/**
 * Demo Mode Protection
 * 
 * This file provides protection for demo installations
 * to prevent unauthorized modifications.
 * 
 * Usage in controllers:
 * if (DemoProtection::isDemo()) {
 *     $_SESSION['admin_error'] = 'Demo mode: This action is disabled';
 *     return;
 * }
 */

class DemoProtection
{
    /**
     * Check if demo mode is enabled
     */
    public static function isDemo(): bool
    {
        // Check environment variable
        if (getenv('DEMO_MODE') === 'true') {
            return true;
        }
        
        // Check config if available
        if (file_exists(__DIR__ . '/../config/app.php')) {
            $config = require __DIR__ . '/../config/app.php';
            if (isset($config['demo_mode']) && $config['demo_mode'] === true) {
                return true;
            }
        }
        
        // Check for demo file marker
        if (file_exists(__DIR__ . '/../.demo')) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Block action if in demo mode
     * Returns true if action should be blocked
     */
    public static function blockIfDemo(): bool
    {
        if (self::isDemo()) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            $_SESSION['admin_error'] = '🎭 Demo Mode: This action is disabled in the demo version';
            return true;
        }
        return false;
    }
    
    /**
     * Get list of blocked actions in demo mode
     */
    public static function getBlockedActions(): array
    {
        return [
            'saveSettings',
            'updateUser',
            'deleteUser',
            'createUser',
            'modifyPunishment',
            'removePunishment',
            'importData',
            'clearCache'
        ];
    }
    
    /**
     * Check if specific action is blocked
     */
    public static function isActionBlocked(string $action): bool
    {
        return self::isDemo() && in_array($action, self::getBlockedActions());
    }
}
