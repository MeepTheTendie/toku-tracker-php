#!/usr/bin/env bash
set -euo pipefail
cd -- "$(dirname -- "$0")"
PHP_BIN="${PHP_BIN:-php}"
"$PHP_BIN" -r 'if (!extension_loaded("pdo_sqlite")) {fwrite(STDERR, "Install PHP pdo_sqlite first.\n"); exit(1);}'
printf 'Toku Tracker: http://127.0.0.1:8080\n'
exec "$PHP_BIN" -S 127.0.0.1:8080 -t public public/router.php
