<?php
declare(strict_types=1);
// Development server: expose only known assets, never arbitrary filesystem paths.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = rtrim(getenv('TOKU_BASE_PATH') ?: '', '/');
if (in_array($path, [$base . '/assets/app.css', $base . '/assets/app.js', $base . '/assets/favicon.svg'], true)) {
    $file = basename($path);
    header('Content-Type: ' . match ($file) { 'app.css' => 'text/css', 'app.js' => 'text/javascript', 'favicon.svg' => 'image/svg+xml' });
    header('X-Content-Type-Options: nosniff');
    readfile(__DIR__ . '/assets/' . $file);
    return;
}
require __DIR__ . '/index.php';
