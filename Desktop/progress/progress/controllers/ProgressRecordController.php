<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/models/ProgressGoal.php';
require_once ROOT_PATH . '/models/ProgressRecord.php';
require_once ROOT_PATH . '/controllers/GeminiAssistantService.php';

class ProgressRecordController extends BaseController
{
    private const RECORD_MOODS = ['LOW', 'STEADY', 'HIGH'];
    private const RECORD_TYPES = ['CHECKPOINT', 'MILESTONE', 'MEASUREMENT', 'NOTE', 'REPORT'];

    public function index(string $area): void
    {
        $filters = $this->recordFilters($_GET);
        $this->render('records/index', $this->page($area, 'Progress Records') + ['records' => $this->fetchRecords($filters), 'recordFilters' => $filters, 'goals' => $this->fetchGoals()], $area);
    }

    public function exportPdf(string $area): void
    {
        $filters = $this->recordFilters($_GET);
        $records = $this->fetchRecords($filters);
        $lines = ['Progress Records Export', '', 'Records: ' . count($records)];
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all') . ', goal=' . ($filters['goal_id'] !== '' ? $filters['goal_id'] : 'all') . ', type=' . ($filters['record_type'] !== '' ? $filters['record_type'] : 'all') . ', mood=' . ($filters['mood'] !== '' ? $filters['mood'] : 'all') . ', sort=' . $filters['sort'];
        $lines[] = 'Area: ' . $area;
        $lines[] = '';
        foreach ($records as $record) { $lines[] = sprintf('%s | %s | %.2f %s | %s | %s | %s', (string) ($record->getGoalTitle() ?? 'Goal'), $record->getRecordDate(), $record->getRecordedValue(), (string) ($record->getGoalUnit() ?? ''), $record->getRecordType(), $record->getMood(), $record->getAdherenceScore() === null ? '-' : ((string) $record->getAdherenceScore() . '%')); }
        $this->downloadSimplePdf('progress-records.pdf', $lines);
    }

    public function show(string $area): void
    {
        $record = $this->findRecord((int) ($_GET['id'] ?? 0));
        if ($record === null) { $this->renderNotFound(); return; }
        $goal = $this->findGoal((int) $record->getProgressGoalId());
        if ($goal === null) { $this->renderNotFound(); return; }
        $this->redirect($area . '/goals/show', ['id' => (int) $goal->getId()]);
    }

