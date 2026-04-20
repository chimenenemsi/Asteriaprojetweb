<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/ProgressRecord.php';

final class ProgressRecordModel extends BaseModel
{
    public const MOODS = [
        'LOW',
        'STEADY',
        'HIGH',
    ];

    public const RECORD_TYPES = [
        'CHECKPOINT',
        'MILESTONE',
        'MEASUREMENT',
        'NOTE',
        'REPORT',
    ];

    public function __construct(PDO $connection, private ProgressGoalModel $goalModel)
    {
        parent::__construct($connection);
    }

    /**
     * @return ProgressRecord[]
     */
    public function findAll(): array
    {
        $statement = $this->connection->query(
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
    public function findAllByGoal(int $goalId): array
    {
        $statement = $this->connection->prepare(
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

    public function findById(int $id): ?ProgressRecord
    {
        $statement = $this->connection->prepare(
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

    public function defaultFormData(int $goalId = 0, string $recordType = 'CHECKPOINT'): array
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

    public function prepareFormData(ProgressRecord|array|null $source, int $selectedGoalId = 0): array
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
            return $this->defaultFormData($selectedGoalId);
        }

        return [
            'progress_goal_id' => $this->clean($source['progress_goal_id'] ?? ($selectedGoalId > 0 ? (string) $selectedGoalId : '')),
            'record_date' => $this->clean($source['record_date'] ?? date('Y-m-d')),
            'recorded_value' => $this->clean($source['recorded_value'] ?? ''),
            'adherence_score' => $this->clean($source['adherence_score'] ?? ''),
            'mood' => $this->clean($source['mood'] ?? 'STEADY'),
            'record_type' => $this->clean($source['record_type'] ?? 'CHECKPOINT'),
            'notes' => $this->clean($source['notes'] ?? ''),
        ];
    }

    public function validate(array $input): array
    {
        $values = $this->prepareFormData($input, (int) ($input['progress_goal_id'] ?? 0));
        $errors = [];
        $goal = null;

        $goalId = $values['progress_goal_id'];
        if (!ctype_digit($goalId) || (int) $goalId <= 0) {
            $errors['progress_goal_id'] = 'Goal is required.';
        } else {
            $goal = $this->goalModel->findById((int) $goalId);
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

        if (!in_array($values['mood'], self::MOODS, true)) {
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

    public function entityFromValues(array $values, ?int $id = null): ProgressRecord
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

    public function create(ProgressRecord $record): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO progress_records (progress_goal_id, record_date, recorded_value, adherence_score, mood, record_type, notes)
            VALUES (:progress_goal_id, :record_date, :recorded_value, :adherence_score, :mood, :record_type, :notes)'
        );
        $statement->execute($this->payload($record));

        $id = (int) $this->connection->lastInsertId();
        $record->setId($id);

        return $id;
    }

    public function update(ProgressRecord $record): void
    {
        $payload = $this->payload($record);
        $payload['id'] = $record->getId();

        $statement = $this->connection->prepare(
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

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM progress_records WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function payload(ProgressRecord $record): array
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
