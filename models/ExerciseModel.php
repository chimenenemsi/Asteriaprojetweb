<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/Exercise.php';

final class ExerciseModel extends BaseModel
{
    public function __construct(PDO $connection, private ProgramModel $programModel)
    {
        parent::__construct($connection);
    }

    /**
     * @param int[] $programIds
     * @return array<int, Exercise[]>
     */
    public function groupByProgramIds(array $programIds): array
    {
        $programIds = array_values(array_unique(array_map('intval', $programIds)));

        if ($programIds === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($programIds), '?'));
        $statement = $this->connection->prepare(
            "SELECT id, program_id, name, description, muscle_group, sets, reps, rest_seconds
            FROM exercises
            WHERE program_id IN ($placeholders)
            ORDER BY program_id ASC, id ASC"
        );
        $statement->execute($programIds);

        $grouped = [];

        foreach ($statement->fetchAll() as $row) {
            $exercise = $this->mapExerciseRow($row);
            $grouped[(int) ($exercise->getProgramId() ?? 0)][] = $exercise;
        }

        return $grouped;
    }

    /**
     * @return Exercise[]
     */
    public function findAllByProgram(int $programId): array
    {
        $statement = $this->connection->prepare(
            'SELECT id, program_id, name, description, muscle_group, sets, reps, rest_seconds
            FROM exercises
            WHERE program_id = :program_id
            ORDER BY id ASC'
        );
        $statement->execute(['program_id' => $programId]);

        return array_map([$this, 'mapExerciseRow'], $statement->fetchAll());
    }

    public function findById(int $id): ?Exercise
    {
        $statement = $this->connection->prepare(
            'SELECT id, program_id, name, description, muscle_group, sets, reps, rest_seconds
            FROM exercises
            WHERE id = :id
            LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapExerciseRow($row);
    }

    public function prepareFormData(Exercise|array|null $source, int $selectedProgramId = 0): array
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
            'id' => $this->clean($source['id'] ?? $source['exercise_id'] ?? ''),
            'program_id' => $this->clean($source['program_id'] ?? ($selectedProgramId > 0 ? (string) $selectedProgramId : '')),
            'name' => $this->clean($source['name'] ?? ''),
            'description' => $this->clean($source['description'] ?? ''),
            'muscle_group' => $this->clean($source['muscle_group'] ?? ''),
            'sets' => $this->clean($source['sets'] ?? ''),
            'reps' => $this->clean($source['reps'] ?? ''),
            'rest_seconds' => $this->clean($source['rest_seconds'] ?? ''),
        ];
    }

    public function validate(array $input): array
    {
        $values = $this->prepareFormData($input, (int) ($input['program_id'] ?? 0));
        $errors = [];

        if (!$this->isPositiveInteger($values['program_id'])) {
            $errors['program_id'] = 'Select a valid program before adding exercises.';
        } elseif ($this->programModel->findById((int) $values['program_id']) === null) {
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

    public function entityFromValues(array $values, ?int $id = null): Exercise
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

    public function create(Exercise $exercise): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO exercises (program_id, name, description, muscle_group, sets, reps, rest_seconds)
            VALUES (:program_id, :name, :description, :muscle_group, :sets, :reps, :rest_seconds)'
        );
        $statement->execute($this->payload($exercise));

        $id = (int) $this->connection->lastInsertId();
        $exercise->setId($id);

        return $id;
    }

    public function update(Exercise $exercise): void
    {
        $payload = $this->payload($exercise);
        $payload['id'] = $exercise->getId();

        $statement = $this->connection->prepare(
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
        $statement->execute($payload);
    }

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM exercises WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function payload(Exercise $exercise): array
    {
        return [
            'program_id' => $exercise->getProgramId(),
            'name' => $exercise->getName(),
            'description' => $exercise->getDescription(),
            'muscle_group' => $exercise->getMuscleGroup(),
            'sets' => $exercise->getSets(),
            'reps' => $exercise->getReps(),
            'rest_seconds' => $exercise->getRestSeconds(),
        ];
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
}
