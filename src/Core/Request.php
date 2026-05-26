<?php
declare(strict_types=1);
// File: src/Core/Request.php

namespace App\Core;

class Request {
    private array $routeParams = [];

    public function method(): string {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function uri(): string {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        return rtrim($uri, '/') ?: '/';
    }

    public function input(string $key, mixed $default = null): mixed {
        return $this->all()[$key] ?? $default;
    }

    public function only(array $keys): array {
        $all = $this->all();
        $result = [];
        foreach ($keys as $key) {
            if (array_key_exists($key, $all)) {
                $result[$key] = $all[$key];
            }
        }
        return $result;
    }

    public function all(): array {
        $method = $this->method();
        if ($method === 'GET') {
            return $_GET;
        }
        return array_merge($_GET, $_POST);
    }

    public function file(string $key): ?array {
        if (isset($_FILES[$key]) && $_FILES[$key]['error'] !== UPLOAD_ERR_NO_FILE) {
            return $_FILES[$key];
        }
        return null;
    }

    public function has(string $key): bool {
        return array_key_exists($key, $this->all());
    }

    public function setRouteParams(array $params): void {
        $this->routeParams = $params;
    }

    public function param(string $key, mixed $default = null): mixed {
        return $this->routeParams[$key] ?? $default;
    }

    public function isAjax(): bool {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public function expectsJson(): bool {
        $accept = $_SERVER['HTTP_ACCEPT'] ?? '';
        return str_contains($accept, 'application/json');
    }

    public function ip(): string {
        return $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
    }
}
