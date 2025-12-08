<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.4
 *  Market URI:    https://builtbybit.com/resources/litebansu-litebans-website.69448/
 *  Author URI:    https://yamiru.com
 *  License:       MIT
 *  License URI:   https://opensource.org/licenses/MIT
 * ============================================================================
 */

declare(strict_types=1);

class DetailController extends BaseController
{
    public function show(): void
    {
        // Get parameters
        $type = $_GET['type'] ?? '';
        $id = (int)($_GET['id'] ?? 0);
        
        // Validate type - normalize to singular form
        $type = rtrim($type, 's'); // Remove 's' if present
        if (!in_array($type, ['ban', 'mute', 'warning', 'kick'], true)) {
            $this->showError('Invalid punishment type');
            return;
        }
        
        // Validate ID
        if ($id <= 0) {
            $this->showError('Invalid punishment ID');
            return;
        }
        
        try {
            // Get punishment details
            $tableName = $type . 's'; // Convert ban -> bans, mute -> mutes, etc.
            $punishment = $this->repository->getPunishmentById($tableName, $id);
            
            if (!$punishment) {
                $this->showError($this->lang->get('error.punishment_not_found'));
                return;
            }
            
            // Get player name
            $playerName = $punishment['player_name'] ?? $this->repository->getPlayerName($punishment['uuid'] ?? '');
            
            // Format the punishment data
            $formattedPunishment = $this->formatPunishmentDetail($punishment, $type, $playerName);
            
            // Get related punishments for this player
            $relatedPunishments = [];
            if (!empty($punishment['uuid']) && $punishment['uuid'] !== '#') {
                $relatedPunishments = $this->repository->getPlayerPunishments($punishment['uuid']);
                // Filter out current punishment
                $relatedPunishments = array_filter($relatedPunishments, function($p) use ($id, $tableName) {
                    return !($p['id'] == $id && $p['type'] == $tableName);
                });
            }
            
            $this->render('detail', [
                'title' => ucfirst($type) . ' #' . $id,
                'punishment' => $formattedPunishment,
                'relatedPunishments' => $this->formatPunishments($relatedPunishments),
                'type' => $type,
                'currentPage' => $tableName
            ]);
            
        } catch (Exception $e) {
            error_log("Error loading punishment detail: " . $e->getMessage());
            $this->showError($this->lang->get('error.loading_failed'));
        }
    }
    
    private function showError(string $message): void
    {
        $this->render('error', [
            'title' => $this->lang->get('error.not_found'),
            'message' => $message,
            'currentPage' => 'error'
        ]);
    }
    
    private function formatPunishmentDetail(array $punishment, string $type, ?string $playerName): array
    {
        $uuid = $punishment['uuid'] ?? '';
        $name = $playerName ?? 'Unknown';
        
        // Calculate duration for bans/mutes only (warnings and kicks don't have 'until' field)
        $duration = null;
        $timeLeft = null;
        $progress = 0;
        
        if (in_array($type, ['ban', 'mute']) && isset($punishment['until'])) {
            $startTime = (int)$punishment['time'];
            $endTime = (int)$punishment['until'];
            $currentTime = time() * 1000;
            
            if ($endTime > 0) {
                // Calculate total duration
                $totalDuration = $endTime - $startTime;
                $duration = $this->formatDurationDetailed($totalDuration / 1000);
                
                // Calculate time left
                if ($punishment['active'] && $endTime > $currentTime) {
                    $timeLeftMs = $endTime - $currentTime;
                    $timeLeft = $this->formatDurationDetailed($timeLeftMs / 1000);
                    
                    // Calculate progress percentage
                    $elapsed = $currentTime - $startTime;
                    $progress = min(100, max(0, ($elapsed / $totalDuration) * 100));
                } else {
                    $timeLeft = $this->lang->get('punishment.expired');
                    $progress = 100;
                }
            } else {
                $duration = $this->lang->get('punishment.permanent');
                $timeLeft = $this->lang->get('punishment.permanent');
            }
        }
        
        return [
            'id' => (int)$punishment['id'],
            'uuid' => $uuid,
            'name' => SecurityManager::preventXss($name),
            'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
            'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? 'Console'),
            'staff_uuid' => $punishment['banned_by_uuid'] ?? '',
            'date' => $this->formatDate((int)($punishment['time'] ?? 0)),
            'timestamp' => (int)($punishment['time'] ?? 0),
            'until' => isset($punishment['until']) && $punishment['until'] > 0 && in_array($type, ['ban', 'mute'])
                ? $this->formatDate((int)$punishment['until']) 
                : null,
            'until_timestamp' => in_array($type, ['ban', 'mute']) ? (int)($punishment['until'] ?? 0) : 0,
            'duration' => $duration,
            'timeLeft' => $timeLeft,
            'progress' => $progress,
            'active' => (bool)($punishment['active'] ?? false),
            'removed' => !empty($punishment['removed_by_name']),
            'removed_by' => isset($punishment['removed_by_name']) 
                ? SecurityManager::preventXss($punishment['removed_by_name']) 
                : null,
            'removed_by_uuid' => $punishment['removed_by_uuid'] ?? null,
            'removed_date' => $this->formatRemovedDate($punishment['removed_by_date'] ?? null),
            'silent' => (bool)($punishment['silent'] ?? false),
            'ipban' => (bool)($punishment['ipban'] ?? false),
            'warned' => (bool)($punishment['warned'] ?? false),
            'server' => $punishment['server_origin'] ?? $punishment['server_scope'] ?? 'Global',
            'server_origin' => $punishment['server_origin'] ?? null,
            'server_scope' => $punishment['server_scope'] ?? null,
            'avatar' => $this->getAvatarUrl($uuid, $name),
            'type' => $type
        ];
    }
    
