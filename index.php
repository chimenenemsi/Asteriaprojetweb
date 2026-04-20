<?php
declare(strict_types=1);

define('ROOT_PATH', __DIR__);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once ROOT_PATH . '/config/Database.php';
require_once ROOT_PATH . '/controllers/BaseController.php';
require_once ROOT_PATH . '/controllers/HomeController.php';
require_once ROOT_PATH . '/controllers/FrontofficeController.php';
require_once ROOT_PATH . '/controllers/BackofficeController.php';

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

function frontoffice_template_nav_html(string $currentRoute): string
{
    $items = [
        'home' => 'Home',
        'about-us' => 'About',
        'frontoffice/programs' => 'Programs',
    ];

    $listItems = '';

    foreach ($items as $route => $label) {
        $classes = [
            'menu-item',
            'menu-item-type-custom',
            'menu-item-object-custom',
            'parent',
            'hfe-creative-menu',
        ];

        if ($currentRoute === $route) {
            $classes[] = 'current-menu-item';
        }

        $listItems .= sprintf(
            '<li class="%s"><a href="%s" class="hfe-menu-item">%s</a></li>',
            htmlspecialchars(implode(' ', $classes), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars(route_url($route), ENT_QUOTES, 'UTF-8'),
            htmlspecialchars($label, ENT_QUOTES, 'UTF-8')
        );
    }

    return '<nav class="hfe-nav-menu__layout-horizontal hfe-nav-menu__submenu-arrow" '
        . 'data-toggle-icon="&lt;i aria-hidden=&quot;true&quot; tabindex=&quot;0&quot; class=&quot;icon icon-menu1&quot;&gt;&lt;/i&gt;" '
        . 'data-close-icon="&lt;i aria-hidden=&quot;true&quot; tabindex=&quot;0&quot; class=&quot;icon icon-cross&quot;&gt;&lt;/i&gt;" '
        . 'data-full-width="yes"><ul id="menu-1-f761f9e" class="hfe-nav-menu">'
        . $listItems
        . '</ul></nav>';
}

function rewrite_frontoffice_template(string $templateHtml, string $currentRoute, ?string $templateBaseHref = null): string
{
    if ($templateBaseHref !== null && str_contains($templateHtml, '<head>')) {
        $templateHtml = str_replace(
            '<head>',
            "<head>\n\t<base href=\"" . htmlspecialchars($templateBaseHref, ENT_QUOTES, 'UTF-8') . '">',
            $templateHtml
        );
    }

    $templateHtml = str_replace(
        [
            'href="index.html"',
            "href='index.html'",
            'href="https://nutrio.radiantthemes.com"',
            'href="https://nutrio.radiantthemes.com/"',
            'href="https://nutrio.radiantthemes.com/template-kit/home-one/"',
            'href="https://nutrio.radiantthemes.com/template-kit/about-us/"',
            'href="/cdn-cgi/l/email-protection"',
            'src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"',
            'href="https://nutrio.radiantthemes.com/template-kit/contact-us/"',
            'href="https://nutrio.radiantthemes.com/template-kit/our-services/"',
            'href="https://nutrio.radiantthemes.com/template-kit/service-details/"',
            'href="https://nutrio.radiantthemes.com/template-kit/pricing/"',
            'href="https://nutrio.radiantthemes.com/template-kit/team/"',
            'href="https://nutrio.radiantthemes.com/template-kit/faq/"',
            'href="https://nutrio.radiantthemes.com/template-kit/404/"',
        ],
        [
            'href="' . htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') . '"',
            "href='" . htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') . "'",
            'href="' . htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') . '"',
            'href="' . htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') . '"',
            'href="' . htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') . '"',
            'href="' . htmlspecialchars(route_url('about-us'), ENT_QUOTES, 'UTF-8') . '"',
            'href="https://nutrio.radiantthemes.com/cdn-cgi/l/email-protection"',
            'src="https://nutrio.radiantthemes.com/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"',
            'href="#"',
            'href="#"',
            'href="#"',
            'href="#"',
            'href="#"',
            'href="#"',
            'href="#"',
        ],
        $templateHtml
    );

    $templateHtml = str_replace(
        [
            'src="../../wp-content/uploads/2022/05/MicrosoftTeams-image-2.png"',
            'src="https://nutrio.radiantthemes.com/wp-content/uploads/2022/05/MicrosoftTeams-image-2.png"',
        ],
        [
            'src="' . htmlspecialchars(frontoffice_brand_logo_url(), ENT_QUOTES, 'UTF-8') . '"',
            'src="' . htmlspecialchars(frontoffice_brand_logo_url(), ENT_QUOTES, 'UTF-8') . '"',
        ],
        $templateHtml
    );

    $templateHtml = preg_replace(
        '~<p class="main-title bhf-hidden" itemprop="headline"><a href="[^"]*" title="[^"]*" rel="home">.*?</a></p>~',
        '<p class="main-title bhf-hidden" itemprop="headline"><a href="'
            . htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8')
            . '" title="'
            . htmlspecialchars(frontoffice_brand_name(), ENT_QUOTES, 'UTF-8')
            . '" rel="home">'
            . htmlspecialchars(frontoffice_brand_name(), ENT_QUOTES, 'UTF-8')
            . '</a></p>',
        $templateHtml,
        1
    ) ?? $templateHtml;

    $templateHtml = preg_replace(
        '~(<div class="elementor-element elementor-element-638a369.*?<div class="elementor-widget-container">.*?<img[^>]*src="[^"]*assets/imgs/logo\.png"[^>]*>)(\s*</div>\s*</div>)~s',
        '$1<span class="monta-brand-label">Asteria</span>$2',
        $templateHtml,
        1
    ) ?? $templateHtml;

    $templateHtml = preg_replace(
        '~(<div class="elementor-element elementor-element-1b3d1d7.*?<div class="elementor-widget-container">\s*<a [^>]*>\s*<img[^>]*src="[^"]*assets/imgs/logo\.png"[^>]*>)(\s*</a>\s*</div>\s*</div>)~s',
        '$1<span class="monta-brand-label footer-brand-label">Asteria</span>$2',
        $templateHtml,
        1
    ) ?? $templateHtml;

    $rewrittenNav = preg_replace(
        '~<nav class="hfe-nav-menu__layout-horizontal.*?</nav>~s',
        frontoffice_template_nav_html($currentRoute),
        $templateHtml,
        1
    );

    $templateHtml = $rewrittenNav ?? $templateHtml;

    if (str_contains($templateHtml, '</head>')) {
        $templateHtml = str_replace(
            '</head>',
            "<style>"
            . "img[src$=\"assets/imgs/logo.png\"]{width:88px!important;height:auto!important;max-width:none!important;}"
            . ".elementor-element-638a369 .elementor-widget-container,.elementor-element-1b3d1d7 .elementor-widget-container a{display:inline-flex!important;align-items:center;gap:12px;}"
            . ".monta-brand-label{display:inline-block;font-size:22px;font-weight:700;line-height:1;color:#ffffff;vertical-align:middle;}"
            . ".footer-brand-label{color:#153122;}"
            . "</style>\n</head>",
            $templateHtml
        );
    }

    return $templateHtml;
}

function rewrite_backoffice_dashboard_template(string $templateHtml): string
{
    $templateHtml = str_replace(
        '<body data-menu-color="light" data-sidebar="default"',
        '<body data-menu-color="light" data-sidebar="default">',
        $templateHtml
    );

    if (str_contains($templateHtml, '<head>')) {
        $templateHtml = str_replace(
            '<head>',
            "<head>\n        <base href=\"" . htmlspecialchars(
                asset_url('assets/backoffice/zoyothemes.com/silva/html') . '/',
                ENT_QUOTES,
                'UTF-8'
            ) . '">',
            $templateHtml
        );
    }

    $templateHtml = str_replace(
        [
            '<title>Dashboard | Silva - Responsive Admin Dashboard Template</title>',
            '<meta name="author" content="Zoyothemes"/>',
            '<h5 class="mb-0">Good Morning, John Smith</h5>',
            'John Smith <i class="mdi mdi-chevron-down"></i>',
            '&copy; <script>document.write(new Date().getFullYear())</script> - Made with <span class="mdi mdi-heart text-danger"></span> by <a href="#!" class="text-reset fw-semibold">Zoyothemes</a>',
            'src="assets/images/logo-sm.png"',
            'src="assets/images/logo-light.png"',
            'src="assets/images/logo-dark.png"',
        ],
        [
            '<title>Dashboard | ' . htmlspecialchars(backoffice_brand_name(), ENT_QUOTES, 'UTF-8') . ' Backoffice</title>',
            '<meta name="author" content="' . htmlspecialchars(backoffice_brand_name(), ENT_QUOTES, 'UTF-8') . '"/>',
            '<h5 class="mb-0">Good Morning, ' . htmlspecialchars(backoffice_brand_name(), ENT_QUOTES, 'UTF-8') . ' Admin</h5>',
            htmlspecialchars(backoffice_brand_name(), ENT_QUOTES, 'UTF-8') . ' Admin <i class="mdi mdi-chevron-down"></i>',
            '&copy; <script>document.write(new Date().getFullYear())</script> '
                . htmlspecialchars(backoffice_brand_name(), ENT_QUOTES, 'UTF-8')
                . ' native PHP MVC backoffice.',
            'src="' . htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') . '"',
            'src="' . htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') . '"',
            'src="' . htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') . '"',
        ],
        $templateHtml
    );

    $templateHtml = preg_replace(
        '~<span class="logo-lg">\s*(<img[^>]*src="[^"]*assets/imgs/logo\.png"[^>]*>)\s*</span>~',
        '<span class="logo-lg"><span class="monta-backoffice-logo-wrap">$1<span class="monta-backoffice-brand-text">'
            . htmlspecialchars(backoffice_brand_name(), ENT_QUOTES, 'UTF-8')
            . '</span></span></span>',
        $templateHtml
    ) ?? $templateHtml;

    $templateHtml = str_replace(
        [
            "href='index.html'",
            'href="index.html"',
            "href='user-management.html'",
            "href='meal-planning.html'",
            "href='progress-tracking.html'",
            "href='nutrition-blog.html'",
            "href='consultation.html'",
            "href='pages-profile.html'",
            "href='auth-lock-screen.html'",
            "href='auth-logout.html'",
        ],
        [
            "href='" . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . "'",
            'href="' . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . '"',
            "href='" . htmlspecialchars(route_url('backoffice/user-management'), ENT_QUOTES, 'UTF-8') . "'",
            "href='" . htmlspecialchars(route_url('backoffice/meal-planning'), ENT_QUOTES, 'UTF-8') . "'",
            "href='" . htmlspecialchars(route_url('backoffice/progress-tracking'), ENT_QUOTES, 'UTF-8') . "'",
            "href='" . htmlspecialchars(route_url('backoffice/nutrition-blog'), ENT_QUOTES, 'UTF-8') . "'",
            "href='" . htmlspecialchars(route_url('backoffice/programs'), ENT_QUOTES, 'UTF-8') . "'",
            "href='#'",
            "href='#'",
            "href='#'",
        ],
        $templateHtml
    );

    $templateHtml = str_replace(
        [
            '<span> Consultation </span>',
            '<h4 class="fs-18 fw-semibold m-0">Consultation</h4>',
            '<li class="breadcrumb-item active">Consultation</li>',
        ],
        [
            '<span> Programs </span>',
            '<h4 class="fs-18 fw-semibold m-0">Programs</h4>',
            '<li class="breadcrumb-item active">Programs</li>',
        ],
        $templateHtml
    );

    $templateHtml = str_replace(
        [
            '<a class=\'tp-link\' href=\'' . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . '\'>',
            '<a class=\'logo logo-light\' href=\'' . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . '\'>',
            '<a class=\'logo logo-dark\' href=\'' . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . '\'>',
        ],
        [
            '<a class=\'tp-link active\' href=\'' . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . '\'>',
            '<a class=\'logo logo-light\' href=\'' . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . '\'>',
            '<a class=\'logo logo-dark\' href=\'' . htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') . '\'>',
        ],
        $templateHtml
    );

    if (str_contains($templateHtml, '</head>')) {
        $templateHtml = str_replace(
            '</head>',
            "<style>"
            . ".logo-box .logo-sm img{height:34px!important;width:auto!important;}"
            . ".logo-box .logo-lg img{height:52px!important;width:auto!important;max-height:none!important;}"
            . ".monta-backoffice-logo-wrap{display:inline-flex;align-items:center;gap:10px;}"
            . ".logo-light .monta-backoffice-brand-text{color:#ffffff;font-size:22px;font-weight:700;line-height:1;}"
            . ".logo-dark .monta-backoffice-brand-text{color:#1f2937;font-size:22px;font-weight:700;line-height:1;}"
            . "</style>\n</head>",
            $templateHtml
        );
    }

    return $templateHtml;
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
        (new FrontofficeController())->show($page ?: 'home');
        break;

    case 'backoffice':
        (new BackofficeController())->show($page ?: 'dashboard');
        break;

    default:
        (new HomeController())->notFound();
        break;
}
