<?php

namespace App\Utils;

use App\Core\Session;

class FlashMessages
{
    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function success(string $message): void
    {
        $this->session->flash('success', $message);
    }

    public function error(string $message): void
    {
        $this->session->flash('error', $message);
    }

    public function info(string $message): void
    {
        $this->session->flash('info', $message);
    }

    public function warning(string $message): void
    {
        $this->session->flash('warning', $message);
    }

    public function get(string $type): ?string
    {
        return $this->session->flash($type);
    }

    public function all(): array
    {
        return $this->session->flashAll();
    }

    public function has(string $type): bool
    {
        return $this->session->has('_flash.' . $type);
    }
}