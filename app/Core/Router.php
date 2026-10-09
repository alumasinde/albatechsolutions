<?php

declare(strict_types=1);

namespace AlbaTech\Core;

final class Router
{
    /** @var array<string, list<array{path:string, regex:string, keys:list<string>, handler:callable}>> */
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
        [$regex, $keys] = $this->compilePath($this->normalizePath($path));
        $this->routes[strtoupper($method)][] = [
            'path' => $this->normalizePath($path),
            'regex' => $regex,
            'keys' => $keys,
            'handler' => $handler,
        ];
    }

    public function dispatch(Request $request): void
    {
        $path = $request->path();
        $method = $request->method();
        $pathMatched = false;

        foreach ($this->routes[$method] ?? [] as $route) {
            if (preg_match($route['regex'], $path, $matches) !== 1) {
                continue;
            }

            $pathMatched = true;
            $params = [];
            foreach ($route['keys'] as $key) {
                $params[$key] = $matches[$key] ?? '';
            }

            ($route['handler'])($request, $params);
            return;
        }

        foreach ($this->routes as $registeredMethod => $routes) {
            foreach ($routes as $route) {
                if (preg_match($route['regex'], $path) === 1) {
                    $pathMatched = true;
                    header('Allow: ' . $this->allowedMethodsFor($path));
                    Response::html(View::render('errors/405', [
                        'pageTitle' => 'Method not allowed',
                    ]), 405);
                    return;
                }
            }
        }

        Response::html(View::render('errors/404', [
            'pageTitle' => 'Page not found',
        ]), 404);
    }

    private function allowedMethodsFor(string $path): string
    {
        $methods = [];
        foreach ($this->routes as $method => $routes) {
            foreach ($routes as $route) {
                if (preg_match($route['regex'], $path) === 1) {
                    $methods[] = $method;
                    break;
                }
            }
        }

        return implode(', ', $methods);
    }

    /** @return array{string, list<string>} */
    private function compilePath(string $path): array
    {
        $keys = [];
        $quoted = preg_quote($path, '~');
        $regex = preg_replace_callback(
            '/\\\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\\\}/',
            static function (array $matches) use (&$keys): string {
                $keys[] = $matches[1];
                return '(?P<' . $matches[1] . '>[^/]+)';
            },
            $quoted
        );

        return ['~^' . $regex . '$~D', $keys];
    }

    private function normalizePath(string $path): string
    {
        $path = '/' . trim($path, '/');
        return $path === '/' ? '/' : rtrim($path, '/');
    }
}
