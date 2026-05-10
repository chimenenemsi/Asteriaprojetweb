<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/BackofficeDataService.php';
require_once ROOT_PATH . '/controllers/ExerciseCatalogService.php';

class BackofficeController extends BaseController
{
    private BackofficeDataService $data;
    private ExerciseCatalogService $catalog;

    public function __construct()
    {
        $this->data = new BackofficeDataService();
        $this->catalog = new ExerciseCatalogService();
    }

    public function show(string $slug): void
    {
        if ($slug === 'dashboard') {
            $page = $this->getBackofficePage('dashboard');
            $this->render('backoffice/dashboard', [
                'pageTitle' => $page['title'] ?? 'Dashboard',
                'page' => $page,
                'navigation' => $this->getBackofficeNavigation(),
                'currentRoute' => 'backoffice/dashboard',
            ], 'backoffice');
            return;
        }

        if ($slug === 'programs') {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                $this->handleProgramsSubmission();
            } else {
                $this->showPrograms();
            }
            return;
        }

        if ($slug === 'programs-pdf') {
            $this->exportProgramsPdf();
            return;
        }

        if ($slug === 'exercises') {
            if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
                $this->handleExercisesSubmission();
            } else {
                $this->showExercises();
            }
            return;
        }

        if ($slug === 'exercises-pdf') {
            $this->downloadExerciseSearchPdf();
            return;
        }

        if ($slug === 'exercises-list-pdf') {
            $this->exportExercisesPdf();
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
        $programFilters = $this->data->programFilters($_GET);

        try {
            $programs = $this->data->fetchAllPrograms($programFilters);
            $stats = $this->data->programStatistics();

            if ($selectedProgramId === 0 && $programs !== []) {
                $selectedProgramId = (int) ($programs[0]->getId() ?? 0);
            }

            if (isset($state['programForm'])) {
                $editingProgram = $state['programForm'];
            } elseif ($editProgramId > 0) {
                $program = $this->data->findProgramById($editProgramId);
                $editingProgram = $program === null ? null : $this->data->prepareProgramFormData($program);
            }
        } catch (Throwable $exception) {
            $dbError = $this->data->formatDatabaseError($exception);
        }

        $this->render('backoffice/programs', [
            'pageTitle' => 'Programs',
            'navigation' => $this->getBackofficeNavigation(),
            'currentRoute' => 'backoffice/programs',
            'programs' => $programs,
            'stats' => $stats,
            'programFilters' => $programFilters,
            'selectedProgramId' => $selectedProgramId,
            'programForm' => $state['programForm'] ?? $this->data->prepareProgramFormData($editingProgram),
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
        $exerciseFilters = $this->data->exerciseFilters($_GET);

        try {
            $programs = $this->data->fetchAllPrograms();

            if (isset($state['exerciseForm']) && $selectedProgramId === 0) {
                $selectedProgramId = (int) ($state['exerciseForm']['program_id'] ?? 0);
            }

            if ($editExerciseId > 0 && !isset($state['exerciseForm'])) {
                $editingExercise = $this->data->findExerciseById($editExerciseId);
                if ($editingExercise !== null && $selectedProgramId === 0) {
                    $selectedProgramId = (int) ($editingExercise->getProgramId() ?? 0);
                }
            }

            if ($selectedProgramId === 0 && $programs !== []) {
                $selectedProgramId = (int) ($programs[0]->getId() ?? 0);
            }

            if ($selectedProgramId > 0) {
                $selectedProgram = $this->data->findProgramWithExercises($selectedProgramId);
                if ($selectedProgram !== null) {
                    $selectedProgram->setExercises($this->data->filterExercises($selectedProgram->getExercises(), $exerciseFilters));
                }
            }

            if ($selectedProgram === null && $programs !== []) {
                $selectedProgramId = (int) ($programs[0]->getId() ?? 0);
                $selectedProgram = $this->data->findProgramWithExercises($selectedProgramId);
            }
        } catch (Throwable $exception) {
            $dbError = $this->data->formatDatabaseError($exception);
        }

        $exerciseFormSource = $state['exerciseForm'] ?? $editingExercise;
        $exerciseSearchForm = $state['exerciseSearchForm'] ?? $this->catalog->prepareFormData($_GET);
        $exerciseSearchResults = $state['exerciseSearchResults'] ?? [];
        $exerciseSearchError = $state['exerciseSearchError'] ?? null;

        if (!isset($state['exerciseSearchResults']) && $this->catalog->hasFilters($exerciseSearchForm)) {
            try {
                $exerciseSearchResults = $this->catalog->searchExercises($exerciseSearchForm);
            } catch (Throwable $exception) {
                $exerciseSearchError = 'The exercise search service is unavailable right now. ' . $exception->getMessage();
            }
        }

        $this->render('backoffice/exercises', [
            'pageTitle' => 'Exercises',
            'navigation' => $this->getBackofficeNavigation(),
            'currentRoute' => 'backoffice/exercises',
            'programs' => $programs,
            'selectedProgram' => $selectedProgram,
            'selectedProgramId' => $selectedProgramId,
            'exerciseForm' => $this->data->prepareExerciseFormData($exerciseFormSource, $selectedProgramId),
            'exerciseErrors' => $state['exerciseErrors'] ?? [],
            'exerciseFilters' => $exerciseFilters,
            'exerciseSearchForm' => $exerciseSearchForm,
            'exerciseSearchResults' => $exerciseSearchResults,
            'exerciseSearchError' => $exerciseSearchError,
            'exerciseStats' => $this->data->exerciseStatistics($selectedProgram, $selectedProgram?->getExercises() ?? []),
            'flash' => $flash,
            'dbError' => $dbError,
        ], 'backoffice');
    }

    private function handleProgramsSubmission(): void
    {
        $action = trim((string) ($_POST['action'] ?? ''));

        try {
            if ($action === 'save-program') {
                $validation = $this->data->validateProgram($_POST);

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
                $savedProgramId = $this->data->saveProgram($validation['values']);
                $_SESSION['program_admin_flash'] = [
                    'type' => 'success',
                    'message' => $programId > 0 ? 'Program updated successfully.' : 'Program created successfully.',
                ];
                $this->redirect('backoffice/programs', ['program' => $savedProgramId]);
            }

            if ($action === 'delete-program') {
                $programId = max(0, (int) ($_POST['program_id'] ?? 0));
                if ($programId > 0) {
                    $this->data->deleteProgram($programId);
                    $_SESSION['program_admin_flash'] = [
                        'type' => 'success',
                        'message' => 'Program deleted successfully. Related exercises were removed automatically.',
                    ];
                }
                $this->redirect('backoffice/programs');
            }
        } catch (Throwable $exception) {
            $this->showPrograms([
                'programForm' => $this->data->prepareProgramFormData($_POST),
                'selectedProgramId' => (int) ($_POST['program_id'] ?? 0),
                'flash' => [
                    'type' => 'danger',
                    'message' => 'The requested change could not be saved.',
                ],
                'dbError' => $this->data->formatDatabaseError($exception),
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
                $validation = $this->data->validateExercise($_POST);

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
                $programId = $this->data->saveExercise($validation['values']);
                $_SESSION['exercise_admin_flash'] = [
                    'type' => 'success',
                    'message' => $exerciseId > 0 ? 'Exercise updated successfully.' : 'Exercise created successfully.',
                ];
                $this->redirect('backoffice/exercises', ['program' => $programId]);
            }

            if ($action === 'delete-exercise') {
                $exerciseId = max(0, (int) ($_POST['exercise_id'] ?? 0));
                $programId = max(0, (int) ($_POST['program_id'] ?? 0));
                if ($exerciseId > 0) {
                    $this->data->deleteExercise($exerciseId);
                    $_SESSION['exercise_admin_flash'] = [
                        'type' => 'success',
                        'message' => 'Exercise deleted successfully.',
                    ];
                }
                $this->redirect('backoffice/exercises', ['program' => $programId]);
            }
        } catch (Throwable $exception) {
            $this->showExercises([
                'exerciseForm' => $this->data->prepareExerciseFormData($_POST, (int) ($_POST['program_id'] ?? 0)),
                'selectedProgramId' => (int) ($_POST['program_id'] ?? 0),
                'flash' => [
                    'type' => 'danger',
                    'message' => 'The requested change could not be saved.',
                ],
                'dbError' => $this->data->formatDatabaseError($exception),
            ]);
            return;
        }

        $this->redirect('backoffice/exercises');
    }

    private function exportProgramsPdf(): void
    {
        $filters = $this->data->programFilters($_GET);
        $programs = $this->data->fetchAllPrograms($filters);
        $this->downloadSimplePdf('coaching-programs.pdf', $this->data->buildProgramsExportLines($programs, $filters));
    }

    private function exportExercisesPdf(): void
    {
        $programId = max(0, (int) ($_GET['program'] ?? 0));
        $program = $programId > 0 ? $this->data->findProgramWithExercises($programId) : null;

        if ($program === null) {
            $this->renderNotFound();
            return;
        }

        $filters = $this->data->exerciseFilters($_GET);
        $exercises = $this->data->filterExercises($program->getExercises(), $filters);
        $this->downloadSimplePdf('coaching-exercises.pdf', $this->data->buildExercisesExportLines($program, $exercises, $filters));
    }

    private function downloadExerciseSearchPdf(): void
    {
        $token = trim((string) ($_GET['token'] ?? ''));
        $exercise = $token !== '' ? $this->catalog->findByToken($token) : null;

        if ($exercise === null) {
            $this->renderNotFound();
            return;
        }

        $fileName = preg_replace('/[^A-Za-z0-9_-]+/', '-', strtolower((string) $exercise['name'])) ?: 'exercise-guide';
        $this->downloadSimplePdf($fileName . '.pdf', $this->catalog->buildPdfLines($exercise));
    }
}
