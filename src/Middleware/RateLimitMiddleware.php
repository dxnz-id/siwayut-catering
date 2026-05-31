<?php
declare(strict_types=1);

namespace App\Middleware;
use App\Core\Request;
use App\Exceptions\HttpException;

class RateLimitMiddleware implements MiddlewareInterface {
    private static string $storagePath = '';
    private int $maxRequests;
    private int $windowSeconds;

    public function __construct(string $config = '10,60') {
        $parts = explode(',', $config);
        $this->maxRequests = (int)($parts[0] ?? 10);
        $this->windowSeconds = (int)($parts[1] ?? 60);
    }

    public function handle(Request $request): bool {
        if (self::$storagePath === '') {
            self::$storagePath = dirname(__DIR__, 2) . '/storage/rate-limit';
            if (!is_dir(self::$storagePath)) {
                mkdir(self::$storagePath, 0750, true);
            }
        }

        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $key = 'rl_' . md5($ip);
        $file = self::$storagePath . '/' . $key . '.json';

        $data = ['requests' => []];
        if (file_exists($file)) {
            $content = @file_get_contents($file);
            if ($content !== false) {
                $decoded = json_decode($content, true);
                if (is_array($decoded)) {
                    $data = $decoded;
                }
            }
        }

        $now = time();
        $windowStart = $now - $this->windowSeconds;
        $data['requests'] = array_values(array_filter($data['requests'], fn($t) => $t > $windowStart));

        if (count($data['requests']) >= $this->maxRequests) {
            throw new HttpException(429, 'Too many requests. Please try again later.');
        }

        $data['requests'][] = $now;
        file_put_contents($file, json_encode($data), LOCK_EX);

        return true;
    }
}
