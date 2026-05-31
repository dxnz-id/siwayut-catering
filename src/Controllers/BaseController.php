<?php
declare(strict_types=1);
// File: src/Controllers/BaseController.php

namespace App\Controllers;
use App\Core\{View, Response, Session};

abstract class BaseController {
    protected View $view;

    // CONTRACT: Constructor MUST instantiate: $this->view = new View(BASE_PATH . '/src/Views');
    public function __construct() {
        $this->view = new View(BASE_PATH . '/src/Views');
    }

    protected function render(string $template, array $data = [], string $layout = 'main'): void {
        $this->view->render($template, $data, $layout);
    }

    protected function redirect(string $url): never {
        Response::redirect($url);
    }

    protected function redirectWithFlash(string $url, string $type, string $message): never {
        Session::flash($type, $message);
        Response::redirect($url);
    }

    protected function currentUser(): ?array {
        return Session::get('user');
    }

    protected function back(string $fallback = '/dashboard'): never {
        $referer = $_SERVER['HTTP_REFERER'] ?? $fallback;
        $host = parse_url($referer, PHP_URL_HOST);
        if ($host && $host !== ($_SERVER['HTTP_HOST'] ?? '')) {
            $referer = $fallback;
        }
        Response::redirect($referer);
    }

    protected function withOldInput(array $data): void {
        Session::setOld($data);
    }
}
