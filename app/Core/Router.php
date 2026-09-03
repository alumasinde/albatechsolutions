<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router - front-controller style regex router.
 *
 * Routes are registered with placeholders like /services/{slug} which
 * are compiled to regex. Middleware stacks run before the handler.
 */
final class Router
{
    private array $routes = [];
    private array $groupMiddleware = [];
    private string $groupPrefix = '';

    public function get(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('GET', $path, $handler, $middleware);
    }

    public function post(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('POST', $path, $handler, $middleware);
    }

    public function put(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('PUT', $path, $handler, $middleware);
    }

    public function delete(string $path, callable|array $handler, array $middleware = []): void
    {
        $this->add('DELETE', $path, $handler, $middleware);
    }

    /**
     * Group routes under a shared prefix and middleware stack.
     */
    public function group(string $prefix, array $middleware, callable $callback): void
    {
        $previousPrefix = $this->groupPrefix;
        $previousMiddleware = $this->groupMiddleware;

        $this->groupPrefix .= $prefix;
        $this->groupMiddleware = array_merge($this->groupMiddleware, $middleware);

        $callback($this);

        $this->groupPrefix = $previousPrefix;
        $this->groupMiddleware = $previousMiddleware;
    }

    private function add(string $method, string $path, callable|array $handler, array $middleware): void
    {
        $fullPath = rtrim($this->groupPrefix . $path, '/') ?: '/';

        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $this->compile($fullPath),
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    private function compile(string $path): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $path);

        return '#^' . $pattern . '$#';
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $uri = rtrim($request->path(), '/') ?: '/';

        $allowedMethods = [];

        foreach ($this->routes as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                if ($route['method'] !== $method) {
                    $allowedMethods[] = $route['method'];
                    continue;
                }

                $params = array_filter(
                    $matches,
                    static fn ($key) => is_string($key),
                    ARRAY_FILTER_USE_KEY
                );

                $request->setRouteParams($params);

                return $this->runMiddlewareStack($route['middleware'], $request, function () use ($route, $request) {
                    return $this->callHandler($route['handler'], $request);
                });
            }
        }

        if (!empty($allowedMethods)) {
            return Response::text('Method Not Allowed', 405);
        }

        return Response::text('Not Found', 404);
    }

    /**
     * Middleware entries may carry a parameter using 'ClassName:param'
     * syntax, e.g. 'App\Core\Middleware\RbacMiddleware:services.view'.
     */
    private function runMiddlewareStack(array $middleware, Request $request, callable $destination): Response
    {
        $pipeline = array_reduce(
            array_reverse($middleware),
            function (callable $next, string $middlewareEntry) {
                return function (Request $request) use ($next, $middlewareEntry) {
                    [$middlewareClass, $param] = array_pad(explode(':', $middlewareEntry, 2), 2, null);
                    $instance = Container::resolve($middlewareClass);

                    return $instance->handle($request, $next, $param);
                };
            },
            $destination
        );

        return $pipeline($request);
    }

    private function callHandler(callable|array $handler, Request $request): Response
    {
        if (is_array($handler)) {
            [$controllerClass, $method] = $handler;
            $controller = Container::resolve($controllerClass);

            return $controller->{$method}($request);
        }

        return $handler($request);
    }
}
