<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Encryptor;
use App\Models\User;

class ProfileService
{
    public function __construct(
        private User $user
    ) {}

    public function getProfile(int $userId): array
    {
        $user = $this->user->find($userId);

        return [
            'name' => $user['name'] ?? '',
            'email' => $user['email'] ?? '',
            'avatar' => null,
            'phone' => $user['phone'] ?? '',
            'address' => $user['address'] ?? '',
            'user_code' => $user['user_code'] ?? '',
            'created_at' => $user['created_at'] ?? '',
            'role' => $user['role'] ?? '',
        ];
    }

    public function updateProfile(int $userId, array $data): void
    {
        $this->user->update($userId, [
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? '',
            'address' => $data['address'] ?? '',
        ]);
    }

    public function changePassword(int $userId, string $currentPassword, string $newPassword): bool
    {
        $user = $this->user->find($userId);
        if (!$user) {
            return false;
        }

        if (!password_verify(Encryptor::hmac($currentPassword), $user['password'])) {
            return false;
        }

        $this->user->update($userId, [
            'password' => password_hash(Encryptor::hmac($newPassword), PASSWORD_DEFAULT),
        ]);

        return true;
    }
}
