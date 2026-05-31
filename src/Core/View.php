<?php
declare(strict_types=1);
// File: src/Core/View.php

namespace App\Core;

class View {
    private string $viewsPath;
    private string $layoutsPath;

    public function __construct(string $viewsPath) {
        $this->viewsPath = rtrim($viewsPath, '/');
        $this->layoutsPath = $this->viewsPath . '/layouts';
    }

    public function render(string $template, array $data = [], string $layout = 'main'): void {
        $content = $this->partial($template, $data);
        if ($layout) {
            foreach ($data as $_key => $_value) {
                if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_key)) {
                    $$_key = $_value;
                }
            }
            unset($_key, $_value);
            require $this->layoutsPath . '/' . $layout . '.php';
        } else {
            echo $content;
        }
    }

    public function partial(string $template, array $data = []): string {
        foreach ($data as $_key => $_value) {
            if (preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $_key)) {
                $$_key = $_value;
            }
        }
        unset($_key, $_value);
        ob_start();
        require $this->viewsPath . '/' . $template . '.php';
        return ob_get_clean();
    }

    // CONTRACT: MUST use exactly: htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8')
    public static function e(mixed $value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
