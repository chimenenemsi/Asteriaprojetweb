<?php
declare(strict_types=1);

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
require_once ROOT_PATH . '/controllers/ProgressGoalController.php';
require_once ROOT_PATH . '/controllers/ProgressRecordController.php';
require_once ROOT_PATH . '/controllers/AiSurveyController.php';

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

function frontoffice_navigation(): array
{
    return [
        'home' => 'Home',
        'goals' => 'Progress Goals',
        'records' => 'Progress Records',
        'ai-survey' => 'AI Goal Survey',
    ];
}

function backoffice_navigation(): array
{
    return [
        'dashboard' => 'Dashboard',
        'goals' => 'Progress Goals',
        'records' => 'Progress Records',
    ];
}

$route = trim((string) ($_GET['route'] ?? 'frontoffice/home'), '/');
$segments = array_values(array_filter(explode('/', $route), 'strlen'));
$area = ($segments[0] ?? 'frontoffice') === 'backoffice' ? 'backoffice' : 'frontoffice';
$resource = $segments[1] ?? 'goals';
$action = $segments[2] ?? 'index';

switch ($resource) {
    case 'home':
        (new BaseController())->render('home', [
            'pageTitle' => 'Progress Home',
            'area' => 'frontoffice',
            'currentSection' => 'home',
        ], 'frontoffice');
        break;

    case 'dashboard':
        (new BaseController())->render('dashboard', [
            'pageTitle' => 'Progress Dashboard',
            'area' => 'backoffice',
            'currentSection' => 'dashboard',
        ], 'backoffice');
        break;

    case 'goals':
        $controller = new ProgressGoalController();
        if ($action === 'motivation') {
            $controller->motivation($area);
        } elseif ($action === 'pdf') {
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
        break;

    case 'ai-survey':
        $controller = new AiSurveyController();
        if ($action === 'recommend') {
            $controller->recommend();
        } else {
            $controller->survey($area);
        }
        break;

    case 'records':
        $controller = new ProgressRecordController();
        if ($action === 'new') {
            $controller->form($area, 'create');
        } elseif ($action === 'pdf') {
            $controller->exportPdf($area);
        } elseif ($action === 'create') {
            $controller->create($area);
        } elseif ($action === 'insights') {
            $controller->insights($area);
        } elseif ($action === 'edit') {
            $controller->form($area, 'edit');
        } elseif ($action === 'update') {
            $controller->update($area);
        } elseif ($action === 'delete') {
            $controller->delete($area);
        } elseif ($action === 'show') {
            $controller->show($area);
        } else {
            $controller->index($area);
        }
        break;

    default:
        (new BaseController())->renderNotFound();
        break;
}
