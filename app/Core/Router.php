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
        // HEAD is answered by the matching GET route with the body discarded (crawlers,
        // uptime monitors and link checkers send HEAD; it used to return 405).
        if ($request->method() !== 'HEAD') {
            $this->handle($request, $request->method());
            return;
        }

        ob_start();
        try {
            $this->handle($request, 'GET');
        } finally {
            ob_end_clean();
        }
    }

    private function handle(Request $request, string $method): void
    {
        $this->redirectCanonicalCase($method);
        $this->redirectTrailingSlash($method);

        $path = $request->path();
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

            // Central defense-in-depth: every admin POST must carry the session CSRF token.
            // Controllers may also validate it; the duplicate check is intentional.
            if ($method === 'POST' && ($path === '/admin' || str_starts_with($path, '/admin/'))) {
                if (!AdminSecurity::verifyCsrf($request->input('_csrf'))) {
                    Response::html(
                        '<!doctype html><html lang="en"><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Request verification failed</title><body><main><h1>Request verification failed</h1><p>Your session token is missing or expired. Refresh the page and submit the form again.</p></main></body></html>',
                        419
                    );
                    return;
                }
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
                        'metaRobots' => 'noindex, nofollow',
                        'canonicalUrl' => '',
                    ]), 405);
                    return;
                }
            }
        }

        Response::html(View::render('errors/404', [
            'pageTitle' => 'Page not found | AlbaTech Solutions',
            'metaRobots' => 'noindex, nofollow',
            'canonicalUrl' => '',
        ]), 404);
    }

    /** Redirect safe GET/HEAD paths to lowercase so route case variants cannot duplicate content. */
    private function redirectCanonicalCase(string $method): void
    {
        if ($method !== 'GET') {
            return;
        }

        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $rawPath = (string) (parse_url($uri, PHP_URL_PATH) ?? '/');
        // Only normalize predictable URL characters. Encoded paths and unusual bytes
        // are left to route validation rather than being rewritten ambiguously.
        if ($rawPath === '' || preg_match('#^/[A-Za-z0-9._~!$&\'()*+,;=:@/-]*$#', $rawPath) !== 1) {
            return;
        }

        $targetPath = strtolower($rawPath);
        // Normalize case and a trailing slash in one hop to avoid redirect chains such as
        // /Services/ -> /services/ -> /services. Root remains exactly '/'.
        if (strlen($targetPath) > 1 && str_ends_with($targetPath, '/')) {
            $targetPath = rtrim($targetPath, '/');
        }
        if ($targetPath !== $rawPath) {
            $query = parse_url($uri, PHP_URL_QUERY);
            Response::redirect($targetPath . (is_string($query) && $query !== '' ? '?' . $query : ''), 301);
        }
    }

    /** /services/ -> 301 /services so each page has exactly one crawlable URL. */
    private function redirectTrailingSlash(string $method): void
    {
        if ($method !== 'GET') {
            return;
        }
        $uri = (string) ($_SERVER['REQUEST_URI'] ?? '/');
        $rawPath = (string) (parse_url($uri, PHP_URL_PATH) ?? '/');
        // Strict allow-list: no backslashes, no scheme-relative ("//host") tricks, no control chars.
        if (strlen($rawPath) > 1 && str_ends_with($rawPath, '/')
            && preg_match('#^/[A-Za-z0-9._~%!$&\'()*+,;=:@/-]*$#', $rawPath) === 1) {
            $target = '/' . ltrim(rtrim($rawPath, '/'), '/');
            $query = parse_url($uri, PHP_URL_QUERY);
            Response::redirect($target . (is_string($query) && $query !== '' ? '?' . $query : ''), 301);
        }
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
