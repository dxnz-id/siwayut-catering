<?php
declare(strict_types=1);
// File: src/Core/Csrf.php

namespace App\Core;

class Csrf {
    private const SESSION_KEY = '_csrf_token';

    public static function token(): string {
        if (!Session::has(self::SESSION_KEY)) {
            return self::regenerate();
        }
        return Session::get(self::SESSION_KEY);
    }

    public static function verify(string $token): bool {
        $stored = Session::get(self::SESSION_KEY);
        if ($stored === null || $token === '') {
            return false;
        }
        return hash_equals($stored, $token);
    }

    public static function field(): string {
        return '<input type="hidden" name="_csrf_token" value="' . self::token() . '">';
    }

    public static function regenerate(): string {
        $token = bin2hex(random_bytes(32));
        Session::set(self::SESSION_KEY, $token);
        return $token;
    }
}
