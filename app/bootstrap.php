<?php
declare(strict_types=1);

require_once __DIR__ . '/Store.php';
require_once __DIR__ . '/Security.php';

function settings(): array {
    $root = dirname(__DIR__);
    $local = is_file($root . '/config.local.php') ? require $root . '/config.local.php' : [];
    $get = static fn(string $name, mixed $fallback) => getenv($name) !== false ? getenv($name) : ($local[$name] ?? $fallback);
    $base = rtrim((string) $get('TOKU_BASE_PATH', ''), '/');
    if ($base !== '' && !preg_match('~^(/[a-zA-Z0-9_-]+)+$~D', $base)) {
        throw new RuntimeException('Invalid TOKU_BASE_PATH');
    }
    return [
        'database' => $get('TOKU_DB', $root . '/data/toku.db'),
        'password_hash' => (string) $get('TOKU_PASSWORD_HASH', ''),
        'allow_anonymous' => $get('TOKU_ALLOW_ANONYMOUS', '0') === '1',
        'base' => $base,
        'secure' => $get('TOKU_HTTPS', '0') === '1' || (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ];
}

function h(mixed $value): string {
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function url(string $path = '', array $params = []): string {
    global $config;
    return $config['base'] . '/' . ltrim($path, '/') . ($params ? '?' . http_build_query($params) : '');
}

function franchises(): array {
    return [
        'kamen_rider' => ['name' => 'Kamen Rider', 'icon' => '🦗'],
        'super_sentai' => ['name' => 'Super Sentai', 'icon' => '🦖'],
        'ultraman' => ['name' => 'Ultraman', 'icon' => '✨'],
        'metal_hero' => ['name' => 'Metal Hero', 'icon' => '🤖'],
        'garo' => ['name' => 'GARO', 'icon' => '🐺'],
    ];
}
