<?php
/**
 * API: Watch/Unwatch Episode
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/TokuTracker.php';

header('Content-Type: application/json');

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Get input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$seriesId = (int) ($data['series_id'] ?? 0);
$episode = (int) ($data['episode'] ?? 0);
$action = $data['action'] ?? 'watch';

if ($seriesId <= 0 || $episode <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing series_id or episode']);
    exit;
}

try {
    // Verify series exists
    $series = TokuTracker::getSeries($seriesId);
    if (!$series) {
        http_response_code(404);
        echo json_encode(['error' => 'Series not found']);
        exit;
    }

    // Validate episode number is within range
    if ($episode < 1 || $episode > $series['episodes']) {
        http_response_code(400);
        echo json_encode(['error' => 'Episode number out of range']);
        exit;
    }

    // Perform action
    if ($action === 'watch') {
        $success = TokuTracker::watchEpisode($seriesId, $episode);
        echo json_encode([
            'success' => $success,
            'action' => 'watched',
            'series_id' => $seriesId,
            'episode' => $episode
        ]);
    } elseif ($action === 'unwatch') {
        $success = TokuTracker::unwatchEpisode($seriesId, $episode);
        echo json_encode([
            'success' => $success,
            'action' => 'unwatched',
            'series_id' => $seriesId,
            'episode' => $episode
        ]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid action']);
    }
} catch (Exception $e) {
    error_log('API Error in watch.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
