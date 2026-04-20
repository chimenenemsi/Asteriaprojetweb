<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/ProgramModel.php';
require_once ROOT_PATH . '/models/ExerciseModel.php';

class BackofficeController extends BaseController
{
    private ProgramModel $programModel;
    private ExerciseModel $exerciseModel;

    public function __construct()
    {
        $connection = Database::connection();
        $this->programModel = new ProgramModel($connection);
        $this->exerciseModel = new ExerciseModel($connection, $this->programModel);
    }

    public function show(string $slug): void
    {
        if ($slug === 'dashboard') {
            $page = $this->getBackofficePage('dashboard');

            $this->render('backoffice/dashboard', [
                'pageTitle' => $page['title'] ?? 'Dashboard',
                'currentRoute' => 'backoffice/dashboard',
            ], 'raw');
            return;
        }

        if ($slug === 'programs') {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                $this->handleProgramsSubmission();
                return;
            }

            $this->showPrograms();
            return;
        }

        if ($slug === 'exercises') {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                $this->handleExercisesSubmission();
                return;
            }

            $this->showExercises();
            return;
        }

        $page = $this->getBackofficePage($slug);

        if ($page === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('backoffice/page', [
            'pageTitle' => $page['title'],
            'page' => $page,
            'navigation' => $this->getBackofficeNavigation(),
            'currentRoute' => 'backoffice/' . $slug,
        ], 'backoffice');
    }

    private function showPrograms(array $state = []): void
    {
        $flash = $state['flash'] ?? ($_SESSION['program_admin_flash'] ?? null);
        unset($_SESSION['program_admin_flash']);

        $dbError = $state['dbError'] ?? null;
        $programs = [];
        $stats = [
            'total_programs' => 0,
            'total_exercises' => 0,
            'total_weeks' => 0,
            'average_exercises' => 0,
        ];

        $selectedProgramId = isset($state['selectedProgramId'])
            ? (int) $state['selectedProgramId']
            : max(0, (int) ($_GET['program'] ?? 0));
        $editProgramId = isset($state['editProgramId'])
            ? (int) $state['editProgramId']
            : max(0, (int) ($_GET['editProgram'] ?? 0));

        $editingProgram = null;

        try {
            $programs = $this->programModel->findAll();
            $stats = $this->programModel->statistics();

            if ($selectedProgramId === 0 && $programs !== []) {
                $selectedProgramId = (int) ($programs[0]->getId() ?? 0);
            }

            if (isset($state['programForm'])) {
                $editingProgram = $state['programForm'];
            } elseif ($editProgramId > 0) {
                $program = $this->programModel->findById($editProgramId);
                $editingProgram = $program === null ? null : $this->programModel->prepareFormData($program);
            }
        } catch (Throwable $exception) {
            $dbError = $this->formatDatabaseError($exception);
        }

        $this->render('backoffice/programs', [
            'pageTitle' => 'Programs',
            'navigation' => $this->getBackofficeNavigation(),
            'currentRoute' => 'backoffice/programs',
            'programs' => $programs,
            'stats' => $stats,
            'selectedProgramId' => $selectedProgramId,
            'programForm' => $state['programForm'] ?? $this->programModel->prepareFormData($editingProgram),
            'programErrors' => $state['programErrors'] ?? [],
            'flash' => $flash,
            'dbError' => $dbError,
        ], 'backoffice');
    }

    private function showExercises(array $state = []): void
    {
        $flash = $state['flash'] ?? ($_SESSION['exercise_admin_flash'] ?? null);
        unset($_SESSION['exercise_admin_flash']);

        $dbError = $state['dbError'] ?? null;
        $programs = [];
        $selectedProgram = null;
        $selectedProgramId = isset($state['selectedProgramId'])
            ? (int) $state['selectedProgramId']
            : max(0, (int) ($_GET['program'] ?? 0));
        $editExerciseId = isset($state['editExerciseId'])
            ? (int) $state['editExerciseId']
            : max(0, (int) ($_GET['editExercise'] ?? 0));
        $editingExercise = null;

        try {
            $programs = $this->programModel->findAll();

            if (isset($state['exerciseForm']) && $selectedProgramId === 0) {
                $selectedProgramId = (int) ($state['exerciseForm']['program_id'] ?? 0);
            }

            if ($editExerciseId > 0 && !isset($state['exerciseForm'])) {
                $editingExercise = $this->exerciseModel->findById($editExerciseId);
                if ($editingExercise !== null && $selectedProgramId === 0) {
                    $selectedProgramId = (int) ($editingExercise->getProgramId() ?? 0);
                }
            }

            if ($selectedProgramId === 0 && $programs !== []) {
                $selectedProgramId = (int) ($programs[0]->getId() ?? 0);
            }

            if ($selectedProgramId > 0) {
                $selectedProgram = $this->programModel->findWithExercises($selectedProgramId, $this->exerciseModel);
            }

            if ($selectedProgram === null && $programs !== []) {
                $selectedProgramId = (int) ($programs[0]->getId() ?? 0);
                $selectedProgram = $this->programModel->findWithExercises($selectedProgramId, $this->exerciseModel);
            }
        } catch (Throwable $exception) {
            $dbError = $this->formatDatabaseError($exception);
        }

        $exerciseFormSource = $state['exerciseForm'] ?? $editingExercise;
        $exerciseForm = $this->exerciseModel->prepareFormData($exerciseFormSource, $selectedProgramId);

        $this->render('backoffice/exercises', [
            'pageTitle' => 'Exercises',
            'navigation' => $this->getBackofficeNavigation(),
            'currentRoute' => 'backoffice/exercises',
            'programs' => $programs,
            'selectedProgram' => $selectedProgram,
            'selectedProgramId' => $selectedProgramId,
            'exerciseForm' => $exerciseForm,
            'exerciseErrors' => $state['exerciseErrors'] ?? [],
            'flash' => $flash,
            'dbError' => $dbError,
        ], 'backoffice');
    }

    private function handleProgramsSubmission(): void
    {
        $action = trim((string) ($_POST['action'] ?? ''));

        try {
            if ($action === 'save-program') {
                $validation = $this->programModel->validate($_POST);

                if ($validation['errors'] !== []) {
                    $selectedProgramId = (int) ($validation['values']['id'] ?: 0);

                    $this->showPrograms([
                        'programForm' => $validation['values'],
                        'programErrors' => $validation['errors'],
                        'selectedProgramId' => $selectedProgramId,
                        'editProgramId' => $selectedProgramId,
                        'flash' => [
                            'type' => 'danger',
                            'message' => 'Please fix the program form before saving.',
                        ],
                    ]);
                    return;
                }

                $programId = (int) ($validation['values']['id'] ?: 0);
                $program = $this->programModel->entityFromValues($validation['values'], $programId > 0 ? $programId : null);

                if ($programId > 0) {
                    $this->programModel->update($program);
                    $savedProgramId = $programId;
                } else {
                    $savedProgramId = $this->programModel->create($program);
                }

                $_SESSION['program_admin_flash'] = [
                    'type' => 'success',
                    'message' => $programId > 0
                        ? 'Program updated successfully.'
                        : 'Program created successfully.',
                ];

                $this->redirect('backoffice/programs', ['program' => $savedProgramId]);
            }

            if ($action === 'delete-program') {
                $programId = max(0, (int) ($_POST['program_id'] ?? 0));

                if ($programId > 0) {
                    $this->programModel->delete($programId);
                    $_SESSION['program_admin_flash'] = [
                        'type' => 'success',
                        'message' => 'Program deleted successfully. Related exercises were removed automatically.',
                    ];
                }

                $this->redirect('backoffice/programs');
            }
        } catch (Throwable $exception) {
            $this->showPrograms([
                'programForm' => $this->programModel->prepareFormData($_POST),
                'selectedProgramId' => (int) ($_POST['program_id'] ?? 0),
                'flash' => [
                    'type' => 'danger',
                    'message' => 'The requested change could not be saved.',
                ],
                'dbError' => $this->formatDatabaseError($exception),
            ]);
            return;
        }

        $this->redirect('backoffice/programs');
    }

    private function handleExercisesSubmission(): void
    {
        $action = trim((string) ($_POST['action'] ?? ''));

        try {
            if ($action === 'save-exercise') {
                $validation = $this->exerciseModel->validate($_POST);

                if ($validation['errors'] !== []) {
                    $selectedProgramId = (int) ($validation['values']['program_id'] ?: 0);

                    $this->showExercises([
                        'exerciseForm' => $validation['values'],
                        'exerciseErrors' => $validation['errors'],
                        'selectedProgramId' => $selectedProgramId,
                        'editExerciseId' => (int) ($validation['values']['id'] ?: 0),
                        'flash' => [
                            'type' => 'danger',
                            'message' => 'Please fix the exercise form before saving.',
                        ],
                    ]);
                    return;
                }

                $exerciseId = (int) ($validation['values']['id'] ?: 0);
                $programId = (int) $validation['values']['program_id'];
                $exercise = $this->exerciseModel->entityFromValues($validation['values'], $exerciseId > 0 ? $exerciseId : null);

                if ($exerciseId > 0) {
                    $this->exerciseModel->update($exercise);
                } else {
                    $this->exerciseModel->create($exercise);
                }

                $_SESSION['exercise_admin_flash'] = [
                    'type' => 'success',
                    'message' => $exerciseId > 0
                        ? 'Exercise updated successfully.'
                        : 'Exercise created successfully.',
                ];

                $this->redirect('backoffice/exercises', ['program' => $programId]);
            }

            if ($action === 'delete-exercise') {
                $exerciseId = max(0, (int) ($_POST['exercise_id'] ?? 0));
                $programId = max(0, (int) ($_POST['program_id'] ?? 0));

                if ($exerciseId > 0) {
                    $this->exerciseModel->delete($exerciseId);
                    $_SESSION['exercise_admin_flash'] = [
                        'type' => 'success',
                        'message' => 'Exercise deleted successfully.',
                    ];
                }

                $this->redirect('backoffice/exercises', ['program' => $programId]);
            }
        } catch (Throwable $exception) {
            $this->showExercises([
                'exerciseForm' => $this->exerciseModel->prepareFormData($_POST, (int) ($_POST['program_id'] ?? 0)),
                'selectedProgramId' => (int) ($_POST['program_id'] ?? 0),
                'flash' => [
                    'type' => 'danger',
                    'message' => 'The requested change could not be saved.',
                ],
                'dbError' => $this->formatDatabaseError($exception),
            ]);
            return;
        }

        $this->redirect('backoffice/exercises');
    }

    private function formatDatabaseError(Throwable $exception): string
    {
        return 'Database connection or query failed for the Asteria MySQL module: ' . $exception->getMessage();
    }
}
