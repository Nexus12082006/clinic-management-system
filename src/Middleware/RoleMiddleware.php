<?php

namespace App\Middleware;

use App\Core\Application;

class RoleMiddleware
{
    private Application $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function handle(string $roles = ''): void
    {
        if ($this->app->auth()->guest()) {
            $this->app->session()->flash('error', 'Please login to access this page.');
            header('Location: ' . config('app.url') . '/login');
            exit;
        }

        $allowedRoles = array_map('trim', explode(',', $roles));
        
        if (!$this->app->auth()->hasRole($allowedRoles)) {
            $role = $this->app->auth()->role();
            $redirect = match ($role) {
                'Doctor' => '/doctor',
                'Receptionist' => '/receptionist',
                'Patient' => '/patient',
                default => '/admin',
            };
            
            $this->app->session()->flash('error', 'You do not have permission to access this page.');
            header('Location: ' . config('app.url') . $redirect);
            exit;
        }
    }
}