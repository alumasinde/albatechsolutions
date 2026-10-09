<?php

declare(strict_types=1);

namespace AlbaTech\Core;

use RuntimeException;

final class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/main'): string
    {
        $viewsRoot = dirname(__DIR__, 2) . '/resources/views/';
        $viewPath = $viewsRoot . $view . '.php';
        $layoutPath = $viewsRoot . $layout . '.php';

        if (!is_file($viewPath) || !is_file($layoutPath)) {
            throw new RuntimeException('View template could not be found.');
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $viewPath;
        $content = (string) ob_get_clean();

        ob_start();
        require $layoutPath;
        return (string) ob_get_clean();
    }

    public static function escape(mixed $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}
