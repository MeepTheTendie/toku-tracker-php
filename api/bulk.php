<?php
/**
 * API: Bulk Operations (watch all, unwatch all)
 */

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/TokuTracker.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$seriesId = (int) ($data['series_id'] ?? 0);
$action = $data['action'] ?? '';

if ($seriesId <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing series_id']);
    exit;
}

try {
    // Verify series exists before action
    $series = TokuTracker::getSeries($seriesId);
    if (!$series) {
        http_response_code(404);
        echo json_encode(['error' => 'Series not found']);
        exit;
    }

    switch ($action) {
        case 'watch_all':
            $success = TokuTracker::watchAll($seriesId);
            echo json_encode([
                'success' => $success,
                'action' => 'watch_all',
                'series_id' => $seriesId
            ]);
            break;
            
        case 'unwatch_all':
            $success = TokuTracker::unwatchAll($seriesId);
            echo json_encode([
                'success' => $success,
                'action' => 'unwatch_all',
                'series_id' => $seriesId
            ]);
            break;
            
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action. Use: watch_all, unwatch_all']);
            break;
    }
} catch (Exception $e) {
    error_log('API Error in bulk.php: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Internal server error']);
}
