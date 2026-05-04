<?php
declare(strict_types=1);

require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/models/Program.php';
require_once ROOT_PATH . '/models/Exercise.php';

class FrontofficeController extends BaseController
{
    public function show(string $slug): void
    {
        if ($slug === 'about-us') {
            $page = $this->getFrontofficePage('about-us') ?? [
                'title' => 'About Us',
                'eyebrow' => 'About',
                'intro' => 'Asteria coaching and nutrition support.',
                'summary' => 'Native PHP page using the local frontoffice assets.',
                'image' => 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg',
                'cards' => [],
            ];
            $this->render('frontoffice/about-us', [
                'pageTitle' => $page['title'],
                'page' => $page,
                'navigation' => $this->getFrontofficeNavigation(),
                'currentRoute' => 'about-us',
            ], 'frontoffice');
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
        $filters = $this->programFilters($_GET);

        try {
            $programs = $this->fetchAllProgramsWithExercises($filters);
        } catch (Throwable $exception) {
            $dbError = 'Programs are temporarily unavailable: ' . $exception->getMessage();
        }

        $this->render('frontoffice/programs', [
            'pageTitle' => 'Programs',
            'navigation' => $this->getFrontofficeNavigation(),
            'currentRoute' => 'frontoffice/programs',
            'programs' => $programs,
            'programFilters' => $filters,
            'dbError' => $dbError,
        ], 'frontoffice');
    }

    /**
     * @return Program[]
     */
    private function fetchAllPrograms(): array
    {
        $statement = $this->connection()->query(
            'SELECT p.id, p.title, p.goal_type, p.duration_weeks, p.description, COUNT(e.id) AS exercise_count
            FROM programs p
            LEFT JOIN exercises e ON e.program_id = p.id
            GROUP BY p.id, p.title, p.goal_type, p.duration_weeks, p.description
            ORDER BY p.id DESC'
        );

        return array_map([$this, 'mapProgramRow'], $statement->fetchAll());
    }

    /**
     * @return Program[]
     */
    private function fetchAllProgramsWithExercises(array $filters = []): array
    {
        $programs = $this->fetchAllPrograms();

        if ($programs === []) {
            return [];
        }

        $programIds = array_map(
            static fn(Program $program): int => (int) ($program->getId() ?? 0),
            $programs
        );
        $groupedExercises = $this->groupExercisesByProgramIds($programIds);

        foreach ($programs as $program) {
            $programId = (int) ($program->getId() ?? 0);
            $program->setExercises($groupedExercises[$programId] ?? []);
        }

        return $this->filterPrograms($programs, $filters);
    }

    /**
     * @param int[] $programIds
     * @return array<int, Exercise[]>
     */
    private function groupExercisesByProgramIds(array $programIds): array
    {
        $programIds = array_values(array_unique(array_map('intval', $programIds)));

        if ($programIds === []) {
            return [];
        }

        $placeholders = implode(', ', array_fill(0, count($programIds), '?'));
        $statement = $this->connection()->prepare(
            "SELECT id, program_id, name, description, muscle_group, sets, reps, rest_seconds
            FROM exercises
            WHERE program_id IN ($placeholders)
            ORDER BY program_id ASC, id ASC"
        );
        $statement->execute($programIds);

        $grouped = [];

        foreach ($statement->fetchAll() as $row) {
            $exercise = $this->mapExerciseRow($row);
            $grouped[(int) ($exercise->getProgramId() ?? 0)][] = $exercise;
        }

        return $grouped;
    }

    private function mapProgramRow(array $row): Program
    {
        return new Program(
            (int) $row['id'],
            (string) $row['title'],
            $row['goal_type'] !== null ? (string) $row['goal_type'] : null,
            (int) $row['duration_weeks'],
            $row['description'] !== null ? (string) $row['description'] : null,
            (int) ($row['exercise_count'] ?? 0)
        );
    }

    private function mapExerciseRow(array $row): Exercise
    {
        return new Exercise(
            (int) $row['id'],
            (int) $row['program_id'],
            (string) $row['name'],
            $row['description'] !== null ? (string) $row['description'] : null,
            $row['muscle_group'] !== null ? (string) $row['muscle_group'] : null,
            (int) $row['sets'],
            (int) $row['reps'],
            (int) $row['rest_seconds']
        );
    }

    private function programFilters(array $source): array
    {
        return [
            'search' => trim((string) ($source['search'] ?? '')),
            'goal_type' => trim((string) ($source['goal_type'] ?? '')),
            'sort' => trim((string) ($source['sort'] ?? 'created_desc')),
        ];
    }

    /**
     * @param Program[] $programs
     * @return Program[]
     */
    private function filterPrograms(array $programs, array $filters): array
    {
        $filtered = array_values(array_filter($programs, static function (Program $program) use ($filters): bool {
            $search = mb_strtolower(trim((string) ($filters['search'] ?? '')));
            $goalType = mb_strtolower(trim((string) ($filters['goal_type'] ?? '')));

            if ($search !== '') {
                $exerciseText = '';
                foreach ($program->getExercises() as $exercise) {
                    $exerciseText .= ' ' . $exercise->getName() . ' ' . (string) $exercise->getDescription();
                }

                $haystack = mb_strtolower(
                    $program->getTitle()
                    . ' '
                    . (string) ($program->getDescription() ?? '')
                    . ' '
                    . (string) ($program->getGoalType() ?? '')
                    . $exerciseText
                );

                if (!str_contains($haystack, $search)) {
                    return false;
                }
            }

            if ($goalType !== '') {
                $programGoalType = mb_strtolower((string) ($program->getGoalType() ?? ''));
                if (!str_contains($programGoalType, $goalType)) {
                    return false;
                }
            }

            return true;
        }));

        $sort = $filters['sort'] ?? 'created_desc';
        usort($filtered, static function (Program $left, Program $right) use ($sort): int {
            return match ($sort) {
                'title_asc' => strcasecmp($left->getTitle(), $right->getTitle()),
                'title_desc' => strcasecmp($right->getTitle(), $left->getTitle()),
                'weeks_asc' => $left->getDurationWeeks() <=> $right->getDurationWeeks(),
                'weeks_desc' => $right->getDurationWeeks() <=> $left->getDurationWeeks(),
                default => ((int) ($right->getId() ?? 0)) <=> ((int) ($left->getId() ?? 0)),
            };
        });

        return $filtered;
    }
}
