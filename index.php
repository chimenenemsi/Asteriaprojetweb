<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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
require_once ROOT_PATH . '/controllers/HomeController.php';
require_once ROOT_PATH . '/controllers/FrontofficeController.php';
require_once ROOT_PATH . '/controllers/BackofficeController.php';
require_once ROOT_PATH . '/controllers/GeminiCoachService.php';
require_once ROOT_PATH . '/controllers/AiCoachController.php';

function base_url(): string
{
    $scriptName = str_replace('\\', '/', (string) ($_SERVER['SCRIPT_NAME'] ?? '/index.php'));
    $basePath = rtrim(str_replace('/index.php', '', $scriptName), '/');

    return $basePath === '' ? '' : $basePath;
}

function route_url(string $route = 'home'): string
{
    return base_url() . '/index.php?' . http_build_query(['route' => $route]);
}

function asset_url(string $path): string
{
    $normalized = ltrim(str_replace('\\', '/', $path), '/');
    $segments = array_map('rawurlencode', explode('/', $normalized));

    return base_url() . '/' . implode('/', $segments);
}

function frontoffice_brand_name(): string
{
    return 'Asteria';
}

function frontoffice_brand_logo_url(): string
{
    return asset_url('assets/imgs/logo.png');
}

function backoffice_brand_name(): string
{
    return 'Asteria';
}

$route = trim((string) ($_GET['route'] ?? 'home'), '/');
$segments = array_values(array_filter(explode('/', $route), 'strlen'));
$area = $segments[0] ?? 'home';
$page = $segments[1] ?? '';

switch ($area) {
    case 'home':
        (new HomeController())->index();
        break;

    case 'about-us':
        (new FrontofficeController())->show('about-us');
        break;

    case 'frontoffice':
        if ($page === 'ai-coach') {
            (new AiCoachController())->chat();
            break;
        }
        if ($page === 'ai-summary') {
            (new AiCoachController())->summarize();
            break;
        }
        (new FrontofficeController())->show($page ?: 'home');
        break;

    case 'backoffice':
        (new BackofficeController())->show($page ?: 'dashboard');
        break;

    default:
        (new HomeController())->notFound();
        break;
}
