#!/bin/bash
# Toku Tracker Launcher

cd "$(dirname "$0")"

echo "=============================================="
echo "           ⚡ Toku Tracker PHP"
echo "=============================================="
echo ""

# Check PHP
if ! command -v php &> /dev/null; then
    echo "Error: PHP is not installed"
    exit 1
fi

PHP_VERSION=$(php -v | head -n 1 | cut -d " " -f 2 | cut -d "." -f 1,2)
echo "PHP version: $PHP_VERSION"

# Check required extensions
MISSING=""

if ! php -m | grep -q "pdo"; then
    MISSING="$MISSING pdo"
fi

if ! php -m | grep -q "pdo_sqlite"; then
    MISSING="$MISSING pdo_sqlite"
fi

if [ -n "$MISSING" ]; then
    echo "Error: Missing required PHP extensions:$MISSING"
    echo "Install them with your package manager:"
    echo "  Ubuntu/Debian: sudo apt install php-sqlite3"
    echo "  Arch: sudo pacman -S php-sqlite"
    exit 1
fi

echo "Required extensions: OK"
echo ""
echo "Data folder: $(pwd)/data"
echo "Cache folder: $(pwd)/cache"
echo ""
echo "----------------------------------------------"
echo "Starting server on http://localhost:8080"
echo "Press Ctrl+C to stop"
echo "=============================================="
echo ""

php -S localhost:8080 index.php
