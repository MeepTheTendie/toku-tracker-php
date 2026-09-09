<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') exit;
require dirname(__DIR__) . '/app/bootstrap.php';
if ($argc !== 2) { fwrite(STDERR, "Usage: php bin/backup.php /private/path/backup.db\n"); exit(1); }
$config = settings();
$store = new Store($config['database'], require dirname(__DIR__) . '/catalog/series.php');
$store->backup($argv[1]);
echo "Consistent SQLite backup created.\n";
