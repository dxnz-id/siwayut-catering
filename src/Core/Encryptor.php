<?php
declare(strict_types=1);

namespace App\Core;

class Encryptor {
    private const CIPHER = 'aes-256-cbc';

    public static function key(): string {
        $raw = APP_KEY;
        if (str_starts_with($raw, 'base64:')) {
            $raw = base64_decode(substr($raw, 7), true);
        }
        return $raw !== false ? $raw : '';
    }

    public static function encrypt(string $plaintext): string {
        $key = self::key();
        if ($key === '') {
            throw new \RuntimeException('APP_KEY is not set.');
        }
        $iv = random_bytes(openssl_cipher_iv_length(self::CIPHER));
        $ciphertext = openssl_encrypt($plaintext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($iv . $ciphertext);
    }

    public static function decrypt(string $payload): string {
        $key = self::key();
        if ($key === '') {
            throw new \RuntimeException('APP_KEY is not set.');
        }
        $data = base64_decode($payload, true);
        if ($data === false) {
            throw new \RuntimeException('Invalid encrypted payload.');
        }
        $ivLen = openssl_cipher_iv_length(self::CIPHER);
        $iv = substr($data, 0, $ivLen);
        $ciphertext = substr($data, $ivLen);
        $result = openssl_decrypt($ciphertext, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv);
        if ($result === false) {
            throw new \RuntimeException('Decryption failed.');
        }
        return $result;
    }

    public static function hmac(string $value): string {
        $key = self::key();
        if ($key === '') {
            throw new \RuntimeException('APP_KEY is not set.');
        }
        return hash_hmac('sha256', $value, $key);
    }
}
