<?php
declare(strict_types=1);

require_once ROOT_PATH . '/config/services.php';

class GeminiCoachService
{
    private array $services;

    public function __construct(?array $services = null)
    {
        $this->services = $services ?? coaching_services();
    }

    public function isConfigured(): bool
    {
        return $this->apiKey() !== '';
    }

    public function recommendProgram(string $message, array $history, string $programContext): string
    {
        $message = $this->limitText($message, 900);
        if ($programContext === '') {
            $programContext = 'No programs or exercises are currently available.';
        }

        $rankedContext = $this->buildRankedRecommendationContext($message, $programContext);

        if (!$this->isConfigured()) {
            return $this->localProgramFallback($message, $rankedContext);
        }

        $systemPrompt = <<<PROMPT
You are Asteria Coach AI, a careful professional fitness coach.
You MUST use the ranked catalog below. Do not invent programs or exercises.
Accuracy rules:
1. First infer the user's goal, level, schedule, constraints, and target muscles from the message.
2. Compare the ranked candidate programs, not just the first exercise you see.
3. Recommend one PRIMARY program and one BACKUP option when available.
4. Mention 4-6 exercises total, spread across the recommended program(s), and explain why each exercise supports the user's goal.
5. If the user's request is too vague, ask one focused follow-up question while still suggesting the closest safe option.
6. Keep the answer concise, motivating, and practical. Avoid medical advice.
7. Never run with a single exercise for the entire answer unless the catalog truly contains only one exercise.

RANKED PROGRAM ANALYSIS AND AVAILABLE EXERCISES:
{$rankedContext}
PROMPT;

        $contents = [];
        foreach ($this->cleanHistory($history) as $item) {
            $contents[] = ['role' => $item['role'], 'parts' => [['text' => $item['text']]]];
        }
        $contents[] = ['role' => 'user', 'parts' => [['text' => $message]]];

        try {
            return $this->generateText($systemPrompt, $contents, 850, 0.35);
        } catch (Throwable) {
            return $this->localProgramFallback($message, $rankedContext);
        }
    }

    public function summarizePage(string $title, string $pageText): string
    {
        $title = $this->limitText($title, 120);
        $pageText = $this->limitText($pageText, 7000);
        if ($pageText === '') {
            return "Résumé en 5 lignes:\n- Cette page ne contient pas assez de texte à résumer.\n\nPoints clés:\n- Ajoutez du contenu visible puis relancez le résumé.";
        }

        if (!$this->isConfigured()) {
            return $this->localSummary($title, $pageText);
        }

        $systemPrompt = <<<PROMPT
Tu es l'assistant Asteria. Résume une page coaching pour un utilisateur.
Réponds en français avec exactement deux sections:
Résumé en 5 lignes:
- ligne 1
- ligne 2
- ligne 3
- ligne 4
- ligne 5
Points clés:
- point clé 1
- point clé 2
- point clé 3
Reste fidèle au texte donné. N'invente pas de programmes, prix, promesses médicales ou données absentes.
PROMPT;

        $userPrompt = "Titre de page: {$title}\n\nTexte de page:\n{$pageText}";

        try {
            return $this->generateText($systemPrompt, [['role' => 'user', 'parts' => [['text' => $userPrompt]]]], 620, 0.35);
        } catch (Throwable) {
            return $this->localSummary($title, $pageText);
        }
    }

    /**
     * @return array<int,array<string,mixed>>
     */
    public function exerciseIdeas(array $filters): array
    {
        if (!$this->isConfigured()) {
            return [];
        }

        $type = trim((string) ($filters['type'] ?? ''));
        $name = trim((string) ($filters['name'] ?? ''));
        $systemPrompt = <<<PROMPT
You generate exercise ideas for a coaching backoffice.
Return ONLY a valid JSON array of 5 objects. Each object must contain these keys:
name, type, muscle, difficulty, instructions, safety_info, equipments.
Make the 5 exercises diverse: do not repeat the same movement pattern or muscle every time.
The equipments value must be an array of short strings.
Keep instructions practical and under 45 words. Do not include unsafe or medically risky advice.
PROMPT;
        $userPrompt = "Exercise search filters:\nType: " . ($type !== '' ? $type : 'any') . "\nName contains: " . ($name !== '' ? $name : 'any');

        try {
            $text = $this->generateText($systemPrompt, [['role' => 'user', 'parts' => [['text' => $userPrompt]]]], 1100, 0.35, true);
            $decoded = $this->decodeJson($text);
            if (!is_array($decoded)) {
                return [];
            }

            $ideas = [];
            foreach ($decoded as $item) {
                if (!is_array($item)) {
                    continue;
                }
                $nameValue = trim((string) ($item['name'] ?? ''));
                if ($nameValue === '') {
                    continue;
                }
                $ideas[] = [
                    'name' => $this->limitText($nameValue, 100),
                    'type' => $this->limitText((string) ($item['type'] ?? ($type !== '' ? $type : 'strength')), 40),
                    'muscle' => $this->limitText((string) ($item['muscle'] ?? 'full body'), 60),
                    'difficulty' => $this->limitText((string) ($item['difficulty'] ?? 'beginner'), 40),
                    'instructions' => $this->limitText((string) ($item['instructions'] ?? ''), 400),
                    'safety_info' => $this->limitText((string) ($item['safety_info'] ?? ''), 280),
                    'equipments' => array_values(array_filter(array_map('strval', is_array($item['equipments'] ?? null) ? $item['equipments'] : []))),
                ];
            }

            return $this->dedupeExercises($ideas);
        } catch (Throwable) {
            return [];
        }
    }

