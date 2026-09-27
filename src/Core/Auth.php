<?php

namespace App\Core;

use App\Models\User;

class Auth
{
    private Session $session;
    private Database $db;
    private ?User $user = null;

    public function __construct(Session $session, Database $db)
    {
        $this->session = $session;
        $this->db = $db;
    }

    public function attempt(string $username, string $password, bool $remember = false): bool
    {
        $user = $this->db->fetch('SELECT * FROM users WHERE username = ?', [$username]);
        
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'] ?? '')) {
            if ($this->legacyVerify($password, $user['password'] ?? '')) {
                $this->migratePassword($user['id'], $password);
            } else {
                return false;
            }
        }

        $this->login($user);
        
        if ($remember) {
            $this->setRememberToken($user['id']);
        }

        return true;
    }

    private function legacyVerify(string $password, string $storedPassword): bool
    {
        return $password === $storedPassword;
    }

    private function migratePassword(int $userId, string $password): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->db->update('users', ['password_hash' => $hash], 'id = ?', [$userId]);
    }

    private function setRememberToken(int $userId): void
    {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);
        $this->db->update('users', ['remember_token' => $token, 'remember_expires' => $expires], 'id = ?', [$userId]);
        setcookie('remember_token', $token, [
            'expires' => time() + 60 * 60 * 24 * 30,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'lax',
        ]);
    }

    public function login(array $user): void
    {
        $this->session->regenerate();
        $this->session->set('user_id', $user['id']);
        $this->session->set('username', $user['username']);
        $this->session->set('role', $user['role']);
        $this->user = new User($user);
    }

    public function logout(): void
    {
        if ($this->session->has('user_id')) {
            $userId = $this->session->get('user_id');
            $this->db->update('users', ['remember_token' => null, 'remember_expires' => null], 'id = ?', [$userId]);
        }
        
        setcookie('remember_token', '', [
            'expires' => time() - 3600,
            'path' => '/',
            'secure' => false,
            'httponly' => true,
            'samesite' => 'lax',
        ]);

        $this->session->destroy();
        $this->user = null;
    }

    public function checkRememberToken(): bool
    {
        $token = $_COOKIE['remember_token'] ?? '';
        if (!$token) {
            return false;
        }

        $user = $this->db->fetch(
            'SELECT * FROM users WHERE remember_token = ? AND remember_expires > NOW()',
            [$token]
        );

        if ($user) {
            $this->login($user);
            return true;
        }

        return false;
    }

    public function check(): bool
    {
        if ($this->user) {
            return true;
        }

        if ($this->session->has('user_id')) {
            $this->user = new User([
                'id' => $this->session->get('user_id'),
                'username' => $this->session->get('username'),
                'role' => $this->session->get('role'),
            ]);
            return true;
        }

        return $this->checkRememberToken();
    }

    public function user(): ?User
    {
        $this->check();
        return $this->user;
    }

    public function id(): ?int
    {
        return $this->user()?->id;
    }

    public function role(): ?string
    {
        return $this->user()?->role;
    }

    public function hasRole(string|array $roles): bool
    {
        $role = $this->role();
        if (!$role) {
            return false;
        }

        $roles = is_array($roles) ? $roles : [$roles];
        return in_array($role, $roles);
    }

    public function guest(): bool
    {
        return !$this->check();
    }
}