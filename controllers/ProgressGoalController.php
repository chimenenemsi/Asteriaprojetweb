<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProgressDataTrait.php';

class ProgressGoalController extends BaseController
{
    use ProgressDataTrait;

    public function index(string $area): void
    {
        $this->render('goals/index', [
            'pageTitle' => 'Progress Goals',
            'area' => $area,
            'currentSection' => 'goals',
            'goals' => $this->fetchAllGoals(),
        ], $area);
    }

    public function show(string $area): void
    {
        $goal = $this->findGoalById((int) ($_GET['id'] ?? 0));
        if ($goal === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('goals/show', [
            'pageTitle' => 'Progress Goal',
            'area' => $area,
            'currentSection' => 'goals',
            'goal' => $goal,
            'records' => $this->fetchRecordsByGoal((int) ($goal->getId() ?? 0)),
        ], $area);
    }

    public function form(string $area, string $mode): void
    {
        $goal = $mode === 'edit' ? $this->findGoalById((int) ($_GET['id'] ?? 0)) : null;
        if ($mode === 'edit' && $goal === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('goals/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Progress Goal' : 'New Progress Goal',
            'area' => $area,
            'currentSection' => 'goals',
            'mode' => $mode,
            'goal' => $goal,
            'errors' => [],
            'values' => $this->prepareGoalFormData($goal),
        ], $area);
    }

    public function create(string $area): void
    {
        $validation = $this->validateGoal($_POST);
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

        $goal = $this->goalFromValues($validation['values']);
        $id = $this->createGoal($goal);
        $this->redirect($area . '/goals/show', ['id' => $id]);
    }

    public function update(string $area): void
    {
        $id = (int) ($_GET['id'] ?? 0);
        $goal = $this->findGoalById($id);
        if ($goal === null) {
            $this->renderNotFound();
            return;
        }

        $validation = $this->validateGoal($_POST);
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

        $updatedGoal = $this->goalFromValues($validation['values'], $id);
        $this->updateGoal($updatedGoal);
        $this->redirect($area . '/goals/show', ['id' => $id]);
    }

    public function delete(string $area): void
    {
        $this->deleteGoal((int) ($_GET['id'] ?? 0));
        $this->redirect($area . '/goals');
    }
}
