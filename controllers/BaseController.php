<?php
declare(strict_types=1);

class BaseController
{
    public function render(string $view, array $data = [], string $area = 'frontoffice'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require ROOT_PATH . '/views/' . $view . '.php';
        $content = (string) ob_get_clean();

        require ROOT_PATH . '/views/layouts/' . $area . '.php';
    }

    protected function redirect(string $route, array $params = []): void
    {
        header('Location: ' . route_url($route, $params));
        exit;
    }

    protected function connection(): PDO
    {
        return Database::connection();
    }

    protected function executeNamed(PDOStatement $statement, array $params = []): bool
    {
        return $statement->execute($this->namedParams($params));
    }

    protected function downloadSimplePdf(string $fileName, array $lines): void
    {
        $pdf = $this->buildSimplePdfDocument($lines);
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $fileName . '"');
        header('Content-Length: ' . (string) strlen($pdf));
        echo $pdf;
        exit;
    }

    /**
     * @param string[] $lines
     */
    protected function buildSimplePdfDocument(array $lines): string
    {
        [$title, $metaLines, $bodyLines] = $this->pdfSections($lines);
        $pageStreams = $this->buildPdfPageStreams($title, $metaLines, $bodyLines);

        return $this->compilePdf($pageStreams);
    }

    /**
     * @return string[]
     */
    private function wrapPdfText(string $text, int $length): array
    {
        $ascii = function_exists('iconv')
            ? (string) iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text)
            : $text;
        $normalized = preg_replace('/\s+/', ' ', trim($ascii)) ?: '';

        return $normalized === '' ? [' '] : explode("\n", wordwrap($normalized, $length, "\n", true));
    }

    private function pdfEscape(string $text): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
    }

    private function namedParams(array $params): array
    {
        $bound = [];
        foreach ($params as $key => $value) {
            if (is_int($key)) {
                $bound[$key] = $value;
                continue;
            }

            $bound[':' . ltrim((string) $key, ':')] = $value;
        }

        return $bound;
    }

    /**
     * @param string[] $lines
     * @return array{0:string,1:array<int,string>,2:array<int,string>}
     */
    private function pdfSections(array $lines): array
    {
        $normalized = array_map(static fn ($line): string => trim((string) $line), $lines);
        $title = $normalized[0] !== '' ? $normalized[0] : 'Export';
        $metaLines = [];
        $bodyLines = [];
        $bodyStarted = false;

        foreach (array_slice($normalized, 1) as $line) {
            if ($line === '') {
                if ($metaLines !== []) {
                    $bodyStarted = true;
                }
                continue;
            }

            if (!$bodyStarted && preg_match('/^[A-Za-z][A-Za-z ]+:/', $line) === 1) {
                $metaLines[] = $line;
                continue;
            }

            $bodyStarted = true;
            $bodyLines[] = $line;
        }

        if ($bodyLines === []) {
            $bodyLines[] = 'No rows available for the selected filters.';
        }

        return [$title, $metaLines, $bodyLines];
    }

    /**
     * @param string[] $metaLines
     * @param string[] $bodyLines
     * @return string[]
     */
    private function buildPdfPageStreams(string $title, array $metaLines, array $bodyLines): array
    {
        $pageWidth = 595.0;
        $pageHeight = 842.0;
        $margin = 40.0;
        $contentWidth = $pageWidth - ($margin * 2);
        $top = $pageHeight - $margin;
        $bottom = 46.0;
        $pageStreams = [];
        $pageIndex = 0;
        $currentY = $top;
        $stream = '';

        $startPage = function (bool $includeMeta) use (
            &$stream,
            &$currentY,
            &$pageIndex,
            $title,
            $metaLines,
            $pageHeight,
            $margin,
            $contentWidth,
            $top
        ): void {
            $pageIndex++;
            $currentY = $top;
            $stream = '';
            $subtitle = $pageIndex === 1 ? 'Catalog export overview' : 'Catalog export overview (continued)';

            $stream .= $this->pdfRect($margin, $pageHeight - 122.0, $contentWidth, 82.0, [0.09, 0.20, 0.35], null, 'f');
            $stream .= $this->pdfTextLine($margin + 24.0, $pageHeight - 76.0, 'F2', 23.0, $title, [1.0, 1.0, 1.0]);
            $stream .= $this->pdfTextLine($margin + 24.0, $pageHeight - 101.0, 'F1', 11.0, $subtitle, [0.87, 0.92, 0.98]);
            $stream .= $this->pdfTextLine($margin + $contentWidth - 128.0, $pageHeight - 76.0, 'F1', 10.0, date('Y-m-d H:i'), [0.87, 0.92, 0.98]);
            $currentY = $pageHeight - 146.0;

            if ($includeMeta && $metaLines !== []) {
                $wrappedMeta = [];
                foreach ($metaLines as $metaLine) {
                    foreach ($this->wrapPdfText($metaLine, 76) as $wrappedLine) {
                        $wrappedMeta[] = $wrappedLine;
                    }
                }

                $metaHeight = 18.0 + (count($wrappedMeta) * 16.0);
                $stream .= $this->pdfRect($margin, $currentY - $metaHeight, $contentWidth, $metaHeight, [0.95, 0.97, 0.99], [0.82, 0.88, 0.94], 'B');
                $stream .= $this->pdfTextLine($margin + 18.0, $currentY - 20.0, 'F2', 11.0, 'Filters and context', [0.18, 0.28, 0.40]);
                $metaTextY = $currentY - 40.0;
                foreach ($wrappedMeta as $wrappedLine) {
                    $stream .= $this->pdfTextLine($margin + 18.0, $metaTextY, 'F1', 10.5, $wrappedLine, [0.30, 0.36, 0.45]);
                    $metaTextY -= 15.0;
                }

                $currentY -= $metaHeight + 18.0;
            }
        };

        $finalizePage = function () use (&$stream, &$pageStreams): void {
            $pageStreams[] = $stream;
        };

        $startPage(true);

        foreach ($bodyLines as $index => $line) {
            $wrapped = $this->wrapPdfText($line, 72);
            $rowHeight = 24.0 + (count($wrapped) * 15.0);

            if ($currentY - $rowHeight < $bottom) {
                $finalizePage();
                $startPage(false);
            }

            $rowY = $currentY - $rowHeight;
            $fillColor = $index % 2 === 0 ? [1.0, 1.0, 1.0] : [0.98, 0.99, 1.0];
            $stream .= $this->pdfRect($margin, $rowY, $contentWidth, $rowHeight, $fillColor, [0.88, 0.91, 0.95], 'B');
            $stream .= $this->pdfTextLine($margin + 16.0, $rowY + $rowHeight - 18.0, 'F2', 10.0, 'Record ' . (string) ($index + 1), [0.20, 0.32, 0.45]);

            $textY = $rowY + $rowHeight - 36.0;
            foreach ($wrapped as $wrappedLine) {
                $stream .= $this->pdfTextLine($margin + 16.0, $textY, 'F1', 10.5, $wrappedLine, [0.16, 0.20, 0.27]);
                $textY -= 14.0;
            }

            $currentY = $rowY - 10.0;
        }

        $finalizePage();

        $totalPages = count($pageStreams);
        foreach ($pageStreams as $pageNumber => $pageStream) {
            $pageStreams[$pageNumber] = $pageStream
                . $this->pdfLine($margin, 34.0, $pageWidth - $margin, 34.0, [0.82, 0.88, 0.94])
                . $this->pdfTextLine($margin, 22.0, 'F1', 9.0, 'Generated by Coaching export', [0.48, 0.54, 0.63])
                . $this->pdfTextLine($pageWidth - 94.0, 22.0, 'F1', 9.0, 'Page ' . (string) ($pageNumber + 1) . ' / ' . (string) $totalPages, [0.48, 0.54, 0.63]);
        }

        return $pageStreams;
    }

    /**
     * @param string[] $pageStreams
     */
    private function compilePdf(array $pageStreams): string
    {
        $catalogId = 1;
        $pagesId = 2;
        $fontRegularId = 3;
        $fontBoldId = 4;
        $nextId = 5;
        $pageIds = [];
        $objects = [];

        $objects[$catalogId] = '<< /Type /Catalog /Pages ' . $pagesId . ' 0 R >>';
        $objects[$fontRegularId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>';
        $objects[$fontBoldId] = '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>';

        foreach ($pageStreams as $pageStream) {
            $contentId = $nextId++;
            $pageId = $nextId++;

            $objects[$contentId] = '<< /Length ' . strlen($pageStream) . " >>\nstream\n" . $pageStream . "\nendstream";
            $objects[$pageId] = '<< /Type /Page /Parent ' . $pagesId . ' 0 R /MediaBox [0 0 595 842] '
                . '/Resources << /Font << /F1 ' . $fontRegularId . ' 0 R /F2 ' . $fontBoldId . ' 0 R >> >> '
                . '/Contents ' . $contentId . ' 0 R >>';
            $pageIds[] = $pageId;
        }

        $objects[$pagesId] = '<< /Type /Pages /Kids [' . implode(' ', array_map(
            static fn (int $pageId): string => $pageId . ' 0 R',
            $pageIds
        )) . '] /Count ' . count($pageIds) . ' >>';

        ksort($objects);
        $pdf = "%PDF-1.4\n";
        $offsets = [0];

        foreach ($objects as $id => $object) {
            $offsets[$id] = strlen($pdf);
            $pdf .= $id . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";

        $maxObjectId = max(array_keys($objects));
        for ($id = 1; $id <= $maxObjectId; $id++) {
            $offset = $offsets[$id] ?? 0;
            $pdf .= str_pad((string) $offset, 10, '0', STR_PAD_LEFT) . " 00000 n \n";
        }

        $pdf .= "trailer\n<< /Size " . ($maxObjectId + 1) . ' /Root ' . $catalogId . " 0 R >>\n";
        $pdf .= "startxref\n" . $xrefPosition . "\n%%EOF";

        return $pdf;
    }

    /**
     * @param array<int,float>|null $strokeColor
     * @param array<int,float> $fillColor
     */
    private function pdfRect(float $x, float $y, float $width, float $height, array $fillColor, ?array $strokeColor, string $paintOperator): string
    {
        $commands = "q\n";
        $commands .= $this->pdfColorCommand('rg', $fillColor) . "\n";
        if ($strokeColor !== null) {
            $commands .= $this->pdfColorCommand('RG', $strokeColor) . "\n";
            $commands .= "0.8 w\n";
        }
        $commands .= sprintf("%.2F %.2F %.2F %.2F re\n%s\nQ\n", $x, $y, $width, $height, $paintOperator);

        return $commands;
    }

    /**
     * @param array<int,float> $color
     */
    private function pdfLine(float $x1, float $y1, float $x2, float $y2, array $color): string
    {
        return "q\n"
            . $this->pdfColorCommand('RG', $color) . "\n"
            . "0.8 w\n"
            . sprintf("%.2F %.2F m\n%.2F %.2F l\nS\nQ\n", $x1, $y1, $x2, $y2);
    }

    /**
     * @param array<int,float> $color
     */
    private function pdfTextLine(float $x, float $y, string $font, float $size, string $text, array $color): string
    {
        return "BT\n"
            . $this->pdfColorCommand('rg', $color) . "\n"
            . '/' . $font . ' ' . number_format($size, 2, '.', '') . " Tf\n"
            . sprintf("1 0 0 1 %.2F %.2F Tm\n", $x, $y)
            . '(' . $this->pdfEscape($text) . ") Tj\nET\n";
    }

    /**
     * @param array<int,float> $color
     */
    private function pdfColorCommand(string $operator, array $color): string
    {
        return implode(' ', array_map(
            static fn (float $value): string => number_format($value, 3, '.', ''),
            $color
        )) . ' ' . $operator;
    }

    public function renderNotFound(): void
    {
        http_response_code(404);

        $this->render('errors/not-found', [
            'pageTitle' => 'Page Not Found',
        ]);
    }

    protected function getFrontofficeNavigation(): array
    {
        return [
            'home' => 'Home',
            'about-us' => 'About',
            'programs' => 'Programs',
        ];
    }

    protected function getBackofficeNavigation(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'user-management' => 'User Management',
            'meal-planning' => 'Meal Planning',
            'progress-tracking' => 'Progress Tracking',
            'nutrition-blog' => 'Nutrition Blog',
            'programs' => 'Programs',
            'exercises' => 'Exercises',
        ];
    }

    protected function getFrontofficePage(string $slug): ?array
    {
        $pages = [
            'about-us' => [
                'title' => 'About Asteria Coaching',
                'eyebrow' => 'Our Mission',
                'intro' => 'At Asteria, we combine expert human coaching with advanced AI to deliver personalized nutrition and fitness journeys.',
                'summary' => 'Our mission is to empower individuals to achieve their health goals through sustainable habits, science-backed programs, and 24/7 AI-powered support.',
                'image' => 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg',
                'cards' => [
                    [
                        'title' => 'Expert Coaching',
                        'text' => 'Get guidance from certified professionals who understand your unique needs and challenges.',
                    ],
                    [
                        'title' => 'AI Support',
                        'text' => 'Our Gemini-powered AI Coach is always available to answer questions and refine your program.',
                    ],
                    [
                        'title' => 'Holistic Approach',
                        'text' => 'We focus on both nutrition and fitness, ensuring a balanced path to long-term wellness.',
                    ],
                ],
            ],
            'home' => [
                'title' => 'Healthy Nutrition for Everyday Life',
                'eyebrow' => 'Frontoffice Home',
                'intro' => 'This native PHP home route now opens a static frontoffice experience inspired by your Nutrio template and prepared for later MVC integration.',
                'summary' => 'Visitors can move through the public nutrition modules from one clean entry point while you keep the architecture simple and framework-free.',
                'image' => 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg',
                'primaryRoute' => 'frontoffice/meal-planning',
                'primaryLabel' => 'Explore Meal Planning',
                'secondaryRoute' => 'backoffice/dashboard',
                'secondaryLabel' => 'Open Backoffice',
                'cards' => [
                    [
                        'title' => 'Meal Planning',
                        'text' => 'Present healthy weekly plans, recipe collections, and structured guidance for each profile.',
                    ],
                    [
                        'title' => 'Progress Tracking',
                        'text' => 'Show visual milestones, body metrics, and static progress summaries for each user journey.',
                    ],
                    [
                        'title' => 'Nutrition Blog',
                        'text' => 'Highlight wellness articles, food education, and expert content in a dedicated public space.',
                    ],
                ],
                'highlights' => [
                    'Static-only integration ready for your native MVC project',
                    'Uses the existing frontoffice template assets already stored in this workspace',
                    'Prepared for later dynamic data without changing the architecture',
                ],
            ],
            'meal-planning' => [
                'title' => 'Meal Planning',
                'eyebrow' => 'Frontoffice Module',
                'intro' => 'A public meal planning page for showcasing structured daily menus, calorie-balanced plans, and recipe ideas.',
                'summary' => 'This page is static for now, but it is already routed through the MVC structure so you can attach database queries later.',
                'image' => 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/get-free-consultation-bg.png',
                'cards' => [
                    [
                        'title' => 'Weekly Programs',
                        'text' => 'Display ready-made meal plans for weight loss, maintenance, or performance goals.',
                    ],
                    [
                        'title' => 'Daily Menus',
                        'text' => 'Organize breakfast, lunch, dinner, and snack blocks with clean static presentation.',
                    ],
                    [
                        'title' => 'Recipe Library',
                        'text' => 'Reserve this area for ingredients, preparation notes, and nutrition breakdowns.',
                    ],
                ],
            ],
            'progress-tracking' => [
                'title' => 'Progress Tracking',
                'eyebrow' => 'Frontoffice Module',
                'intro' => 'A public-facing page for displaying personal progress snapshots, measurement summaries, and visible motivation cues.',
                'summary' => 'It gives you a clear frontoffice target for future charts or user history without adding backend logic yet.',
                'image' => 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/bg-effect.png',
                'cards' => [
                    [
                        'title' => 'Goal Monitoring',
                        'text' => 'Keep body goals, target milestones, and success indicators easy to follow.',
                    ],
                    [
                        'title' => 'Progress Timeline',
                        'text' => 'Use this section later for weekly or monthly updates rendered from your database tables.',
                    ],
                    [
                        'title' => 'Motivation Blocks',
                        'text' => 'Feature encouraging summaries and visible achievements in a simple static layout.',
                    ],
                ],
            ],
            'nutrition-blog' => [
                'title' => 'Nutrition Blog',
                'eyebrow' => 'Frontoffice Module',
                'intro' => 'A public content page dedicated to nutrition articles, wellness tips, and educational posts.',
                'summary' => 'It is already part of the MVC flow, so later you can swap the static cards for SQL-driven posts.',
                'image' => 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/cta-bg.jpg',
                'cards' => [
                    [
                        'title' => 'Featured Articles',
                        'text' => 'Highlight the most important nutrition stories and expert editorial content.',
                    ],
                    [
                        'title' => 'Healthy Tips',
                        'text' => 'Use compact cards to present guidance on habits, ingredients, and meal balance.',
                    ],
                    [
                        'title' => 'Reader Categories',
                        'text' => 'Prepare separate spaces for recipes, sports nutrition, and general well-being.',
                    ],
                ],
            ],
        ];

        return $pages[$slug] ?? null;
    }

    protected function getBackofficePage(string $slug): ?array
    {
        $pages = [
            'dashboard' => [
                'title' => 'Dashboard',
                'badge' => 'Backoffice',
                'intro' => 'The dashboard centralizes the static backoffice modules you requested and keeps the Silva admin look inside a native PHP MVC structure.',
                'description' => 'Use this page as the control room for the administration area while the rest of the data layer is still under construction.',
                'cards' => [
                    [
                        'title' => 'User Management',
                        'text' => 'Administrative access, profile review, and role supervision stay grouped in one section.',
                    ],
                    [
                        'title' => 'Meal Planning',
                        'text' => 'Manage the static meal planning experience for both the admin and public sides.',
                    ],
                    [
                        'title' => 'Progress Tracking',
                        'text' => 'Keep room for later charts, progress updates, and historical summaries.',
                    ],
                    [
                        'title' => 'Nutrition Blog',
                        'text' => 'Prepare the editorial area for articles, topics, and publication states.',
                    ],
                    [
                        'title' => 'Exercise CRUD',
                        'text' => 'Open the exercise backoffice to create, update, and delete exercises inside each program.',
                    ],
                ],
                'highlights' => [
                    'Native PHP router with no framework dependency',
                    'MVC folders ready for future query logic and business rules',
                    'Program and exercise CRUD screens are wired directly in the coaching backoffice',
                ],
            ],
            'user-management' => [
                'title' => 'User Management',
                'badge' => 'Backoffice Only',
                'intro' => 'A static management page for supervising users, roles, and account states from the admin side.',
                'description' => 'This section is intentionally static now, but it is already isolated in its own route and controller action.',
                'cards' => [
                    [
                        'title' => 'Users',
                        'text' => 'Future user listings, search blocks, and account details can be added here.',
                    ],
                    [
                        'title' => 'Roles',
                        'text' => 'Separate nutritionists, clients, and administrators without changing the MVC flow.',
                    ],
                    [
                        'title' => 'Status Control',
                        'text' => 'Activation, suspension, and validation states can later come from your database.',
                    ],
                ],
                'highlights' => [
                    'Backoffice exclusive module',
                    'Ready for static tables or future CRUD screens',
                    'Fits directly into the reduced admin navigation',
                ],
            ],
            'meal-planning' => [
                'title' => 'Meal Planning',
                'badge' => 'Backoffice + Frontoffice',
                'intro' => 'A static admin page for curating meal plans, recipes, and weekly nutrition programs.',
                'description' => 'It mirrors the public meal planning route while keeping administration controls in the backoffice.',
                'cards' => [
                    [
                        'title' => 'Plan Templates',
                        'text' => 'Keep reusable plan structures ready for future database-driven content.',
                    ],
                    [
                        'title' => 'Schedule Builder',
                        'text' => 'Reserve the layout for daily planning blocks and weekly organization.',
                    ],
                    [
                        'title' => 'Recipe Catalog',
                        'text' => 'Prepare a place for recipe cards, ingredients, and portion notes.',
                    ],
                ],
                'highlights' => [
                    'Connected to the public meal planning route',
                    'Good target for future static-to-dynamic transition',
                    'Built without altering the native MVC organization',
                ],
            ],
            'progress-tracking' => [
                'title' => 'Progress Tracking',
                'badge' => 'Backoffice + Frontoffice',
                'intro' => 'A static admin route for monitoring goals, measurements, and user progress summaries.',
                'description' => 'The page is set up for later chart widgets, reports, or personalized tracking history.',
                'cards' => [
                    [
                        'title' => 'Metrics Review',
                        'text' => 'Weight, calories, BMI, or custom indicators can later appear in summary cards.',
                    ],
                    [
                        'title' => 'Goal Status',
                        'text' => 'Track milestones, adherence, and weekly outcomes in one place.',
                    ],
                    [
                        'title' => 'History',
                        'text' => 'Leave room for future snapshots and database-backed progress logs.',
                    ],
                ],
                'highlights' => [
                    'Available in both admin and public navigation',
                    'Static cards already separated by responsibility',
                    'Prepared for chart integration later',
                ],
            ],
            'nutrition-blog' => [
                'title' => 'Nutrition Blog',
                'badge' => 'Backoffice + Frontoffice',
                'intro' => 'A static editorial page for managing nutrition blog topics, post ideas, and publishing preparation.',
                'description' => 'This gives the backoffice a clear place for content administration before dynamic database content is introduced.',
                'cards' => [
                    [
                        'title' => 'Posts',
                        'text' => 'Feature article cards, summaries, and content placeholders in a clean admin view.',
                    ],
                    [
                        'title' => 'Categories',
                        'text' => 'Organize recipes, health education, and nutrition guidance by topic.',
                    ],
                    [
                        'title' => 'Publishing Flow',
                        'text' => 'Reserve this block for draft, review, and publication states.',
                    ],
                ],
                'highlights' => [
                    'Shared concept between frontoffice and backoffice',
                    'Suitable for static previews right away',
                    'Easy to replace later with real blog queries',
                ],
            ],
        ];

        return $pages[$slug] ?? null;
    }
}
