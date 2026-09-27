<?php

namespace App\Middleware;

use App\Core\Application;

class AuthMiddleware
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(): void
    {
        if ($this->app->auth()->guest()) {
            $this->app->session()->flash('error', 'Please login to access this page.');
            $this->app->abort(302);
            // We don't actually reach here due to abort, but for clarity:
            header('Location: ' . config('app.url') . '/login');
            exit;
        }
    }
}