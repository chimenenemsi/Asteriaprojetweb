<?php
declare(strict_types=1);

require_once ROOT_PATH . '/config/services.php';
require_once ROOT_PATH . '/controllers/GeminiCoachService.php';

class ExerciseCatalogService
{
    public function prepareFormData(array $source): array
    {
        return [
            'type' => $this->cleanValue($source['search_type'] ?? ''),
            'name' => $this->cleanValue($source['search_name'] ?? ''),
        ];
    }

    public function hasFilters(array $filters): bool
    {
        return $filters['type'] !== '' || $filters['name'] !== '';
    }

    public function searchExercises(array $filters): array
    {
        $services = coaching_services();
        $query = array_filter([
            'type' => $filters['type'],
            'name' => $filters['name'],
        ], static fn(string $value): bool => $value !== '');

        if ($query === []) {
            return [];
        }

        $catalog = [];
        $apiKey = trim((string) ($services['exercise_api_key'] ?? ''));
        if ($apiKey !== '') {
            try {
                $url = rtrim((string) ($services['exercise_api_url'] ?? 'https://api.api-ninjas.com/v1/exercises'), '?') . '?' . http_build_query($query);
                $results = $this->requestJson($url, [
                    'X-Api-Key: ' . $apiKey,
                ]);

                if (is_array($results)) {
                    foreach ($results as $exercise) {
                        $normalized = is_array($exercise) ? $this->normalizeExercise($exercise) : null;
                        if ($normalized === null) {
                            continue;
                        }

                        $this->addExerciseToCatalog($catalog, $normalized, 'API Ninjas');
                    }
                }
            } catch (Throwable) {
                $catalog = [];
            }
        }

        if ($catalog === []) {
            try {
                foreach ((new GeminiCoachService())->exerciseIdeas($filters) as $exercise) {
                    $normalized = is_array($exercise) ? $this->normalizeExercise($exercise) : null;
                    if ($normalized === null) {
                        continue;
                    }

                    $this->addExerciseToCatalog($catalog, $normalized, 'Gemini');
                }
            } catch (Throwable) {
                $catalog = [];
            }
        }

        if ($catalog === []) {
            foreach ($this->fallbackCatalog($filters) as $exercise) {
                $this->addExerciseToCatalog($catalog, $exercise, 'Fallback');
            }
        }

        if ($catalog === [] && $this->hasFilters($filters)) {
            foreach ($this->genericFallbackCatalog($filters) as $exercise) {
                $this->addExerciseToCatalog($catalog, $exercise, 'Local Fallback');
            }
        }

        return array_values($catalog);
    }

    public function findByToken(string $token): ?array
    {
        $catalog = $_SESSION['exercise_search_catalog'] ?? [];

        return is_array($catalog) && isset($catalog[$token]) && is_array($catalog[$token])
            ? $catalog[$token]
            : null;
    }

    /**
     * @return string[]
     */
    public function buildPdfLines(array $exercise): array
    {
        return [
            'Exercise Guide',
            '',
            'Name: ' . (string) ($exercise['name'] ?? 'Exercise'),
            'Type: ' . ucfirst(str_replace('_', ' ', (string) ($exercise['type'] ?? '-'))),
            'Difficulty: ' . ucfirst((string) ($exercise['difficulty'] ?? '-')),
            'Muscle Group: ' . ucfirst(str_replace('_', ' ', (string) ($exercise['muscle'] ?? '-'))),
            'Source: ' . (string) ($exercise['source'] ?? 'Catalog'),
            'Equipment: ' . (isset($exercise['equipments']) && is_array($exercise['equipments']) && $exercise['equipments'] !== []
                ? implode(', ', $exercise['equipments'])
                : 'None specified'),
            '',
            'Instructions:',
            (string) ($exercise['instructions'] ?? 'No additional details available.'),
            '',
            'Safety Notes:',
            (string) ($exercise['safety_info'] ?? 'No safety notes provided.'),
        ];
    }

