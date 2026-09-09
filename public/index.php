<?php
declare(strict_types=1);

require dirname(__DIR__) . '/app/bootstrap.php';

function respond(array $body, int $status = 200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($body, JSON_THROW_ON_ERROR);
    exit;
}

function redirect(string $path = '', array $params = []): never {
    header('Location: ' . url($path, $params), true, 303);
    exit;
}

function parameter(array $input, string $key, int $max = 200): string {
    $value = $input[$key] ?? '';
    if (!is_string($value) || strlen($value) > $max) throw new InvalidArgumentException('Invalid ' . $key);
    return trim($value);
}

function positiveId(mixed $value): int {
    if ((!is_string($value) && !is_int($value)) || !preg_match('/^[1-9][0-9]{0,8}$/D', (string) $value)) {
        throw new InvalidArgumentException('Expected a positive integer');
    }
    return (int) $value;
}

$isApi = false;
$config = ['base' => ''];
try {
    $config = settings();
    Security::start($config);
    $rawPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if (!is_string($rawPath) || !str_starts_with($rawPath, $config['base'] . '/')) {
        http_response_code(404); echo 'Not found'; exit;
    }
    $path = trim(substr($rawPath, strlen($config['base'])), '/');
    $method = $_SERVER['REQUEST_METHOD'];
    $routes = ['', 'index.php', 'series', 'series-detail', 'watch', 'search', 'stats', 'login', 'logout', 'export', 'api/watch', 'api/bulk'];
    if (!in_array($path, $routes, true)) { http_response_code(404); echo 'Not found'; exit; }
    $isApi = str_starts_with($path, 'api/');
    $allowed = $isApi || $path === 'logout' ? ['POST'] : ($path === 'login' ? ['GET','POST'] : ['GET']);
    if (!in_array($method, $allowed, true)) {
        header('Allow: ' . implode(', ', $allowed));
        if ($isApi) respond(['error' => 'Method not allowed'], 405);
        http_response_code(405); echo 'Method not allowed'; exit;
    }
    if ($config['password_hash'] === '' && !$config['allow_anonymous']) {
        http_response_code(503);
        echo 'Tracker setup is incomplete. The administrator must run bin/setup.php before signing in.';
        exit;
    }
    $signedIn = $config['allow_anonymous'] || Security::authenticated($config['password_hash']);
    if (!$signedIn && $path !== 'login') {
        if ($isApi) respond(['error' => 'Your session expired. Sign in again.'], 401);
        redirect('login');
    }
    $store = new Store($config['database'], require dirname(__DIR__) . '/catalog/series.php');
    $error = null;
    if ($path === 'login') {
        if ($signedIn) redirect();
        if ($method === 'POST') {
            if (!Security::csrf($_POST['csrf'] ?? null)) { http_response_code(403); $error = 'This form expired. Please try again.'; }
            else {
                $address = hash('sha256', $_SERVER['REMOTE_ADDR'] ?? 'unknown');
                if (!$store->loginAllowed($address, time())) { http_response_code(429); header('Retry-After: 900'); $error = 'Too many attempts. Try again in 15 minutes.'; }
                else {
                    $password = $_POST['password'] ?? null;
                    $ok = is_string($password) && strlen($password) <= 1024 && password_verify($password, $config['password_hash']);
                    $store->recordLogin($address, $ok, time());
                    if ($ok) { Security::login($config['password_hash']); redirect(); }
                    http_response_code(401); $error = 'Incorrect password.';
                }
            }
        }
        $page = 'login'; $title = 'Welcome back';
    } elseif ($path === 'logout') {
        if (!Security::csrf($_POST['csrf'] ?? null)) { http_response_code(403); echo 'Invalid form token'; exit; }
        $_SESSION = [];
        session_regenerate_id(true);
        redirect('login');
    } elseif ($isApi) {
        $json = str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
        if ($json) {
            $body = file_get_contents('php://input', false, null, 0, 8193);
            if ($body === false || strlen($body) > 8192) respond(['error' => 'Request too large'], 413);
            try { $data = json_decode($body, true, 16, JSON_THROW_ON_ERROR); }
            catch (JsonException) { respond(['error' => 'Invalid JSON'], 400); }
            if (!is_array($data) || array_is_list($data)) respond(['error' => 'Expected a JSON object'], 400);
        } else {
            if (!str_starts_with($_SERVER['CONTENT_TYPE'] ?? '', 'application/x-www-form-urlencoded')) respond(['error' => 'Unsupported content type'], 415);
            $data = $_POST;
        }
        if (!Security::csrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? $data['csrf'] ?? null)) respond(['error' => 'This form expired. Reload the page.'], 403);
        $id = positiveId($data['series_id'] ?? null);
        $action = parameter($data, 'action', 20);
        $actions = $path === 'api/watch' ? ['watch','unwatch'] : ['watch_all','unwatch_all'];
        if (!in_array($action, $actions, true)) throw new InvalidArgumentException('Invalid action');
        $ep = $path === 'api/watch' ? positiveId($data['episode'] ?? null) : null;
        $store->change($id, $action, $ep);
        $next = null;
        $destination = url('series-detail', ['id' => $id]);
        if ($path === 'api/watch') {
            $detail = $store->detail($id);
            $next = $action === 'watch' && $ep < $detail['episodes'] ? $ep + 1 : $ep;
            $destination = url('watch', ['id' => $id, 'episode' => $next]);
        }
        if (!$json) { header('Location: ' . $destination, true, 303); exit; }
        respond(['success' => true, 'redirect' => $destination]);
    } elseif ($path === 'export') {
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="toku-progress-' . gmdate('Y-m-d') . '.json"');
        echo json_encode($store->export(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        exit;
    } else {
        $all = $store->series();
        $stats = [
            'series' => count($all), 'episodes' => array_sum(array_column($all, 'episodes')),
            'watched' => array_sum(array_column($all, 'watched')),
            'completed' => count(array_filter($all, static fn($s) => $s['status'] === 'completed')),
        ];
        $stats['progress'] = $stats['episodes'] ? round(100 * $stats['watched'] / $stats['episodes'], 1) : 0;
        if ($path === '' || $path === 'index.php') {
            $page = 'dashboard'; $title = 'Your next transformation.';
            $continuing = array_values(array_filter($all, static fn($s) => $s['status'] === 'watching'));
            usort($continuing, static fn($a,$b) => strcmp($b['last_watched'], $a['last_watched']));
            $series = array_slice($continuing, 0, 6);
        } elseif ($path === 'series' || $path === 'search') {
            $page = 'series'; $title = $path === 'search' ? 'Find your next favorite.' : 'The series library.';
            $filters = [];
            foreach (['q','franchise','era','filter'] as $key) $filters[$key] = parameter($_GET, $key);
            if ($filters['franchise'] !== '' && !isset(franchises()[$filters['franchise']])) throw new InvalidArgumentException('Unknown franchise');
            if (!in_array($filters['filter'], ['', 'watching','completed','unwatched'], true)) throw new InvalidArgumentException('Unknown status');
            $series = $store->series($filters);
            $eras = array_values(array_unique(array_column($all, 'era'))); sort($eras);
        } elseif ($path === 'series-detail' || $path === 'watch') {
            $id = positiveId($_GET['id'] ?? null);
            $detail = $store->detail($id);
            if (!$detail) throw new OutOfBoundsException('Series not found');
            $page = $path === 'watch' ? 'watch' : 'detail'; $title = $detail['name'];
            $episode = isset($_GET['episode']) ? positiveId($_GET['episode']) : ($detail['next_episode'] ?? $detail['episodes']);
            if ($episode > $detail['episodes']) throw new OutOfBoundsException('Episode not found');
        } else { $page = 'stats'; $title = 'Every episode counts.'; }
    }
    require dirname(__DIR__) . '/app/view.php';
} catch (Throwable $e) {
    $status = $e instanceof OutOfBoundsException ? 404 : ($e instanceof InvalidArgumentException ? 400 : 500);
    $message = $status === 500 ? 'Something went wrong. Your change may not have saved. Please try again.' : $e->getMessage();
    if ($status === 500) error_log('Toku Tracker: ' . $e);
    if ($isApi) respond(['success' => false, 'error' => $message], $status);
    http_response_code($status);
    echo '<!doctype html><meta charset="utf-8"><title>Request failed</title><h1>Request failed</h1><p>' . h($message) . '</p><a href="' . h(url()) . '">Return to tracker</a>';
}
