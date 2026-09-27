<?php

namespace App\Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private mixed $content = '';
    private string $contentType = 'text/html; charset=UTF-8';

    public function __construct(mixed $content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public static function make(mixed $content = '', int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, $headers);
    }

    public static function json(mixed $data, int $statusCode = 200, array $headers = []): self
    {
        $response = new self(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), $statusCode, $headers);
        $response->header('Content-Type', 'application/json; charset=UTF-8');
        return $response;
    }

    public static function redirect(string $url, int $statusCode = 302): self
    {
        $response = new self('', $statusCode);
        $response->header('Location', $url);
        return $response;
    }

    public static function view(string $view, array $data = [], int $statusCode = 200): self
    {
        $app = Application::getInstance();
        $content = $app->view($view, $data);
        return new self($content, $statusCode);
    }

    public function setContent(mixed $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent(): mixed
    {
        return $this->content;
    }

    public function setStatusCode(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function withCookie(
        string $name,
        string $value,
        int $expire = 0,
        string $path = '/',
        string $domain = '',
        bool $secure = false,
        bool $httponly = true,
        string $samesite = 'lax'
    ): self {
        $cookie = sprintf(
            '%s=%s; expires=%s; path=%s; samesite=%s%s%s',
            $name,
            $value,
            gmdate('D, d-M-Y H:i:s', $expire) . ' GMT',
            $path,
            $samesite,
            $domain ? "; domain={$domain}" : '',
            $secure ? '; secure' : '',
            $httponly ? '; httponly' : ''
        );
        $this->headers['Set-Cookie'] = $cookie;
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);
        
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->content;
    }
}