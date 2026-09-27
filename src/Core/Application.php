<?php

namespace App\Core;

use App\Utils\Validator;

class Application
{
    private static ?self $instance = null;
    private array $bindings = [];
    private array $config = [];
    private Router $router;
    private Request $request;
    private Response $response;
    private Session $session;
    private Auth $auth;
    private Database $database;
    private string $viewsPath;
    private string $basePath;

    private function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->viewsPath = $basePath . '/src/Views';
        $this->loadConfig();
        $this->registerCoreBindings();
    }

    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    public static function create(string $basePath): self
    {
        if (self::$instance === null) {
            self::$instance = new self($basePath);
        }
        return self::$instance;
    }

    private function loadConfig(): void
    {
        $this->config = [
            'app' => require $this->basePath . '/config/app.php',
            'database' => require $this->basePath . '/config/database.php',
            'routes' => require $this->basePath . '/config/routes.php',
        ];
    }

    private function registerCoreBindings(): void
    {
        $this->bind('config', $this->config);
        $this->bind('router', fn() => new Router(config('app.url')));
        $this->bind('request', fn() => new Request());
        $this->bind('session', fn() => new Session(config('app.session')));
        $this->bind('database', fn() => new Database(config('database')));
        $this->bind('auth', fn() => new Auth($this->make('session'), $this->make('database')));
    }

    public function bind(string $key, mixed $value): void
    {
        $this->bindings[$key] = $value;
    }

    public function make(string $key): mixed
    {
        if (!isset($this->bindings[$key])) {
            throw new \RuntimeException("Binding [{$key}] not found");
        }

        $value = $this->bindings[$key];
        
        if (is_callable($value)) {
            $value = $value($this);
            $this->bindings[$key] = $value;
        }

        return $value;
    }

    public function boot(): void
    {
        $this->request = $this->make('request');
        $this->session = $this->make('session');
        $this->database = $this->make('database');
        $this->auth = $this->make('auth');
        $this->router = $this->make('router');

        $this->session->start();
        
        if ($this->auth->guest()) {
            $this->auth->checkRememberToken();
        }

        $this->loadRoutes();
    }

    private function loadRoutes(): void
    {
        $routes = config('routes');
        if (is_callable($routes)) {
            $routes($this->router);
        }
    }

    public function handle(): void
    {
        try {
            $route = $this->router->dispatch($this->request);
            
            if (!$route['action']) {
                $this->abort(404, 'Page not found');
            }

            $this->runMiddleware($route['middleware']);
            
            $response = $this->executeAction($route['action'], $route['params']);
            $response->send();
            
        } catch (\App\Core\Controller\ValidationException $e) {
            $this->handleValidationException($e);
        } catch (\Throwable $e) {
            $this->handleException($e);
        }
    }

    private function runMiddleware(array $middleware): void
    {
        foreach ($middleware as $mw) {
            $middlewareClass = "App\\Middleware\\{$mw}";
            if (class_exists($middlewareClass)) {
                $instance = new $middlewareClass($this);
                $instance->handle($this->request);
            }
        }
    }

    private function executeAction(string|array|callable $action, array $params): Response
    {
        if (is_string($action) && str_contains($action, '@')) {
            [$controllerClass, $method] = explode('@', $action);
            $controllerClass = "App\\Controllers\\{$controllerClass}";
            
            if (!class_exists($controllerClass)) {
                $this->abort(500, "Controller {$controllerClass} not found");
            }

            $controller = new $controllerClass(
                $this->request,
                $this->session,
                $this->auth,
                $this->database
            );

            return $controller->$method(...array_values($params));
        }

        if (is_callable($action)) {
            return $action(...array_values($params));
        }

        $this->abort(500, 'Invalid route action');
    }

    private function handleValidationException(\App\Core\Controller\ValidationException $e): void
    {
        $response = Response::redirect($this->request->header('referer') ?? '/')
            ->setStatusCode(302);
        $response->send();
    }

    private function handleException(\Throwable $e): void
    {
        if (config('app.debug')) {
            $content = "<h1>Error</h1><pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
            $content .= "<h2>Stack Trace</h2><pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        } else {
            $content = "<h1>Internal Server Error</h1><p>Something went wrong. Please try again later.</p>";
            error_log($e->getMessage() . "\n" . $e->getTraceAsString());
        }

        $response = Response::make($content, 500);
        $response->send();
    }

    public function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        
        if (config('app.debug')) {
            echo "<h1>Error {$code}</h1><p>" . htmlspecialchars($message) . "</p>";
        } else {
            echo "<h1>Error {$code}</h1><p>An error occurred.</p>";
        }
        exit;
    }

    public function view(string $view, array $data = []): string
    {
        extract($data);
        
        $layoutPath = $this->viewsPath . '/layouts/app.php';
        $viewPath = $this->viewsPath . '/' . $view . '.php';
        
        ob_start();
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            throw new \RuntimeException("View [{$view}] not found");
        }
        
        $content = ob_get_clean();
        
        if (file_exists($layoutPath)) {
            ob_start();
            include $layoutPath;
            return ob_get_clean();
        }
        
        return $content;
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function request(): Request
    {
        return $this->request;
    }

    public function response(): Response
    {
        return $this->response;
    }

    public function session(): Session
    {
        return $this->session;
    }

    public function auth(): Auth
    {
        return $this->auth;
    }

    public function db(): Database
    {
        return $this->database;
    }

    public function config(string $key = null): mixed
    {
        if (!$key) {
            return $this->config;
        }
        
        $keys = explode('.', $key);
        $value = $this->config;
        
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }
        
        return $value;
    }
}

function config(string $key = null): mixed
{
    return Application::getInstance()?->config($key);
}