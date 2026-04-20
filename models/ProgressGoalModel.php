<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/ProgressGoal.php';

final class ProgressGoalModel extends BaseModel
{
    public const GOAL_TYPES = [
        'WEIGHT_LOSS',
        'FITNESS',
        'CAREER',
        'FINANCE',
        'LEARNING',
        'HEALTH',
        'PRODUCTIVITY',
        'OTHER',
    ];

    public const STATUSES = [
        'ACTIVE',
        'COMPLETED',
        'ON_HOLD',
    ];

    /**
     * @return ProgressGoal[]
     */
    public function findAll(): array
    {
        $statement = $this->connection->query('SELECT * FROM progress_goals ORDER BY target_date ASC, id DESC');

        return array_map([$this, 'mapGoalRow'], $statement->fetchAll());
    }

    public function findById(int $id): ?ProgressGoal
    {
        $statement = $this->connection->prepare('SELECT * FROM progress_goals WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();

        return $row === false ? null : $this->mapGoalRow($row);
    }

    public function defaultFormData(): array
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

    public function prepareFormData(ProgressGoal|array|null $source): array
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
            return $this->defaultFormData();
        }

        return [
            'title' => $this->clean($source['title'] ?? ''),
            'metric' => $this->clean($source['metric'] ?? ''),
            'start_value' => $this->clean($source['start_value'] ?? ''),
            'target_value' => $this->clean($source['target_value'] ?? ''),
            'unit' => $this->clean($source['unit'] ?? ''),
            'start_date' => $this->clean($source['start_date'] ?? ''),
            'target_date' => $this->clean($source['target_date'] ?? ''),
            'goal_type' => $this->clean($source['goal_type'] ?? 'OTHER'),
            'status' => $this->clean($source['status'] ?? 'ACTIVE'),
            'description' => $this->clean($source['description'] ?? ''),
        ];
    }

    public function validate(array $input): array
    {
        $values = $this->prepareFormData($input);
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

        if (!in_array($values['status'], self::STATUSES, true)) {
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

    public function entityFromValues(array $values, ?int $id = null): ProgressGoal
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

    public function create(ProgressGoal $goal): int
    {
        $statement = $this->connection->prepare(
            'INSERT INTO progress_goals (title, metric, start_value, target_value, unit, start_date, target_date, goal_type, status, description)
            VALUES (:title, :metric, :start_value, :target_value, :unit, :start_date, :target_date, :goal_type, :status, :description)'
        );
        $statement->execute($this->payload($goal));

        $id = (int) $this->connection->lastInsertId();
        $goal->setId($id);

        return $id;
    }

    public function update(ProgressGoal $goal): void
    {
        $payload = $this->payload($goal);
        $payload['id'] = $goal->getId();

        $statement = $this->connection->prepare(
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

    public function delete(int $id): void
    {
        $statement = $this->connection->prepare('DELETE FROM progress_goals WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    private function payload(ProgressGoal $goal): array
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
}
