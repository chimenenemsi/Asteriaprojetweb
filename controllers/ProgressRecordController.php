<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProgressDataTrait.php';

class ProgressRecordController extends BaseController
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
        
        $this->render('records/index', [
            'pageTitle' => 'Progress Records',
            'area' => $area,
            'currentSection' => 'records',
            'records' => $this->fetchAllRecords($userId),
        ], $area);
    }

    public function show(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $record = $this->findRecordById((int) ($_GET['id'] ?? 0), $userId);
        
        if ($record === null) {
            $this->renderNotFound();
            return;
        }

        $goal = $this->findGoalById((int) ($record->getProgressGoalId() ?? 0), $userId);
        if ($goal === null) {
            $this->renderNotFound();
            return;
        }

        $this->redirect($area . '/goals/show', ['id' => (int) ($goal->getId() ?? 0)]);
    }

    public function form(string $area, string $mode): void
    {
        $userId = $this->getUserIdForArea($area);
        $record = $mode === 'edit' ? $this->findRecordById((int) ($_GET['id'] ?? 0), $userId) : null;
        
        if ($mode === 'edit' && $record === null) {
            $this->renderNotFound();
            return;
        }

        $goalId = (int) ($_GET['goal_id'] ?? ($record?->getProgressGoalId() ?? 0));
        $defaultType = ($_GET['type'] ?? '') === 'REPORT' ? 'REPORT' : 'CHECKPOINT';
        $values = $record !== null
            ? $this->prepareRecordFormData($record, $goalId)
            : $this->defaultRecordFormData($goalId, $defaultType);

        $this->render('records/form', [
            'pageTitle' => $mode === 'edit' ? 'Edit Progress Record' : 'New Progress Record',
            'area' => $area,
            'currentSection' => 'records',
            'mode' => $mode,
            'record' => $record,
            'errors' => [],
            'goals' => $this->fetchAllGoals($userId),
            'values' => $values,
        ], $area);
    }

    public function create(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $validation = $this->validateRecord($_POST, $userId);
        
        if ($validation['errors'] !== []) {
            $this->render('records/form', [
                'pageTitle' => 'New Progress Record',
                'area' => $area,
                'currentSection' => 'records',
                'mode' => 'create',
                'errors' => $validation['errors'],
                'goals' => $this->fetchAllGoals($userId),
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $record = $this->recordFromValues($validation['values']);
        $this->createRecord($record);
        $this->redirect($area . '/goals/show', ['id' => (int) $validation['values']['progress_goal_id']]);
    }

    public function update(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $id = (int) ($_GET['id'] ?? 0);
        $record = $this->findRecordById($id, $userId);
        
        if ($record === null) {
            $this->renderNotFound();
            return;
        }

        $validation = $this->validateRecord($_POST, $userId);
        if ($validation['errors'] !== []) {
            $this->render('records/form', [
                'pageTitle' => 'Edit Progress Record',
                'area' => $area,
                'currentSection' => 'records',
                'mode' => 'edit',
                'record' => $record,
                'errors' => $validation['errors'],
                'goals' => $this->fetchAllGoals($userId),
                'values' => $validation['values'],
            ], $area);
            return;
        }

        $updatedRecord = $this->recordFromValues($validation['values'], $id);
        $this->updateRecord($updatedRecord);
        $this->redirect($area . '/goals/show', ['id' => (int) $validation['values']['progress_goal_id']]);
    }

    public function delete(string $area): void
    {
        $userId = $this->getUserIdForArea($area);
        $record = $this->findRecordById((int) ($_GET['id'] ?? 0), $userId);
        
        if ($record !== null) {
            $goalId = (int) ($record->getProgressGoalId() ?? 0);
            $this->deleteRecord((int) ($record->getId() ?? 0));
            $this->redirect($area . '/goals/show', ['id' => $goalId]);
        }

        $this->redirect($area . '/records');
    }
}
