<?php
declare(strict_types=1);

require_once ROOT_PATH . '/config/services.php';
require_once ROOT_PATH . '/models/ProgressGoal.php';

class GeminiAssistantService
{
    private array $services;

    public function __construct(?array $services = null)
    {
        $this->services = $services ?? progress_services();
    }

    public function isConfigured(): bool
    {
        return $this->apiKey() !== '';
    }

    public function goalMotivation(ProgressGoal $goal, array $summary, string $type, array $fallback): array
    {
        if (!$this->isConfigured()) {
            return $fallback;
        }

        $prompt = sprintf(
            "Create one short motivational message for a progress-tracking app.\n" .
            "Return exactly one JSON object with keys content, author, tag.\n" .
            "Rules: content must be under 38 words, practical, kind, and not medical/financial/legal advice. Do not mention that you are an AI.\n" .
            "Goal title: %s\nMetric: %s\nGoal type: %s\nStart value: %.2f %s\nTarget value: %.2f %s\nCurrent value: %.2f %s\nRemaining to target: %.2f %s\nSuggested weekly pace: %.2f %s\nMotivation style: %s\n" .
            "Use author value Asteria Gemini assistant and tag value %s.",
            $goal->getTitle(),
            $goal->getMetric(),
            $goal->getGoalType(),
            $goal->getStartValue(),
            $goal->getUnit(),
            $goal->getTargetValue(),
            $goal->getUnit(),
            (float) ($summary['current_value'] ?? $goal->getStartValue()),
            $goal->getUnit(),
            (float) ($summary['remaining_to_target'] ?? 0.0),
            $goal->getUnit(),
            (float) ($summary['weekly_target_pace'] ?? 0.0),
            $goal->getUnit(),
            $type !== '' ? $type : 'subject',
            $type !== '' ? $type : 'subject'
        );

        return $this->safeQuoteFromGemini($prompt, $fallback, $type !== '' ? $type : 'subject');
    }

    public function recordInsight(ProgressGoal $goal, ?array $baseline, array $fallback): array
    {
        if (!$this->isConfigured()) {
            return $fallback;
        }

        $baselineText = $baseline === null
            ? 'No record exists from the previous week.'
            : sprintf('Previous baseline: %.2f %s on %s.', (float) $baseline['recorded_value'], $goal->getUnit(), (string) $baseline['record_date']);

        $prompt = sprintf(
            "Create one short progress insight for a record form in a progress-tracking app.\n" .
            "Return exactly one JSON object with keys content, author, tag.\n" .
            "Rules: content must be under 38 words, practical, kind, and not medical/financial/legal advice. Do not mention that you are an AI.\n" .
            "Goal title: %s\nMetric: %s\nGoal type: %s\nStart value: %.2f %s\nTarget value: %.2f %s\n%s\n" .
            "Use author value Asteria Gemini assistant and tag value %s.",
            $goal->getTitle(),
            $goal->getMetric(),
            $goal->getGoalType(),
            $goal->getStartValue(),
            $goal->getUnit(),
            $goal->getTargetValue(),
            $goal->getUnit(),
            $baselineText,
            strtolower($goal->getGoalType())
        );

        return $this->safeQuoteFromGemini($prompt, $fallback, strtolower($goal->getGoalType()));
    }

    private function safeQuoteFromGemini(string $prompt, array $fallback, string $defaultTag): array
    {
        try {
            $responseText = $this->generateText($prompt);
            $decoded = $this->decodeJsonObject($responseText);
            $quote = $this->normalizeQuote($decoded, $fallback, $defaultTag);

            return $quote;
        } catch (Throwable) {
            return $fallback;
        }
    }

    private function generateText(string $prompt): string
    {
        $apiKey = $this->apiKey();
        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $model = trim((string) ($this->services['gemini_model'] ?? 'gemini-2.5-flash'));
        $baseUrl = rtrim((string) ($this->services['gemini_api_url'] ?? 'https://generativelanguage.googleapis.com/v1beta/models'), '/');
        $url = $baseUrl . '/' . rawurlencode($model) . ':generateContent';

        $payload = [
            'systemInstruction' => [
                'parts' => [
                    ['text' => 'You write concise, supportive, safe coaching copy for a PHP progress tracker. Return only valid JSON.'],
                ],
            ],
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'topP' => 0.9,
                'maxOutputTokens' => 220,
                'responseMimeType' => 'application/json',
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

        $parts = $response['candidates'][0]['content']['parts'] ?? [];
        if (!is_array($parts)) {
            throw new RuntimeException('Gemini response did not include content parts.');
        }

        $text = '';
        foreach ($parts as $part) {
            if (is_array($part) && isset($part['text'])) {
                $text .= (string) $part['text'];
            }
        }

        $text = trim($text);
        if ($text === '') {
            throw new RuntimeException('Gemini returned an empty response.');
        }

        return $text;
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
            $context = stream_context_create([
                'http' => [
                    'method' => 'POST',
                    'header' => implode("\r\n", $headers),
                    'content' => $body,
                    'timeout' => 20,
                ],
            ]);
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

    private function decodeJsonObject(string $text): array
    {
        $trimmed = trim($text);
        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed) ?? $trimmed;
            $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?? $trimmed;
        }

        $decoded = json_decode($trimmed, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (preg_match('/\{.*\}/s', $trimmed, $matches) === 1) {
            $decoded = json_decode($matches[0], true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        throw new RuntimeException('Gemini did not return a JSON object.');
    }

    private function normalizeQuote(array $decoded, array $fallback, string $defaultTag): array
    {
        $content = trim((string) ($decoded['content'] ?? ''));
        $author = trim((string) ($decoded['author'] ?? 'Asteria Gemini assistant'));
        $tag = trim((string) ($decoded['tag'] ?? $defaultTag));

        if ($content === '') {
            return $fallback;
        }

        $content = $this->limitText($content, 320);

        return [
            'content' => $content,
            'author' => $author !== '' ? $this->limitText($author, 80) : 'Asteria Gemini assistant',
            'tag' => $tag !== '' ? $this->limitText($tag, 40) : $defaultTag,
        ];
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
