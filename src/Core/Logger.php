<?php
declare(strict_types=1);
// File: src/Core/Logger.php

namespace App\Core;

class Logger {
    private static string $logPath = '';

    public static function setPath(string $path): void {
        self::$logPath = rtrim($path, '/');
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }
    }

    public static function info(string $message, array $context = []): void {
        self::write('INFO', $message, $context);
    }

    public static function warning(string $message, array $context = []): void {
        self::write('WARNING', $message, $context);
    }

    public static function error(string $message, array $context = []): void {
        self::write('ERROR', $message, $context);
    }

    public static function debug(string $message, array $context = []): void {
        self::write('DEBUG', $message, $context);
    }

    private static function write(string $level, string $message, array $context): void {
        if (self::$logPath === '') {
            return;
        }
        $date = date('Y-m-d');
        $time = date('Y-m-d H:i:s');
        $contextStr = !empty($context) ? ' ' . json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '';
        $line = "[{$time}] {$level}: {$message}{$contextStr}" . PHP_EOL;
        file_put_contents(self::$logPath . "/{$date}.log", $line, FILE_APPEND | LOCK_EX);
    }
}
