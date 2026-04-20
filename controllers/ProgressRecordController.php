<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProgressDataTrait.php';

class ProgressRecordController extends BaseController
{
    use ProgressDataTrait;

    public function index(string $area): void
    {
        $this->render('records/index', [
            'pageTitle' => 'Progress Records',
            'area' => $area,
            'currentSection' => 'records',
            'records' => $this->fetchAllRecords(),
        ], $area);
    }

    public function show(string $area): void
    {
        $record = $this->findRecordById((int) ($_GET['id'] ?? 0));
        if ($record === null) {
            $this->renderNotFound();
            return;
        }

        $goal = $this->findGoalById((int) ($record->getProgressGoalId() ?? 0));
        if ($goal === null) {
            $this->renderNotFound();
            return;
        }

        $this->redirect($area . '/goals/show', ['id' => (int) ($goal->getId() ?? 0)]);
    }

    public function form(string $area, string $mode): void
    {
        $record = $mode === 'edit' ? $this->findRecordById((int) ($_GET['id'] ?? 0)) : null;
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
            'goals' => $this->fetchAllGoals(),
            'values' => $values,
        ], $area);
    }

    public function create(string $area): void
    {
        $validation = $this->validateRecord($_POST);
        if ($validation['errors'] !== []) {
            $this->render('records/form', [
                'pageTitle' => 'New Progress Record',
                'area' => $area,
                'currentSection' => 'records',
                'mode' => 'create',
                'errors' => $validation['errors'],
                'goals' => $this->fetchAllGoals(),
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
        $id = (int) ($_GET['id'] ?? 0);
        $record = $this->findRecordById($id);
        if ($record === null) {
            $this->renderNotFound();
            return;
        }

        $validation = $this->validateRecord($_POST);
        if ($validation['errors'] !== []) {
            $this->render('records/form', [
                'pageTitle' => 'Edit Progress Record',
                'area' => $area,
                'currentSection' => 'records',
                'mode' => 'edit',
                'record' => $record,
                'errors' => $validation['errors'],
                'goals' => $this->fetchAllGoals(),
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
        $record = $this->findRecordById((int) ($_GET['id'] ?? 0));
        if ($record !== null) {
            $goalId = (int) ($record->getProgressGoalId() ?? 0);
            $this->deleteRecord((int) ($record->getId() ?? 0));
            $this->redirect($area . '/goals/show', ['id' => $goalId]);
        }

        $this->redirect($area . '/records');
    }
}
