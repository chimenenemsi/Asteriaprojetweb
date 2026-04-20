<?php
declare(strict_types=1);

class BaseController
{
    protected function render(string $view, array $data = [], string $layout = 'default'): void
    {
        extract($data, EXTR_SKIP);

        ob_start();
        require ROOT_PATH . '/views/' . $view . '.php';
        $content = (string) ob_get_clean();

        require ROOT_PATH . '/views/layouts/' . $layout . '.php';
    }

    protected function redirect(string $route, array $params = []): void
    {
        $query = array_merge(['route' => $route], $params);

        header('Location: ' . base_url() . '/index.php?' . http_build_query($query));
        exit;
    }

    protected function renderNotFound(): void
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
        ];
    }

    protected function getFrontofficePage(string $slug): ?array
    {
        $pages = [
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
                ],
                'highlights' => [
                    'Native PHP router with no framework dependency',
                    'MVC folders ready for future query logic and business rules',
                    'Backoffice links reduced to the required admin modules only',
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
