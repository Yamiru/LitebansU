<?php
/**
 * ============================================================================
 *  LiteBansU
 * ============================================================================
 *
 *  Plugin Name:   LiteBansU
 *  Description:   A modern, secure, and responsive web interface for LiteBans punishment management system.
 *  Version:       3.9
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
        $type = $_GET['type'] ?? '';
        $id = (int)($_GET['id'] ?? 0);
        
        $type = rtrim($type, 's');
        if (!in_array($type, ['ban', 'mute', 'warning', 'kick'], true)) {
            $this->showError('Invalid punishment type');
            return;
        }
        
        if ($id <= 0) {
            $this->showError('Invalid punishment ID');
            return;
        }
        
        try {
            $tableName = $type . 's';
            $punishment = $this->repository->getPunishmentById($tableName, $id);
            
            if (!$punishment) {
                $this->showError($this->lang->get('error.punishment_not_found'));
                return;
            }
            
            $playerName = $punishment['player_name'] ?? $this->repository->getPlayerName($punishment['uuid'] ?? '');
            
            $formattedPunishment = $this->formatPunishmentDetail($punishment, $type, $playerName);
            
            $relatedPunishments = [];
            if (!empty($punishment['uuid']) && $punishment['uuid'] !== '#') {
                $relatedPunishments = $this->repository->getPlayerPunishments($punishment['uuid']);
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
        
        $duration = null;
        $timeLeft = null;
        $progress = 0;
        
        if (in_array($type, ['ban', 'mute']) && isset($punishment['until'])) {
            $startTime = (int)$punishment['time'];
            $endTime = (int)$punishment['until'];
            $currentTime = time() * 1000;
            
            if ($endTime > 0) {
                $totalDuration = $endTime - $startTime;
                $duration = $this->formatDurationDetailed($totalDuration / 1000);
                
                if ($punishment['active'] && $endTime > $currentTime) {
                    $timeLeftMs = $endTime - $currentTime;
                    $timeLeft = $this->formatDurationDetailed($timeLeftMs / 1000);
                    
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
            'active' => $this->isPunishmentActive($punishment, $type),
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
            'type' => $type,
            'ip' => $this->isAdminLoggedIn() && !empty($punishment['ip']) && $punishment['ip'] !== '#'
                ? SecurityManager::preventXss((string)$punishment['ip'])
                : null
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
                
                if (count($parts) >= 2) break;
            }
        }
        
        return implode(' ', $parts);
    }
    
    private function formatRemovedDate($removedDate): ?string
    {
        if (empty($removedDate) || $removedDate === 'NULL' || $removedDate === '#expired') {
            return null;
        }
        
        if (!is_numeric($removedDate)) {
            return null;
        }
        
        $timestamp = (int)$removedDate;
        
        if ($timestamp < 1000000000) {
            return null;
        }
        
        return $this->formatDate($timestamp * 1000);
    }
    
    protected function formatPunishments(array $punishments): array
    {
        return array_map(function($punishment) {
            $playerName = $punishment['player_name'] ?? $punishment['name'] ?? null;
            if (!$playerName && isset($punishment['uuid'])) {
                $playerName = $this->repository->getPlayerName($punishment['uuid']);
            }
            
            $rowType = $punishment['type'] ?? 'unknown';
            
            return [
                'id' => (int)$punishment['id'],
                'type' => $rowType,
                'uuid' => $punishment['uuid'] ?? '',
                'name' => SecurityManager::preventXss($playerName ?? 'Unknown'),
                'reason' => SecurityManager::preventXss($punishment['reason'] ?? 'No reason provided'),
                'staff' => SecurityManager::preventXss($punishment['banned_by_name'] ?? 'Console'),
                'date' => $this->formatDate((int)($punishment['time'] ?? 0)),
                'until' => isset($punishment['until']) ? $this->formatDuration((int)$punishment['until']) : null,
                'active' => $this->isPunishmentActive($punishment, $rowType),
                'removed_by' => isset($punishment['removed_by_name']) ? SecurityManager::preventXss($punishment['removed_by_name']) : null,
                'avatar' => $this->getAvatarUrl($punishment['uuid'] ?? '', $playerName ?? 'Unknown')
            ];
        }, $punishments);
    }
}
