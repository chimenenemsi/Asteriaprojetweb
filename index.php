<?php
declare(strict_types=1);

session_start();

define('ROOT_PATH', __DIR__);

if (!function_exists('mb_strlen')) {
    function mb_strlen(string $string, ?string $encoding = null): int
    {
        unset($encoding);
        return strlen($string);
    }
}

if (!function_exists('mb_substr')) {
    function mb_substr(string $string, int $start, ?int $length = null, ?string $encoding = null): string
    {
        unset($encoding);
        return $length === null ? substr($string, $start) : substr($string, $start, $length);
    }
}

if (!function_exists('mb_strtolower')) {
    function mb_strtolower(string $string, ?string $encoding = null): string
    {
        unset($encoding);
        return strtolower($string);
    }
}

require_once ROOT_PATH . '/config/Database.php';
class_alias('Config\Database', 'Database');
require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProductCategoryController.php';
require_once ROOT_PATH . '/controllers/ProductController.php';
require_once ROOT_PATH . '/controllers/OrderController.php';
require_once ROOT_PATH . '/controllers/AiChatController.php';
require_once ROOT_PATH . '/controllers/UserController.php';
require_once ROOT_PATH . '/controllers/HomeController.php';
require_once ROOT_PATH . '/controllers/FrontofficeController.php';
require_once ROOT_PATH . '/controllers/BackofficeController.php';
require_once ROOT_PATH . '/controllers/GeminiCoachService.php';
require_once ROOT_PATH . '/controllers/AiCoachController.php';
require_once ROOT_PATH . '/controllers/AiSurveyController.php';
require_once ROOT_PATH . '/controllers/ProgressGoalController.php';
require_once ROOT_PATH . '/controllers/ProgressRecordController.php';

function base_url(): string
{
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

    return $basePath === '' ? '' : $basePath;
}

function route_url(string $route = 'frontoffice/home', array $params = []): string
{
    return base_url() . '/index.php?' . http_build_query(['route' => $route] + $params);
}

function action_url(string $action, array $params = []): string
{
    return base_url() . '/index.php?' . http_build_query(['action' => $action] + $params);
}

function asset_url(string $path): string
{
    $normalized = ltrim(str_replace('\\', '/', $path), '/');
    $segments = array_map('rawurlencode', explode('/', $normalized));

    return base_url() . '/' . implode('/', $segments);
}

function area_label(string $area): string
{
    return $area === 'backoffice' ? 'Backoffice' : 'Frontoffice';
}

function product_status_badge(string $status): string
{
    $map = [
        'ACTIVE'       => ['Active',       '#dcfce7', '#166534'],
        'DRAFT'        => ['Draft',         '#fef9c3', '#854d0e'],
        'OUT_OF_STOCK' => ['Out of Stock',  '#fee2e2', '#991b1b'],
        'INACTIVE'     => ['Inactive',      '#f1f5f9', '#475569'],
    ];
    [$label, $bg, $color] = $map[$status] ?? [ucfirst(strtolower(str_replace('_', ' ', $status))), '#f1f5f9', '#475569'];
    return '<span style="display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;background:' . $bg . ';color:' . $color . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
}

function order_status_badge(string $status): string
{
    $map = [
        'PENDING'       => ['Not Delivered', '#fef9c3', '#854d0e'],
        'PAID'          => ['Not Delivered', '#fef9c3', '#854d0e'],
        'NOT_DELIVERED' => ['Not Delivered', '#fef9c3', '#854d0e'],
        'SHIPPED'       => ['Delivered',     '#dcfce7', '#166534'],
        'DELIVERED'     => ['Delivered',     '#dcfce7', '#166534'],
        'CANCELLED'     => ['Cancelled',     '#fee2e2', '#991b1b'],
    ];
    [$label, $bg, $color] = $map[$status] ?? [ucfirst(strtolower(str_replace('_', ' ', $status))), '#f1f5f9', '#475569'];
    return '<span style="display:inline-block;padding:4px 10px;border-radius:999px;font-size:12px;font-weight:700;background:' . $bg . ';color:' . $color . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</span>';
}

