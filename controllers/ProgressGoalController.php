<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProgressDataTrait.php';

class ProgressGoalController extends BaseController
{
    use ProgressDataTrait;

    private function getUserIdForArea(string $area): ?int
    {
        if ($area === 'backoffice') {
            return null;
        }

        if (!isset($_SESSION['user'])) {
            $this->redirect('login');
        }

        return (int) ($_SESSION['user']['id'] ?? 0);
    }

    public function index(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        
        $this->render('goals/index', [
            'pageTitle' => 'Progress Goals',
            'area' => $area,
            'currentSection' => 'goals',
            'goals' => $this->fetchAllGoals($userId),
        ], $area);
    }

    public function show(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $goal = $this->findGoalById((int) ($_GET['id'] ?? 0), $userId);
        
        if ($goal === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('goals/show', [
            'pageTitle' => 'Progress Goal',
            'area' => $area,
            'currentSection' => 'goals',
            'goal' => $goal,
            'records' => $this->fetchRecordsByGoal((int) ($goal->getId() ?? 0), $userId),
        ], $area);
    }

    public function form(string $area, string $mode): void
    {
        $userId = $this->getUserIdForArea($area);
        $goal = $mode === 'edit' ? $this->findGoalById((int) ($_GET['id'] ?? 0), $userId) : null;
        
        if ($mode === 'edit' && $goal === null) {
            $this->renderNotFound();
            return;
        }

        $values = $this->prepareGoalFormData($goal);
        
        // Handle AI Survey pre-fills
        if ($mode === 'create') {
            foreach ([
                'title' => 'prefill_title',
                'metric' => 'prefill_metric',
                'unit' => 'prefill_unit',
                'goal_type' => 'prefill_goal_type',
                'start_value' => 'prefill_start_value',
                'target_value' => 'prefill_target_value',
                'target_date' => 'prefill_target_date',
                'description' => 'prefill_description',
            ] as $key => $getParam) {
                if (isset($_GET[$getParam]) && trim((string) $_GET[$getParam]) !== '') {
                    $values[$key] = trim((string) $_GET[$getParam]);
                }
            }
        }

        $this->render('goals/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Progress Goal' : 'New Progress Goal',
            'area' => $area,
            'currentSection' => 'goals',
            'mode' => $mode,
            'goal' => $goal,
            'errors' => [],
            'values' => $values,
        ], $area);
    }

    public function create(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $validation = $this->validateGoal($_POST, $userId);
        
        if ($validation['errors'] !== []) {
            $this->render('goals/form', [
                'pageTitle' => 'New Progress Goal',
                'area' => $area,
                'currentSection' => 'goals',
                'mode' => 'create',
                'errors' => $validation['errors'],
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $goal = $this->goalFromValues($validation['values'], null, $userId);
        $id = $this->createGoal($goal);
        $this->redirect($area . '/goals/show', ['id' => $id]);
    }

    public function update(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $id = (int) ($_GET['id'] ?? 0);
        $goal = $this->findGoalById($id, $userId);
        
        if ($goal === null) {
            $this->renderNotFound();
            return;
        }

        $validation = $this->validateGoal($_POST, $userId);
        if ($validation['errors'] !== []) {
            $this->render('goals/form', [
                'pageTitle' => 'Edit Progress Goal',
                'area' => $area,
                'currentSection' => 'goals',
                'mode' => 'edit',
                'goal' => $goal,
                'errors' => $validation['errors'],
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $updatedGoal = $this->goalFromValues($validation['values'], $id, $userId);
        $this->updateGoal($updatedGoal);
        $this->redirect($area . '/goals/show', ['id' => $id]);
    }

    public function delete(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $this->deleteGoal((int) ($_GET['id'] ?? 0), $userId);
        $this->redirect($area . '/goals');
    }
}
