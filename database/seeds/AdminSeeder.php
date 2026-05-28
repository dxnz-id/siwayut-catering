<?php
declare(strict_types=1);

namespace Database\Seeds;

use App\Core\Encryptor;

class AdminSeeder {
    public function __construct(private \PDO $db) {}

    public function run(): void {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM `users` WHERE `email` = ?");
        $stmt->execute(['admin@admin.com']);
        if ((int) $stmt->fetchColumn() > 0) {
            echo "Admin user already exists. Skipping.\n";
            return;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO `users` (`name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES (?, ?, ?, ?, ?, ?)"
        );
        $stmt->execute([
            'Administrator',
            'admin@admin.com',
            password_hash(Encryptor::hmac('password'), PASSWORD_DEFAULT),
            'admin',
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        ]);

        echo "Admin user created successfully.\n";
        echo "Email: admin@admin.com\n";
        echo "Password: password\n";
    }
}
