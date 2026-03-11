<?php
/**
 * Toku Tracker - Router
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/lib/TokuTracker.php';

// Get path
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = ltrim($path, '/');
$path = strtok($path, '?');

// Handle subdirectory deployment (/toku/...)
if (strpos($path, 'toku/') === 0) {
    $path = substr($path, 5); // Remove 'toku/' prefix
}
$path = ltrim($path, '/'); // Remove any leading slash remaining

// API routes
if (strpos($path, 'api/') === 0) {
    require __DIR__ . '/api/' . substr($path, 4) . '.php';
    exit;
}

// Page routes
switch ($path) {
    case '':
    case 'index.php':
        $stats = TokuTracker::getStats();
        $nextToWatch = TokuTracker::getNextToWatch(6);
        $recentlyWatched = TokuTracker::getRecentlyWatched(5);
        include __DIR__ . '/templates/dashboard.php';
        break;
        
    case 'series':
        $franchise = $_GET['franchise'] ?? null;
        $filter = $_GET['filter'] ?? null;
        $era = $_GET['era'] ?? null;
        $series = TokuTracker::getAllSeries($franchise, $filter);
        
        // Filter by era if specified
        if ($era) {
            $series = array_filter($series, fn($s) => $s['era'] === $era);
        }
        
        include __DIR__ . '/templates/series.php';
        break;
        
    case 'series-detail':
        $id = (int) ($_GET['id'] ?? 0);
        $series = TokuTracker::getSeries($id);
        if (!$series) {
            header('Location: ./series');
            exit;
        }
        include __DIR__ . '/templates/series_detail.php';
        break;
        
    case 'watch':
        $id = (int) ($_GET['id'] ?? 0);
        $series = TokuTracker::getSeries($id);
        if (!$series) {
            header('Location: ./series');
            exit;
        }
        $episode = (int) ($_GET['episode'] ?? TokuTracker::getNextEpisodeNumber($id));
        include __DIR__ . '/templates/watch.php';
        break;
        
    case 'search':
        $query = $_GET['q'] ?? '';
        $results = $query ? TokuTracker::search($query) : [];
        include __DIR__ . '/templates/search.php';
        break;
        
    case 'stats':
        $stats = TokuTracker::getStats();
        $franchiseStats = TokuTracker::getFranchiseStats();
        include __DIR__ . '/templates/stats.php';
        break;
        
    default:
        http_response_code(404);
        echo '<h1>404 - Not Found</h1><a href="./">Go Home</a>';
        break;
}
