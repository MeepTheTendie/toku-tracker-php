<?php
/**
 * Toku Tracker - Database Handler
 * Complete with full tokusatsu library
 */

class Database {
    private static ?PDO $instance = null;
    
    public static function get(): PDO {
        if (self::$instance === null) {
            self::$instance = new PDO('sqlite:' . DB_FILE);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::init();
        }
        return self::$instance;
    }
    
    private static function init(): void {
        $db = self::$instance;
        
        // Series table with tags support
        $db->exec('CREATE TABLE IF NOT EXISTS series (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            franchise TEXT NOT NULL,
            name TEXT NOT NULL,
            era TEXT NOT NULL,
            year INTEGER NOT NULL,
            episodes INTEGER NOT NULL,
            tags TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )');
        
        // Create indexes for better search performance
        $db->exec('CREATE INDEX IF NOT EXISTS idx_franchise ON series(franchise)');
        $db->exec('CREATE INDEX IF NOT EXISTS idx_era ON series(era)');
        $db->exec('CREATE INDEX IF NOT EXISTS idx_year ON series(year)');
        
        // Episodes table
        $db->exec('CREATE TABLE IF NOT EXISTS episodes (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            series_id INTEGER NOT NULL,
            episode_number INTEGER NOT NULL,
            title TEXT,
            FOREIGN KEY (series_id) REFERENCES series(id)
        )');
        
        $db->exec('CREATE INDEX IF NOT EXISTS idx_series_ep ON episodes(series_id, episode_number)');
        
        // Watched episodes tracking
        $db->exec('CREATE TABLE IF NOT EXISTS watched (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            series_id INTEGER NOT NULL,
            episode_number INTEGER NOT NULL,
            watched_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE(series_id, episode_number)
        )');
        
        // Insert full database if empty
        $count = $db->query('SELECT COUNT(*) FROM series')->fetchColumn();
        if ($count == 0) {
            self::seedFullDatabase();
        }
    }
    
    private static function seedFullDatabase(): void {
        $db = self::$instance;
        $db->beginTransaction();
        
        try {
            $stmt = $db->prepare('INSERT INTO series (franchise, name, era, year, episodes, tags) VALUES (?, ?, ?, ?, ?, ?)');
            $epStmt = $db->prepare('INSERT INTO episodes (series_id, episode_number, title) VALUES (?, ?, ?)');
            
            foreach (FULL_SERIES_DATABASE as $series) {
                // Skip series from disabled franchises
                if (!isset(FRANCHISES[$series['franchise']])) {
                    continue;
                }
                
                $stmt->execute([
                    $series['franchise'],
                    $series['name'],
                    $series['era'],
                    $series['year'],
                    $series['episodes'],
                    json_encode($series['tags'] ?? [])
                ]);
                
                $seriesId = $db->lastInsertId();
                
                // Generate episodes
                for ($i = 1; $i <= $series['episodes']; $i++) {
                    $epStmt->execute([$seriesId, $i, "Episode $i"]);
                }
            }
            
            $db->commit();
            
            // Log the seeding
            $totalSeries = count(FULL_SERIES_DATABASE);
            $totalEps = array_sum(array_column(FULL_SERIES_DATABASE, 'episodes'));
            error_log("Seeded database with $totalSeries series and $totalEps episodes");
            
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }
    
    /**
     * Reset database (for development)
     */
    public static function reset(): void {
        $db = self::$instance;
        $db->exec('DELETE FROM watched');
        $db->exec('DELETE FROM episodes');
        $db->exec('DELETE FROM series');
        $db->exec('DELETE FROM sqlite_sequence');
        self::seedFullDatabase();
    }
}
