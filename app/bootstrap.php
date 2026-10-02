<?php
declare(strict_types=1);

define('ROOT', dirname(__DIR__));

$GLOBALS['config'] = require ROOT . '/config/config.php';

date_default_timezone_set(config('timezone', 'Asia/Jakarta'));
mb_internal_encoding('UTF-8');

// Catat semua error PHP ke storage/php-error.log (folder ini diblokir dari browser)
ini_set('log_errors', '1');
ini_set('error_log', ROOT . '/storage/php-error.log');

if (config('debug')) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
}

require ROOT . '/app/core/helpers.php';
require ROOT . '/app/core/Database.php';
require ROOT . '/app/core/Schema.php';
require ROOT . '/app/core/Csrf.php';
require ROOT . '/app/core/Auth.php';
require ROOT . '/app/core/Upload.php';
require ROOT . '/app/core/Form.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('ab_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'secure'   => is_https(),
        'samesite' => 'Lax',
    ]);
    session_start();
}

/** Baca nilai konfigurasi dengan notasi titik, mis. config('db.driver') */
function config(string $key, mixed $default = null): mixed
{
    $value = $GLOBALS['config'];
    foreach (explode('.', $key) as $part) {
        if (!is_array($value) || !array_key_exists($part, $value)) {
            return $default;
        }
        $value = $value[$part];
    }
    return $value;
}