    private function addExerciseToCatalog(array &$catalog, array $exercise, string $source): void
    {
        $token = sha1((string) json_encode($exercise + ['source' => $source]));
        $record = $exercise + ['source' => $source, 'token' => $token];
        $catalog[$token] = $record;
        $_SESSION['exercise_search_catalog'][$token] = $record;
    }

    private function normalizeExercise(array $exercise): ?array
    {
        $normalized = [
            'name' => (string) ($exercise['name'] ?? ''),
            'type' => (string) ($exercise['type'] ?? ''),
            'muscle' => (string) ($exercise['muscle'] ?? ''),
            'difficulty' => (string) ($exercise['difficulty'] ?? ''),
            'instructions' => (string) ($exercise['instructions'] ?? ''),
            'safety_info' => (string) ($exercise['safety_info'] ?? ''),
            'equipments' => $this->normalizeEquipment($exercise),
        ];

        return $normalized['name'] === '' ? null : $normalized;
    }

    private function normalizeEquipment(array $exercise): array
    {
        $raw = $exercise['equipments'] ?? $exercise['equipment'] ?? [];
        if (is_string($raw)) {
            $raw = preg_split('/[,;]+/', $raw) ?: [];
        }
        if (!is_array($raw)) {
            return [];
        }

        return array_values(array_filter(
            array_map(static fn(mixed $item): string => trim((string) $item), $raw),
            static fn(string $item): bool => $item !== '' && strtolower($item) !== 'none'
        ));
    }

    private function fallbackCatalog(array $filters): array
    {
        $catalog = [
            ['name' => 'Push-Up', 'type' => 'strength', 'muscle' => 'chest', 'difficulty' => 'beginner', 'instructions' => 'Start in a strong plank, lower under control, and press back up while keeping the core tight.', 'safety_info' => 'Keep the ribs down and avoid sagging through the lower back.', 'equipments' => []],
            ['name' => 'Goblet Squat', 'type' => 'strength', 'muscle' => 'quadriceps', 'difficulty' => 'beginner', 'instructions' => 'Hold a dumbbell or kettlebell at the chest, sit down between the hips, and stand tall through the whole foot.', 'safety_info' => 'Keep the chest tall and knees tracking with the toes.', 'equipments' => ['dumbbell']],
            ['name' => 'Romanian Deadlift', 'type' => 'strength', 'muscle' => 'hamstrings', 'difficulty' => 'intermediate', 'instructions' => 'Hinge from the hips with a soft knee bend, lower the weight close to the legs, and drive back up through the hips.', 'safety_info' => 'Maintain a neutral spine and stop before the lower back rounds.', 'equipments' => ['barbell']],
            ['name' => 'Lat Pulldown', 'type' => 'strength', 'muscle' => 'lats', 'difficulty' => 'beginner', 'instructions' => 'Pull the bar toward the upper chest while driving the elbows down and keeping the torso tall.', 'safety_info' => 'Do not yank the bar behind the neck.', 'equipments' => ['cable machine']],
            ['name' => 'Walking Lunge', 'type' => 'strength', 'muscle' => 'glutes', 'difficulty' => 'intermediate', 'instructions' => 'Take a controlled step forward, lower both knees, then drive through the front foot into the next step.', 'safety_info' => 'Stay balanced and avoid collapsing inward at the front knee.', 'equipments' => ['dumbbell']],
            ['name' => 'Biceps Curl', 'type' => 'strength', 'muscle' => 'biceps', 'difficulty' => 'beginner', 'instructions' => 'Curl the weight without swinging, squeeze at the top, and lower slowly.', 'safety_info' => 'Keep elbows close to the torso to avoid using momentum.', 'equipments' => ['dumbbell']],
            ['name' => 'Plank', 'type' => 'core', 'muscle' => 'abdominals', 'difficulty' => 'beginner', 'instructions' => 'Brace the core, squeeze glutes, and hold a straight line from shoulders to heels.', 'safety_info' => 'Stop when the lower back starts dipping.', 'equipments' => []],
            ['name' => 'Rowing Sprint', 'type' => 'cardio', 'muscle' => 'full body', 'difficulty' => 'intermediate', 'instructions' => 'Drive hard through the legs, finish with the arms, and recover smoothly between intervals.', 'safety_info' => 'Avoid rounding the back at the catch.', 'equipments' => ['rower']],
        ];

        return array_values(array_filter($catalog, function (array $exercise) use ($filters): bool {
            foreach (['type', 'name'] as $field) {
                $needle = strtolower(trim((string) ($filters[$field] ?? '')));
                if ($needle === '') {
                    continue;
                }

                $haystack = strtolower((string) ($exercise[$field] ?? ''));
                if (!str_contains($haystack, $needle)) {
                    return false;
                }
            }

            return true;
        }));
    }

