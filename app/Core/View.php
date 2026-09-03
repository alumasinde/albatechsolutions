<?php

declare(strict_types=1);

namespace App\Core;

/**
 * View - plain PHP template renderer. No templating DSL by design
 * (keeps the stack framework-free); use e($value) in templates for
 * escaped output.
 */
final class View
{
    public static function render(string $view, array $data = []): string
    {
        $path = dirname(__DIR__, 2) . '/resources/views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($path)) {
            throw new \RuntimeException("View not found: {$view}");
        }

        extract($data, EXTR_SKIP);

        ob_start();
        require $path;

        return ob_get_clean();
    }
}