function frontoffice_navigation(): array
{
    return [
        'home' => 'Home',
        'about-us' => 'About',
        'programs' => 'Programs',
        'categories' => 'Product Categories',
        'products' => 'Products',
        'orders' => 'Orders',
        'goals' => 'Progress Goals',
        'records' => 'Progress Records',
        'ai-survey' => 'AI Goal Survey',
        'diet-client' => 'My Diet Plan',
    ];
}

function backoffice_navigation(): array
{
    return [
        'dashboard' => 'Dashboard',
        'diet-admin' => 'Diet Management',
        'programs' => 'Programs',
        'exercises' => 'Exercises',
        'categories' => 'Product Categories',
        'products' => 'Products',
        'orders' => 'Orders',
        'users' => 'Users',
        'follow' => 'Follow',
        'goals' => 'Progress Goals',
        'records' => 'Progress Records',
        'ai-survey' => 'AI Goal Survey',
    ];
}

function order_status_label(string $status): string
{
    return match ($status) {
        'SHIPPED', 'DELIVERED' => 'Delivered',
        'CANCELLED' => 'Cancelled',
        'PAID', 'PENDING', 'NOT_DELIVERED' => 'Not delivered',
        default => ucfirst(strtolower(str_replace('_', ' ', $status))),
    };
}

function order_backoffice_status_options(): array
{
    return [
        'PENDING' => 'Not delivered',
        'SHIPPED' => 'Delivered',
        'CANCELLED' => 'Cancelled',
    ];
}

function handle_user_action(string $action): void
{
    $controller = new UserController();

    switch ($action) {
        case 'home':
            header('Location: ' . route_url('frontoffice/home'));
            exit;

        case 'admin':
            if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
                header('Location: ' . action_url('login'));
                exit;
            }
            header('Location: ' . action_url('list'));
            exit;

        case 'register':
            $controller->register();
            include ROOT_PATH . '/views/register.php';
            return;

        case 'login':
            $controller->login();
            include ROOT_PATH . '/views/login.php';
            return;

        case 'forgot_password':
            $controller->forgotPassword();
            include ROOT_PATH . '/views/login.php';
            return;

        case 'reset_password':
            $controller->resetPassword();
            include ROOT_PATH . '/views/login.php';
            return;

        case 'list':
            if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
                header('Location: ' . action_url('login'));
                exit;
            }
            header('Location: ' . route_url('backoffice/users'));
            exit;

        case 'delete':
            $controller->delete();
            return;

        case 'save_user':
            $controller->saveUser();
            return;

        case 'user_dashboard':
            if (!isset($_SESSION['user'])) {
                header('Location: ' . action_url('login'));
                exit;
            }
            (new BaseController())->render('user_dashboard', [
                'pageTitle' => 'User Home',
                'area' => 'frontoffice',
                'currentSection' => 'home',
            ], 'frontoffice');
            return;

        case 'logout':
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
            header('Location: ' . route_url('frontoffice/home'));
            exit;

        case 'change_role':
            $controller->changeRole();
            return;

        default:
            header('Location: ' . route_url('frontoffice/home'));
            exit;
    }
}

// Diet Management Routes (Integrated from gestion-diet)
if (isset($_GET['controller'])) {
    $controller = $_GET['controller'];
    $action = $_GET['action'] ?? 'obtenirTous';
    
    // Autoload for diet controllers in app/controllers
    $file = ROOT_PATH . '/app/controllers/' . $controller . 'Controller.php';
    if (file_exists($file)) {
        require_once $file;
        $controllerClass = $controller . 'Controller';
        if (class_exists($controllerClass) && method_exists($controllerClass, $action)) {
            $controllerClass::$action();
            exit;
        }
    }
}