    private function genericFallbackCatalog(array $filters): array
    {
        $type = strtolower(trim((string) ($filters['type'] ?? 'strength')));
        $name = trim((string) ($filters['name'] ?? ''));
        $displayName = $name !== '' ? ucwords($name) : ucwords(str_replace('_', ' ', $type));

        $templates = [
            'cardio' => [
                ['name' => $displayName . ' Interval Walk', 'muscle' => 'full body', 'difficulty' => 'beginner', 'instructions' => 'Alternate moderate pace and faster pace intervals while keeping breathing controlled and posture tall.', 'safety_info' => 'Start easy and stop if you feel dizziness or sharp pain.', 'equipments' => []],
                ['name' => $displayName . ' Bike Intervals', 'muscle' => 'legs', 'difficulty' => 'intermediate', 'instructions' => 'Ride easy for recovery, then push short hard intervals with smooth cadence and relaxed shoulders.', 'safety_info' => 'Adjust the bike fit and avoid locking the knees.', 'equipments' => ['stationary bike']],
            ],
            'stretching' => [
                ['name' => $displayName . ' Mobility Flow', 'muscle' => 'full body', 'difficulty' => 'beginner', 'instructions' => 'Move slowly through controlled ranges, breathing deeply and pausing where tension feels manageable.', 'safety_info' => 'Never force a painful range of motion.', 'equipments' => ['mat']],
                ['name' => $displayName . ' Hip Flexor Stretch', 'muscle' => 'hips', 'difficulty' => 'beginner', 'instructions' => 'Use a half-kneeling position, tuck the pelvis slightly, and hold a gentle stretch while breathing.', 'safety_info' => 'Pad the back knee and keep pressure gentle.', 'equipments' => ['mat']],
            ],
            'plyometrics' => [
                ['name' => $displayName . ' Low Box Jump', 'muscle' => 'legs', 'difficulty' => 'intermediate', 'instructions' => 'Jump onto a low stable box, land softly, stand tall, and step down under control.', 'safety_info' => 'Use a low box and avoid this if jumping causes joint pain.', 'equipments' => ['box']],
                ['name' => $displayName . ' Skater Hop', 'muscle' => 'glutes', 'difficulty' => 'intermediate', 'instructions' => 'Hop side to side with soft landings, keeping the knee aligned with the toes.', 'safety_info' => 'Reduce range if balance or knee control breaks down.', 'equipments' => []],
            ],
            'powerlifting' => [
                ['name' => $displayName . ' Squat Technique Work', 'muscle' => 'quadriceps', 'difficulty' => 'intermediate', 'instructions' => 'Use submaximal sets, brace hard, keep depth consistent, and pause briefly to build position strength.', 'safety_info' => 'Use spotters or safeties and keep loads manageable.', 'equipments' => ['barbell', 'rack']],
                ['name' => $displayName . ' Bench Press Pauses', 'muscle' => 'chest', 'difficulty' => 'intermediate', 'instructions' => 'Lower under control, pause on the chest without bouncing, then press while maintaining tight upper back position.', 'safety_info' => 'Use a spotter or safeties for heavier sets.', 'equipments' => ['barbell', 'bench']],
            ],
            'strongman' => [
                ['name' => $displayName . ' Farmer Carry', 'muscle' => 'full body', 'difficulty' => 'intermediate', 'instructions' => 'Hold heavy implements, stand tall, brace the core, and walk with controlled steps.', 'safety_info' => 'Keep the spine neutral and stop before grip fails suddenly.', 'equipments' => ['dumbbells']],
                ['name' => $displayName . ' Sled Push', 'muscle' => 'legs', 'difficulty' => 'intermediate', 'instructions' => 'Drive through the floor with steady steps while keeping the torso strong and arms locked.', 'safety_info' => 'Start light and keep the surface clear.', 'equipments' => ['sled']],
            ],
            'olympic_weightlifting' => [
                ['name' => $displayName . ' Hang Power Clean Drill', 'muscle' => 'full body', 'difficulty' => 'advanced', 'instructions' => 'Practice extension, fast elbows, and controlled receiving position with light technical loads.', 'safety_info' => 'Use coaching supervision and avoid maximal loads when learning.', 'equipments' => ['barbell']],
                ['name' => $displayName . ' Overhead Squat Position', 'muscle' => 'shoulders', 'difficulty' => 'advanced', 'instructions' => 'Hold a light bar overhead, brace, and squat only as deep as position stays stable.', 'safety_info' => 'Do not force overhead range if shoulders feel pinched.', 'equipments' => ['barbell']],
            ],
            'core' => [
                ['name' => $displayName . ' Dead Bug', 'muscle' => 'abdominals', 'difficulty' => 'beginner', 'instructions' => 'Press the lower back gently toward the floor while extending opposite arm and leg under control.', 'safety_info' => 'Keep breathing and stop if the lower back arches.', 'equipments' => ['mat']],
                ['name' => $displayName . ' Side Plank', 'muscle' => 'obliques', 'difficulty' => 'beginner', 'instructions' => 'Stack the body in a straight line, brace the core, and hold without letting the hips drop.', 'safety_info' => 'Use knees down if the full version strains the shoulder.', 'equipments' => ['mat']],
            ],
        ];

        $selected = $templates[$type] ?? [
            ['name' => $displayName . ' Strength Pattern', 'muscle' => 'full body', 'difficulty' => 'beginner', 'instructions' => 'Use controlled reps, stable posture, and a load that lets you finish every repetition with good technique.', 'safety_info' => 'Keep intensity moderate and avoid movements that cause sharp pain.', 'equipments' => []],
            ['name' => $displayName . ' Dumbbell Variation', 'muscle' => 'full body', 'difficulty' => 'beginner', 'instructions' => 'Perform slow, repeatable reps with a neutral spine and steady breathing.', 'safety_info' => 'Choose a weight you can control through the full range.', 'equipments' => ['dumbbell']],
        ];

        return array_map(static function (array $exercise) use ($type): array {
            $exercise['type'] = $type !== '' ? $type : 'strength';
            return $exercise;
        }, $selected);
    }

    private function requestJson(string $url, array $headers = []): mixed
    {
        if (function_exists('curl_init')) {
            $handle = curl_init($url);
            curl_setopt_array($handle, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => 0,
            ]);

            $response = curl_exec($handle);
            if ($response === false) {
                $message = curl_error($handle);
                curl_close($handle);
                throw new RuntimeException($message !== '' ? $message : 'The remote service did not respond.');
            }

            $statusCode = (int) curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
            curl_close($handle);
        } else {
            $context = stream_context_create([
                'http' => [
                    'method' => 'GET',
                    'header' => implode("\r\n", $headers),
                    'timeout' => 20,
                ],
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ]);
            $response = @file_get_contents($url, false, $context);
            $statusCode = 200;
            if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches) === 1) {
                $statusCode = (int) $matches[1];
            }
            if ($response === false) {
                throw new RuntimeException('The remote service did not respond.');
            }
        }

        if ($statusCode >= 400) {
            throw new RuntimeException('The remote service returned HTTP ' . $statusCode . '.');
        }

        return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    }

    private function cleanValue(mixed $value): string
    {
        return trim((string) $value);
    }
}
