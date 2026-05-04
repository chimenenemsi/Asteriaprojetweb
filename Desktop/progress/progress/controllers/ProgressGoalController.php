<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/models/ProgressGoal.php';
require_once ROOT_PATH . '/models/ProgressRecord.php';
require_once ROOT_PATH . '/controllers/GeminiAssistantService.php';

class ProgressGoalController extends BaseController
{
    private const GOAL_TYPES = ['WEIGHT_LOSS', 'FITNESS', 'CAREER', 'FINANCE', 'LEARNING', 'HEALTH', 'PRODUCTIVITY', 'OTHER'];
    private const GOAL_STATUSES = ['ACTIVE', 'COMPLETED', 'ON_HOLD'];

    public function index(string $area): void
    {
        $filters = $this->goalFilters($_GET);
        $this->render('goals/index', $this->page($area, 'Progress Goals') + ['goals' => $this->fetchGoals($filters), 'goalFilters' => $filters], $area);
    }

    public function exportPdf(string $area): void
    {
        $filters = $this->goalFilters($_GET);
        $goals = $this->fetchGoals($filters);
        $lines = ['Progress Goals Export', '', 'Records: ' . count($goals)];
        $lines[] = 'Filters: search=' . ($filters['search'] !== '' ? $filters['search'] : 'all') . ', type=' . ($filters['goal_type'] !== '' ? $filters['goal_type'] : 'all') . ', status=' . ($filters['status'] !== '' ? $filters['status'] : 'all') . ', sort=' . $filters['sort'];
        $lines[] = 'Area: ' . $area;
        $lines[] = '';
        foreach ($goals as $goal) { $lines[] = sprintf('%s | %s | %s %.2f %s | %s | %s', $goal->getTitle(), $goal->getMetric(), $goal->getGoalType(), $goal->getTargetValue(), $goal->getUnit(), $goal->getStatus(), $goal->getTargetDate()); }
        $this->downloadSimplePdf('progress-goals.pdf', $lines);
    }

    public function show(string $area): void
    {
        $goal = $this->findGoal((int) ($_GET['id'] ?? 0));
        if ($goal === null) { $this->renderNotFound(); return; }
        $this->render('goals/show', $this->page($area, 'Progress Goal') + ['goal' => $goal, 'records' => $this->fetchRecordsByGoal((int) $goal->getId()), 'goalSummary' => $this->goalSummary($goal)], $area);
    }

    public function motivation(string $area): void
    {
        unset($area);
        header('Content-Type: application/json; charset=UTF-8');
        $goal = $this->findGoal((int) ($_GET['id'] ?? 0));
        $type = $this->cleanValue($_GET['motivation_type'] ?? 'subject');
        if ($goal === null) { http_response_code(404); echo json_encode(['error' => 'Goal not found.']); return; }

        try {
            echo json_encode(['quote' => $this->goalQuote($goal, $type), 'summary' => $this->goalSummary($goal)], JSON_THROW_ON_ERROR);
        } catch (Throwable $exception) {
            http_response_code(503);
            echo json_encode(['quote' => ['content' => 'Progress compounds when you keep turning intention into reps.', 'author' => 'Asteria', 'tag' => 'fallback'], 'summary' => $this->goalSummary($goal), 'error' => $exception->getMessage()]);
        }
    }

