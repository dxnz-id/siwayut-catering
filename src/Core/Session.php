<?php
declare(strict_types=1);
// File: src/Core/Session.php

namespace App\Core;

class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function forget(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }
        session_destroy();
    }

    public static function flash(string $key, string $message): void {
        $_SESSION['_flash'][$key] = $message;
    }

    public static function getFlash(string $key): ?string {
        $message = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $message;
    }

    public static function regenerate(bool $deleteOld = true): void {
        session_regenerate_id($deleteOld);
    }

    public static function old(): array {
        $old = $_SESSION['_old_input'] ?? [];
        unset($_SESSION['_old_input']);
        return $old;
    }

    public static function setOld(array $data): void {
        $_SESSION['_old_input'] = $data;
    }
}