if (isset($_GET['action'])) {
    handle_user_action(trim((string) $_GET['action']));
    exit;
}

$route = trim((string) ($_GET['route'] ?? 'frontoffice/home'), '/');
$segments = array_values(array_filter(explode('/', $route), 'strlen'));
$area = ($segments[0] ?? 'frontoffice') === 'backoffice' ? 'backoffice' : 'frontoffice';
$resource = $segments[1] ?? 'home';
$action = $segments[2] ?? 'index';

// Admin-only access for backoffice
if ($area === 'backoffice') {
    if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
        // Redirect to login or show an unauthorized message
        header('Location: ' . action_url('login'));
        exit;
    }
}

switch ($resource) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'about-us':
    case 'meal-planning':
    case 'progress-tracking':
    case 'nutrition-blog':
    case 'consultation':
        (new FrontofficeController())->show($resource);
        break;

    case 'programs':
        if ($area === 'backoffice') {
            (new BackofficeController())->show('programs');
        } else {
            (new FrontofficeController())->show('programs');
        }
        break;

    case 'programs-pdf':
        if ($area === 'backoffice') {
            (new BackofficeController())->show('programs-pdf');
        } else {
            (new HomeController())->notFound();
        }
        break;

    case 'exercises':
        if ($area === 'backoffice') {
            (new BackofficeController())->show('exercises');
        } else {
            (new HomeController())->notFound();
        }
        break;

    case 'exercises-pdf':
        if ($area === 'backoffice') {
            (new BackofficeController())->show('exercises-pdf');
        } else {
            (new HomeController())->notFound();
        }
        break;

    case 'exercises-list-pdf':
        if ($area === 'backoffice') {
            (new BackofficeController())->show('exercises-list-pdf');
        } else {
            (new HomeController())->notFound();
        }
        break;

    case 'ai-coach':
        (new AiCoachController())->chat();
        break;

    case 'ai-summary':
        (new AiCoachController())->summarize();
        break;

    case 'ai-survey':
        if ($action === 'recommend') {
            (new AiSurveyController())->recommend();
        } else {
            (new AiSurveyController())->survey($area);
        }
        break;

    case 'dashboard':
        if ($area === 'backoffice') {
            (new BackofficeController())->show('dashboard');
        } else {
            (new HomeController())->index();
        }
        break;

    case 'categories':
        $controller = new ProductCategoryController();
        if ($area === 'backoffice') {
            if ($action === 'pdf') {
                $controller->exportPdf($area);
            } elseif ($action === 'products-pdf') {
                $controller->exportCategoryProductsPdf($area);
            } elseif ($action === 'new') {
                $controller->form($area, 'create');
            } elseif ($action === 'create') {
                $controller->create($area);
            } elseif ($action === 'show') {
                $controller->show($area);
            } elseif ($action === 'edit') {
                $controller->form($area, 'edit');
            } elseif ($action === 'update') {
                $controller->update($area);
            } elseif ($action === 'delete') {
                $controller->delete($area);
            } else {
                $controller->index($area);
            }
        } else {
            if ($action === 'pdf') {
                $controller->exportPdf($area);
            } elseif ($action === 'products-pdf') {
                $controller->exportCategoryProductsPdf($area);
            } elseif ($action === 'show') {
                $controller->show($area);
            } else {
                $controller->index($area);
            }
        }
        break;

    case 'products':
        $controller = new ProductController();
        if ($area === 'backoffice') {
            if ($action === 'pdf') {
                $controller->exportPdf($area);
            } elseif ($action === 'new') {
                $controller->form($area, 'create');
            } elseif ($action === 'create') {
                $controller->create($area);
            } elseif ($action === 'edit') {
                $controller->form($area, 'edit');
            } elseif ($action === 'update') {
                $controller->update($area);
            } elseif ($action === 'delete') {
                $controller->delete($area);
            } else {
                $controller->index($area);
            }
        } else {
            if ($action === 'pdf') {
                $controller->exportPdf($area);
            } else {
                $controller->index($area);
            }
        }
        break;

    case 'ai-chat':
        (new AiChatController())->chat();
        break;

    case 'orders':
        $controller = new OrderController();
        if ($area === 'backoffice') {
            if ($action === 'pdf') {
                $controller->exportPdf($area);
            } elseif ($action === 'new') {
                $controller->form($area, 'create');
            } elseif ($action === 'create') {
                $controller->create($area);
            } elseif ($action === 'show') {
                $controller->show($area);
            } elseif ($action === 'edit') {
                $controller->form($area, 'edit');
            } elseif ($action === 'update') {
                $controller->update($area);
            } elseif ($action === 'delete') {
                $controller->delete($area);
            } else {
                $controller->index($area);
            }
        } else {
            if ($action === 'pdf') {
                $controller->exportPdf($area);
            } elseif ($action === 'new') {
                $controller->form($area, 'create');
            } elseif ($action === 'create') {
                $controller->create($area);
            } elseif ($action === 'show') {
                $controller->show($area);
            } else {
                $controller->index($area);
            }
        }
        break;

    case 'diet-admin':
        if ($area !== 'backoffice') {
            (new HomeController())->notFound();
            break;
        }
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            header('Location: ' . action_url('login'));
            exit;
        }
        (new BaseController())->render('diet/admin', [
            'pageTitle' => 'Diet Management',
            'area' => 'backoffice',
            'currentSection' => 'diet',
        ], 'backoffice');
        break;

    case 'diet-client':
        if (!isset($_SESSION['user'])) {
            header('Location: ' . action_url('login'));
            exit;
        }
        (new BaseController())->render('diet/client', [
            'pageTitle' => 'My Diet Plan',
            'area' => 'frontoffice',
            'currentSection' => 'diet',
        ], 'frontoffice');
        break;

    case 'users':
        if ($area !== 'backoffice') {
            (new HomeController())->notFound();
            break;
        }
        if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] ?? 'user') !== 'admin') {
            header('Location: ' . action_url('login'));
            exit;
        }
        (new UserController())->admin(true);
        break;

    case 'follow':
        if ($area !== 'backoffice') {
            (new HomeController())->notFound();
            break;
        }
        (new BaseController())->render('follow/index', [
            'pageTitle' => 'Follow',
            'area' => 'backoffice',
            'currentSection' => 'follow',
        ], 'backoffice');
        break;

    case 'goals':
        $controller = new ProgressGoalController();
        if ($area === 'frontoffice') {
            if ($action === 'new') {
                $controller->form($area, 'create');
            } elseif ($action === 'create') {
                $controller->create($area);
            } elseif ($action === 'show') {
                $controller->show($area);
            } elseif ($action === 'edit') {
                $controller->form($area, 'edit');
            } elseif ($action === 'update') {
                $controller->update($area);
            } elseif ($action === 'delete') {
                $controller->delete($area);
            } else {
                $controller->index($area);
            }
        } else {
            if ($action === 'show') {
                $controller->show($area);
            } else {
                $controller->index($area);
            }
        }
        break;

    case 'records':
        $controller = new ProgressRecordController();
        if ($area === 'frontoffice') {
            if ($action === 'new') {
                $controller->form($area, 'create');
            } elseif ($action === 'create') {
                $controller->create($area);
            } elseif ($action === 'edit') {
                $controller->form($area, 'edit');
            } elseif ($action === 'update') {
                $controller->update($area);
            } elseif ($action === 'delete') {
                $controller->delete($area);
            } else {
                $controller->index($area);
            }
        } else {
            if ($action === 'show') {
                $controller->show($area);
            } else {
                $controller->index($area);
            }
        }
        break;

    default:
        (new BaseController())->renderNotFound();
        break;
}
