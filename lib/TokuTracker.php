<?php
/**
 * Toku Tracker - Business Logic
 */
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Database.php';

class TokuTracker {
    
    /**
     * Get all franchises
     */
    public static function getFranchises(): array {
        return FRANCHISES;
    }
    
    /**
     * Get franchise stats
     */
    public static function getFranchiseStats(): array {
        $db = Database::get();
        $stats = [];
        
        foreach (FRANCHISES as $key => $franchise) {
            $stmt = $db->prepare('SELECT 
                COALESCE(SUM(episodes), 0) as total_episodes, 
                COUNT(*) as series_count, 
                MIN(year) as min_year, 
                MAX(year) as max_year 
                FROM series WHERE franchise = ?');
            $stmt->execute([$key]);
            $row = $stmt->fetch();
            
            $watchedStmt = $db->prepare('
                SELECT COUNT(*) FROM watched w
                JOIN series s ON w.series_id = s.id
                WHERE s.franchise = ?
            ');
            $watchedStmt->execute([$key]);
            $watched = (int) $watchedStmt->fetchColumn();
            
            $total = (int) $row['total_episodes'];
            
            $stats[$key] = [
                'name' => $franchise['name'],
                'icon' => $franchise['icon'],
                'color' => $franchise['color'],
                'bg_gradient' => $franchise['bg_gradient'],
                'series_count' => (int) $row['series_count'],
                'total_episodes' => $total,
                'watched' => $watched,
                'progress' => $total > 0 ? round(($watched / $total) * 100, 1) : 0,
                'year_range' => $row['min_year'] && $row['max_year'] ? $row['min_year'] . '-' . $row['max_year'] : '-',
            ];
        }
        
        return $stats;
    }
    
    /**
     * Get all series with watch progress
     */
    public static function getAllSeries(?string $franchise = null, ?string $filter = null): array {
        $db = Database::get();
        
        $sql = 'SELECT s.* FROM series s WHERE 1=1';
        $params = [];
        
        if ($franchise) {
            $sql .= ' AND s.franchise = ?';
            $params[] = $franchise;
        }
        
        // Filter by completion status
        if ($filter === 'completed') {
            $sql .= ' AND (SELECT COUNT(*) FROM watched w WHERE w.series_id = s.id) = s.episodes';
        } elseif ($filter === 'watching') {
            $sql .= ' AND (SELECT COUNT(*) FROM watched w WHERE w.series_id = s.id) > 0 
                      AND (SELECT COUNT(*) FROM watched w WHERE w.series_id = s.id) < s.episodes';
        } elseif ($filter === 'unwatched') {
            $sql .= ' AND (SELECT COUNT(*) FROM watched w WHERE w.series_id = s.id) = 0';
        }
        
        $sql .= ' ORDER BY s.year DESC, s.name';
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $series = $stmt->fetchAll();
        
        // Add progress and metadata
        foreach ($series as &$s) {
            $s['watched'] = self::getWatchedCount($s['id']);
            $s['progress'] = $s['episodes'] > 0 ? round(($s['watched'] / $s['episodes']) * 100) : 0;
            $s['franchise_name'] = FRANCHISES[$s['franchise']]['name'] ?? $s['franchise'];
            $s['franchise_icon'] = FRANCHISES[$s['franchise']]['icon'] ?? '📺';
            $s['franchise_color'] = FRANCHISES[$s['franchise']]['color'] ?? '#666';
            $s['tags'] = json_decode($s['tags'] ?? '[]', true);
            $s['status'] = $s['watched'] >= $s['episodes'] ? 'completed' : ($s['watched'] > 0 ? 'watching' : 'unwatched');
        }
        
        return $series;
    }
    
    /**
     * Get single series with episodes
     */
    public static function getSeries(int $id): ?array {
        $db = Database::get();
        
        $stmt = $db->prepare('SELECT * FROM series WHERE id = ?');
        $stmt->execute([$id]);
        $series = $stmt->fetch();
        
        if (!$series) return null;
        
        // Get episodes with watched status
        $epStmt = $db->prepare('
            SELECT e.*, CASE WHEN w.id IS NOT NULL THEN 1 ELSE 0 END as is_watched
            FROM episodes e
            LEFT JOIN watched w ON e.series_id = w.series_id AND e.episode_number = w.episode_number
            WHERE e.series_id = ?
            ORDER BY e.episode_number
        ');
        $epStmt->execute([$id]);
        $series['episodes_list'] = $epStmt->fetchAll();
        
        $series['watched'] = self::getWatchedCount($id);
        $series['progress'] = $series['episodes'] > 0 ? round(($series['watched'] / $series['episodes']) * 100) : 0;
        $series['franchise_data'] = FRANCHISES[$series['franchise']] ?? null;
        $series['tags'] = json_decode($series['tags'] ?? '[]', true);
        $series['status'] = $series['watched'] >= $series['episodes'] ? 'completed' : ($series['watched'] > 0 ? 'watching' : 'unwatched');
        
        return $series;
    }
    
    /**
     * Get watched count
     */
    public static function getWatchedCount(int $seriesId): int {
        $db = Database::get();
        $stmt = $db->prepare('SELECT COUNT(*) FROM watched WHERE series_id = ?');
        $stmt->execute([$seriesId]);
        return (int) $stmt->fetchColumn();
    }
    
    /**
     * Watch/unwatch episode
     */
    public static function watchEpisode(int $seriesId, int $episodeNumber): bool {
        $db = Database::get();
        try {
            $stmt = $db->prepare('INSERT OR IGNORE INTO watched (series_id, episode_number) VALUES (?, ?)');
            $stmt->execute([$seriesId, $episodeNumber]);
            
            // Update session
            if (!isset($_SESSION['watched'][$seriesId])) {
                $_SESSION['watched'][$seriesId] = [];
            }
            $_SESSION['watched'][$seriesId][$episodeNumber] = true;
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public static function unwatchEpisode(int $seriesId, int $episodeNumber): bool {
        $db = Database::get();
        try {
            $stmt = $db->prepare('DELETE FROM watched WHERE series_id = ? AND episode_number = ?');
            $stmt->execute([$seriesId, $episodeNumber]);
            
            unset($_SESSION['watched'][$seriesId][$episodeNumber]);
            
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    /**
     * Get dashboard stats
     */
    public static function getStats(): array {
        $db = Database::get();
        
        $stats = [
            'total_series' => (int) $db->query('SELECT COUNT(*) FROM series')->fetchColumn(),
            'total_episodes' => (int) $db->query('SELECT SUM(episodes) FROM series')->fetchColumn(),
            'watched_episodes' => (int) $db->query('SELECT COUNT(*) FROM watched')->fetchColumn(),
        ];
        
        $stats['overall_progress'] = $stats['total_episodes'] > 0 
            ? round(($stats['watched_episodes'] / $stats['total_episodes']) * 100, 1)
            : 0;
        
        // Count by status
        $stats['completed_series'] = (int) $db->query('
            SELECT COUNT(*) FROM series s 
            WHERE (SELECT COUNT(*) FROM watched w WHERE w.series_id = s.id) = s.episodes
        ')->fetchColumn();
        
        $stats['watching_series'] = (int) $db->query('
            SELECT COUNT(*) FROM series s 
            WHERE (SELECT COUNT(*) FROM watched w WHERE w.series_id = s.id) > 0
            AND (SELECT COUNT(*) FROM watched w WHERE w.series_id = s.id) < s.episodes
        ')->fetchColumn();
        
        $stats['franchise_stats'] = self::getFranchiseStats();
        
        return $stats;
    }
    
    /**
     * Get "Next to Watch" recommendations
     */
    public static function getNextToWatch(int $limit = 6): array {
        $db = Database::get();
        
        $stmt = $db->prepare('
            SELECT s.*, COUNT(w.id) as watched_count
            FROM series s
            LEFT JOIN watched w ON s.id = w.series_id
            GROUP BY s.id
            HAVING watched_count > 0 AND watched_count < s.episodes
            ORDER BY s.year DESC
            LIMIT ?
        ');
        $stmt->execute([$limit]);
        
        $inProgress = $stmt->fetchAll();
        
        foreach ($inProgress as &$s) {
            $s['next_episode'] = self::getNextEpisodeNumber($s['id']);
            $s['franchise_name'] = FRANCHISES[$s['franchise']]['name'] ?? $s['franchise'];
            $s['franchise_icon'] = FRANCHISES[$s['franchise']]['icon'] ?? '📺';
            $s['franchise_color'] = FRANCHISES[$s['franchise']]['color'] ?? '#666';
        }
        
        return $inProgress;
    }
    
    /**
     * Get recently watched (for history)
     */
    public static function getRecentlyWatched(int $limit = 10): array {
        $db = Database::get();
        
        $stmt = $db->prepare('
            SELECT s.*, w.episode_number, w.watched_at
            FROM watched w
            JOIN series s ON w.series_id = s.id
            ORDER BY w.watched_at DESC
            LIMIT ?
        ');
        $stmt->execute([$limit]);
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get next unwatched episode
     */
    public static function getNextEpisodeNumber(int $seriesId): int {
        $db = Database::get();
        
        // First try to find the first unwatched episode
        $stmt = $db->prepare('
            SELECT MIN(e.episode_number) as next_ep
            FROM episodes e
            LEFT JOIN watched w ON e.series_id = w.series_id AND e.episode_number = w.episode_number
            WHERE e.series_id = ? AND w.id IS NULL
        ');
        $stmt->execute([$seriesId]);
        $result = $stmt->fetchColumn();
        
        // If found, return it; otherwise return total episodes + 1 (all watched)
        if ($result !== null && $result !== false) {
            return (int) $result;
        }
        
        // All episodes watched - return episode count + 1
        $countStmt = $db->prepare('SELECT episodes FROM series WHERE id = ?');
        $countStmt->execute([$seriesId]);
        $total = (int) $countStmt->fetchColumn();
        
        return $total > 0 ? $total : 1;
    }
    
    /**
     * Search series by name or tags
     */
    public static function search(string $query): array {
        $db = Database::get();
        
        $stmt = $db->prepare('
            SELECT s.* FROM series s 
            WHERE s.name LIKE ? OR s.tags LIKE ?
            ORDER BY s.year DESC
        ');
        $searchTerm = '%' . $query . '%';
        $stmt->execute([$searchTerm, $searchTerm]);
        
        $series = $stmt->fetchAll();
        
        foreach ($series as &$s) {
            $s['watched'] = self::getWatchedCount($s['id']);
            $s['progress'] = $s['episodes'] > 0 ? round(($s['watched'] / $s['episodes']) * 100) : 0;
            $s['franchise_name'] = FRANCHISES[$s['franchise']]['name'] ?? $s['franchise'];
            $s['franchise_icon'] = FRANCHISES[$s['franchise']]['icon'] ?? '📺';
            $s['tags'] = json_decode($s['tags'] ?? '[]', true);
        }
        
        return $series;
    }
    
    /**
     * Get series by tag
     */
    public static function getByTag(string $tag): array {
        $db = Database::get();
        
        $stmt = $db->prepare('SELECT * FROM series WHERE tags LIKE ? ORDER BY year DESC');
        $stmt->execute(['%"' . $tag . '"%']);
        
        $series = $stmt->fetchAll();
        
        foreach ($series as &$s) {
            $s['watched'] = self::getWatchedCount($s['id']);
            $s['progress'] = $s['episodes'] > 0 ? round(($s['watched'] / $s['episodes']) * 100) : 0;
            $s['franchise_name'] = FRANCHISES[$s['franchise']]['name'] ?? $s['franchise'];
            $s['franchise_icon'] = FRANCHISES[$s['franchise']]['icon'] ?? '📺';
            $s['tags'] = json_decode($s['tags'] ?? '[]', true);
        }
        
        return $series;
    }
    
    /**
     * Bulk mark series as watched
     */
    public static function watchAll(int $seriesId): bool {
        $db = Database::get();
        $series = self::getSeries($seriesId);
        
        if (!$series) return false;
        
        $db->beginTransaction();
        try {
            $stmt = $db->prepare('INSERT OR IGNORE INTO watched (series_id, episode_number) VALUES (?, ?)');
            for ($i = 1; $i <= $series['episodes']; $i++) {
                $stmt->execute([$seriesId, $i]);
            }
            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            return false;
        }
    }
    
    /**
     * Bulk unwatch series
     */
    public static function unwatchAll(int $seriesId): bool {
        $db = Database::get();
        
        try {
            $stmt = $db->prepare('DELETE FROM watched WHERE series_id = ?');
            $stmt->execute([$seriesId]);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Get era breakdown for a franchise
     */
    public static function getEraBreakdown(string $franchise): array {
        $db = Database::get();
        
        $stmt = $db->prepare('
            SELECT era, COUNT(*) as count, SUM(episodes) as eps
            FROM series WHERE franchise = ?
            GROUP BY era
            ORDER BY MIN(year)
        ');
        $stmt->execute([$franchise]);
        
        return $stmt->fetchAll();
    }
}
