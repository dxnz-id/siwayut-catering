<?php
declare(strict_types=1);
// File: src/Core/Response.php

namespace App\Core;

// CONTRACT: All methods returning `never` MUST terminate using exactly: exit;
class Response {
    public static function redirect(string $url, int $code = 302): never {
        http_response_code($code);
        header('Location: ' . $url);
        exit;
    }

    public static function json(mixed $data, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function jsonSuccess(mixed $data = null, string $message = 'OK', int $code = 200): never {
        self::json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }

    public static function jsonError(string $message, array $errors = [], int $code = 400): never {
        self::json(['success' => false, 'message' => $message, 'errors' => $errors], $code);
    }

    public static function setStatusCode(int $code): void {
        http_response_code($code);
    }

    public static function text(string $text, int $code = 200): never {
        http_response_code($code);
        header('Content-Type: text/plain; charset=utf-8');
        echo $text;
        exit;
    }

    public static function download(string $filePath, string $filename): never {
        if (!file_exists($filePath)) {
            throw new \App\Exceptions\NotFoundException('File not found');
        }
        http_response_code(200);
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    }
}
