<?php
declare(strict_types=1);

namespace App\Services;

class AiService {
    private string $apiUrl;
    private string $apiKey;
    private string $model;

    public function __construct() {
        $this->apiUrl = rtrim(env('AI_API_URL', ''), '/');
        $this->apiKey = env('AI_API_KEY', '');
        $this->model = env('AI_MODEL', '');
    }

    public function generateDescription(array $context): string {
        if ($this->apiUrl === '') {
            throw new \RuntimeException('AI_API_URL is not configured. Set it in .env file.');
        }
        if ($this->model === '') {
            throw new \RuntimeException('AI_MODEL is not configured. Set it in .env file.');
        }

        $prompt = $this->buildPrompt($context);

        $payload = [
            'model' => $this->model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'You are a professional catering menu description writer. Generate concise, appetizing descriptions in Indonesian language. Keep it under 200 characters. Respond only with the description text, no extra formatting.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'max_tokens' => 200,
            'temperature' => 0.7,
        ];

        $headers = ['Content-Type: application/json'];
        if ($this->apiKey !== '') {
            $headers[] = 'Authorization: Bearer ' . $this->apiKey;
        }

        $ch = curl_init($this->apiUrl . '/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 20,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        if ($response === false || $response === '') {
            throw new \RuntimeException('AI API connection failed: ' . ($curlError ?: 'No response'));
        }

        if ($httpCode !== 200) {
            $error = json_decode($response, true);
            $msg = $error['error']['message'] ?? ($error['message'] ?? 'HTTP ' . $httpCode);
            throw new \RuntimeException('AI API error: ' . $msg);
        }

        $data = json_decode($response, true);
        $text = trim($data['choices'][0]['message']['content'] ?? '');

        if ($text === '') {
            throw new \RuntimeException('AI returned empty response.');
        }

        return $text;
    }

    private function buildPrompt(array $ctx): string {
        $parts = [];
        if (!empty($ctx['name'])) $parts[] = "Menu: {$ctx['name']}";
        if (!empty($ctx['category'])) $parts[] = "Kategori: {$ctx['category']}";
        if (!empty($ctx['event'])) $parts[] = "Acara: {$ctx['event']}";
        if (!empty($ctx['price'])) $parts[] = "Harga: Rp " . number_format((float)$ctx['price'], 0, ',', '.');
        if (!empty($ctx['minimum_portions'])) $parts[] = "Minimal porsi: {$ctx['minimum_portions']}";

        return "Buat deskripsi menu catering yang menggugah selera dalam Bahasa Indonesia berdasarkan data berikut:\n" . implode("\n", $parts) . "\n\nDeskripsi:";
    }
}
