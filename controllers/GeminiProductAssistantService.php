<?php
declare(strict_types=1);

require_once ROOT_PATH . '/config/services.php';

class GeminiProductAssistantService
{
    private array $services;

    public function __construct(?array $services = null)
    {
        $this->services = $services ?? produits_services();
    }

    public function isConfigured(): bool
    {
        return $this->apiKey() !== '';
    }

    public function chat(string $message, array $history, string $catalogContext): string
    {
        $message = $this->limitText($message, 700);
        if ($catalogContext === '') {
            $catalogContext = 'No products are currently available.';
        }

        if (!$this->isConfigured()) {
            return $this->localFallback($message, $catalogContext);
        }

        $systemPrompt = <<<PROMPT
You are Asteria Product Assistant, a friendly nutrition-store chatbot.
Answer using ONLY the product catalog context below. Be short, practical, and specific.
Make the response attractive and easy to scan. Use this format when recommending products:
🎯 Best match
- Product name | price DT | stock status
✅ Why it fits: one short reason
💡 How to choose: one practical tip
⭐ Alternatives
- Product name | price DT | stock status
- Product name | price DT | stock status
If nothing matches, politely say what information you need, such as budget, goal, flavor, or category.
Do not invent products, prices, discounts, stock, medical claims, or delivery promises.

PRODUCT CATALOG:
{$catalogContext}
PROMPT;

        $contents = [];
        foreach ($this->cleanHistory($history) as $item) {
            $contents[] = [
                'role' => $item['role'],
                'parts' => [['text' => $item['text']]],
            ];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        try {
            return $this->generateText($systemPrompt, $contents, 520, 0.45);
        } catch (Throwable) {
            return $this->localFallback($message, $catalogContext);
        }
    }

    private function generateText(string $systemPrompt, array $contents, int $maxTokens, float $temperature): string
    {
        $apiKey = $this->apiKey();
        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $model = trim((string) ($this->services['gemini_model'] ?? 'gemini-2.5-flash'));
        $baseUrl = rtrim((string) ($this->services['gemini_api_url'] ?? 'https://generativelanguage.googleapis.com/v1beta/models'), '/');
        $url = $baseUrl . '/' . rawurlencode($model) . ':generateContent';
        $payload = [
            'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents' => $contents,
            'generationConfig' => [
                'temperature' => $temperature,
                'topP' => 0.9,
                'maxOutputTokens' => $maxTokens,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ],
        ];

        $response = $this->requestJson($url, $payload, [
            'Content-Type: application/json',
            'x-goog-api-key: ' . $apiKey,
        ]);

        $text = '';
        foreach (($response['candidates'][0]['content']['parts'] ?? []) as $part) {
            if (is_array($part) && isset($part['text'])) {
                $text .= (string) $part['text'];
            }
        }

        $text = trim($text);
        if ($text === '') {
            throw new RuntimeException('Gemini returned an empty response.');
        }

        return $this->limitText($text, 1200);
    }

    private function requestJson(string $url, array $payload, array $headers): array
    {
        $body = json_encode($payload, JSON_THROW_ON_ERROR);

        if (function_exists('curl_init')) {
            $handle = curl_init($url);
            curl_setopt_array($handle, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => $body,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);
            $response = curl_exec($handle);
            if ($response === false) {
                $message = curl_error($handle);
                curl_close($handle);
                throw new RuntimeException($message !== '' ? $message : 'Gemini did not respond.');
            }
            $statusCode = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
            curl_close($handle);
        } else {
            $context = stream_context_create(['http' => [
                'method' => 'POST',
                'header' => implode("\r\n", $headers),
                'content' => $body,
                'timeout' => 20,
            ]]);
            $response = @file_get_contents($url, false, $context);
            $statusCode = 200;
            if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches) === 1) {
                $statusCode = (int) $matches[1];
            }
            if ($response === false) {
                throw new RuntimeException('Gemini did not respond.');
            }
        }

        if ($statusCode >= 400) {
            throw new RuntimeException('Gemini returned HTTP ' . $statusCode . '.');
        }

        $decoded = json_decode((string) $response, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw new RuntimeException('Gemini returned invalid JSON.');
        }

        return $decoded;
    }

    private function cleanHistory(array $history): array
    {
        $clean = [];
        foreach (array_slice($history, -8) as $item) {
            if (!is_array($item)) {
                continue;
            }
            $role = (string) ($item['role'] ?? 'user');
            $text = trim((string) ($item['text'] ?? ''));
            if ($text === '') {
                continue;
            }
            $clean[] = [
                'role' => $role === 'model' ? 'model' : 'user',
                'text' => $this->limitText($text, 700),
            ];
        }

        return $clean;
    }

    private function localFallback(string $message, string $catalogContext): string
    {
        $messageWords = array_values(array_filter(preg_split('/[^\p{L}\p{N}.]+/u', mb_strtolower($message)) ?: []));
        $lines = array_values(array_filter(explode("\n", $catalogContext)));
        $matches = [];
        foreach ($lines as $line) {
            $score = 0;
            $haystack = mb_strtolower($line);
            foreach ($messageWords as $word) {
                if (mb_strlen($word) >= 3 && str_contains($haystack, $word)) {
                    $score++;
                }
            }
            if ($score > 0) {
                $matches[] = ['score' => $score, 'line' => $line];
            }
        }
        usort($matches, static fn(array $a, array $b): int => $b['score'] <=> $a['score']);

        if ($matches === []) {
            return "👋 I can help you pick from the Asteria catalog.\n\n💡 Tell me your goal, budget, or product type, for example: protein under 50 DT, weight loss, creatine, vitamins, or snacks.";
        }

        $suggestions = array_slice(array_column($matches, 'line'), 0, 4);
        return "🎯 Best catalog matches\n" . implode("\n", $suggestions) . "\n\n✅ Tip: compare price, stock, and category before ordering.\n💡 For smarter Gemini recommendations, set GEMINI_API_KEY in produits/.env.";
    }

    private function limitText(string $text, int $maxLength): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }

        return rtrim(mb_substr($text, 0, $maxLength - 1)) . '...';
    }

    private function apiKey(): string
    {
        return trim((string) ($this->services['gemini_api_key'] ?? ''));
    }
}
