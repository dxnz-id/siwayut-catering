<?php
declare(strict_types=1);
// File: src/Helpers/functions.php

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed {
        return $_ENV[$key] ?? $default;
    }
}
if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed {
        static $configs = [];
        $parts = explode('.', $key, 2);
        $file = $parts[0];
        $configKey = $parts[1] ?? null;
        if (!isset($configs[$file])) {
            $path = BASE_PATH . '/config/' . $file . '.php';
            if (file_exists($path)) {
                $configs[$file] = require $path;
            } else {
                return $default;
            }
        }
        if ($configKey === null) {
            return $configs[$file];
        }
        return $configs[$file][$configKey] ?? $default;
    }
}
if (!function_exists('base_path')) {
    function base_path(string $path = ''): string {
        return BASE_PATH . ($path ? '/' . ltrim($path, '/') : '');
    }
}
if (!function_exists('asset')) {
    function asset(string $path): string {
        return APP_URL . '/assets/' . ltrim($path, '/');
    }
}
if (!function_exists('url')) {
    function url(string $path = ''): string {
        return APP_URL . '/' . ltrim($path, '/');
    }
}
if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed {
        $old = \App\Core\Session::old();
        // Put it back since old() consumes the session data — we re-store for multi-field access
        if (!empty($old)) {
            \App\Core\Session::setOld($old);
        }
        return $old[$key] ?? $default;
    }
}
if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return \App\Core\Csrf::field();
    }
}
if (!function_exists('e')) {
    function e(mixed $value): string {
        return \App\Core\View::e($value);
    }
}
if (!function_exists('dd')) {
    function dd(mixed ...$vars): never {
        echo '<pre>';
        foreach ($vars as $var) {
            var_dump($var);
        }
        echo '</pre>';
        exit;
    }
}
if (!function_exists('now')) {
    function now(): string {
        return date('Y-m-d H:i:s');
    }
}
