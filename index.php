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
require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/ProductCategoryController.php';
require_once ROOT_PATH . '/controllers/ProductController.php';
require_once ROOT_PATH . '/controllers/OrderController.php';
require_once ROOT_PATH . '/controllers/AiChatController.php';
require_once ROOT_PATH . '/controllers/UserController.php';

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
        'categories' => 'Product Categories',
        'products' => 'Products',
        'orders' => 'Orders',
    ];
}

function backoffice_navigation(): array
{
    return [
        'dashboard' => 'Dashboard',
        'categories' => 'Product Categories',
        'products' => 'Products',
        'orders' => 'Orders',
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

if (isset($_GET['action'])) {
    handle_user_action(trim((string) $_GET['action']));
    exit;
}

$route = trim((string) ($_GET['route'] ?? 'frontoffice/home'), '/');
$segments = array_values(array_filter(explode('/', $route), 'strlen'));
$area = ($segments[0] ?? 'frontoffice') === 'backoffice' ? 'backoffice' : 'frontoffice';
$resource = $segments[1] ?? 'home';
$action = $segments[2] ?? 'index';

switch ($resource) {
    case 'home':
        (new BaseController())->render('home', [
            'pageTitle' => 'Asteria Produits Home',
            'area' => 'frontoffice',
            'currentSection' => 'home',
        ], 'frontoffice');
        break;

    case 'dashboard':
        (new BaseController())->render('dashboard', [
            'pageTitle' => 'Produits Dashboard',
            'area' => 'backoffice',
            'currentSection' => 'dashboard',
        ], 'backoffice');
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

    case 'users':
        if ($area !== 'backoffice') {
            http_response_code(404);
            echo '<h1>Not Found</h1>';
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
            http_response_code(404);
            echo '<h1>Not Found</h1>';
            break;
        }
        (new BaseController())->render('follow/index', [
            'pageTitle' => 'Follow',
            'area' => 'backoffice',
            'currentSection' => 'follow',
        ], 'backoffice');
        break;

    default:
        http_response_code(404);
        echo '<h1>Not Found</h1>';
        break;
}
