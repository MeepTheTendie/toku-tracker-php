# Live deployment

Deployed September 5, 2026, from application commit `29004d0`.

- URL: https://myworkouttracker.xyz/
- Host: `162.55.208.142` (Debian 13 ARM64)
- Application: `/var/www/toku-tracker`
- Public document root: `/var/www/toku-tracker/public`
- Base path: empty (root deployment)
- PHP: 8.4, with `php8.4-sqlite3` installed
- Configuration: `/var/www/toku-tracker/config.local.php`, root:www-data, 0640
- Private data: `/var/www/toku-tracker/data`, www-data:www-data, 0700

The user chose to retire the workout site. Both existing Apache virtual hosts
now point to Toku Tracker's public directory; the TLS certificate and HTTP-to-
HTTPS redirect are retained. The earlier `/toku` deployment example is optional
and is not installed on this server.

The old application remains at `/var/www/workout-tracker-v2`. Its files, an
all-databases MariaDB dump, and copies of both original Apache virtual-host
configurations are backed up in `/root/toku-deployment-backup/`.

Validation: 38 database checks and 90 HTTP checks passed on the server. Apache
configuration validation passed. HTTPS login and authenticated pages, static
assets, export, and denial of private paths were checked against the deployed
domain from the server. Local Chromium checks passed before deployment.

Rollback: restore both original Apache site configurations from the backup
directory, run `apache2ctl configtest`, then reload Apache. The workout site's
files and MariaDB data have not been removed.

The tracker has its own password, separate from SSH. No plaintext password is
stored in this repository. Run `php bin/setup.php` in an interactive SSH session
to change it, then restore root:www-data ownership and 0640 permissions on the
local configuration file.
