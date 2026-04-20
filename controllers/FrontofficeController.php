<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/models/BaseModel.php';
require_once ROOT_PATH . '/models/ProgramModel.php';
require_once ROOT_PATH . '/models/ExerciseModel.php';

class FrontofficeController extends BaseController
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
        if ($slug === 'about-us') {
            $this->render('frontoffice/about-us', [
                'pageTitle' => 'About Us',
                'currentRoute' => 'about-us',
            ], 'raw');
            return;
        }

        if ($slug === 'programs') {
            $this->showPrograms();
            return;
        }

        $page = $this->getFrontofficePage($slug);

        if ($page === null) {
            $this->renderNotFound();
            return;
        }

        $this->render('frontoffice/page', [
            'pageTitle' => $page['title'],
            'page' => $page,
            'navigation' => $this->getFrontofficeNavigation(),
            'currentRoute' => 'frontoffice/' . $slug,
        ], 'frontoffice');
    }

    private function showPrograms(): void
    {
        $programs = [];
        $dbError = null;

        try {
            $programs = $this->programModel->findAllWithExercises($this->exerciseModel);
        } catch (Throwable $exception) {
            $dbError = 'Programs are temporarily unavailable: ' . $exception->getMessage();
        }

        $this->render('frontoffice/programs', [
            'pageTitle' => 'Programs',
            'navigation' => $this->getFrontofficeNavigation(),
            'currentRoute' => 'frontoffice/programs',
            'programs' => $programs,
            'dbError' => $dbError,
        ], 'frontoffice');
    }
}
