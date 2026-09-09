<?php
declare(strict_types=1);

final class Security {
    public static function start(array $config): void {
        ini_set('display_errors', '0');
        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        session_name('toku_session');
        session_set_cookie_params(['httponly' => true, 'secure' => $config['secure'], 'samesite' => 'Strict', 'path' => $config['base'] . '/']);
        session_start();
        header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data:; object-src 'none'; base-uri 'none'; frame-ancestors 'none'; form-action 'self'");
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('Referrer-Policy: same-origin');
        header('Cache-Control: no-store');
        $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    }

    public static function csrf(mixed $token): bool {
        return is_string($token) && isset($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $token);
    }

    public static function authenticated(string $hash): bool {
        return $hash !== '' && isset($_SESSION['auth'], $_SESSION['expires']) &&
            $_SESSION['expires'] > time() && hash_equals(hash('sha256', $hash), $_SESSION['auth']);
    }

    public static function login(string $hash): void {
        session_regenerate_id(true);
        $_SESSION = ['auth' => hash('sha256', $hash), 'expires' => time() + 43200, 'csrf' => bin2hex(random_bytes(32))];
    }
}
