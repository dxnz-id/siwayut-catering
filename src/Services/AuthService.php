<?php
declare(strict_types=1);
// File: src/Services/AuthService.php

namespace App\Services;
use App\Models\User;
use App\Core\Session;

class AuthService {
    public function __construct(private User $userModel) {
    }

    public function login(string $email, string $password): bool {
        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            return false;
        }
        if (!password_verify($password, $user['password'])) {
            return false;
        }
        Session::regenerate();
        Session::set('user', [
            'id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role' => $user['role'],
        ]);
        return true;
    }

    public function logout(): void {
        Session::destroy();
    }
}
