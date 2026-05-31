<?php
declare(strict_types=1);
// File: src/Services/FileUploadService.php

namespace App\Services;

class FileUploadService {
    private array $allowedMimes = ['image/jpeg', 'image/png', 'image/webp'];
    private int $maxFileSize = 5242880; // 5MB

    public function __construct(private string $uploadPath) {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }

    public function setAllowedMimes(array $mimes): self {
        $this->allowedMimes = $mimes;
        return $this;
    }

    public function setMaxFileSize(int $bytes): self {
        $this->maxFileSize = $bytes;
        return $this;
    }

    public function upload(array $file, ?string $subdirectory = null): string {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('File upload failed with error code: ' . $file['error']);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, $this->allowedMimes, true)) {
            $allowed = array_map(function ($m) {
                return strtoupper(str_replace('image/', '', $m));
            }, $this->allowedMimes);
            throw new \RuntimeException('Invalid file type. Only ' . implode(', ', $allowed) . ' files are allowed.');
        }

        if ($file['size'] > $this->maxFileSize) {
            $maxLabel = $this->maxFileSize >= 1048576
                ? (round($this->maxFileSize / 1048576, 1) . ' MB')
                : (round($this->maxFileSize / 1024, 1) . ' KB');
            throw new \RuntimeException('File too large. Maximum size is ' . $maxLabel . '.');
        }

        $destinationPath = $this->uploadPath;
        if ($subdirectory !== null) {
            $destinationPath .= '/' . $subdirectory;
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $destinationPath . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('Failed to move uploaded file.');
        }

        $this->generateThumbnail($destination);

        return $subdirectory !== null ? $subdirectory . '/' . $filename : $filename;
    }

    public function uploadFromUrl(string $url, ?string $subdirectory = null): string {
        $scheme = parse_url($url, PHP_URL_SCHEME);
        if (!in_array($scheme, ['http', 'https'], true)) {
            throw new \RuntimeException('Only HTTP/HTTPS URLs are allowed.');
        }

        $host = parse_url($url, PHP_URL_HOST);
        $ip = gethostbyname($host);
        if ($ip !== $host && filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            throw new \RuntimeException('Access to private or reserved IP ranges is not allowed.');
        }

        $context = stream_context_create(['http' => ['timeout' => 15, 'user_agent' => 'SiwayutCatering/1.0']]);
        $content = @file_get_contents($url, false, $context);
        if ($content === false) {
            throw new \RuntimeException('Failed to download image from URL.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->buffer($content);

        $extension = match ($mimeType) {
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/gif'  => 'gif',
            'image/webp' => 'webp',
            default      => 'jpg',
        };

        $destinationPath = $this->uploadPath;
        if ($subdirectory !== null) {
            $destinationPath .= '/' . $subdirectory;
            if (!is_dir($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $extension;
        $destination = $destinationPath . '/' . $filename;

        if (file_put_contents($destination, $content) === false) {
            throw new \RuntimeException('Failed to save downloaded image.');
        }

        $this->generateThumbnail($destination);

        return $subdirectory !== null ? $subdirectory . '/' . $filename : $filename;
    }

    public function delete(string $filename): bool {
        $this->deleteThumbnail($filename);
        $filepath = $this->uploadPath . '/' . $filename;
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    private function generateThumbnail(string $fullPath): void {
        $info = @getimagesize($fullPath);
        if ($info === false) return;

        [$origW, $origH] = $info;
        $thumbW = 20;
        $thumbH = max(1, (int)round($origH * $thumbW / $origW));

        $srcImg = match ($info[2]) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($fullPath),
            IMAGETYPE_PNG  => @imagecreatefrompng($fullPath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($fullPath),
            default        => null,
        };
        if ($srcImg === null) return;

        $thumbImg = imagecreatetruecolor($thumbW, $thumbH);
        imagecopyresampled($thumbImg, $srcImg, 0, 0, 0, 0, $thumbW, $thumbH, $origW, $origH);

        $thumbDir = dirname($fullPath) . '/thumbs';
        if (!is_dir($thumbDir)) {
            mkdir($thumbDir, 0755, true);
        }
        $thumbPath = $thumbDir . '/' . basename($fullPath);
        imagejpeg($thumbImg, $thumbPath, 30);
    }

    private function deleteThumbnail(string $filename): void {
        $thumbPath = $this->uploadPath . '/' . dirname($filename) . '/thumbs/' . basename($filename);
        if (file_exists($thumbPath)) {
            unlink($thumbPath);
        }
    }
}
