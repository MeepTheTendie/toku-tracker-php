<?php
declare(strict_types=1);

final class Store {
    private PDO $db;

    public function __construct(string $path, array $catalog) {
        if (!is_dir(dirname($path)) && !mkdir(dirname($path), 0700, true) && !is_dir(dirname($path))) {
            throw new RuntimeException('Cannot create data directory');
        }
        $existed = is_file($path);
        $this->db = new PDO('sqlite:' . $path, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
        $this->db->exec('PRAGMA busy_timeout = 5000');
        $version = (int) $this->db->query('PRAGMA user_version')->fetchColumn();
        if ($version > 1) throw new RuntimeException('Database is newer than this application');
        // VACUUM INTO makes a consistent backup, including committed WAL content.
        if ($existed && $version === 0) $this->backup($path . '.pre-rewrite-' . bin2hex(random_bytes(4)) . '.db');
        $this->db->exec('PRAGMA journal_mode = WAL');
        $this->db->exec('PRAGMA foreign_keys = ON');
        $this->migrate();
        $this->syncCatalog($catalog);
    }

    private function transaction(callable $work): mixed {
        $this->db->exec('BEGIN IMMEDIATE');
        try {
            $result = $work();
            $this->db->exec('COMMIT');
            return $result;
        } catch (Throwable $e) {
            $this->db->exec('ROLLBACK');
            throw $e;
        }
    }

    private function migrate(): void {
        if ((int) $this->db->query('PRAGMA user_version')->fetchColumn() === 1) return;
        $this->transaction(function (): void {
            $this->db->exec('CREATE TABLE IF NOT EXISTS series (
                id INTEGER PRIMARY KEY AUTOINCREMENT, franchise TEXT NOT NULL,
                name TEXT NOT NULL, era TEXT NOT NULL, year INTEGER NOT NULL,
                episodes INTEGER NOT NULL, tags TEXT)');
            $columns = array_column($this->db->query('PRAGMA table_info(series)')->fetchAll(), 'name');
            if (!in_array('catalog_key', $columns, true)) $this->db->exec('ALTER TABLE series ADD COLUMN catalog_key TEXT');
            if (!in_array('enabled', $columns, true)) $this->db->exec('ALTER TABLE series ADD COLUMN enabled INTEGER NOT NULL DEFAULT 1');
            $this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS series_catalog_key ON series(catalog_key)');
            $this->db->exec('CREATE TABLE IF NOT EXISTS episodes (
                id INTEGER PRIMARY KEY AUTOINCREMENT, series_id INTEGER NOT NULL,
                episode_number INTEGER NOT NULL, title TEXT)');
            $this->db->exec('CREATE UNIQUE INDEX IF NOT EXISTS episode_identity ON episodes(series_id, episode_number)');
            $this->db->exec('CREATE TABLE IF NOT EXISTS watched (
                id INTEGER PRIMARY KEY AUTOINCREMENT, series_id INTEGER NOT NULL,
                episode_number INTEGER NOT NULL, watched_at TEXT DEFAULT CURRENT_TIMESTAMP,
                UNIQUE(series_id, episode_number))');
            $this->db->exec('CREATE TABLE IF NOT EXISTS metadata (key TEXT PRIMARY KEY, value TEXT NOT NULL)');
            $this->db->exec('CREATE TABLE IF NOT EXISTS login_attempts (address TEXT PRIMARY KEY, failures INTEGER NOT NULL, started INTEGER NOT NULL)');
            $this->db->exec('PRAGMA user_version = 1');
        });
    }

    public function syncCatalog(array $catalog): void {
        $seen = [];
        foreach ($catalog as $row) {
            if (!is_string($row['key'] ?? null) || $row['key'] === '' || isset($seen[$row['key']]) ||
                !is_int($row['episodes'] ?? null) || $row['episodes'] < 1 || $row['episodes'] > 10000) {
                throw new InvalidArgumentException('Invalid or duplicate catalog entry');
            }
            $seen[$row['key']] = true;
        }
        $hash = hash('sha256', json_encode($catalog, JSON_THROW_ON_ERROR));
        if ($this->query("SELECT value FROM metadata WHERE key = 'catalog_hash'")->fetchColumn() === $hash) return;
        $this->transaction(function () use ($catalog, $hash): void {
            if ($this->query("SELECT value FROM metadata WHERE key = 'catalog_hash'")->fetchColumn() === $hash) return;
            foreach ($catalog as $row) {
                $id = $this->query('SELECT id FROM series WHERE catalog_key = ?', [$row['key']])->fetchColumn();
                // Adopt the original row, preserving IDs and all watched timestamps.
                if ($id === false) {
                    $id = $this->query('SELECT id FROM series WHERE catalog_key IS NULL AND franchise = ? AND name = ? ORDER BY id LIMIT 1', [$row['franchise'], $row['name']])->fetchColumn();
                }
                $values = [$row['franchise'], $row['name'], $row['era'], $row['year'], $row['episodes'], json_encode($row['tags'], JSON_THROW_ON_ERROR), $row['key']];
                if ($id === false) {
                    $this->query('INSERT INTO series (franchise,name,era,year,episodes,tags,catalog_key) VALUES (?,?,?,?,?,?,?)', $values);
                    $id = (int) $this->db->lastInsertId();
                } else {
                    $this->query('UPDATE series SET franchise=?,name=?,era=?,year=?,episodes=?,tags=?,catalog_key=? WHERE id=?', [...$values, $id]);
                }
                // One statement per series instead of one per episode. CAST keeps the
                // recursion bound numeric: PDO binds parameters as text by default and
                // SQLite treats an integer as less than any text.
                $this->query('WITH RECURSIVE seq(n) AS (SELECT 1 UNION ALL SELECT n+1 FROM seq WHERE n < CAST(? AS INTEGER))
                    INSERT OR IGNORE INTO episodes (series_id,episode_number,title)
                    SELECT ?, n, \'Episode \' || n FROM seq', [$row['episodes'], $id]);
            }
            // Removed catalog entries stay intact: history is never implicitly deleted.
            $this->query("INSERT INTO metadata (key,value) VALUES ('catalog_hash',?) ON CONFLICT(key) DO UPDATE SET value=excluded.value", [$hash]);
        });
    }

    private function query(string $sql, array $params = []): PDOStatement {
        $statement = $this->db->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function series(array $filters = []): array {
        return array_values($this->mapSeries($this->seriesRows($filters)));
    }

    private function seriesRows(array $filters): array {
        $status = $filters['filter'] ?? '';
        if ($status !== '' && !in_array($status, ['watching', 'completed', 'unwatched'], true)) return [];

        $sql = 'SELECT s.*, COUNT(w.id) AS watched, MAX(w.watched_at) AS last_watched FROM series s
            LEFT JOIN watched w ON w.series_id=s.id AND w.episode_number BETWEEN 1 AND s.episodes WHERE s.enabled=1';
        $params = [];
        if (($filters['id'] ?? null) !== null) { $sql .= ' AND s.id=?'; $params[] = (int) $filters['id']; }
        foreach (['franchise', 'era'] as $field) {
            if (($filters[$field] ?? '') !== '') { $sql .= " AND s.$field=?"; $params[] = $filters[$field]; }
        }
        if (($filters['q'] ?? '') !== '') {
            $term = '%' . str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $filters['q']) . '%';
            $sql .= " AND (s.name LIKE ? ESCAPE '\' OR s.tags LIKE ? ESCAPE '\' OR s.era LIKE ? ESCAPE '\')";
            array_push($params, $term, $term, $term);
        }
        $sql .= ' GROUP BY s.id';
        if ($status !== '') {
            // Filtering the derived status in SQL avoids loading every row and discarding in PHP.
            $sql .= " HAVING (CASE WHEN COUNT(w.id) >= s.episodes THEN 'completed'
                WHEN COUNT(w.id) > 0 THEN 'watching' ELSE 'unwatched' END) = ?";
            $params[] = $status;
        }
        $sql .= ' ORDER BY s.year DESC, s.name';
        return $this->query($sql, $params)->fetchAll();
    }

