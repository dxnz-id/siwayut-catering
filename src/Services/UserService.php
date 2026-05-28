<?php
declare(strict_types=1);
// File: src/Services/UserService.php

namespace App\Services;
use App\Models\User;
use App\Core\Encryptor;
use App\Exceptions\NotFoundException;

class UserService {
    public function __construct(private User $userModel) {
    }

    public function getAll(int $page = 1, int $perPage = 15, string $search = '', array $filters = [], string $orderBy = 'created_at', string $direction = 'DESC'): array {
        $conditions = [];
        if (!empty($filters['role'])) $conditions['role'] = $filters['role'];
        return $this->userModel->paginate($page, $perPage, $conditions, $search, $this->userModel->getSearchableColumns(), $orderBy, $direction);
    }

    public function getById(int $id): array {
        $user = $this->userModel->find($id);
        if (!$user) {
            throw new NotFoundException('User not found');
        }
        return $user;
    }

    public function create(array $data): int {
        $data['password'] = password_hash(Encryptor::hmac($data['password']), PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->userModel->create($data);
    }

    public function update(int $id, array $data): bool {
        $this->getById($id); // ensure exists
        if (isset($data['password']) && $data['password'] !== '') {
            $data['password'] = password_hash(Encryptor::hmac($data['password']), PASSWORD_DEFAULT);
        } else {
            unset($data['password']);
        }
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->userModel->update($id, $data);
    }

    public function delete(int $id): bool {
        $this->getById($id); // ensure exists
        return $this->userModel->delete($id);
    }
}
