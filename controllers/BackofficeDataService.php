<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/Program.php';
require_once ROOT_PATH . '/models/Exercise.php';

class BackofficeDataService
{
    public function programFilters(array $source): array
    {
        return [
            'search' => $this->cleanValue($source['program_search'] ?? ''),
            'goal_type' => $this->cleanValue($source['program_goal_type'] ?? ''),
            'sort' => $this->cleanValue($source['program_sort'] ?? 'created_desc'),
        ];
    }

    public function exerciseFilters(array $source): array
    {
        return [
            'search' => $this->cleanValue($source['exercise_search'] ?? ''),
            'muscle_group' => $this->cleanValue($source['exercise_muscle_group'] ?? ''),
            'sort' => $this->cleanValue($source['exercise_sort'] ?? 'name_asc'),
        ];
    }

    /**
     * @return Program[]
     */
    public function fetchAllPrograms(array $filters = []): array
    {
        $query = 'SELECT p.id, p.title, p.goal_type, p.duration_weeks, p.description, COUNT(e.id) AS exercise_count
            FROM programs p
            LEFT JOIN exercises e ON e.program_id = p.id
            WHERE 1=1';
        $params = [];

        if (($filters['search'] ?? '') !== '') {
            $searchTerm = '%' . $filters['search'] . '%';
            $query .= ' AND (p.title LIKE :search_title OR COALESCE(p.description, "") LIKE :search_description)';
            $params['search_title'] = $searchTerm;
            $params['search_description'] = $searchTerm;
        }

        if (($filters['goal_type'] ?? '') !== '') {
            $query .= ' AND p.goal_type = :goal_type';
            $params['goal_type'] = $filters['goal_type'];
        }

        $sort = $filters['sort'] ?? 'created_desc';
        $orderBy = match ($sort) {
            'title_asc' => 'p.title ASC, p.id DESC',
            'title_desc' => 'p.title DESC, p.id DESC',
            'weeks_asc' => 'p.duration_weeks ASC, p.id DESC',
            'weeks_desc' => 'p.duration_weeks DESC, p.id DESC',
            'goal_type_asc' => 'p.goal_type ASC, p.title ASC',
            default => 'p.id DESC',
        };

        $query .= ' GROUP BY p.id, p.title, p.goal_type, p.duration_weeks, p.description
            ORDER BY ' . $orderBy;
        $statement = $this->connection()->prepare($query);
        $statement->execute($params);

        return array_map([$this, 'mapProgramRow'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findProgramById(int $id): ?Program
    {
        $statement = $this->connection()->prepare(
            'SELECT p.id, p.title, p.goal_type, p.duration_weeks, p.description, COUNT(e.id) AS exercise_count
            FROM programs p
            LEFT JOIN exercises e ON e.program_id = p.id
            WHERE p.id = :id
            GROUP BY p.id, p.title, p.goal_type, p.duration_weeks, p.description
            LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $this->mapProgramRow($row);
    }

    public function findProgramWithExercises(int $id): ?Program
    {
        $program = $this->findProgramById($id);

        if ($program === null) {
            return null;
        }

        $program->setExercises($this->fetchExercisesByProgram($id));

        return $program;
    }

    public function programStatistics(): array
    {
        $programStatement = $this->connection()->query(
            'SELECT COUNT(*) AS total_programs, COALESCE(SUM(duration_weeks), 0) AS total_weeks
            FROM programs'
        );
        $programStats = $programStatement->fetch(PDO::FETCH_ASSOC) ?: [
            'total_programs' => 0,
            'total_weeks' => 0,
        ];

        $exerciseStatement = $this->connection()->query('SELECT COUNT(*) AS total_exercises FROM exercises');
        $exerciseStats = $exerciseStatement->fetch(PDO::FETCH_ASSOC) ?: ['total_exercises' => 0];

        $programCount = (int) $programStats['total_programs'];
        $exerciseCount = (int) $exerciseStats['total_exercises'];

        $goalTypeStatement = $this->connection()->query(
            'SELECT COALESCE(NULLIF(goal_type, ""), "General") AS label, COUNT(*) AS total
             FROM programs
             GROUP BY COALESCE(NULLIF(goal_type, ""), "General")
             ORDER BY total DESC, label ASC'
        );
        $durationStatement = $this->connection()->query(
            'SELECT
                SUM(CASE WHEN duration_weeks <= 4 THEN 1 ELSE 0 END) AS short_cycle,
                SUM(CASE WHEN duration_weeks BETWEEN 5 AND 8 THEN 1 ELSE 0 END) AS medium_cycle,
                SUM(CASE WHEN duration_weeks >= 9 THEN 1 ELSE 0 END) AS long_cycle
             FROM programs'
        );
        $durationStats = $durationStatement->fetch(PDO::FETCH_ASSOC) ?: [];

        return [
            'total_programs' => $programCount,
            'total_exercises' => $exerciseCount,
            'total_weeks' => (int) $programStats['total_weeks'],
            'average_exercises' => $programCount > 0 ? (int) ceil($exerciseCount / $programCount) : 0,
            'goal_type_distribution' => $goalTypeStatement->fetchAll(PDO::FETCH_ASSOC) ?: [],
            'duration_distribution' => [
                ['label' => '1 to 4 weeks', 'total' => (int) ($durationStats['short_cycle'] ?? 0)],
                ['label' => '5 to 8 weeks', 'total' => (int) ($durationStats['medium_cycle'] ?? 0)],
                ['label' => '9+ weeks', 'total' => (int) ($durationStats['long_cycle'] ?? 0)],
            ],
        ];
    }

    public function exerciseStatistics(?Program $program, array $exercises): array
    {
        $totalExercises = count($exercises);
        $setsTotal = 0;
        $repsTotal = 0;
        $restTotal = 0;
        $muscleDistribution = [];

        foreach ($exercises as $exercise) {
            if (!$exercise instanceof Exercise) {
                continue;
            }

            $setsTotal += $exercise->getSets();
            $repsTotal += $exercise->getReps();
            $restTotal += $exercise->getRestSeconds();
            $label = trim((string) ($exercise->getMuscleGroup() ?? ''));
            $label = $label !== '' ? $label : 'General';
            $muscleDistribution[$label] = ($muscleDistribution[$label] ?? 0) + 1;
        }

        arsort($muscleDistribution);
        $topMuscle = array_key_first($muscleDistribution);

        return [
            'total_exercises' => $totalExercises,
            'program_weeks' => (int) ($program?->getDurationWeeks() ?? 0),
            'goal_type' => (string) ($program?->getGoalType() ?? ''),
            'average_sets' => $totalExercises > 0 ? round($setsTotal / $totalExercises, 1) : 0,
            'average_reps' => $totalExercises > 0 ? round($repsTotal / $totalExercises, 1) : 0,
            'average_rest' => $totalExercises > 0 ? (int) round($restTotal / $totalExercises) : 0,
            'top_muscle' => $topMuscle ?? 'General',
            'muscle_distribution' => array_map(
                static fn(string $label, int $total): array => ['label' => $label, 'total' => $total],
                array_keys($muscleDistribution),
                array_values($muscleDistribution)
            ),
        ];
    }

    public function prepareProgramFormData(Program|array|null $source): array
    {
        if ($source instanceof Program) {
            return [
                'id' => (string) ($source->getId() ?? ''),
                'title' => $source->getTitle(),
                'goal_type' => (string) ($source->getGoalType() ?? ''),
                'duration_weeks' => $source->getDurationWeeks() > 0 ? (string) $source->getDurationWeeks() : '',
                'description' => (string) ($source->getDescription() ?? ''),
            ];
        }

        return [
            'id' => $this->cleanValue($source['id'] ?? $source['program_id'] ?? ''),
            'title' => $this->cleanValue($source['title'] ?? ''),
            'goal_type' => $this->cleanValue($source['goal_type'] ?? ''),
            'duration_weeks' => $this->cleanValue($source['duration_weeks'] ?? ''),
            'description' => $this->cleanValue($source['description'] ?? ''),
        ];
    }

    public function validateProgram(array $input): array
    {
        $values = $this->prepareProgramFormData($input);
        $errors = [];

        if ($values['title'] === '') {
            $errors['title'] = 'Title is required.';
        } elseif (mb_strlen($values['title']) > 100) {
            $errors['title'] = 'Title must be 100 characters or fewer.';
        } elseif (!$this->matchesLabelPattern($values['title'])) {
            $errors['title'] = 'Title contains invalid characters.';
        }

        if ($values['goal_type'] !== '') {
            if (mb_strlen($values['goal_type']) > 50) {
                $errors['goal_type'] = 'Goal type must be 50 characters or fewer.';
            } elseif (!$this->matchesLabelPattern($values['goal_type'])) {
                $errors['goal_type'] = 'Goal type contains invalid characters.';
            }
        }

        if ($values['duration_weeks'] === '') {
            $errors['duration_weeks'] = 'Duration in weeks is required.';
        } elseif (!$this->isPositiveInteger($values['duration_weeks'])) {
            $errors['duration_weeks'] = 'Duration must be a positive number.';
        }

        if ($values['description'] !== '' && mb_strlen($values['description']) > 255) {
            $errors['description'] = 'Description must be 255 characters or fewer.';
        }

        return [
            'values' => $values,
            'errors' => $errors,
        ];
    }

    public function saveProgram(array $values): int
    {
        $programId = (int) ($values['id'] ?: 0);
        $program = $this->programFromValues($values, $programId > 0 ? $programId : null);

        if ($programId > 0) {
            $this->updateProgram($program);
            return $programId;
        }

        return $this->createProgram($program);
    }

    public function deleteProgram(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM programs WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @return Exercise[]
     */
    public function fetchExercisesByProgram(int $programId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT id, program_id, name, description, muscle_group, sets, reps, rest_seconds
            FROM exercises
            WHERE program_id = :program_id
            ORDER BY id ASC'
        );
        $statement->execute(['program_id' => $programId]);

        return array_map([$this, 'mapExerciseRow'], $statement->fetchAll(PDO::FETCH_ASSOC));
    }

    public function findExerciseById(int $id): ?Exercise
    {
        $statement = $this->connection()->prepare(
            'SELECT id, program_id, name, description, muscle_group, sets, reps, rest_seconds
            FROM exercises
            WHERE id = :id
            LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        return $row === false ? null : $this->mapExerciseRow($row);
    }

    public function prepareExerciseFormData(Exercise|array|null $source, int $selectedProgramId = 0): array
    {
        if ($source instanceof Exercise) {
            return [
                'id' => (string) ($source->getId() ?? ''),
                'program_id' => (string) ($source->getProgramId() ?? ($selectedProgramId > 0 ? $selectedProgramId : '')),
                'name' => $source->getName(),
                'description' => (string) ($source->getDescription() ?? ''),
                'muscle_group' => (string) ($source->getMuscleGroup() ?? ''),
                'sets' => $source->getSets() > 0 ? (string) $source->getSets() : '',
                'reps' => $source->getReps() > 0 ? (string) $source->getReps() : '',
                'rest_seconds' => $source->getRestSeconds() > 0 ? (string) $source->getRestSeconds() : '',
            ];
        }

        return [
            'id' => $this->cleanValue($source['id'] ?? $source['exercise_id'] ?? ''),
            'program_id' => $this->cleanValue($source['program_id'] ?? ($selectedProgramId > 0 ? (string) $selectedProgramId : '')),
            'name' => $this->cleanValue($source['name'] ?? ''),
            'description' => $this->cleanValue($source['description'] ?? ''),
            'muscle_group' => $this->cleanValue($source['muscle_group'] ?? ''),
            'sets' => $this->cleanValue($source['sets'] ?? ''),
            'reps' => $this->cleanValue($source['reps'] ?? ''),
            'rest_seconds' => $this->cleanValue($source['rest_seconds'] ?? ''),
        ];
    }

    public function validateExercise(array $input): array
    {
        $values = $this->prepareExerciseFormData($input, (int) ($input['program_id'] ?? 0));
        $errors = [];

        if (!$this->isPositiveInteger($values['program_id'])) {
            $errors['program_id'] = 'Select a valid program before adding exercises.';
        } elseif ($this->findProgramById((int) $values['program_id']) === null) {
            $errors['program_id'] = 'The selected program does not exist anymore.';
        }

        if ($values['name'] === '') {
            $errors['name'] = 'Exercise name is required.';
        } elseif (mb_strlen($values['name']) > 100) {
            $errors['name'] = 'Exercise name must be 100 characters or fewer.';
        } elseif (!$this->matchesLabelPattern($values['name'])) {
            $errors['name'] = 'Exercise name contains invalid characters.';
        }

        if ($values['description'] !== '' && mb_strlen($values['description']) > 255) {
            $errors['description'] = 'Description must be 255 characters or fewer.';
        }

        if ($values['muscle_group'] !== '') {
            if (mb_strlen($values['muscle_group']) > 50) {
                $errors['muscle_group'] = 'Muscle group must be 50 characters or fewer.';
            } elseif (!$this->matchesLabelPattern($values['muscle_group'])) {
                $errors['muscle_group'] = 'Muscle group contains invalid characters.';
            }
        }

        foreach (['sets' => 'Sets', 'reps' => 'Reps', 'rest_seconds' => 'Rest seconds'] as $field => $label) {
            if ($values[$field] === '') {
                $errors[$field] = $label . ' is required.';
                continue;
            }

            if (!$this->isPositiveInteger($values[$field])) {
                $errors[$field] = $label . ' must be a positive number.';
            }
        }

        return [
            'values' => $values,
            'errors' => $errors,
        ];
    }

    public function saveExercise(array $values): int
    {
        $exerciseId = (int) ($values['id'] ?: 0);
        $exercise = $this->exerciseFromValues($values, $exerciseId > 0 ? $exerciseId : null);

        if ($exerciseId > 0) {
            $this->updateExercise($exercise);
        } else {
            $this->createExercise($exercise);
        }

        return (int) $values['program_id'];
    }

    public function deleteExercise(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM exercises WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @param Exercise[] $exercises
     * @return Exercise[]
     */
    public function filterExercises(array $exercises, array $filters): array
    {
        $filtered = array_values(array_filter($exercises, function (Exercise $exercise) use ($filters): bool {
            $search = $filters['search'] ?? '';
            $muscleGroup = $filters['muscle_group'] ?? '';

            if ($search !== '') {
                $haystack = mb_strtolower($exercise->getName() . ' ' . (string) $exercise->getDescription());
                if (!str_contains($haystack, mb_strtolower($search))) {
                    return false;
                }
            }

            if ($muscleGroup !== '' && mb_strtolower((string) $exercise->getMuscleGroup()) !== mb_strtolower($muscleGroup)) {
                return false;
            }

            return true;
        }));

        $sort = $filters['sort'] ?? 'name_asc';
        usort($filtered, static function (Exercise $left, Exercise $right) use ($sort): int {
            return match ($sort) {
                'name_desc' => strcasecmp($right->getName(), $left->getName()),
                'sets_desc' => $right->getSets() <=> $left->getSets(),
                'sets_asc' => $left->getSets() <=> $right->getSets(),
                'reps_desc' => $right->getReps() <=> $left->getReps(),
                'reps_asc' => $left->getReps() <=> $right->getReps(),
                'rest_desc' => $right->getRestSeconds() <=> $left->getRestSeconds(),
                'rest_asc' => $left->getRestSeconds() <=> $right->getRestSeconds(),
                default => strcasecmp($left->getName(), $right->getName()),
            };
        });

        return $filtered;
    }

    /**
     * @param Program[] $programs
     * @return string[]
     */
    public function buildProgramsExportLines(array $programs, array $filters): array
    {
        $lines = ['Coaching Programs Export', ''];
        $lines[] = 'Records: ' . count($programs);
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all')
            . ', goal_type=' . ($filters['goal_type'] !== '' ? $filters['goal_type'] : 'all')
            . ', sort=' . $filters['sort'];
        $lines[] = '';

        foreach ($programs as $program) {
            $lines[] = sprintf(
                '%s | Goal: %s | Weeks: %d | Exercises: %d',
                $program->getTitle(),
                (string) ($program->getGoalType() ?? 'General'),
                $program->getDurationWeeks(),
                $program->getExerciseCount()
            );
        }

        return $lines;
    }

    /**
     * @param Exercise[] $exercises
     * @return string[]
     */
    public function buildExercisesExportLines(Program $program, array $exercises, array $filters): array
    {
        $lines = ['Coaching Exercises Export', ''];
        $lines[] = 'Program: ' . $program->getTitle();
        $lines[] = 'Records: ' . count($exercises);
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all')
            . ', muscle_group=' . ($filters['muscle_group'] !== '' ? $filters['muscle_group'] : 'all')
            . ', sort=' . $filters['sort'];
        $lines[] = '';

        foreach ($exercises as $exercise) {
            $lines[] = sprintf(
                '%s | Muscle: %s | Sets: %d | Reps: %d | Rest: %d sec',
                $exercise->getName(),
                (string) ($exercise->getMuscleGroup() ?? 'General'),
                $exercise->getSets(),
                $exercise->getReps(),
                $exercise->getRestSeconds()
            );
        }

        return $lines;
    }

    public function formatDatabaseError(Throwable $exception): string
    {
        return 'Database connection or query failed for the Asteria MySQL module: ' . $exception->getMessage();
    }

    private function programFromValues(array $values, ?int $id = null): Program
    {
        return new Program(
            $id,
            $values['title'],
            $this->nullableString($values['goal_type']),
            (int) $values['duration_weeks'],
            $this->nullableString($values['description'])
        );
    }

    private function createProgram(Program $program): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO programs (title, goal_type, duration_weeks, description)
            VALUES (:title, :goal_type, :duration_weeks, :description)'
        );
        $statement->execute([
            'title' => $program->getTitle(),
            'goal_type' => $program->getGoalType(),
            'duration_weeks' => $program->getDurationWeeks(),
            'description' => $program->getDescription(),
        ]);

        $id = (int) $this->connection()->lastInsertId();
        $program->setId($id);

        return $id;
    }

    private function updateProgram(Program $program): void
    {
        $statement = $this->connection()->prepare(
            'UPDATE programs
            SET title = :title,
                goal_type = :goal_type,
                duration_weeks = :duration_weeks,
                description = :description
            WHERE id = :id'
        );
        $statement->execute([
            'id' => $program->getId(),
            'title' => $program->getTitle(),
            'goal_type' => $program->getGoalType(),
            'duration_weeks' => $program->getDurationWeeks(),
            'description' => $program->getDescription(),
        ]);
    }

    private function exerciseFromValues(array $values, ?int $id = null): Exercise
    {
        return new Exercise(
            $id,
            (int) $values['program_id'],
            $values['name'],
            $this->nullableString($values['description']),
            $this->nullableString($values['muscle_group']),
            (int) $values['sets'],
            (int) $values['reps'],
            (int) $values['rest_seconds']
        );
    }

    private function createExercise(Exercise $exercise): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO exercises (program_id, name, description, muscle_group, sets, reps, rest_seconds)
            VALUES (:program_id, :name, :description, :muscle_group, :sets, :reps, :rest_seconds)'
        );
        $statement->execute([
            'program_id' => $exercise->getProgramId(),
            'name' => $exercise->getName(),
            'description' => $exercise->getDescription(),
            'muscle_group' => $exercise->getMuscleGroup(),
            'sets' => $exercise->getSets(),
            'reps' => $exercise->getReps(),
            'rest_seconds' => $exercise->getRestSeconds(),
        ]);

        $id = (int) $this->connection()->lastInsertId();
        $exercise->setId($id);

        return $id;
    }

    private function updateExercise(Exercise $exercise): void
    {
        $statement = $this->connection()->prepare(
            'UPDATE exercises
            SET program_id = :program_id,
                name = :name,
                description = :description,
                muscle_group = :muscle_group,
                sets = :sets,
                reps = :reps,
                rest_seconds = :rest_seconds
            WHERE id = :id'
        );
        $statement->execute([
            'id' => $exercise->getId(),
            'program_id' => $exercise->getProgramId(),
            'name' => $exercise->getName(),
            'description' => $exercise->getDescription(),
            'muscle_group' => $exercise->getMuscleGroup(),
            'sets' => $exercise->getSets(),
            'reps' => $exercise->getReps(),
            'rest_seconds' => $exercise->getRestSeconds(),
        ]);
    }

    private function cleanValue(mixed $value): string
    {
        return trim((string) $value);
    }

    private function nullableString(mixed $value): ?string
    {
        $value = $this->cleanValue($value);

        return $value === '' ? null : $value;
    }

    private function isPositiveInteger(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]) !== false;
    }

    private function matchesLabelPattern(string $value): bool
    {
        return preg_match("/^[\p{L}\p{N}\s&(),.'\/+\-]+$/u", $value) === 1;
    }

    private function mapProgramRow(array $row): Program
    {
        return new Program(
            (int) $row['id'],
            (string) $row['title'],
            $row['goal_type'] !== null ? (string) $row['goal_type'] : null,
            (int) $row['duration_weeks'],
            $row['description'] !== null ? (string) $row['description'] : null,
            (int) ($row['exercise_count'] ?? 0)
        );
    }

    private function mapExerciseRow(array $row): Exercise
    {
        return new Exercise(
            (int) $row['id'],
            (int) $row['program_id'],
            (string) $row['name'],
            $row['description'] !== null ? (string) $row['description'] : null,
            $row['muscle_group'] !== null ? (string) $row['muscle_group'] : null,
            (int) $row['sets'],
            (int) $row['reps'],
            (int) $row['rest_seconds']
        );
    }

    private function connection(): PDO
    {
        return Database::connection();
    }
}