    private function mapSeries(array $rows): array {
        return array_map(static function (array $s): array {
            $s['watched'] = (int) $s['watched'];
            $s['progress'] = $s['episodes'] ? round(100 * $s['watched'] / $s['episodes']) : 0;
            $s['status'] = $s['watched'] >= $s['episodes'] ? 'completed' : ($s['watched'] ? 'watching' : 'unwatched');
            $s['tags'] = json_decode($s['tags'] ?: '[]', true, 512, JSON_THROW_ON_ERROR);
            return $s;
        }, $rows);
    }

    public function detail(int $id): ?array {
        $rows = $this->mapSeries($this->seriesRows(['id' => $id]));
        $s = $rows[0] ?? null;
        if (!$s) return null;
        $s['episodes_list'] = $this->query('SELECT e.episode_number,e.title,w.watched_at,
            CASE WHEN w.id IS NULL THEN 0 ELSE 1 END AS is_watched FROM episodes e
            LEFT JOIN watched w ON w.series_id=e.series_id AND w.episode_number=e.episode_number
            WHERE e.series_id=? AND e.episode_number BETWEEN 1 AND ? ORDER BY e.episode_number', [$id, $s['episodes']])->fetchAll();
        $s['next_episode'] = null;
        foreach ($s['episodes_list'] as $ep) if (!$ep['is_watched']) { $s['next_episode'] = $ep['episode_number']; break; }
        return $s;
    }

    public function change(int $id, string $action, ?int $episode = null): void {
        if (!in_array($action, ['watch','unwatch','watch_all','unwatch_all'], true)) throw new InvalidArgumentException('Unknown action');
        $this->transaction(function () use ($id, $action, $episode): void {
            $series = $this->query('SELECT episodes FROM series WHERE id=? AND enabled=1', [$id])->fetch();
            if (!$series) throw new OutOfBoundsException('Series not found');
            if (in_array($action, ['watch','unwatch'], true) && ($episode === null || $episode < 1 || $episode > $series['episodes'])) {
                throw new InvalidArgumentException('Episode number is out of range');
            }
            match ($action) {
                'watch' => $this->query('INSERT OR IGNORE INTO watched (series_id,episode_number) VALUES (?,?)', [$id,$episode]),
                'unwatch' => $this->query('DELETE FROM watched WHERE series_id=? AND episode_number=?', [$id,$episode]),
                'watch_all' => $this->query('INSERT OR IGNORE INTO watched (series_id,episode_number) SELECT series_id,episode_number FROM episodes WHERE series_id=? AND episode_number BETWEEN 1 AND ?', [$id,$series['episodes']]),
                'unwatch_all' => $this->query('DELETE FROM watched WHERE series_id=?', [$id]),
            };
        });
    }

    public function export(): array {
        return ['version' => 1, 'exported_at' => gmdate(DATE_ATOM), 'watched' => $this->query('SELECT s.catalog_key,s.name,w.episode_number,w.watched_at FROM watched w JOIN series s ON s.id=w.series_id ORDER BY s.id,w.episode_number')->fetchAll()];
    }

    public function backup(string $destination): void {
        if (file_exists($destination)) throw new RuntimeException('Backup destination already exists');
        $this->db->exec('VACUUM INTO ' . $this->db->quote($destination));
        chmod($destination, 0600);
    }

    public function loginAllowed(string $address, int $now): bool {
        $row = $this->query('SELECT * FROM login_attempts WHERE address=?', [$address])->fetch();
        return !$row || $row['started'] <= $now - 900 || $row['failures'] < 10;
    }

    public function recordLogin(string $address, bool $success, int $now): void {
        if ($success) { $this->query('DELETE FROM login_attempts WHERE address=?', [$address]); return; }
        $this->transaction(function () use ($address,$now): void {
            $this->query('DELETE FROM login_attempts WHERE started <= ?', [$now - 900]);
            $this->query('INSERT INTO login_attempts (address,failures,started) VALUES (?,1,?) ON CONFLICT(address) DO UPDATE SET failures=failures+1', [$address,$now]);
        });
    }
}
