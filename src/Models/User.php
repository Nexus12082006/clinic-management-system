<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';
    protected array $fillable = ['username', 'password_hash', 'role', 'remember_token', 'remember_expires'];
    protected array $hidden = ['password_hash', 'remember_token', 'remember_expires'];

    public function findByUsername(string $username): ?array
    {
        return $this->findBy('username', $username);
    }

    public function createUser(string $username, string $password, string $role): int|false
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->create([
            'username' => $username,
            'password_hash' => $hash,
            'role' => $role,
        ]);
    }

    public function verifyPassword(string $username, string $password): ?array
    {
        $user = $this->findByUsername($username);
        
        if (!$user) {
            return null;
        }

        if (!password_verify($password, $user['password_hash'])) {
            if ($this->legacyVerify($password, $user['password'] ?? '')) {
                $this->migratePassword($user['id'], $password);
            } else {
                return null;
            }
        }

        return $this->hide($user);
    }

    private function legacyVerify(string $password, string $storedPassword): bool
    {
        return $password === $storedPassword;
    }

    private function migratePassword(int $userId, string $password): void
    {
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $this->update($userId, ['password_hash' => $hash]);
    }

    public function setRememberToken(int $userId, string $token): void
    {
        $expires = date('Y-m-d H:i:s', time() + 60 * 60 * 24 * 30);
        $this->update($userId, [
            'remember_token' => $token,
            'remember_expires' => $expires,
        ]);
    }

    public function clearRememberToken(int $userId): void
    {
        $this->update($userId, [
            'remember_token' => null,
            'remember_expires' => null,
        ]);
    }

    public function findByRememberToken(string $token): ?array
    {
        return $this->fetch(
            "SELECT * FROM {$this->table} WHERE remember_token = ? AND remember_expires > NOW()",
            [$token]
        );
    }

    public function getByRole(string $role): array
    {
        return $this->where(['role' => $role]);
    }

    public function getAllUsers(): array
    {
        return $this->fetchAll("SELECT id, username, role FROM {$this->table} ORDER BY id DESC");
    }

    public function countByRole(): array
    {
        return $this->fetchAll("SELECT role, COUNT(*) as count FROM {$this->table} GROUP BY role");
    }
}