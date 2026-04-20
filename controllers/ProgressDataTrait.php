<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ProgressGoal.php';
require_once ROOT_PATH . '/models/ProgressRecord.php';

trait ProgressDataTrait
{
    private const GOAL_TYPES = [
        'WEIGHT_LOSS',
        'FITNESS',
        'CAREER',
        'FINANCE',
        'LEARNING',
        'HEALTH',
        'PRODUCTIVITY',
        'OTHER',
    ];

    private const GOAL_STATUSES = [
        'ACTIVE',
        'COMPLETED',
        'ON_HOLD',
    ];

    private const RECORD_MOODS = [
        'LOW',
        'STEADY',
        'HIGH',
    ];

    private const RECORD_TYPES = [
        'CHECKPOINT',
        'MILESTONE',
        'MEASUREMENT',
        'NOTE',
        'REPORT',
    ];

    /**
     * @return ProgressGoal[]
     */
    protected function fetchAllGoals(): array
    {
        $statement = $this->connection()->query('SELECT * FROM progress_goals ORDER BY target_date ASC, id DESC');

        return array_map([$this, 'mapGoalRow'], $statement->fetchAll());
    }

    protected function findGoalById(int $id): ?ProgressGoal
    {
        $statement = $this->connection()->prepare('SELECT * FROM progress_goals WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapGoalRow($row);
    }

    protected function defaultGoalFormData(): array
    {
        return [
            'title' => '',
            'metric' => '',
            'start_value' => '',
            'target_value' => '',
            'unit' => '',
            'start_date' => date('Y-m-d'),
            'target_date' => date('Y-m-d', strtotime('+30 days')),
            'goal_type' => 'OTHER',
            'status' => 'ACTIVE',
            'description' => '',
        ];
    }

    protected function prepareGoalFormData(ProgressGoal|array|null $source): array
    {
        if ($source instanceof ProgressGoal) {
            return [
                'title' => $source->getTitle(),
                'metric' => $source->getMetric(),
                'start_value' => (string) $source->getStartValue(),
                'target_value' => (string) $source->getTargetValue(),
                'unit' => $source->getUnit(),
                'start_date' => $source->getStartDate(),
                'target_date' => $source->getTargetDate(),
                'goal_type' => $source->getGoalType(),
                'status' => $source->getStatus(),
                'description' => (string) ($source->getDescription() ?? ''),
            ];
        }

        if ($source === null) {
            return $this->defaultGoalFormData();
        }

        return [
            'title' => $this->cleanValue($source['title'] ?? ''),
            'metric' => $this->cleanValue($source['metric'] ?? ''),
            'start_value' => $this->cleanValue($source['start_value'] ?? ''),
            'target_value' => $this->cleanValue($source['target_value'] ?? ''),
            'unit' => $this->cleanValue($source['unit'] ?? ''),
            'start_date' => $this->cleanValue($source['start_date'] ?? ''),
            'target_date' => $this->cleanValue($source['target_date'] ?? ''),
            'goal_type' => $this->cleanValue($source['goal_type'] ?? 'OTHER'),
            'status' => $this->cleanValue($source['status'] ?? 'ACTIVE'),
            'description' => $this->cleanValue($source['description'] ?? ''),
        ];
    }

    protected function validateGoal(array $input): array
    {
        $values = $this->prepareGoalFormData($input);
        $errors = [];

        if ($values['title'] === '') {
            $errors['title'] = 'Title is required.';
        } elseif (mb_strlen($values['title']) > 150) {
            $errors['title'] = 'Title must be 150 characters or fewer.';
        }

        if ($values['metric'] === '') {
            $errors['metric'] = 'Metric is required.';
        } elseif (mb_strlen($values['metric']) > 100) {
            $errors['metric'] = 'Metric must be 100 characters or fewer.';
        }

        if ($values['unit'] === '') {
            $errors['unit'] = 'Unit is required.';
        } elseif (mb_strlen($values['unit']) > 30) {
            $errors['unit'] = 'Unit must be 30 characters or fewer.';
        }

        if (!is_numeric($values['start_value'])) {
            $errors['start_value'] = 'Start value must be numeric.';
        }

        if (!is_numeric($values['target_value'])) {
            $errors['target_value'] = 'Target value must be numeric.';
        }

        if (!isset($errors['start_value']) && !isset($errors['target_value'])) {
            $startValue = (float) $values['start_value'];
            $targetValue = (float) $values['target_value'];

            if ($targetValue === $startValue) {
                $errors['target_value'] = 'Target value must be different from the start value.';
            }

            if (($values['goal_type'] ?? 'OTHER') === 'WEIGHT_LOSS' && $targetValue >= $startValue) {
                $errors['target_value'] = 'For a weight-loss goal, the target value must be lower than the start value.';
            }
        }

        if (!$this->isDateString($values['start_date'])) {
            $errors['start_date'] = 'Start date must use the YYYY-MM-DD format.';
        }

        if (!$this->isDateString($values['target_date'])) {
            $errors['target_date'] = 'Target date must use the YYYY-MM-DD format.';
        }

        if (!isset($errors['start_date']) && !isset($errors['target_date']) && $values['target_date'] < $values['start_date']) {
            $errors['target_date'] = 'Target date must be on or after the start date.';
        }

        if (!in_array($values['goal_type'], self::GOAL_TYPES, true)) {
            $errors['goal_type'] = 'Invalid goal type.';
        }

        if (!in_array($values['status'], self::GOAL_STATUSES, true)) {
            $errors['status'] = 'Invalid status.';
        }

        if ($values['description'] !== '' && mb_strlen($values['description']) > 2000) {
            $errors['description'] = 'Description must be 2000 characters or fewer.';
        }

        return [
            'values' => $values,
            'errors' => $errors,
        ];
    }

    protected function goalFromValues(array $values, ?int $id = null): ProgressGoal
    {
        return new ProgressGoal(
            $id,
            $values['title'],
            $values['metric'],
            (float) $values['start_value'],
            (float) $values['target_value'],
            $values['unit'],
            $values['start_date'],
            $values['target_date'],
            $values['goal_type'],
            $values['status'],
            $this->nullableString($values['description'])
        );
    }

    protected function createGoal(ProgressGoal $goal): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO progress_goals (title, metric, start_value, target_value, unit, start_date, target_date, goal_type, status, description)
            VALUES (:title, :metric, :start_value, :target_value, :unit, :start_date, :target_date, :goal_type, :status, :description)'
        );
        $statement->execute($this->goalPayload($goal));