    private function formatDurationDetailed(float $seconds): string
    {
        if ($seconds <= 0) return '0s';
        
        $intervals = [
            'y' => 31536000,
            'mo' => 2592000,
            'd' => 86400,
            'h' => 3600,
            'm' => 60,
            's' => 1
        ];
        
        $parts = [];
        
        foreach ($intervals as $unit => $value) {
            $count = floor($seconds / $value);
            if ($count > 0) {
                $parts[] = $count . $unit;
                $seconds = fmod($seconds, $value);
                
                // Only show 2 units max
                if (count($parts) >= 2) break;
            }
        }
        
        return implode(' ', $parts);
    }
    
    private function formatRemovedDate($removedDate): ?string
    {
        // Handle NULL or empty values
        if (empty($removedDate) || $removedDate === 'NULL' || $removedDate === '#expired') {
            return null;
        }
        
        // Check if it's a numeric timestamp
        if (!is_numeric($removedDate)) {
            return null;
        }
        
        $timestamp = (int)$removedDate;
        
        // Validate timestamp - must be greater than 1000000000 (Sep 2001)
        // This filters out invalid small numbers that would result in 1970 dates
        if ($timestamp < 1000000000) {
            return null;
        }
        
        // LiteBans stores removed_by_date in seconds (not milliseconds like other timestamps)
        // So we need to multiply by 1000 before passing to formatDate
        return $this->formatDate($timestamp * 1000);
    }
    
    protected function formatPunishments(array $punishments): array
    {
        return array_map(function($punishment) {
            $playerName = $punishment['player_name'] ?? $punishment['name'] ?? null;
            if (!$playerName && isset($punishment['uuid'])) {
                $playerName = $this->repository->getPlayerName($punishment['uuid']);
            }
            
            return [
                'id' => (int)$punishment['id'],
                'type' => $punishment['type'] ?? 'unknown',
                'uuid' => $punishment['uuid'] ?? '',
                'name' => SecurityManager::preventXss($playerName ?? 'Unknown'),
                'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
                'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? 'Console'),
                'date' => $this->formatDate((int)($punishment['time'] ?? 0)),
                'until' => isset($punishment['until']) ? $this->formatDuration((int)$punishment['until']) : null,
                'active' => (bool)($punishment['active'] ?? false),
                'removed_by' => isset($punishment['removed_by_name']) ? SecurityManager::preventXss($punishment['removed_by_name']) : null,
                'avatar' => $this->getAvatarUrl($punishment['uuid'] ?? '', $playerName ?? 'Unknown')
            ];
        }, $punishments);
    }
}
