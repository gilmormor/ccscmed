<?php

namespace App\Services;

class ClaudeService
{
    protected $apiKey;
    protected $model = 'claude-opus-4-6';
    protected $url   = 'https://api.anthropic.com/v1/messages';

    public function __construct()
    {
        $this->apiKey = config('services.anthropic.key');
    }

    public function preguntar(string $prompt): string
    {
        $payload = json_encode([
            'model'      => $this->model,
            'max_tokens' => 4096,
            'messages'   => [
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => implode("\r\n", [
                    'Content-Type: application/json',
                    'x-api-key: ' . $this->apiKey,
                    'anthropic-version: 2023-06-01',
                    'Content-Length: ' . strlen($payload),
                ]),
                'content'       => $payload,
                'timeout'       => 60,
                'ignore_errors' => true,
            ],
            'ssl' => [
                'verify_peer'      => true,
                'verify_peer_name' => true,
            ],
        ]);

        $raw = file_get_contents($this->url, false, $context);

        if ($raw === false) {
            throw new \RuntimeException('No se pudo conectar con la API de Anthropic.');
        }

        $body = json_decode($raw, true);

        if (isset($body['error'])) {
            throw new \RuntimeException('Error de la API: ' . $body['error']['message']);
        }

        return $body['content'][0]['text'] ?? '';
    }
}
