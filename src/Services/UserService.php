<?php

namespace App\Services;

use App\Models\User;

class UserService
{
    private User $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    public function authenticate(string $username, string $password): ?array
    {
        return $this->userModel->verifyPassword($username, $password);
    }

    public function register(string $username, string $password, string $role): int|false
    {
        if ($this->userModel->findByUsername($username)) {
            return false;
        }

        return $this->userModel->createUser($username, $password, $role);
    }

    public function getAllUsers(): array
    {
        return $this->userModel->getAllUsers();
    }

    public function getUsersByRole(string $role): array
    {
        return $this->userModel->getByRole($role);
    }

    public function getUserStats(): array
    {
        $counts = $this->userModel->countByRole();
        $stats = [
            'total' => 0,
            'Administrator' => 0,
            'Doctor' => 0,
            'Receptionist' => 0,
            'Patient' => 0,
        ];

        foreach ($counts as $row) {
            $stats[$row['role']] = (int)$row['count'];
            $stats['total'] += (int)$row['count'];
        }

        return $stats;
    }

    public function deleteUser(int $id, int $currentUserId): bool
    {
        if ($id === $currentUserId) {
            return false;
        }

        return $this->userModel->delete($id) > 0;
    }
}