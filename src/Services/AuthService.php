<?php
declare(strict_types=1);
// File: src/Services/AuthService.php

namespace App\Services;
use App\Models\User;
use App\Models\Customer;
use App\Core\Encryptor;
use App\Core\Session;

class AuthService {
    public function __construct(
        private User $userModel,
        private Customer $customerModel
    ) {
    }

    public function login(string $email, string $password): bool {
        $attempts = (int)Session::get('_login_attempts', 0);
        if ($attempts >= 3) {
            sleep(min($attempts - 2, 10));
        }

        $user = $this->userModel->findByEmail($email);
        if (!$user) {
            Session::set('_login_attempts', $attempts + 1);
            return false;
        }
        if (!password_verify(Encryptor::hmac($password), $user['password'])) {
            Session::set('_login_attempts', $attempts + 1);
            return false;
        }
        Session::forget('_login_attempts');
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
        session_regenerate_id(true);
        Session::destroy();
    }

    public function register(string $name, string $email, string $phone, string $password): int {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone) ?? '';

        $userId = $this->userModel->create([
            'name' => $name,
            'email' => $email,
            'password' => password_hash(Encryptor::hmac($password), PASSWORD_DEFAULT),
            'role' => 'user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $customerData = [
            'user_id' => $userId,
            'name' => $name,
            'phone' => $cleanPhone,
            'email' => $email,
            'address' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if ($cleanPhone !== '') {
            $existing = $this->customerModel->findByPhone($cleanPhone);
            if ($existing) {
                $this->customerModel->linkUserByPhone($cleanPhone, $userId);
                $customerData = null;
            }
        }

        if ($customerData !== null) {
            $this->customerModel->create($customerData);
        }

        return $userId;
    }
}
