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

class HomeController extends BaseController
{
    public function index(): void
    {
        $stats = $this->repository->getStats();
        $recentBans = $this->repository->getBans(5, 0, true);
        $recentMutes = $this->repository->getMutes(5, 0, true);
        
        // Ensure data is properly formatted
        $recentBans = $this->ensurePlayerNames($recentBans);
        $recentMutes = $this->ensurePlayerNames($recentMutes);
        
        $this->render('home', [
            'stats' => $stats,
            'recentBans' => $recentBans,
            'recentMutes' => $recentMutes,
            'controller' => $this
        ]);
    }
    
    public function search(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleSearch();
            return;
        }
        
        $this->render('search');
    }
    
    private function handleSearch(): void
    {
        // Verify it's an AJAX request
        if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
            $this->jsonResponse(['error' => 'Invalid request'], 400);
            return;
        }
        
        // Validate CSRF token
        if (!SecurityManager::validateCsrfToken($_POST['csrf_token'] ?? '')) {
            $this->jsonResponse(['error' => 'Invalid CSRF token'], 400);
            return;
        }
        
        $query = trim($_POST['query'] ?? '');
        if (empty($query) || strlen($query) < 3) {
            $this->jsonResponse(['error' => 'Search query must be at least 3 characters'], 400);
            return;
        }
        
        // Rate limiting
        $clientIp = SecurityManager::getClientIp();
        if (!SecurityManager::rateLimitCheck('search_' . $clientIp, 30, 60)) {
            $this->jsonResponse(['error' => 'Too many requests. Please try again later.'], 429);
            return;
        }
        
        $query = SecurityManager::sanitizeInput($query);
        
        try {
            $punishments = $this->repository->getPlayerPunishments($query);
            
            $this->jsonResponse([
                'success' => true,
                'player' => $query,
                'punishments' => $this->formatPunishmentsForSearch($punishments)
            ]);
        } catch (Exception $e) {
            error_log("Search error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Search failed. Please try again.'], 500);
        }
    }
    
    private function formatPunishmentsForSearch(array $punishments): array
    {
        return array_map(function($punishment) {
            return [
                'type' => $punishment['type'] ?? 'unknown',
                'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
                'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? 'Console'),
                'date' => $this->formatDate((int)($punishment['time'] ?? 0)),
                'until' => isset($punishment['until']) && $punishment['until'] > 0 
                    ? $this->formatDuration((int)$punishment['until']) 
                    : null,
                'active' => (bool)($punishment['active'] ?? false)
            ];
        }, $punishments);
    }
    
    private function ensurePlayerNames(array $punishments): array
    {
        foreach ($punishments as &$punishment) {
            // Ensure player name exists
            if (empty($punishment['player_name']) && empty($punishment['name'])) {
                if (!empty($punishment['uuid'])) {
                    $name = $this->repository->getPlayerName($punishment['uuid']);
                    $punishment['player_name'] = $name ?? 'Unknown';
                } else {
                    $punishment['player_name'] = 'Unknown';
                }
            } elseif (empty($punishment['player_name']) && !empty($punishment['name'])) {
                $punishment['player_name'] = $punishment['name'];
            }
            
            // Ensure UUID exists
            if (empty($punishment['uuid'])) {
                $punishment['uuid'] = '';
            }
            
            // Ensure other required fields exist
            $punishment['reason'] = $punishment['reason'] ?? 'No reason provided';
            $punishment['time'] = $punishment['time'] ?? 0;
            $punishment['active'] = $punishment['active'] ?? 0;
        }
        
        return $punishments;
    }
}