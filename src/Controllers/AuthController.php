<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Session;
use App\Core\Auth;
use App\Core\Database;
use App\Services\UserService;
use App\Models\User;

class AuthController extends Controller
{
    private UserService $userService;

    public function __construct(Request $request, Session $session, Auth $auth, Database $db)
    {
        parent::__construct($request, $session, $auth, $db);
        $this->userService = new UserService(new User($db));
    }

    public function login(): \App\Core\Response
    {
        if ($this->auth->check()) {
            return $this->redirectToDashboard();
        }

        if ($this->request->isPost()) {
            $data = $this->validate([
                'username' => 'required|string|min:3|max:50',
                'password' => 'required|string|min:1|max:100',
            ]);

            $user = $this->userService->authenticate($data['username'], $data['password']);

            if ($user) {
                $this->auth->login($user);
                $this->flash('success', 'Welcome back, ' . $user['username'] . '!');
                return $this->redirectToDashboard();
            }

            $this->flash('error', 'Invalid username or password.');
            return $this->back();
        }

        return $this->view('auth.login');
    }

    public function register(): \App\Core\Response
    {
        if ($this->request->isPost()) {
            $data = $this->validate([
                'username' => 'required|string|min:3|max:50',
                'password' => 'required|string|min:6|max:100|confirmed',
                'role' => 'required|in:Administrator,Doctor,Receptionist,Patient',
            ]);

            $userId = $this->userService->register($data['username'], $data['password'], $data['role']);

            if ($userId) {
                $this->flash('success', 'Account registered successfully! You can now login.');
                return $this->redirect(config('app.url') . '/login');
            }

            $this->flash('error', 'Username is already taken.');
            return $this->back();
        }

        return $this->view('auth.register');
    }

    public function logout(): \App\Core\Response
    {
        $this->auth->logout();
        $this->flash('success', 'You have been logged out.');
        return $this->redirect(config('app.url') . '/login');
    }

    private function redirectToDashboard(): \App\Core\Response
    {
        $role = $this->auth->role();
        $url = match ($role) {
            'Administrator' => '/admin',
            'Doctor' => '/doctor',
            'Receptionist' => '/receptionist',
            'Patient' => '/patient',
            default => '/admin',
        };

        return $this->redirect(config('app.url') . $url);
    }
}