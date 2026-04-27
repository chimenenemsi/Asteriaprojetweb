<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/Program.php';

final class ProgramModel extends BaseModel
{
    /**
     * @return Program[]
     */
    public function findAll(): array
    {
        $statement = $this->connection->query(
            'SELECT p.id, p.title, p.goal_type, p.duration_weeks, p.description, COUNT(e.id) AS exercise_count
            FROM programs p
            LEFT JOIN exercises e ON e.program_id = p.id
            GROUP BY p.id, p.title, p.goal_type, p.duration_weeks, p.description
            ORDER BY p.id DESC'
        );

        return array_map([$this, 'mapProgramRow'], $statement->fetchAll());
    }

    /**
     * @return Program[]
     */
    public function findAllWithExercises(ExerciseModel $exerciseModel): array
    {
        $programs = $this->findAll();

        if ($programs === []) {
            return [];
        }

        $programIds = array_map(
            static fn(Program $program): int => (int) ($program->getId() ?? 0),
            $programs
        );
        $groupedExercises = $exerciseModel->groupByProgramIds($programIds);

        foreach ($programs as $program) {
            $programId = (int) ($program->getId() ?? 0);
            $program->setExercises($groupedExercises[$programId] ?? []);
        }

        return $programs;
    }

    public function findById(int $id): ?Program
    {
        $statement = $this->connection->prepare(
            'SELECT p.id, p.title, p.goal_type, p.duration_weeks, p.description, COUNT(e.id) AS exercise_count
            FROM programs p
            LEFT JOIN exercises e ON e.program_id = p.id
            WHERE p.id = :id
            GROUP BY p.id, p.title, p.goal_type, p.duration_weeks, p.description
            LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapProgramRow($row);
    }

    public function findWithExercises(int $id, ExerciseModel $exerciseModel): ?Program
    {
        $program = $this->findById($id);

        if ($program === null) {
            return null;
        }

        $program->setExercises($exerciseModel->findAllByProgram($id));

        return $program;
    }

    public function statistics(): array
    {
        $programStatement = $this->connection->query(
            'SELECT COUNT(*) AS total_programs, COALESCE(SUM(duration_weeks), 0) AS total_weeks
            FROM programs'
        );
        $programStats = $programStatement->fetch() ?: [
            'total_programs' => 0,
            'total_weeks' => 0,
        ];

        $exerciseStatement = $this->connection->query('SELECT COUNT(*) AS total_exercises FROM exercises');
        $exerciseStats = $exerciseStatement->fetch() ?: ['total_exercises' => 0];

        $programCount = (int) $programStats['total_programs'];
        $exerciseCount = (int) $exerciseStats['total_exercises'];

        return [
            'total_programs' => $programCount,
            'total_exercises' => $exerciseCount,
            'total_weeks' => (int) $programStats['total_weeks'],
            'average_exercises' => $programCount > 0 ? (int) ceil($exerciseCount / $programCount) : 0,
        ];
    }

    public function prepareFormData(Program|array|null $source): array
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
            'id' => $this->clean($source['id'] ?? $source['program_id'] ?? ''),
            'title' => $this->clean($source['title'] ?? ''),
            'goal_type' => $this->clean($source['goal_type'] ?? ''),
            'duration_weeks' => $this->clean($source['duration_weeks'] ?? ''),
            'description' => $this->clean($source['description'] ?? ''),
        ];
    }

    public function validate(array $input): array
    {
        $values = $this->prepareFormData($input);
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

    public function entityFromValues(array $values, ?int $id = null): Program
    {
        return new Program(
            $id,
            $values['title'],
            $this->nullableString($values['goal_type']),
            (int) $values['duration_weeks'],
            $this->nullableString($values['description'])
        );
    }

    public function create(Program $program): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO programs (title, goal_type, duration_weeks, description)
            VALUES (:title, :goal_type, :duration_weeks, :description)'
        );
        $statement->execute($this->payload($program));

        $id = (int) $this->connection->lastInsertId();
        $program->setId($id);

        return $id;
    }

    public function update(Program $program): void
    {
        $payload = $this->payload($program);
        $payload['id'] = $program->getId();

        $statement = $this->connection->prepare(
            'UPDATE programs
            SET title = :title,
                goal_type = :goal_type,
                duration_weeks = :duration_weeks,
                description = :description
            WHERE id = :id'
        );
        $statement->execute($payload);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM programs WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function payload(Program $program): array
    {
        return [
            'title' => $program->getTitle(),
            'goal_type' => $program->getGoalType(),
            'duration_weeks' => $program->getDurationWeeks(),
            'description' => $program->getDescription(),
        ];
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
}
