<?php
declare(strict_types=1);
if (PHP_SAPI !== 'cli') exit;
require dirname(__DIR__) . '/app/bootstrap.php';
$file = dirname(__DIR__) . '/config.local.php';
$local = is_file($file) ? require $file : [];
$tty = fopen('/dev/tty', 'r+');
if (!$tty) { fwrite(STDERR, "Run this command in an interactive terminal.\n"); exit(1); }
fwrite($tty, "Choose a tracker password (12–72 bytes): ");
system('stty -echo < /dev/tty');
try {
    $password = rtrim((string) fgets($tty), "\r\n");
    fwrite($tty, "\nConfirm password: ");
    $confirm = rtrim((string) fgets($tty), "\r\n");
} finally { system('stty echo < /dev/tty'); fwrite($tty, "\n"); }
if (strlen($password) < 12 || strlen($password) > 72 || !hash_equals($password, $confirm)) {
    fwrite(STDERR, "Passwords must match and contain 12–72 bytes. Nothing changed.\n"); exit(1);
}
$local['TOKU_PASSWORD_HASH'] = password_hash($password, PASSWORD_DEFAULT);
unset($password, $confirm);
umask(0077);
$temp = tempnam(dirname($file), '.toku-config-');
if ($temp === false || file_put_contents($temp, "<?php\nreturn " . var_export($local, true) . ";\n") === false || !rename($temp, $file)) {
    throw new RuntimeException('Cannot save local configuration');
}
echo "Tracker password saved as a hash. Existing sessions are invalidated.\n";
echo "For Apache, give its service group read access to config.local.php (mode 0640).\n";
