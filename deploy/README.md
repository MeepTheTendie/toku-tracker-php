# Deploying to the existing Hetzner server

Inspected server: `ubuntu-4gb-nbg1-2`, `162.55.208.142`, ARM64 Debian 13.
Apache serves `myworkouttracker.xyz`; PHP 8.4 is installed. The SQLite extension
was missing at inspection. No old Toku installation was found in the usual
application directories. These instructions deploy alongside the workout site.

## Install and configure

1. Back up the existing Apache site configuration before editing it. Install
   `php8.4-sqlite3` with apt; check `php -m` includes `pdo_sqlite`. This enables
   SQLite without replacing the existing MySQL/MariaDB extensions.
2. Put this checkout at `/var/www/toku-tracker`. Keep application source owned by
   root and readable by `www-data`; only the private data directory needs writes.
3. Create the private data directory:

   ```sh
   install -d -o www-data -g www-data -m 0700 /var/www/toku-tracker/data
   ```

4. If recovering the original tracker, copy its consistent `data/toku.db` backup
   into that directory **before the first request**. Give that file to
   `www-data`, mode `0600`. Never delete it to add series.
5. Copy `config.example.php` to `config.local.php`. Set `TOKU_BASE_PATH` to
   `/toku` and `TOKU_HTTPS` to `1`. Run `php bin/setup.php` in an interactive
   terminal to choose a separate tracker password. It is entered without echo
   and only a hash is saved. Then set:

   ```sh
   chown root:www-data /var/www/toku-tracker/config.local.php
   chmod 0640 /var/www/toku-tracker/config.local.php
   ```

6. Copy `deploy/apache-toku.conf` to `/etc/apache2/toku-tracker.conf`. Add
   `Include /etc/apache2/toku-tracker.conf` **inside the existing HTTPS
   VirtualHost** in the workout site's TLS configuration. Keep its DocumentRoot,
   TLS certificate, and existing application directives intact. The HTTP site
   should continue redirecting to HTTPS.
7. Check before reloading:

   ```sh
   apache2ctl configtest
   systemctl reload apache2
   ```

8. Visit `https://myworkouttracker.xyz/toku/`. Verify login, a watched episode,
   search, export, and the existing workout site. Check the private paths
   `/toku/data/toku.db`, `/toku/config.local.php`, and `/toku/.git/config` return 404.

For a dedicated domain, point its DocumentRoot at `public/`, set the base path
to the empty string, and use the supplied `public/.htaccess` with Apache rewrite
enabled. Never serve the repository root. For Nginx or PHP-FPM, likewise expose
only `public/` and route requests to its `index.php`.

## Backups, updates, and rollback

Run `php bin/backup.php /private/backup/destination.db` as the database owner.
The directory must already exist and the destination must be new. Backups use
SQLite's `VACUUM INTO`, so committed WAL changes are included. Keep copies off
the server. The JSON export is useful for portability, but SQLite backups are
the restore format supported by this release.

Before an update, make a backup and preserve `config.local.php` and `data/`.
Catalog keys are permanent: edits to names and counts update the same series.
The first legacy migration also creates a `.pre-rewrite-*.db` backup adjacent
to the database. Removed catalog rows and out-of-range watch records are kept
for recovery rather than silently discarded.

To roll back the web deployment, remove the added Include, run the Apache
config test, and reload. Keep the new tracker directory and database until the
rollback is verified. To restore a database, stop tracker access first and move
the current database **and any matching `-wal`/`-shm` files** into a private
recovery directory before copying the backup into place. Restore ownership and
permissions, then re-enable access. Do not combine old backups with newer WAL
files.
