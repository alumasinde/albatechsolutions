<?php

declare(strict_types=1);

namespace AlbaTech\Core;

final class Router
{
    /** @var array<string, array<string, callable>> */
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, callable $handler): void
    {
        $this->add('POST', $path, $handler);
    }

    public function add(string $method, string $path, callable $handler): void
    {
        $normalized = $this->normalizePath($path);
        $this->routes[strtoupper($method)][$normalized] = $handler;
    }

    public function dispatch(Request $request): void
    {
        $path = $request->path();
        $method = $request->method();

        if (isset($this->routes[$method][$path])) {
            ($this->routes[$method][$path])($request);
            return;
        }

        foreach ($this->routes as $registeredMethod => $routes) {
            if (isset($routes[$path])) {
                header('Allow: ' . implode(', ', array_keys(array_filter(
                    $this->routes,
                    static fn (array $items): bool => isset($items[$path])
                ))));
                Response::html(View::render('errors/405', [
                    'pageTitle' => 'Method not allowed',
                ]), 405);
                return;
            }
        }

        Response::html(View::render('errors/404', [
            'pageTitle' => 'Page not found',
        ]), 404);
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
