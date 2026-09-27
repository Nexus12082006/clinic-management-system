<?php

namespace App\Core;

class Request
{
    private array $input;
    private array $query;
    private array $files;
    private array $server;
    private array $cookies;
    private string $method;
    private string $uri;
    private ?string $contentType = null;

    public function __construct()
    {
        $this->input = $_POST;
        $this->query = $_GET;
        $this->files = $_FILES;
        $this->server = $_SERVER;
        $this->cookies = $_COOKIE;
        $this->method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $this->uri = $this->parseUri();
        
        if ($this->isJson()) {
            $json = json_decode(file_get_contents('php://input'), true);
            if (is_array($json)) {
                $this->input = array_merge($this->input, $json);
            }
        }
    }

    private function parseUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $pos = strpos($uri, '?');
        if ($pos !== false) {
            $uri = substr($uri, 0, $pos);
        }
        $basePath = dirname($_SERVER['SCRIPT_NAME'] ?? '');
        if ($basePath !== '/' && str_starts_with($uri, $basePath)) {
            $uri = substr($uri, strlen($basePath));
        }
        return $uri !== '' ? $uri : '/';
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getPath(): string
    {
        return $this->uri;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->input[$key] ?? $default;
    }

    public function all(): array
    {
        return $this->input;
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function queryAll(): array
    {
        return $this->query;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] === UPLOAD_ERR_OK;
    }

    public function header(string $name): ?string
    {
        $name = strtolower(str_replace('-', '_', $name));
        return $_SERVER['HTTP_' . strtoupper($name)] ?? null;
    }

    public function bearerToken(): ?string
    {
        $header = $this->header('authorization');
        if ($header && str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        return null;
    }

    public function isJson(): bool
    {
        return str_contains($this->header('content-type') ?? '', 'application/json');
    }

    public function isAjax(): bool
    {
        return $this->header('x-requested-with') === 'XMLHttpRequest';
    }

    public function ip(): string
    {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }

    public function userAgent(): string
    {
        return $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    public function cookie(string $name, mixed $default = null): mixed
    {
        return $this->cookies[$name] ?? $default;
    }

    public function ajax(): bool
    {
        return $this->isAjax();
    }

    public function method(string $method): bool
    {
        return strtoupper($this->method) === strtoupper($method);
    }

    public function isGet(): bool
    {
        return $this->method('GET');
    }

    public function isPost(): bool
    {
        return $this->method('POST');
    }

    public function isPut(): bool
    {
        return $this->method('PUT');
    }

    public function isPatch(): bool
    {
        return $this->method('PATCH');
    }

    public function isDelete(): bool
    {
        return $this->method('DELETE');
    }
}