<?php

namespace App\Core;

abstract class Controller
{
    protected Request $request;
    protected Session $session;
    protected Auth $auth;
    protected Database $db;
    protected array $data = [];

    public function __construct(Request $request, Session $session, Auth $auth, Database $db)
    {
        $this->request = $request;
        $this->session = $session;
        $this->auth = $auth;
        $this->db = $db;
        
        $this->data = [
            'app_name' => config('app.name'),
            'base_url' => config('app.base_url'),
            'user' => $this->auth->user(),
            'flashes' => $this->session->flashAll(),
        ];
    }

    protected function view(string $view, array $data = []): string
    {
        return Application::getInstance()->view($view, array_merge($this->data, $data));
    }

    protected function redirect(string $url, int $statusCode = 302): Response
    {
        return Response::redirect($url, $statusCode);
    }

    protected function back(int $statusCode = 302): Response
    {
        $referer = $this->request->header('referer') ?? '/';
        return Response::redirect($referer, $statusCode);
    }

    protected function json(mixed $data, int $statusCode = 200): Response
    {
        return Response::json($data, $statusCode);
    }

    protected function with(string $key, mixed $value): self
    {
        $this->data[$key] = $value;
        return $this;
    }

    protected function flash(string $key, mixed $value): self
    {
        $this->session->flash($key, $value);
        return $this;
    }

    protected function validate(array $rules): array
    {
        $validator = new \App\Utils\Validator($this->request->all(), $rules);
        
        if (!$validator->passes()) {
            $this->flash('errors', $validator->errors());
            $this->flash('old', $this->request->all());
            throw new ValidationException($validator->errors());
        }

        return $validator->validated();
    }

    protected function csrfToken(): string
    {
        if (!$this->session->has('_csrf_token')) {
            $this->session->set('_csrf_token', bin2hex(random_bytes(32)));
        }
        return $this->session->get('_csrf_token');
    }

    protected function verifyCsrf(): bool
    {
        $token = $this->request->input('_csrf_token') ?? $this->request->header('x-csrf-token');
        $sessionToken = $this->session->get('_csrf_token');
        
        return $token && $sessionToken && hash_equals($sessionToken, $token);
    }
}

class ValidationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        $this->errors = $errors;
        parent::__construct('Validation failed');
    }

    public function errors(): array
    {
        return $this->errors;
    }
}