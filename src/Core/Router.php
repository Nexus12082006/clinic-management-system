<?php

namespace App\Core;

class Router
{
    private array $routes = [];
    private array $middleware = [];
    private array $currentGroupMiddleware = [];
    private string $basePath = '';

    public function __construct(string $basePath = '')
    {
        $this->basePath = rtrim($basePath, '/');
    }

    public function get(string $uri, string|array|callable $action): void
    {
        $this->addRoute('GET', $uri, $action);
    }

    public function post(string $uri, string|array|callable $action): void
    {
        $this->addRoute('POST', $uri, $action);
    }

    public function put(string $uri, string|array|callable $action): void
    {
        $this->addRoute('PUT', $uri, $action);
    }

    public function patch(string $uri, string|array|callable $action): void
    {
        $this->addRoute('PATCH', $uri, $action);
    }

    public function delete(string $uri, string|array|callable $action): void
    {
        $this->addRoute('DELETE', $uri, $action);
    }

    public function match(array $methods, string $uri, string|array|callable $action): void
    {
        foreach ($methods as $method) {
            $this->addRoute($method, $uri, $action);
        }
    }

    public function any(string $uri, string|array|callable $action): void
    {
        $this->addRoute('*', $uri, $action);
    }

    private function addRoute(string $method, string $uri, string|array|callable $action): void
    {
        $pattern = $this->basePath . $this->normalizeUri($uri);
        $regex = $this->uriToRegex($pattern);
        
        $this->routes[] = [
            'method' => $method,
            'uri' => $uri,
            'pattern' => $pattern,
            'regex' => $regex,
            'action' => $action,
            'middleware' => $this->currentGroupMiddleware,
        ];
    }

    private function normalizeUri(string $uri): string
    {
        $uri = '/' . ltrim($uri, '/');
        return $uri !== '/' ? rtrim($uri, '/') : $uri;
    }

    private function uriToRegex(string $uri): string
    {
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $uri);
        return '#^' . $regex . '$#';
    }

    public function group(array $attributes, callable $callback): void
    {
        $previousMiddleware = $this->currentGroupMiddleware;
        
        if (isset($attributes['middleware'])) {
            $middleware = is_array($attributes['middleware']) ? $attributes['middleware'] : [$attributes['middleware']];
            $this->currentGroupMiddleware = array_merge($this->currentGroupMiddleware, $middleware);
        }
        
        if (isset($attributes['prefix'])) {
            $previousBasePath = $this->basePath;
            $this->basePath .= $this->normalizeUri($attributes['prefix']);
        }

        $callback($this);

        $this->currentGroupMiddleware = $previousMiddleware;
        if (isset($attributes['prefix'])) {
            $this->basePath = $previousBasePath;
        }
    }

    public function dispatch(Request $request): array
    {
        $method = $request->getMethod();
        $uri = $request->getUri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== '*' && $route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['regex'], $uri, $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            return [
                'action' => $route['action'],
                'params' => $params,
                'middleware' => $route['middleware'],
            ];
        }

        return [
            'action' => null,
            'params' => [],
            'middleware' => [],
        ];
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}