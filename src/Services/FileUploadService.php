<?php
declare(strict_types=1);
// File: src/Services/FileUploadService.php

namespace App\Services;

class FileUploadService {
    public function __construct(private string $uploadPath) {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    public function upload(array $file): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('File upload failed with error code: ' . $file['error']);
        }
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $this->uploadPath . '/' . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('Failed to move uploaded file.');
        }
        return $filename;
    }

    public function delete(string $filename): bool {
        $filepath = $this->uploadPath . '/' . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }
}
