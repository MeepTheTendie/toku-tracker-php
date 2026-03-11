<?php
// Bootstrap for toku-tracker tests

if (!defined('DB_FILE')) {
    define('DB_FILE', __DIR__ . '/../data/toku.db');
}

require_once __DIR__ . '/../lib/Database.php';
require_once __DIR__ . '/../config.php';