    public function form(string $area, string $mode): void
    {
        $record = $mode === 'edit' ? $this->findRecord((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $record === null) { $this->renderNotFound(); return; }
        $goalId = (int) ($_GET['goal_id'] ?? ($record?->getProgressGoalId() ?? 0));
        $type = ($_GET['type'] ?? '') === 'REPORT' ? 'REPORT' : 'CHECKPOINT';
        $this->renderRecordForm($area, $mode, $record !== null ? $this->recordValues($record, $goalId) : $this->recordValues(['record_type' => $type], $goalId), [], $record);
    }

    public function create(string $area): void
    {
        $data = $this->validateRecord($_POST);
        if ($data['errors'] !== []) { $this->renderRecordForm($area, 'create', $data['values'], $data['errors']); return; }
        $this->insertRecord($this->recordEntity($data['values']));
        $this->redirect($area . '/goals/show', ['id' => (int) $data['values']['progress_goal_id']]);
    }

    public function insights(string $area): void
    {
        unset($area);
        header('Content-Type: application/json; charset=UTF-8');
        $goalId = (int) ($_GET['goal_id'] ?? 0);
        $recordDate = $this->cleanValue($_GET['record_date'] ?? '');
        if ($goalId <= 0 || !$this->isDateString($recordDate)) { http_response_code(400); echo json_encode(['error' => 'A valid goal and record date are required.']); return; }
        $goal = $this->findGoal($goalId);
        if ($goal === null) { http_response_code(404); echo json_encode(['error' => 'Goal not found.']); return; }

        $baseline = $this->weeklyBaseline($goalId, $recordDate);

        try {
            echo json_encode(['quote' => $this->recordQuote($goal, $baseline), 'baseline' => $baseline], JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            http_response_code(503);
            echo json_encode(['quote' => ['content' => 'Progress grows when you keep showing up for your next rep.', 'author' => 'Asteria', 'tag' => 'fallback'], 'baseline' => $baseline, 'error' => $exception->getMessage()]);
        }
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $record = $this->findRecord($id);
        if ($record === null) { $this->renderNotFound(); return; }
        $data = $this->validateRecord($_POST);
        if ($data['errors'] !== []) { $this->renderRecordForm($area, 'edit', $data['values'], $data['errors'], $record); return; }
        $this->updateRecord($this->recordEntity($data['values'], $id));
        $this->redirect($area . '/goals/show', ['id' => (int) $data['values']['progress_goal_id']]);
    }

    public function delete(string $area): void
    {
        $record = $this->findRecord((int) ($_GET['id'] ?? 0));
        if ($record === null) { $this->redirect($area . '/records'); return; }
        $statement = $this->connection()->prepare('DELETE FROM progress_records WHERE id = :id');
        $statement->execute(['id' => (int) $record->getId()]);
        $this->redirect($area . '/goals/show', ['id' => (int) $record->getProgressGoalId()]);
    }

    private function renderRecordForm(string $area, string $mode, array $values, array $errors = [], ?ProgressRecord $record = null): void
    {
        $this->render('records/form', $this->page($area, $mode === 'edit' ? 'Edit Progress Record' : 'New Progress Record') + ['mode' => $mode, 'record' => $record, 'errors' => $errors, 'goals' => $this->fetchGoals(), 'values' => $values], $area);
    }

    private function page(string $area, string $title): array
    {
        return ['pageTitle' => $title, 'area' => $area, 'currentSection' => 'records'];
    }

    private function recordFilters(array $source): array
    {
        return ['search' => $this->cleanValue($source['search'] ?? ''), 'goal_id' => $this->cleanValue($source['goal_id'] ?? ''), 'record_type' => $this->cleanValue($source['record_type'] ?? ''), 'mood' => $this->cleanValue($source['mood'] ?? ''), 'sort' => $this->cleanValue($source['sort'] ?? 'record_date_desc')];
    }

    /**
     * @return ProgressRecord[]
     */
    private function fetchRecords(array $filters = []): array
    {
        $query = 'SELECT pr.id, pr.progress_goal_id, pr.record_date, pr.recorded_value, pr.adherence_score, pr.mood, pr.record_type, pr.notes, pr.created_at, pr.updated_at, pg.title AS goal_title, pg.unit AS goal_unit FROM progress_records pr INNER JOIN progress_goals pg ON pg.id = pr.progress_goal_id WHERE 1=1'; $params = [];
        if (($filters['search'] ?? '') !== '') { $search = '%' . $filters['search'] . '%'; $query .= ' AND (pg.title LIKE :search_goal_title OR COALESCE(pr.notes, "") LIKE :search_notes)'; $params += ['search_goal_title' => $search, 'search_notes' => $search]; }
        foreach (['goal_id' => 'pr.progress_goal_id', 'record_type' => 'pr.record_type', 'mood' => 'pr.mood'] as $field => $column) { if (($filters[$field] ?? '') !== '') { $query .= ' AND ' . $column . ' = :' . $field; $params[$field] = $field === 'goal_id' ? (int) $filters[$field] : $filters[$field]; } }
        $query .= ' ORDER BY ' . match ($filters['sort'] ?? 'record_date_desc') { 'record_date_asc' => 'pr.record_date ASC, pr.id ASC', 'value_asc' => 'pr.recorded_value ASC, pr.id DESC', 'value_desc' => 'pr.recorded_value DESC, pr.id DESC', 'adherence_desc' => 'pr.adherence_score DESC, pr.id DESC', 'adherence_asc' => 'pr.adherence_score ASC, pr.id DESC', default => 'pr.record_date DESC, pr.id DESC' };
        $statement = $this->connection()->prepare($query); $statement->execute($params);
        return array_map([$this, 'mapRecord'], $statement->fetchAll());
    }

    private function findRecord(int $id): ?ProgressRecord
    {
        $statement = $this->connection()->prepare('SELECT pr.id, pr.progress_goal_id, pr.record_date, pr.recorded_value, pr.adherence_score, pr.mood, pr.record_type, pr.notes, pr.created_at, pr.updated_at, pg.title AS goal_title, pg.unit AS goal_unit FROM progress_records pr INNER JOIN progress_goals pg ON pg.id = pr.progress_goal_id WHERE pr.id = :id LIMIT 1');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row === false ? null : $this->mapRecord($row);
    }

    /**
     * @return ProgressGoal[]
     */
    private function fetchGoals(): array
    {
        $statement = $this->connection()->query('SELECT * FROM progress_goals ORDER BY target_date ASC, id DESC');
        return array_map([$this, 'mapGoal'], $statement->fetchAll());
    }

    private function findGoal(int $id): ?ProgressGoal
    {
        $statement = $this->connection()->prepare('SELECT * FROM progress_goals WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row === false ? null : $this->mapGoal($row);
    }

    private function recordValues(ProgressRecord|array|null $source, int $goalId = 0): array
    {
        if ($source instanceof ProgressRecord) {
            return ['progress_goal_id' => (string) ($source->getProgressGoalId() ?? ($goalId > 0 ? $goalId : '')), 'record_date' => $source->getRecordDate() !== '' ? $source->getRecordDate() : date('Y-m-d'), 'recorded_value' => (string) $source->getRecordedValue(), 'adherence_score' => $source->getAdherenceScore() === null ? '' : (string) $source->getAdherenceScore(), 'mood' => $source->getMood(), 'record_type' => $source->getRecordType(), 'notes' => (string) ($source->getNotes() ?? '')];
        }
        $data = is_array($source) ? $source : [];
        return ['progress_goal_id' => $this->cleanValue($data['progress_goal_id'] ?? ($goalId > 0 ? (string) $goalId : '')), 'record_date' => $this->cleanValue($data['record_date'] ?? date('Y-m-d')), 'recorded_value' => $this->cleanValue($data['recorded_value'] ?? ''), 'adherence_score' => $this->cleanValue($data['adherence_score'] ?? ''), 'mood' => $this->cleanValue($data['mood'] ?? 'STEADY'), 'record_type' => $this->cleanValue($data['record_type'] ?? 'CHECKPOINT'), 'notes' => $this->cleanValue($data['notes'] ?? '')];
    }

    private function validateRecord(array $input): array
    {
        $values = $this->recordValues($input, (int) ($input['progress_goal_id'] ?? 0)); $errors = []; $goal = null;
        if (!ctype_digit($values['progress_goal_id']) || (int) $values['progress_goal_id'] <= 0) { $errors['progress_goal_id'] = 'Goal is required.'; } else { $goal = $this->findGoal((int) $values['progress_goal_id']); if ($goal === null) { $errors['progress_goal_id'] = 'The selected goal does not exist anymore.'; } }
        if (!$this->isDateString($values['record_date'])) { $errors['record_date'] = 'Record date must use the YYYY-MM-DD format.'; } elseif ($goal !== null && $values['record_date'] < $goal->getStartDate()) { $errors['record_date'] = 'Record date cannot be earlier than the goal start date.'; }
        if (!is_numeric($values['recorded_value'])) { $errors['recorded_value'] = 'Recorded value must be numeric.'; }
        if ($values['adherence_score'] !== '') { if (!ctype_digit($values['adherence_score'])) { $errors['adherence_score'] = 'Adherence score must be a whole number between 0 and 100.'; } elseif ((int) $values['adherence_score'] < 0 || (int) $values['adherence_score'] > 100) { $errors['adherence_score'] = 'Adherence score must be between 0 and 100.'; } }
        if (!in_array($values['mood'], self::RECORD_MOODS, true)) { $errors['mood'] = 'Invalid mood.'; }
        if (!in_array($values['record_type'], self::RECORD_TYPES, true)) { $errors['record_type'] = 'Invalid record type.'; }
        if ($values['notes'] !== '' && mb_strlen($values['notes']) > 2000) { $errors['notes'] = 'Notes must be 2000 characters or fewer.'; }
        return ['values' => $values, 'errors' => $errors];
    }

    private function recordEntity(array $values, ?int $id = null): ProgressRecord
    {
        return new ProgressRecord($id, (int) $values['progress_goal_id'], $values['record_date'], (float) $values['recorded_value'], $values['adherence_score'] === '' ? null : (int) $values['adherence_score'], $values['mood'], $values['record_type'], $this->nullableString($values['notes']));
    }

    private function insertRecord(ProgressRecord $record): int
    {
        $statement = $this->connection()->prepare('INSERT INTO progress_records (progress_goal_id, record_date, recorded_value, adherence_score, mood, record_type, notes) VALUES (:goal_id, :record_date, :recorded_value, :adherence_score, :mood, :record_type, :notes)');
        $statement->execute(['goal_id' => $record->getProgressGoalId(), 'record_date' => $record->getRecordDate(), 'recorded_value' => $record->getRecordedValue(), 'adherence_score' => $record->getAdherenceScore(), 'mood' => $record->getMood(), 'record_type' => $record->getRecordType(), 'notes' => $record->getNotes()]);
        $record->setId((int) $this->connection()->lastInsertId());
        return (int) $record->getId();
    }

    private function updateRecord(ProgressRecord $record): void
    {
        $statement = $this->connection()->prepare('UPDATE progress_records SET progress_goal_id = :goal_id, record_date = :record_date, recorded_value = :recorded_value, adherence_score = :adherence_score, mood = :mood, record_type = :record_type, notes = :notes WHERE id = :id');
        $statement->execute(['id' => $record->getId(), 'goal_id' => $record->getProgressGoalId(), 'record_date' => $record->getRecordDate(), 'recorded_value' => $record->getRecordedValue(), 'adherence_score' => $record->getAdherenceScore(), 'mood' => $record->getMood(), 'record_type' => $record->getRecordType(), 'notes' => $record->getNotes()]);
    }

    private function weeklyBaseline(int $goalId, string $recordDate): ?array
    {
        $baseline = (new DateTimeImmutable($recordDate))->modify('-7 days')->format('Y-m-d');
        $statement = $this->connection()->prepare('SELECT id, record_date, recorded_value, record_type FROM progress_records WHERE progress_goal_id = :goal_id AND record_date <= :baseline ORDER BY record_date DESC, id DESC LIMIT 1');
        $statement->execute(['goal_id' => $goalId, 'baseline' => $baseline]);
        $row = $statement->fetch();
        return $row === false ? null : ['id' => (int) $row['id'], 'record_date' => (string) $row['record_date'], 'recorded_value' => (float) $row['recorded_value'], 'record_type' => (string) $row['record_type']];
    }

    private function recordQuote(ProgressGoal $goal, ?array $baseline = null): array
    {
        $metric = strtolower($goal->getMetric());
        $type = strtoupper($goal->getGoalType());
        $message = match (true) {
            in_array($type, ['WEIGHT_LOSS', 'FITNESS', 'HEALTH'], true) || str_contains($metric, 'weight') => 'Log the number honestly, then pick one recovery, nutrition, or movement action that supports the next reading.',
            in_array($type, ['CAREER', 'FINANCE', 'PRODUCTIVITY'], true) => 'Treat this record as feedback: compare it with last week and choose the highest-leverage next task.',
            $type === 'LEARNING' => 'Convert the result into a study loop: review what worked, repeat it, and remove one blocker before the next checkpoint.',
            default => 'Use this record as a signal, not a judgment. The next useful step is the one you can repeat.',
        };

        $fallback = ['content' => $message, 'author' => 'Asteria local assistant', 'tag' => strtolower($type)];

        return (new GeminiAssistantService())->recordInsight($goal, $baseline, $fallback);
    }

    private function mapGoal(array $row): ProgressGoal
    {
        return new ProgressGoal((int) $row['id'], (string) $row['title'], (string) $row['metric'], (float) $row['start_value'], (float) $row['target_value'], (string) $row['unit'], (string) $row['start_date'], (string) $row['target_date'], (string) $row['goal_type'], (string) $row['status'], $row['description'] !== null ? (string) $row['description'] : null, $row['created_at'] !== null ? (string) $row['created_at'] : null, $row['updated_at'] !== null ? (string) $row['updated_at'] : null);
    }

    private function mapRecord(array $row): ProgressRecord
    {
        return new ProgressRecord((int) $row['id'], (int) $row['progress_goal_id'], (string) $row['record_date'], (float) $row['recorded_value'], $row['adherence_score'] !== null ? (int) $row['adherence_score'] : null, (string) $row['mood'], (string) $row['record_type'], $row['notes'] !== null ? (string) $row['notes'] : null, $row['created_at'] !== null ? (string) $row['created_at'] : null, $row['updated_at'] !== null ? (string) $row['updated_at'] : null, $row['goal_title'] !== null ? (string) $row['goal_title'] : null, $row['goal_unit'] !== null ? (string) $row['goal_unit'] : null);
    }
}
