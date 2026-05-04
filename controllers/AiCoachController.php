<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/GeminiCoachService.php';

class AiCoachController extends BaseController
{
    public function chat(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
            return;
        }

        $body = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($body)) {
            $body = [];
        }

        $message = trim((string) ($body['message'] ?? ''));
        $history = is_array($body['history'] ?? null) ? $body['history'] : [];
        if ($message === '') {
            http_response_code(422);
            echo json_encode(['error' => 'Please describe your goal first.']);
            return;
        }

        try {
            $reply = (new GeminiCoachService())->recommendProgram($message, $history, $this->buildProgramContext());
            echo json_encode(['reply' => $reply], JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            http_response_code(503);
            echo json_encode(['error' => 'Coach AI is temporarily unavailable.']);
        }
    }

    public function summarize(): void
    {
        header('Content-Type: application/json; charset=UTF-8');
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed.']);
            return;
        }

        $body = json_decode((string) file_get_contents('php://input'), true);
        if (!is_array($body)) {
            $body = [];
        }
        $title = trim((string) ($body['title'] ?? 'Asteria page'));
        $text = trim((string) ($body['text'] ?? ''));
        if ($text === '') {
            http_response_code(422);
            echo json_encode(['error' => 'No page text was found to summarize.']);
            return;
        }

        try {
            $summary = (new GeminiCoachService())->summarizePage($title, $text);
            echo json_encode(['summary' => $summary], JSON_THROW_ON_ERROR);
        } catch (Throwable) {
            http_response_code(503);
            echo json_encode(['error' => 'Summary AI is temporarily unavailable.']);
        }
    }

    private function buildProgramContext(): string
    {
        try {
            $programStatement = $this->connection()->query(
                'SELECT id, title, goal_type, duration_weeks, description FROM programs ORDER BY id ASC LIMIT 80'
            );
            $programs = $programStatement->fetchAll(PDO::FETCH_ASSOC);
            if ($programs === []) {
                return 'No programs available.';
            }

            $exerciseStatement = $this->connection()->query(
                'SELECT program_id, name, description, muscle_group, sets, reps, rest_seconds FROM exercises ORDER BY program_id ASC, id ASC LIMIT 300'
            );
            $exercises = [];
            foreach ($exerciseStatement->fetchAll(PDO::FETCH_ASSOC) as $exercise) {
                $exercises[(int) $exercise['program_id']][] = $exercise;
            }

            $lines = [];
            foreach ($programs as $program) {
                $programId = (int) ($program['id'] ?? 0);
                $lines[] = sprintf(
                    'PROGRAM: %s | Goal: %s | Duration: %d weeks',
                    (string) ($program['title'] ?? 'Program'),
                    (string) ($program['goal_type'] ?? 'General'),
                    (int) ($program['duration_weeks'] ?? 0)
                );
                $description = trim((string) ($program['description'] ?? ''));
                if ($description !== '') {
                    $lines[] = 'Description: ' . mb_substr($description, 0, 160);
                }
                foreach (array_slice($exercises[$programId] ?? [], 0, 10) as $exercise) {
                    $lines[] = sprintf(
                        '- %s | Muscle: %s | %d sets x %d reps | Rest %d sec%s',
                        (string) ($exercise['name'] ?? 'Exercise'),
                        (string) ($exercise['muscle_group'] ?? 'General'),
                        (int) ($exercise['sets'] ?? 0),
                        (int) ($exercise['reps'] ?? 0),
                        (int) ($exercise['rest_seconds'] ?? 0),
                        trim((string) ($exercise['description'] ?? '')) !== '' ? ' | ' . mb_substr((string) $exercise['description'], 0, 100) : ''
                    );
                }
                $lines[] = '';
            }

            return implode("\n", $lines);
        } catch (Throwable) {
            return 'Program catalog temporarily unavailable.';
        }
    }
}
