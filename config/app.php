<?php
declare(strict_types=1);
// File: config/app.php

define('APP_NAME',     $_ENV['APP_NAME']     ?? 'My App');
define('APP_ENV',      $_ENV['APP_ENV']       ?? 'production');
define('APP_DEBUG',    filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOLEAN));
define('APP_URL',      $_ENV['APP_URL']       ?? 'http://localhost');
define('APP_KEY',      $_ENV['APP_KEY']       ?? '');

date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'Asia/Jakarta');

if (APP_DEBUG) {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}
