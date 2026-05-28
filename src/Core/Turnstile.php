<?php
declare(strict_types=1);

namespace App\Core;

class Turnstile {
    public static function enabled(): bool {
        return TURNSTILE_ENABLED;
    }

    public static function verify(?string $token): bool {
        if (!self::enabled()) return true;
        if (!$token) return false;

        $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query([
                'secret' => TURNSTILE_SECRET_KEY,
                'response' => $token,
                'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
            ]),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
        ]);
        $result = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return ($result['success'] ?? false) === true;
    }

    public static function widget(): string {
        if (!self::enabled()) return '';
        return '<div class="cf-turnstile" data-sitekey="' . TURNSTILE_SITE_KEY . '" data-theme="dark" data-callback="onTurnstileSuccess" data-error-callback="onTurnstileError" data-expired-callback="onTurnstileExpired"></div>';
    }
}