    public function form(string $area, string $mode): void
    {
        $goal = $mode === 'edit' ? $this->findGoal((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $goal === null) { $this->renderNotFound(); return; }
        $this->renderGoalForm($area, $mode, $this->goalValues($goal), [], $goal);
    }

    public function create(string $area): void
    {
        $data = $this->validateGoal($_POST);
        if ($data['errors'] !== []) { $this->renderGoalForm($area, 'create', $data['values'], $data['errors']); return; }
        $id = $this->insertGoal($this->goalEntity($data['values']));
        $this->redirect($area . '/goals/show', ['id' => $id]);
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $goal = $this->findGoal($id);
        if ($goal === null) { $this->renderNotFound(); return; }
        $data = $this->validateGoal($_POST);
        if ($data['errors'] !== []) { $this->renderGoalForm($area, 'edit', $data['values'], $data['errors'], $goal); return; }
        $this->updateGoal($this->goalEntity($data['values'], $id));
        $this->redirect($area . '/goals/show', ['id' => $id]);
    }

    public function delete(string $area): void
    {
        $statement = $this->connection()->prepare('DELETE FROM progress_goals WHERE id = :id');
        $statement->execute(['id' => (int) ($_GET['id'] ?? 0)]);
        $this->redirect($area . '/goals');
    }

    private function renderGoalForm(string $area, string $mode, array $values, array $errors = [], ?ProgressGoal $goal = null): void
    {
        $this->render('goals/form', $this->page($area, $mode === 'edit' ? 'Edit Progress Goal' : 'New Progress Goal') + ['mode' => $mode, 'goal' => $goal, 'errors' => $errors, 'values' => $values], $area);
    }

    private function page(string $area, string $title): array
    {
        return ['pageTitle' => $title, 'area' => $area, 'currentSection' => 'goals'];
    }

    private function goalFilters(array $source): array
    {
        return ['search' => $this->cleanValue($source['search'] ?? ''), 'goal_type' => $this->cleanValue($source['goal_type'] ?? ''), 'status' => $this->cleanValue($source['status'] ?? ''), 'sort' => $this->cleanValue($source['sort'] ?? 'target_date_asc')];
    }

    /**
     * @return ProgressGoal[]
     */
    private function fetchGoals(array $filters = []): array
    {
        $query = 'SELECT * FROM progress_goals WHERE 1=1'; $params = [];
        if (($filters['search'] ?? '') !== '') { $search = '%' . $filters['search'] . '%'; $query .= ' AND (title LIKE :search_title OR metric LIKE :search_metric OR COALESCE(description, "") LIKE :search_description)'; $params += ['search_title' => $search, 'search_metric' => $search, 'search_description' => $search]; }
        if (($filters['goal_type'] ?? '') !== '') { $query .= ' AND goal_type = :goal_type'; $params['goal_type'] = $filters['goal_type']; }
        if (($filters['status'] ?? '') !== '') { $query .= ' AND status = :status'; $params['status'] = $filters['status']; }
        $query .= ' ORDER BY ' . match ($filters['sort'] ?? 'target_date_asc') { 'title_asc' => 'title ASC, id DESC', 'title_desc' => 'title DESC, id DESC', 'created_desc' => 'id DESC', 'created_asc' => 'id ASC', 'target_date_desc' => 'target_date DESC, id DESC', default => 'target_date ASC, id DESC' };
        $statement = $this->connection()->prepare($query); $statement->execute($params);
        return array_map([$this, 'mapGoal'], $statement->fetchAll());
    }

    private function findGoal(int $id): ?ProgressGoal
    {
        $statement = $this->connection()->prepare('SELECT * FROM progress_goals WHERE id = :id');
        $statement->execute(['id' => $id]);
        $row = $statement->fetch();
        return $row === false ? null : $this->mapGoal($row);
    }

    private function goalValues(ProgressGoal|array|null $source): array
    {
        if ($source instanceof ProgressGoal) {
            return ['title' => $source->getTitle(), 'metric' => $source->getMetric(), 'start_value' => (string) $source->getStartValue(), 'target_value' => (string) $source->getTargetValue(), 'unit' => $source->getUnit(), 'start_date' => $source->getStartDate(), 'target_date' => $source->getTargetDate(), 'goal_type' => $source->getGoalType(), 'status' => $source->getStatus(), 'description' => (string) ($source->getDescription() ?? '')];
        }
        $data = is_array($source) ? $source : [];
        return ['title' => $this->cleanValue($data['title'] ?? ''), 'metric' => $this->cleanValue($data['metric'] ?? ''), 'start_value' => $this->cleanValue($data['start_value'] ?? ''), 'target_value' => $this->cleanValue($data['target_value'] ?? ''), 'unit' => $this->cleanValue($data['unit'] ?? ''), 'start_date' => $this->cleanValue($data['start_date'] ?? date('Y-m-d')), 'target_date' => $this->cleanValue($data['target_date'] ?? date('Y-m-d', strtotime('+30 days'))), 'goal_type' => $this->cleanValue($data['goal_type'] ?? 'OTHER'), 'status' => $this->cleanValue($data['status'] ?? 'ACTIVE'), 'description' => $this->cleanValue($data['description'] ?? '')];
    }

    private function validateGoal(array $input): array
    {
        $values = $this->goalValues($input); $errors = [];
        foreach (['title' => 150, 'metric' => 100, 'unit' => 30] as $field => $max) { $label = ucfirst(str_replace('_', ' ', $field)); if ($values[$field] === '') { $errors[$field] = $label . ' is required.'; } elseif (mb_strlen($values[$field]) > $max) { $errors[$field] = $label . ' must be ' . $max . ' characters or fewer.'; } }
        foreach (['start_value' => 'Start value', 'target_value' => 'Target value'] as $field => $label) { if (!is_numeric($values[$field])) { $errors[$field] = $label . ' must be numeric.'; } }
        if (!isset($errors['start_value']) && !isset($errors['target_value'])) { $start = (float) $values['start_value']; $target = (float) $values['target_value']; if ($target === $start) { $errors['target_value'] = 'Target value must be different from the start value.'; } elseif ($values['goal_type'] === 'WEIGHT_LOSS' && $target >= $start) { $errors['target_value'] = 'For a weight-loss goal, the target value must be lower than the start value.'; } }
        foreach (['start_date' => 'Start date', 'target_date' => 'Target date'] as $field => $label) { if (!$this->isDateString($values[$field])) { $errors[$field] = $label . ' must use the YYYY-MM-DD format.'; } }
        if (!isset($errors['start_date']) && !isset($errors['target_date']) && $values['target_date'] < $values['start_date']) { $errors['target_date'] = 'Target date must be on or after the start date.'; }
        if (!in_array($values['goal_type'], self::GOAL_TYPES, true)) { $errors['goal_type'] = 'Invalid goal type.'; }
        if (!in_array($values['status'], self::GOAL_STATUSES, true)) { $errors['status'] = 'Invalid status.'; }
        if ($values['description'] !== '' && mb_strlen($values['description']) > 2000) { $errors['description'] = 'Description must be 2000 characters or fewer.'; }
        return ['values' => $values, 'errors' => $errors];
    }

    private function goalEntity(array $values, ?int $id = null): ProgressGoal
    {
        return new ProgressGoal($id, $values['title'], $values['metric'], (float) $values['start_value'], (float) $values['target_value'], $values['unit'], $values['start_date'], $values['target_date'], $values['goal_type'], $values['status'], $this->nullableString($values['description']));
    }

    private function insertGoal(ProgressGoal $goal): int
    {
        $statement = $this->connection()->prepare('INSERT INTO progress_goals (title, metric, start_value, target_value, unit, start_date, target_date, goal_type, status, description) VALUES (:title, :metric, :start_value, :target_value, :unit, :start_date, :target_date, :goal_type, :status, :description)');
        $statement->execute(['title' => $goal->getTitle(), 'metric' => $goal->getMetric(), 'start_value' => $goal->getStartValue(), 'target_value' => $goal->getTargetValue(), 'unit' => $goal->getUnit(), 'start_date' => $goal->getStartDate(), 'target_date' => $goal->getTargetDate(), 'goal_type' => $goal->getGoalType(), 'status' => $goal->getStatus(), 'description' => $goal->getDescription()]);
        $goal->setId((int) $this->connection()->lastInsertId());
        return (int) $goal->getId();
    }

    private function updateGoal(ProgressGoal $goal): void
    {
        $statement = $this->connection()->prepare('UPDATE progress_goals SET title = :title, metric = :metric, start_value = :start_value, target_value = :target_value, unit = :unit, start_date = :start_date, target_date = :target_date, goal_type = :goal_type, status = :status, description = :description WHERE id = :id');
        $statement->execute(['id' => $goal->getId(), 'title' => $goal->getTitle(), 'metric' => $goal->getMetric(), 'start_value' => $goal->getStartValue(), 'target_value' => $goal->getTargetValue(), 'unit' => $goal->getUnit(), 'start_date' => $goal->getStartDate(), 'target_date' => $goal->getTargetDate(), 'goal_type' => $goal->getGoalType(), 'status' => $goal->getStatus(), 'description' => $goal->getDescription()]);
    }

    /**
     * @return ProgressRecord[]
     */
    private function fetchRecordsByGoal(int $goalId): array
    {
        $statement = $this->connection()->prepare('SELECT pr.id, pr.progress_goal_id, pr.record_date, pr.recorded_value, pr.adherence_score, pr.mood, pr.record_type, pr.notes, pr.created_at, pr.updated_at, pg.title AS goal_title, pg.unit AS goal_unit FROM progress_records pr INNER JOIN progress_goals pg ON pg.id = pr.progress_goal_id WHERE pr.progress_goal_id = :goal_id ORDER BY pr.record_date DESC, pr.id DESC');
        $statement->execute(['goal_id' => $goalId]);
        return array_map([$this, 'mapRecord'], $statement->fetchAll());
    }

    private function goalSummary(ProgressGoal $goal): array
    {
        $records = $this->fetchRecordsByGoal((int) $goal->getId()); $latest = $records[0] ?? null; $start = $goal->getStartValue(); $target = $goal->getTargetValue(); $current = $latest?->getRecordedValue() ?? $start; $range = $target - $start; $direction = $range >= 0 ? 1 : -1; $targetDate = new DateTimeImmutable($goal->getTargetDate()); $startDate = new DateTimeImmutable($goal->getStartDate()); $days = max(1, (int) $startDate->diff($targetDate)->format('%a'));
        return ['record_count' => count($records), 'current_value' => $current, 'completion' => $range === 0.0 ? 0.0 : max(0.0, min(100.0, (($current - $start) / $range) * 100)), 'days_remaining' => (int) (new DateTimeImmutable())->diff($targetDate)->format('%r%a'), 'latest_record_date' => $latest?->getRecordDate(), 'remaining_to_target' => max(0.0, ($target - $current) * $direction), 'weekly_target_pace' => abs($range) / max(1, $days / 7), 'progress_direction' => $direction > 0 ? 'Increase' : 'Reduce'];
    }

    private function goalQuote(ProgressGoal $goal, string $type): array
    {
        $summary = $this->goalSummary($goal);
        $remaining = number_format((float) $summary['remaining_to_target'], 2);
        $unit = $goal->getUnit();
        $metric = strtolower($goal->getMetric());
        $pace = number_format((float) $summary['weekly_target_pace'], 2);

        $message = match ($type) {
            'discipline' => 'Protect the next small action: one measured update toward ' . $metric . ' is better than waiting for a perfect week.',
            'confidence' => 'You already have a clear target. Move ' . $remaining . ' ' . $unit . ' more and keep the pace visible.',
            'resilience' => 'A slow week is still data. Review it, adjust the next checkpoint, and continue at about ' . $pace . ' ' . $unit . ' per week.',
            default => 'Focus on the next checkpoint for ' . $metric . ': ' . $summary['progress_direction'] . ' by ' . $remaining . ' ' . $unit . ' with steady weekly reviews.',
        };

        $fallback = ['content' => $message, 'author' => 'Asteria local assistant', 'tag' => $type !== '' ? $type : 'subject'];

        return (new GeminiAssistantService())->goalMotivation($goal, $summary, $type, $fallback);
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
