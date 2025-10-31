<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       2.3
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 *  Repository    https://github.com/Yamiru/LitebansU/
 * ============================================================================
 */

declare(strict_types=1);

class PunishmentsController extends BaseController
{
    public function bans(): void
    {
        $punishments = $this->repository->getBans($this->getLimit(), $this->getOffset(), false);
        
        $this->render('punishments', [
            'title' => $this->lang->get('nav.bans'),
            'type' => 'bans',
            'punishments' => $this->formatPunishments($punishments),
            'pagination' => $this->getPaginationData('bans', false),
            'currentPage' => 'bans'
        ]);
    }
    
    public function mutes(): void
    {
        $punishments = $this->repository->getMutes($this->getLimit(), $this->getOffset(), false);
        
        $this->render('punishments', [
            'title' => $this->lang->get('nav.mutes'),
            'type' => 'mutes',
            'punishments' => $this->formatPunishments($punishments),
            'pagination' => $this->getPaginationData('mutes', false),
            'currentPage' => 'mutes'
        ]);
    }
    
    public function warnings(): void
    {
        $punishments = $this->repository->getWarnings($this->getLimit(), $this->getOffset());
        
        $this->render('punishments', [
            'title' => $this->lang->get('nav.warnings'),
            'type' => 'warnings',
            'punishments' => $this->formatPunishments($punishments),
            'pagination' => $this->getPaginationData('warnings'),
            'currentPage' => 'warnings'
        ]);
    }
    
    public function kicks(): void
    {
        $punishments = $this->repository->getKicks($this->getLimit(), $this->getOffset());
        
        $this->render('punishments', [
            'title' => $this->lang->get('nav.kicks'),
            'type' => 'kicks',
            'punishments' => $this->formatPunishments($punishments),
            'pagination' => $this->getPaginationData('kicks'),
            'currentPage' => 'kicks'
        ]);
    }
    
    private function formatPunishments(array $punishments): array
    {
        return array_map(function($punishment) {
            // Get player name - handle null values properly
            $playerName = $punishment['player_name'] ?? $punishment['name'] ?? null;
            if (!$playerName && !empty($punishment['uuid'])) {
                $playerName = $this->repository->getPlayerName($punishment['uuid']);
            }
            
            return [
                'id' => (int)$punishment['id'],
                'uuid' => $punishment['uuid'] ?? '',
                'name' => SecurityManager::preventXss($playerName ?? 'Unknown'),
                'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
                'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? 'Console'),
                'date' => $this->formatDate((int)($punishment['time'] ?? 0)),
                'until' => isset($punishment['until']) ? $this->formatDuration((int)$punishment['until']) : null,
                'active' => (bool)($punishment['active'] ?? false),
                'removed_by' => isset($punishment['removed_by_name']) ? SecurityManager::preventXss($punishment['removed_by_name']) : null,
                'avatar' => $this->getAvatarUrl($punishment['uuid'] ?? '', $playerName ?? 'Unknown'),
                'server' => $punishment['server'] ?? 'Global'
            ];
        }, $punishments);
    }
    
    private function getPaginationData(string $type, bool $activeOnly = true): array
    {
        $currentPage = $this->getPage();
        $limit = $this->getLimit();
        
        // Get total count for accurate pagination
        $totalCount = $this->getTotalCount($type, $activeOnly);
        $totalPages = max(1, (int)ceil($totalCount / $limit));
        
        // Ensure current page is within bounds
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }
        
        return [
            'current' => $currentPage,
            'total' => $totalPages,
            'total_items' => $totalCount,
            'has_prev' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'prev_url' => $currentPage > 1 ? "?page=" . ($currentPage - 1) : null,
            'next_url' => $currentPage < $totalPages ? "?page=" . ($currentPage + 1) : null
        ];
    }
    
    private function getTotalCount(string $type, bool $activeOnly = true): int
    {
        try {
            return match($type) {
                'bans' => $this->repository->getTotalBans($activeOnly),
                'mutes' => $this->repository->getTotalMutes($activeOnly),
                'warnings' => $this->repository->getTotalWarnings(),
                'kicks' => $this->repository->getTotalKicks(),
                default => 0
            };
        } catch (Exception $e) {
            error_log("Error getting total count for {$type}: " . $e->getMessage());
            return 0;
        }
    }
    
    public function info(): void
    {
        $type = $_GET['type'] ?? '';
        $id = (int)($_GET['id'] ?? 0);
        
        if (!in_array($type, ['bans', 'mutes', 'warnings', 'kicks'], true)) {
            $this->redirect(url('/'), 404);
            return;
        }
        
        if ($id <= 0) {
            $this->redirect(url('/'), 404);
            return;
        }
        
        try {
            $punishment = $this->repository->getPunishmentById($type, $id);
            
            if (!$punishment) {
                $this->redirect(url('/'), 404);
                return;
            }
            
            $this->render('punishment_info', [
                'title' => $this->lang->get('nav.' . $type) . ' #' . $id,
                'punishment' => $this->formatPunishments([$punishment])[0],
                'type' => $type,
                'currentPage' => $type
            ]);
        } catch (Exception $e) {
            error_log("Error loading punishment info: " . $e->getMessage());
            $this->redirect(url('/'), 500);
        }
    }
}
