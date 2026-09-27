<?php

namespace App\Core;

class Session
{
    private bool $started = false;
    private array $config;

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'lifetime' => 120,
            'secure' => false,
            'httponly' => true,
            'samesite' => 'lax',
        ], $config);
    }

    public function start(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        $this->configureSession();
        session_start();
        $this->started = true;
    }

    private function configureSession(): void
    {
        ini_set('session.gc_maxlifetime', (string)($this->config['lifetime'] * 60));
        ini_set('session.cookie_lifetime', (string)($this->config['lifetime'] * 60));
        ini_set('session.cookie_secure', $this->config['secure'] ? '1' : '0');
        ini_set('session.cookie_httponly', $this->config['httponly'] ? '1' : '0');
        ini_set('session.cookie_samesite', $this->config['samesite']);
        ini_set('session.use_strict_mode', '1');
    }

    public function regenerate(bool $deleteOld = true): void
    {
        $this->start();
        session_regenerate_id($deleteOld);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    public function has(string $key): bool
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    public function remove(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    public function flush(): void
    {
        $this->start();
        $_SESSION = [];
    }

    public function destroy(): void
    {
        $this->start();
        session_unset();
        session_destroy();
        $this->started = false;
    }

    public function getId(): string
    {
        $this->start();
        return session_id();
    }

    public function flash(string $key, mixed $value = null): mixed
    {
        $this->start();
        
        if ($value !== null) {
            $_SESSION['_flash'][$key] = $value;
            return;
        }

        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public function flashAll(): array
    {
        $this->start();
        $flashes = $_SESSION['_flash'] ?? [];
        $_SESSION['_flash'] = [];
        return $flashes;
    }
}