<?php
declare(strict_types=1);

require_once ROOT_PATH . '/models/ProgramModel.php';

class FrontofficeController extends BaseController
{
    public function show(string $slug): void
    {
        if ($slug === 'programs') {
            $this->programs();
            return;
        }

        $page = $this->getFrontofficePage($slug);
        if (!$page) {
            $this->renderNotFound();
            return;
        }

        $this->render('frontoffice/page', [
            'pageTitle' => $page['title'],
            'page' => $page,
            'area' => 'frontoffice',
            'currentSection' => $slug,
        ], 'frontoffice');
    }

    private function programs(): void
    {
        $model = new ProgramModel();
        $filters = [
            'search' => trim((string) ($_GET['search'] ?? '')),
            'goal_type' => trim((string) ($_GET['goal_type'] ?? '')),
            'sort' => trim((string) ($_GET['sort'] ?? 'created_desc')),
        ];

        // For now we fetch all and filter in memory to keep it simple, 
        // or we could update ProgramModel::findAll to accept filters.
        $programs = $model->findAll();

        if ($filters['search'] !== '' || $filters['goal_type'] !== '') {
            $programs = array_filter($programs, function($p) use ($filters) {
                $match = true;
                if ($filters['search'] !== '') {
                    $search = strtolower($filters['search']);
                    $match = str_contains(strtolower($p->getTitle()), $search) || 
                            str_contains(strtolower($p->getDescription() ?? ''), $search);
                }
                if ($match && $filters['goal_type'] !== '') {
                    $match = str_contains(strtolower($p->getGoalType() ?? ''), strtolower($filters['goal_type']));
                }
                return $match;
            });
        }

        // Sorting
        usort($programs, function($a, $b) use ($filters) {
            return match($filters['sort']) {
                'title_asc' => $a->getTitle() <=> $b->getTitle(),
                'title_desc' => $b->getTitle() <=> $a->getTitle(),
                'weeks_asc' => $a->getDurationWeeks() <=> $b->getDurationWeeks(),
                'weeks_desc' => $b->getDurationWeeks() <=> $a->getDurationWeeks(),
                default => $b->getId() <=> $a->getId(),
            };
        });

        // Fetch and attach exercises for each program
        if ($programs !== []) {
            $programIds = array_map(fn($p) => $p->getId(), $programs);
            $exercises = $this->fetchExercisesForPrograms($programIds);
            
            foreach ($programs as $program) {
                $programExercises = array_filter($exercises, fn($e) => $e->getProgramId() === $program->getId());
                $program->setExercises(array_values($programExercises));
            }
        }

        $this->render('frontoffice/programs', [
            'pageTitle' => 'Coaching Programs',
            'programs' => $programs,
            'programFilters' => $filters,
            'area' => 'frontoffice',
            'currentSection' => 'programs',
        ], 'frontoffice');
    }

    private function fetchExercisesForPrograms(array $programIds): array
    {
        if (empty($programIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($programIds), '?'));
        $sql = "SELECT * FROM exercises WHERE program_id IN ($placeholders) ORDER BY id ASC";
        
        $model = new ProgramModel(); // Reusing the model to get connection
        $statement = $model->connection()->prepare($sql);
        $statement->execute($programIds);
        
        $exercises = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $exercises[] = new Exercise(
                (int) $row['id'],
                (int) $row['program_id'],
                (string) $row['name'],
                $row['description'],
                $row['muscle_group'],
                (int) $row['sets'],
                (int) $row['reps'],
                (int) $row['rest_seconds']
            );
        }
        
        return $exercises;
    }
}
