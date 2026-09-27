<?php

namespace App\Middleware;

use App\Core\Application;

class CsrfMiddleware
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(): void
    {
        $request = $this->app->request();
        
        if (in_array($request->getMethod(), ['GET', 'HEAD', 'OPTIONS'])) {
            return;
        }

        $token = $request->input('_csrf_token') ?? $request->header('x-csrf-token');
        $sessionToken = $this->app->session()->get('_csrf_token');

        if (!$token || !$sessionToken || !hash_equals($sessionToken, $token)) {
            $this->app->abort(419, 'CSRF token mismatch');
        }
    }
}