        $id = (int) $this->connection()->lastInsertId();
        $goal->setId($id);

        return $id;
    }

    protected function updateGoal(ProgressGoal $goal): void
    {
        $payload = $this->goalPayload($goal);
        $payload['id'] = $goal->getId();

        $statement = $this->connection()->prepare(
            'UPDATE progress_goals
            SET title = :title,
                metric = :metric,
                start_value = :start_value,
                target_value = :target_value,
                unit = :unit,
                start_date = :start_date,
                target_date = :target_date,
                goal_type = :goal_type,
                status = :status,
                description = :description
            WHERE id = :id'
        );
        $statement->execute($payload);
    }

    protected function deleteGoal(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM progress_goals WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    /**
     * @return ProgressRecord[]
     */
    protected function fetchAllRecords(): array
    {
        $statement = $this->connection()->query(
            'SELECT pr.id, pr.progress_goal_id, pr.record_date, pr.recorded_value,
            pr.adherence_score, pr.mood, pr.record_type, pr.notes, pr.created_at, pr.updated_at,
            pg.title AS goal_title, pg.unit AS goal_unit
            FROM progress_records pr
            INNER JOIN progress_goals pg ON pg.id = pr.progress_goal_id
            ORDER BY pr.record_date DESC, pr.id DESC'
        );

        return array_map([$this, 'mapRecordRow'], $statement->fetchAll());
    }

    /**
     * @return ProgressRecord[]
     */
    protected function fetchRecordsByGoal(int $goalId): array
    {
        $statement = $this->connection()->prepare(
            'SELECT pr.id, pr.progress_goal_id, pr.record_date, pr.recorded_value,
            pr.adherence_score, pr.mood, pr.record_type, pr.notes, pr.created_at, pr.updated_at,
            pg.title AS goal_title, pg.unit AS goal_unit
            FROM progress_records pr
            INNER JOIN progress_goals pg ON pg.id = pr.progress_goal_id
            WHERE pr.progress_goal_id = :progress_goal_id
            ORDER BY pr.record_date DESC, pr.id DESC'
        );
        $statement->execute(['progress_goal_id' => $goalId]);

        return array_map([$this, 'mapRecordRow'], $statement->fetchAll());
    }

    protected function findRecordById(int $id): ?ProgressRecord
    {
        $statement = $this->connection()->prepare(
            'SELECT pr.id, pr.progress_goal_id, pr.record_date, pr.recorded_value,
            pr.adherence_score, pr.mood, pr.record_type, pr.notes, pr.created_at, pr.updated_at,
            pg.title AS goal_title, pg.unit AS goal_unit
            FROM progress_records pr
            INNER JOIN progress_goals pg ON pg.id = pr.progress_goal_id
            WHERE pr.id = :id
            LIMIT 1'
        );
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapRecordRow($row);
    }

    protected function defaultRecordFormData(int $goalId = 0, string $recordType = 'CHECKPOINT'): array
    {
        return [
            'progress_goal_id' => $goalId > 0 ? (string) $goalId : '',
            'record_date' => date('Y-m-d'),
            'recorded_value' => '',
            'adherence_score' => '',
            'mood' => 'STEADY',
            'record_type' => in_array($recordType, self::RECORD_TYPES, true) ? $recordType : 'CHECKPOINT',
            'notes' => '',
        ];
    }

    protected function prepareRecordFormData(ProgressRecord|array|null $source, int $selectedGoalId = 0): array
    {
        if ($source instanceof ProgressRecord) {
            return [
                'progress_goal_id' => (string) ($source->getProgressGoalId() ?? ($selectedGoalId > 0 ? $selectedGoalId : '')),
                'record_date' => $source->getRecordDate() !== '' ? $source->getRecordDate() : date('Y-m-d'),
                'recorded_value' => (string) $source->getRecordedValue(),
                'adherence_score' => $source->getAdherenceScore() === null ? '' : (string) $source->getAdherenceScore(),
                'mood' => $source->getMood(),
                'record_type' => $source->getRecordType(),
                'notes' => (string) ($source->getNotes() ?? ''),
            ];
        }

        if ($source === null) {
            return $this->defaultRecordFormData($selectedGoalId);
        }

        return [
            'progress_goal_id' => $this->cleanValue($source['progress_goal_id'] ?? ($selectedGoalId > 0 ? (string) $selectedGoalId : '')),
            'record_date' => $this->cleanValue($source['record_date'] ?? date('Y-m-d')),
            'recorded_value' => $this->cleanValue($source['recorded_value'] ?? ''),
            'adherence_score' => $this->cleanValue($source['adherence_score'] ?? ''),
            'mood' => $this->cleanValue($source['mood'] ?? 'STEADY'),
            'record_type' => $this->cleanValue($source['record_type'] ?? 'CHECKPOINT'),
            'notes' => $this->cleanValue($source['notes'] ?? ''),
        ];
    }

    protected function validateRecord(array $input): array
    {
        $values = $this->prepareRecordFormData($input, (int) ($input['progress_goal_id'] ?? 0));
        $errors = [];
        $goal = null;

        $goalId = $values['progress_goal_id'];
        if (!ctype_digit($goalId) || (int) $goalId <= 0) {
            $errors['progress_goal_id'] = 'Goal is required.';
        } else {
            $goal = $this->findGoalById((int) $goalId);
            if ($goal === null) {
                $errors['progress_goal_id'] = 'The selected goal does not exist anymore.';
            }
        }

        if (!$this->isDateString($values['record_date'])) {
            $errors['record_date'] = 'Record date must use the YYYY-MM-DD format.';
        } elseif ($goal !== null && $values['record_date'] < $goal->getStartDate()) {
            $errors['record_date'] = 'Record date cannot be earlier than the goal start date.';
        }

        if (!is_numeric($values['recorded_value'])) {
            $errors['recorded_value'] = 'Recorded value must be numeric.';
        }

        if ($values['adherence_score'] !== '') {
            if (!ctype_digit($values['adherence_score'])) {
                $errors['adherence_score'] = 'Adherence score must be a whole number between 0 and 100.';
            } elseif ((int) $values['adherence_score'] < 0 || (int) $values['adherence_score'] > 100) {
                $errors['adherence_score'] = 'Adherence score must be between 0 and 100.';
            }
        }

        if (!in_array($values['mood'], self::RECORD_MOODS, true)) {
            $errors['mood'] = 'Invalid mood.';
        }

        if (!in_array($values['record_type'], self::RECORD_TYPES, true)) {
            $errors['record_type'] = 'Invalid record type.';
        }

        if ($values['notes'] !== '' && mb_strlen($values['notes']) > 2000) {
            $errors['notes'] = 'Notes must be 2000 characters or fewer.';
        }

        return [
            'values' => $values,
            'errors' => $errors,
        ];
    }

    protected function recordFromValues(array $values, ?int $id = null): ProgressRecord
    {
        return new ProgressRecord(
            $id,
            (int) $values['progress_goal_id'],
            $values['record_date'],
            (float) $values['recorded_value'],
            $values['adherence_score'] === '' ? null : (int) $values['adherence_score'],
            $values['mood'],
            $values['record_type'],
            $this->nullableString($values['notes'])
        );
    }

    protected function createRecord(ProgressRecord $record): int
    {
        $statement = $this->connection()->prepare(
            'INSERT INTO progress_records (progress_goal_id, record_date, recorded_value, adherence_score, mood, record_type, notes)
            VALUES (:progress_goal_id, :record_date, :recorded_value, :adherence_score, :mood, :record_type, :notes)'
        );
        $statement->execute($this->recordPayload($record));

        $id = (int) $this->connection()->lastInsertId();
        $record->setId($id);

        return $id;
    }

    protected function updateRecord(ProgressRecord $record): void
    {
        $payload = $this->recordPayload($record);
        $payload['id'] = $record->getId();

        $statement = $this->connection()->prepare(
            'UPDATE progress_records
            SET progress_goal_id = :progress_goal_id,
                record_date = :record_date,
                recorded_value = :recorded_value,
                adherence_score = :adherence_score,
                mood = :mood,
                record_type = :record_type,
                notes = :notes
            WHERE id = :id'
        );
        $statement->execute($payload);
    }

    protected function deleteRecord(int $id): void
    {
        $statement = $this->connection()->prepare('DELETE FROM progress_records WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    protected function cleanValue(mixed $value): string
    {
        return trim((string) $value);
    }

    protected function nullableString(mixed $value): ?string
    {
        $value = $this->cleanValue($value);

        return $value === '' ? null : $value;
    }

    protected function isDateString(string $value): bool
    {
        $date = DateTimeImmutable::createFromFormat('Y-m-d', $value);

        return $date instanceof DateTimeImmutable && $date->format('Y-m-d') === $value;
    }

    private function goalPayload(ProgressGoal $goal): array
    {
        return [
            'title' => $goal->getTitle(),
            'metric' => $goal->getMetric(),
            'start_value' => $goal->getStartValue(),
            'target_value' => $goal->getTargetValue(),
            'unit' => $goal->getUnit(),
            'start_date' => $goal->getStartDate(),
            'target_date' => $goal->getTargetDate(),
            'goal_type' => $goal->getGoalType(),
            'status' => $goal->getStatus(),
            'description' => $goal->getDescription(),
        ];
    }

    private function recordPayload(ProgressRecord $record): array
    {
        return [
            'progress_goal_id' => $record->getProgressGoalId(),
            'record_date' => $record->getRecordDate(),
            'recorded_value' => $record->getRecordedValue(),
            'adherence_score' => $record->getAdherenceScore(),
            'mood' => $record->getMood(),
            'record_type' => $record->getRecordType(),
            'notes' => $record->getNotes(),
        ];
    }

    private function mapGoalRow(array $row): ProgressGoal
    {
        return new ProgressGoal(
            (int) $row['id'],
            (string) $row['title'],
            (string) $row['metric'],
            (float) $row['start_value'],
            (float) $row['target_value'],
            (string) $row['unit'],
            (string) $row['start_date'],
            (string) $row['target_date'],
            (string) $row['goal_type'],
            (string) $row['status'],
            $row['description'] !== null ? (string) $row['description'] : null,
            $row['created_at'] !== null ? (string) $row['created_at'] : null,
            $row['updated_at'] !== null ? (string) $row['updated_at'] : null
        );
    }

    private function mapRecordRow(array $row): ProgressRecord
    {
        return new ProgressRecord(
            (int) $row['id'],
            (int) $row['progress_goal_id'],
            (string) $row['record_date'],
            (float) $row['recorded_value'],
            $row['adherence_score'] !== null ? (int) $row['adherence_score'] : null,
            (string) $row['mood'],
            (string) $row['record_type'],
            $row['notes'] !== null ? (string) $row['notes'] : null,
            $row['created_at'] !== null ? (string) $row['created_at'] : null,
            $row['updated_at'] !== null ? (string) $row['updated_at'] : null,
            $row['goal_title'] !== null ? (string) $row['goal_title'] : null,
            $row['goal_unit'] !== null ? (string) $row['goal_unit'] : null
        );
    }
}
