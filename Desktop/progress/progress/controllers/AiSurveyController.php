<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/config/services.php';

class AiSurveyController extends BaseController
{
    public function survey(string $area): void
    {
        $this->render('goals/survey', [
            'pageTitle' => 'AI Goal Survey',
            'area' => $area,
            'currentSection' => 'ai-survey',
        ], $area);
    }

    public function recommend(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
            return;
        }

        $answers = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($answers)) {
            $answers = [];
        }

        $required = ['focus', 'days_per_week', 'level', 'limitations', 'timeframe', 'intensity'];
        foreach ($required as $field) {
            if (trim((string) ($answers[$field] ?? '')) === '') {
                http_response_code(422);
                echo json_encode(['error' => 'Please answer all survey questions.']);
                return;
            }
        }

        try {
            $goal = $this->geminiGoal($answers);
        } catch (Throwable) {
            $goal = $this->fallbackGoal($answers);
        }

        echo json_encode(['goal' => $goal], JSON_THROW_ON_ERROR);
    }

    private function geminiGoal(array $answers): array
    {
        $services = progress_services();
        $apiKey = trim((string) ($services['gemini_api_key'] ?? ''));
        if ($apiKey === '') {
            throw new RuntimeException('GEMINI_API_KEY is not configured.');
        }

        $systemPrompt = <<<PROMPT
You are Asteria AI Goal Recommender.
Based on a user's survey answers, recommend one realistic and measurable progress goal.
Return ONLY a valid JSON object with these keys:
title, metric, unit, goal_type, start_value, target_value, timeframe_weeks, explanation, weekly_plan, target_date.
Rules:
- goal_type must be one of WEIGHT_LOSS, FITNESS, HEALTH, OTHER.
- start_value and target_value must be numbers.
- timeframe_weeks must be an integer between 4 and 26.
- target_date must use YYYY-MM-DD and should match the timeframe from today as closely as possible.
- Be realistic and safe. Do not provide medical advice.
PROMPT;

        $today = date('Y-m-d');
        $userPrompt = "Today's date: {$today}\nSurvey answers:\n"
            . 'Main focus: ' . $this->cleanAnswer($answers['focus'] ?? '') . "\n"
            . 'Days per week: ' . $this->cleanAnswer($answers['days_per_week'] ?? '') . "\n"
            . 'Level: ' . $this->cleanAnswer($answers['level'] ?? '') . "\n"
            . 'Limitations: ' . $this->cleanAnswer($answers['limitations'] ?? '') . "\n"
            . 'Timeframe: ' . $this->cleanAnswer($answers['timeframe'] ?? '') . "\n"
            . 'Intensity: ' . $this->cleanAnswer($answers['intensity'] ?? '') . "\n"
            . 'Extra: ' . $this->cleanAnswer($answers['extra'] ?? '');

        $model = trim((string) ($services['gemini_model'] ?? 'gemini-2.5-flash'));
        $baseUrl = rtrim((string) ($services['gemini_api_url'] ?? 'https://generativelanguage.googleapis.com/v1beta/models'), '/');
        $url = $baseUrl . '/' . rawurlencode($model) . ':generateContent';
        $payload = [
            'systemInstruction' => ['parts' => [['text' => $systemPrompt]]],
            'contents' => [[
                'role' => 'user',
                'parts' => [['text' => $userPrompt]],
            ]],
            'generationConfig' => [
                'temperature' => 0.35,
                'topP' => 0.9,
                'maxOutputTokens' => 750,
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

        $text = '';
        foreach (($response['candidates'][0]['content']['parts'] ?? []) as $part) {
            if (is_array($part) && isset($part['text'])) {
                $text .= (string) $part['text'];
            }
        }

        $decoded = $this->decodeJsonObject($text);
        return $this->normalizeGoal($decoded, $answers);
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

    private function normalizeGoal(array $goal, array $answers): array
    {
        $weeks = max(4, min(26, (int) ($goal['timeframe_weeks'] ?? $this->timeframeWeeks((string) ($answers['timeframe'] ?? '')))));
        $targetDate = trim((string) ($goal['target_date'] ?? ''));
        if (!$this->isDate($targetDate)) {
            $targetDate = date('Y-m-d', strtotime('+' . $weeks . ' weeks'));
        }

        $goalType = strtoupper(trim((string) ($goal['goal_type'] ?? 'FITNESS')));
        if (!in_array($goalType, ['WEIGHT_LOSS', 'FITNESS', 'HEALTH', 'OTHER'], true)) {
            $goalType = str_contains(strtolower((string) ($answers['focus'] ?? '')), 'weight') ? 'WEIGHT_LOSS' : 'FITNESS';
        }

        return [
            'title' => $this->limitText((string) ($goal['title'] ?? 'Build Consistent Progress'), 120),
            'metric' => $this->limitText((string) ($goal['metric'] ?? 'Weekly Workouts'), 100),
            'unit' => $this->limitText((string) ($goal['unit'] ?? 'sessions'), 30),
            'goal_type' => $goalType,
            'start_value' => is_numeric($goal['start_value'] ?? null) ? (float) $goal['start_value'] : 0.0,
            'target_value' => is_numeric($goal['target_value'] ?? null) ? (float) $goal['target_value'] : 12.0,
            'timeframe_weeks' => $weeks,
            'target_date' => $targetDate,
            'explanation' => $this->limitText((string) ($goal['explanation'] ?? 'This target is realistic for your schedule and gives you a measurable weekly checkpoint.'), 600),
            'weekly_plan' => $this->limitText((string) ($goal['weekly_plan'] ?? 'Complete your planned sessions and record one progress update each week.'), 300),
        ];
    }

    private function fallbackGoal(array $answers): array
    {
        $focus = strtolower((string) ($answers['focus'] ?? 'fitness'));
        $weeks = $this->timeframeWeeks((string) ($answers['timeframe'] ?? ''));
        if (str_contains($focus, 'weight') || str_contains($focus, 'lose')) {
            $type = 'WEIGHT_LOSS';
            $title = 'Lose Weight Consistently';
            $metric = 'Body Weight Change';
            $unit = 'kg';
            $start = 0.0;
            $target = -max(2.0, round($weeks * 0.35, 1));
        } elseif (str_contains($focus, 'muscle') || str_contains($focus, 'strong')) {
            $type = 'FITNESS';
            $title = 'Build Strength Consistency';
            $metric = 'Completed Workouts';
            $unit = 'sessions';
            $start = 0.0;
            $target = (float) max(8, $weeks * 3);
        } else {
            $type = 'HEALTH';
            $title = 'Improve Weekly Fitness';
            $metric = 'Active Days';
            $unit = 'days';
            $start = 0.0;
            $target = (float) max(12, $weeks * 3);
        }

        return [
            'title' => $title,
            'metric' => $metric,
            'unit' => $unit,
            'goal_type' => $type,
            'start_value' => $start,
            'target_value' => $target,
            'timeframe_weeks' => $weeks,
            'target_date' => date('Y-m-d', strtotime('+' . $weeks . ' weeks')),
            'explanation' => 'This local recommendation is based on your focus, schedule, and timeframe. Add weekly records so Asteria can track your trend and keep the target realistic.',
            'weekly_plan' => 'Complete your planned sessions, record one checkpoint, and adjust intensity only when recovery feels stable.',
        ];
    }

    private function timeframeWeeks(string $timeframe): int
    {
        $value = strtolower($timeframe);
        if (str_contains($value, '4')) return 4;
        if (str_contains($value, '6') || str_contains($value, '8')) return 8;
        if (str_contains($value, '3 month')) return 12;
        if (str_contains($value, '6 month')) return 24;
        return 10;
    }

    private function cleanAnswer(mixed $value): string
    {
        return $this->limitText(trim((string) $value), 500);
    }

    private function limitText(string $text, int $maxLength): string
    {
        $text = trim(preg_replace('/\s+/', ' ', $text) ?? $text);
        if (mb_strlen($text) <= $maxLength) {
            return $text;
        }
        return rtrim(mb_substr($text, 0, $maxLength - 1)) . '...';
    }

    private function isDate(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);
        return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === $value;
    }
}
