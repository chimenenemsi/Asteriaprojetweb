<?php
declare(strict_types=1);

class GeminiCoachService
{
    private string $apiKey;
    private string $model;

    public function __construct()
    {
        $services = require ROOT_PATH . '/config/services.php';
        $this->apiKey = (string)($services['gemini_api_key'] ?? '');
        $this->model = (string)($services['gemini_model'] ?? 'gemini-1.5-flash');
    }

    public function getCoachingResponse(string $message): string
    {
        if (empty($this->apiKey)) {
            return "Configuration Error: GEMINI_API_KEY is missing in .env. Please add it to enable AI coaching features.";
        }

        $prompt = "You are a professional nutrition and fitness coach for Asteria.
        Keep responses concise, motivating, and science-based.
        User message: " . $message;

        return $this->callGemini($prompt);
    }

    public function getSummary(string $content): string
    {
        if (empty($this->apiKey)) {
            return "Configuration Error: GEMINI_API_KEY is missing in .env.";
        }

        $prompt = "Summarize the following nutrition or fitness content in 3-5 bullet points.
        Content: " . $content;

        return $this->callGemini($prompt);
    }

    private function callGemini(string $prompt): string
    {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $data = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // For local development compatibility

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            return "API Error: " . $error;
        }

        $result = json_decode((string)$response, true);
        return $result['candidates'][0]['content']['parts'][0]['text'] ?? "I'm sorry, I couldn't generate a response at this time.";
    }
}