    private function generateText(string $systemPrompt, array $contents, int $maxTokens, float $temperature, bool $json = false): string
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
                'topP' => 0.82,
                'maxOutputTokens' => $maxTokens,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ],
        ];
        if ($json) {
            $payload['generationConfig']['responseMimeType'] = 'application/json';
        }

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

    private function decodeJson(string $text): mixed
    {
        $trimmed = trim($text);
        if (str_starts_with($trimmed, '```')) {
            $trimmed = preg_replace('/^```(?:json)?\s*/i', '', $trimmed) ?? $trimmed;
            $trimmed = preg_replace('/\s*```$/', '', $trimmed) ?? $trimmed;
        }
        $decoded = json_decode($trimmed, true);
        if ($decoded !== null) {
            return $decoded;
        }
        if (preg_match('/\[[\s\S]*\]/', $trimmed, $matches) === 1) {
            return json_decode($matches[0], true);
        }
        if (preg_match('/\{[\s\S]*\}/', $trimmed, $matches) === 1) {
            return json_decode($matches[0], true);
        }
        return null;
    }

    private function buildRankedRecommendationContext(string $message, string $programContext): string
    {
        $programs = $this->parseProgramContext($programContext);
        if ($programs === []) {
            return $programContext;
        }

        $intent = $this->detectIntent($message);
        foreach ($programs as &$program) {
            $program['score'] = $this->scoreProgram($program, $intent);
            $program['matching_exercises'] = $this->rankExercises($program['exercises'], $intent);
        }
        unset($program);
        usort($programs, static fn(array $a, array $b): int => ($b['score'] <=> $a['score']) ?: strcasecmp($a['title'], $b['title']));

        $lines = [];
        $lines[] = 'Detected user intent:';
        $lines[] = '- Goal focus: ' . implode(', ', $intent['goals'] ?: ['general fitness']);
        $lines[] = '- Target muscles/movement patterns: ' . implode(', ', $intent['muscles'] ?: ['full body']);
        $lines[] = '- Schedule hints: ' . ($intent['schedule'] !== '' ? $intent['schedule'] : 'not specified');
        $lines[] = '- Level hints: ' . ($intent['level'] !== '' ? $intent['level'] : 'not specified');
        $lines[] = '';
        $lines[] = 'Ranked candidates. Prefer candidate #1, but compare #2 and #3 before answering:';

        foreach (array_slice($programs, 0, 4) as $index => $program) {
            $lines[] = sprintf('#%d Score %d: %s | Goal: %s | Duration: %s weeks', $index + 1, (int) $program['score'], $program['title'], $program['goal'], (string) $program['duration']);
            if ($program['description'] !== '') {
                $lines[] = 'Why it may fit: ' . $this->limitText($program['description'], 220);
            }
            $exerciseLines = [];
            foreach (array_slice($program['matching_exercises'], 0, 6) as $exercise) {
                $exerciseLines[] = sprintf('- %s | Muscle: %s | %s sets x %s reps | Rest %s sec%s', $exercise['name'], $exercise['muscle'], $exercise['sets'], $exercise['reps'], $exercise['rest'], $exercise['description'] !== '' ? ' | ' . $this->limitText($exercise['description'], 120) : '');
            }
            if ($exerciseLines === []) {
                $exerciseLines[] = '- No exercises listed for this program.';
            }
            $lines[] = 'Relevant exercises:';
            array_push($lines, ...$exerciseLines);
            $lines[] = '';
        }

        return implode("\n", $lines);
    }

    /** @return array<int,array<string,mixed>> */
    private function parseProgramContext(string $context): array
    {
        $programs = [];
        $current = null;
        foreach (preg_split('/\R/u', $context) ?: [] as $rawLine) {
            $line = trim($rawLine);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^PROGRAM:\s*(.*?)\s*\|\s*Goal:\s*(.*?)\s*\|\s*Duration:\s*(\d+)/iu', $line, $m) === 1) {
                if ($current !== null) {
                    $programs[] = $current;
                }
                $current = ['title' => trim($m[1]), 'goal' => trim($m[2]), 'duration' => trim($m[3]), 'description' => '', 'exercises' => []];
                continue;
            }
            if ($current === null) {
                continue;
            }
            if (str_starts_with($line, 'Description:')) {
                $current['description'] = trim(substr($line, strlen('Description:')));
                continue;
            }
            if (str_starts_with($line, '-')) {
                $parts = array_map('trim', explode('|', ltrim($line, '- ')));
                $name = $parts[0] ?? 'Exercise';
                $muscle = 'General';
                $sets = '?';
                $reps = '?';
                $rest = '?';
                $desc = '';
                foreach (array_slice($parts, 1) as $part) {
                    if (stripos($part, 'Muscle:') === 0) {
                        $muscle = trim(substr($part, 7));
                    } elseif (preg_match('/(\d+)\s*sets\s*x\s*(\d+)\s*reps/i', $part, $mm) === 1) {
                        $sets = $mm[1];
                        $reps = $mm[2];
                    } elseif (preg_match('/Rest\s*(\d+)\s*sec/i', $part, $mm) === 1) {
                        $rest = $mm[1];
                    } elseif ($part !== '') {
                        $desc .= ($desc !== '' ? ' ' : '') . $part;
                    }
                }
                $current['exercises'][] = ['name' => $name, 'muscle' => $muscle, 'sets' => $sets, 'reps' => $reps, 'rest' => $rest, 'description' => $desc];
            }
        }
        if ($current !== null) {
            $programs[] = $current;
        }
        return $programs;
    }

    private function detectIntent(string $message): array
    {
        $text = mb_strtolower($message);
        $goalMap = [
            'strength' => ['strength', 'stronger', 'force', 'power', 'progressive overload', 'squat', 'bench', 'deadlift'],
            'hypertrophy' => ['muscle', 'mass', 'aesthetic', 'definition', 'bigger', 'volume', 'shape', 'glutes', 'legs', 'arms'],
            'endurance' => ['endurance', 'cardio', 'stamina', 'conditioning', 'run', 'running'],
            'fat_loss' => ['fat loss', 'lose weight', 'weight loss', 'cut', 'slim'],
            'mobility' => ['mobility', 'flexibility', 'stretch', 'pain free'],
        ];
        $muscleMap = [
            'legs' => ['leg', 'legs', 'quad', 'hamstring', 'glute', 'calf'],
            'back' => ['back', 'lats', 'row', 'pull'],
            'chest' => ['chest', 'pec', 'bench', 'push'],
            'shoulders' => ['shoulder', 'delts', 'overhead'],
            'core' => ['core', 'abs', 'abdominal', 'plank'],
            'arms' => ['arms', 'biceps', 'triceps'],
            'full body' => ['full body', 'whole body', 'overall'],
        ];
        $goals = [];
        foreach ($goalMap as $goal => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($text, $needle)) {
                    $goals[] = $goal;
                    break;
                }
            }
        }
        $muscles = [];
        foreach ($muscleMap as $muscle => $needles) {
            foreach ($needles as $needle) {
                if (str_contains($text, $needle)) {
                    $muscles[] = $muscle;
                    break;
                }
            }
        }
        $schedule = '';
        if (preg_match('/(\d+)\s*(?:days?|x)\s*(?:per|a)?\s*week/i', $message, $m) === 1) {
            $schedule = $m[1] . ' days/week';
        }
        $level = '';
        foreach (['beginner', 'intermediate', 'advanced'] as $candidate) {
            if (str_contains($text, $candidate)) {
                $level = $candidate;
                break;
            }
        }
        return ['goals' => array_values(array_unique($goals)), 'muscles' => array_values(array_unique($muscles)), 'schedule' => $schedule, 'level' => $level, 'raw' => $text];
    }

    private function scoreProgram(array $program, array $intent): int
    {
        $haystack = mb_strtolower($program['title'] . ' ' . $program['goal'] . ' ' . $program['description']);
        foreach ($program['exercises'] as $exercise) {
            $haystack .= ' ' . mb_strtolower($exercise['name'] . ' ' . $exercise['muscle'] . ' ' . $exercise['description']);
        }
        $score = 0;
        foreach ($intent['goals'] as $goal) {
            $synonyms = match ($goal) {
                'strength' => ['strength', 'force', 'power', 'overload', 'squat', 'bench', 'deadlift'],
                'hypertrophy' => ['hypertrophy', 'muscle', 'volume', 'aesthetic', 'definition', 'mass'],
                'endurance' => ['endurance', 'cardio', 'conditioning', 'stamina'],
                'fat_loss' => ['fat', 'weight', 'loss', 'conditioning', 'cardio'],
                'mobility' => ['mobility', 'flexibility', 'stretch'],
                default => [$goal],
            };
            foreach ($synonyms as $word) {
                if (str_contains($haystack, $word)) {
                    $score += 7;
                    break;
                }
            }
        }
        foreach ($intent['muscles'] as $muscle) {
            if (str_contains($haystack, $muscle)) {
                $score += 4;
            }
        }
        $score += min(6, count($program['exercises']));
        return $score;
    }

    /** @return array<int,array<string,string>> */
    private function rankExercises(array $exercises, array $intent): array
    {
        foreach ($exercises as &$exercise) {
            $haystack = mb_strtolower($exercise['name'] . ' ' . $exercise['muscle'] . ' ' . $exercise['description']);
            $score = 0;
            foreach ($intent['goals'] as $goal) {
                if (str_contains($haystack, $goal)) {
                    $score += 3;
                }
            }
            foreach ($intent['muscles'] as $muscle) {
                if (str_contains($haystack, $muscle)) {
                    $score += 5;
                }
            }
            $exercise['_score'] = $score;
        }
        unset($exercise);
        usort($exercises, static fn(array $a, array $b): int => ($b['_score'] <=> $a['_score']) ?: strcasecmp($a['name'], $b['name']));
        return array_values($exercises);
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
            $clean[] = ['role' => $role === 'model' ? 'model' : 'user', 'text' => $this->limitText($text, 700)];
        }
        return $clean;
    }

    private function localProgramFallback(string $message, string $rankedContext): string
    {
        $lines = array_values(array_filter(explode("\n", $rankedContext)));
        $primary = '';
        $backup = '';
        $exercises = [];
        foreach ($lines as $line) {
            if (preg_match('/^#1\s+Score\s+\d+:\s+(.+)$/', $line, $m) === 1) {
                $primary = trim($m[1]);
            } elseif (preg_match('/^#2\s+Score\s+\d+:\s+(.+)$/', $line, $m) === 1) {
                $backup = trim($m[1]);
            } elseif (str_starts_with($line, '- ') && count($exercises) < 5 && !str_contains($line, 'No exercises listed')) {
                $exercises[] = $line;
            }
        }
        if ($primary === '') {
            $primary = 'the closest available program';
        }
        $exerciseText = $exercises !== [] ? "\nUseful exercises to review:\n" . implode("\n", $exercises) : '';
        $backupText = $backup !== '' ? " Backup option: {$backup}." : '';
        return "Best local match: {$primary}.{$backupText} I ranked the catalog by your stated goal and matching exercise coverage instead of using only one exercise.{$exerciseText}\n\nFor full Gemini wording, set GEMINI_API_KEY in coaching/.env.";
    }

    private function localSummary(string $title, string $pageText): string
    {
        $sentences = preg_split('/(?<=[.!?])\s+/u', trim($pageText)) ?: [];
        $sentences = array_values(array_filter(array_map('trim', $sentences)));
        $lines = array_slice($sentences, 0, 5);
        while (count($lines) < 5) {
            $lines[] = $title !== '' ? 'Cette page présente ' . $title . '.' : 'Cette page présente le contenu Asteria disponible.';
        }
        return "Résumé en 5 lignes:\n- " . implode("\n- ", array_map(fn(string $line): string => $this->limitText($line, 160), $lines)) . "\nPoints clés:\n- Consultez les programmes et exercices visibles.\n- Utilisez les filtres pour trouver un objectif adapté.\n- Configurez GEMINI_API_KEY pour un résumé IA plus précis.";
    }

    /** @param array<int,array<string,mixed>> $ideas @return array<int,array<string,mixed>> */
    private function dedupeExercises(array $ideas): array
    {
        $seen = [];
        $deduped = [];
        foreach ($ideas as $idea) {
            $key = mb_strtolower((string) ($idea['name'] ?? ''));
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $deduped[] = $idea;
        }
        return array_slice($deduped, 0, 5);
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
