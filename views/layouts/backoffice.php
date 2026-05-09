<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(($pageTitle ?? 'Asteria') . ' | Backoffice', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="shortcut icon" href="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/css/app.min.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/css/icons.min.css'), ENT_QUOTES, 'UTF-8') ?>">
    <style>
        .monta-brand-label{display:inline-flex;align-items:center;gap:12px;font-weight:700;color:#1f2937;text-decoration:none}
        .monta-brand-label img{display:block;width:42px;height:auto}
        .monta-sidebar-caption{margin:12px 20px 6px;font-size:12px;font-weight:700;letter-spacing:.08em;color:#94a3b8;text-transform:uppercase}
        .logo-box .logo-sm img{height:34px!important;width:auto!important}
        .logo-box .logo-lg img{height:54px!important;width:auto!important;max-height:none!important}
        .logo-box .logo-lg{display:inline-flex!important;align-items:center;gap:10px}
        .logo-box .monta-logo-word{font-size:22px;font-weight:700;line-height:1}
        .logo-box .logo-light .monta-logo-word{color:#fff}
        .logo-box .logo-dark .monta-logo-word{color:#1f2937}
        .module-switch{display:inline-flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;background:#fff;color:#1f2937;text-decoration:none;border:1px solid rgba(15,23,42,.1)}
        .content .card{border:1px solid #e5e7eb;border-radius:18px;padding:24px;box-shadow:0 12px 28px rgba(15,23,42,.05)}
        .content .header-line{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap}
        .content .actions{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
        .content .btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 16px;border-radius:10px;border:1px solid transparent;text-decoration:none;cursor:pointer}
        .content .btn-primary{background:#16a34a;color:#fff}
        .content .btn-secondary{background:#1e293b;color:#fff}
        .content .btn-danger{background:#be123c;color:#fff}
        .content .table{width:100%;border-collapse:collapse}
        .content .table th,.content .table td{padding:12px;border-bottom:1px solid #e5e7eb;text-align:left;vertical-align:top}
        .content .badge{display:inline-block;padding:6px 10px;border-radius:999px;background:#ecfeff;color:#0f766e;font-size:12px;font-weight:700}
        .content input,.content select,.content textarea{width:100%;padding:11px 12px;border:1px solid #cbd5e1;border-radius:10px;box-sizing:border-box}
        .content label{display:block;margin-bottom:6px;font-weight:600}
        .content .row{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
        .content .col-12{grid-column:span 12}.content .col-6{grid-column:span 6}.content .col-4{grid-column:span 4}.content .col-3{grid-column:span 3}
        .content .grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:20px}
        .content .muted{color:#64748b}
        .content .error{color:#b91c1c;font-size:13px;margin-top:4px}
        .content .field{display:grid;gap:8px}
        .content .field label{margin:0;font-weight:700}
        .content .field-help{font-size:13px;color:#64748b;line-height:1.5}
        .content .form-shell{display:grid;gap:20px}
        .content .form-section{padding:22px;border:1px solid #e5e7eb;border-radius:18px;background:linear-gradient(180deg,#fff,#f8fafc)}
        .content .form-section h2{margin:0 0 6px;font-size:20px}
        .content .form-section p{margin:0 0 18px;color:#64748b;line-height:1.6}
        .content .summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px}
        .content .summary-item{padding:14px 16px;border-radius:16px;border:1px solid #e5e7eb;background:#f8fafc}
        .content .summary-item strong{display:block;margin-bottom:4px;font-size:12px;text-transform:uppercase;color:#64748b}
        .content .readonly-input{background:#f8fafc}
        .content form.inline{display:inline}
        @media (max-width:800px){.content .col-6,.content .col-4,.content .col-3{grid-column:span 12}}
    </style>
</head>
<body data-menu-color="light" data-sidebar="default">
    <?php
    $currentRoute = trim((string) ($_GET['route'] ?? 'backoffice/dashboard'), '/');
    $section = $currentSection ?? 'dashboard';
    $switchRoute = in_array($section, ['categories', 'products', 'orders'], true) ? 'frontoffice/' . $section : 'frontoffice/home';
    ?>
    <div id="app-layout">
        <div class="topbar-custom">
            <div class="container-fluid">
                <div class="d-flex align-items-center justify-content-between">
                    <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                        <li>
                            <button class="button-toggle-menu nav-link">
                                <i data-feather="menu" class="noti-icon"></i>
                            </button>
                        </li>
                        <li class="d-none d-lg-block">
                            <a class="monta-brand-label" href="<?= htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>">
                                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria">
                                <span>Asteria</span>
                            </a>
                        </li>
                    </ul>
                    <a class="module-switch" href="<?= htmlspecialchars(route_url($switchRoute), ENT_QUOTES, 'UTF-8') ?>">Open Frontoffice</a>
                </div>
            </div>
        </div>

        <div class="app-sidebar-menu">
            <div class="h-100" data-simplebar>
                <div id="sidebar-menu">
                    <div class="logo-box">
                        <a class="logo logo-light" href="<?= htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>">
                            <span class="logo-sm">
                                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria" height="24">
                                <span class="monta-logo-word">Asteria</span>
                            </span>
                        </a>
                        <a class="logo logo-dark" href="<?= htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>">
                            <span class="logo-sm">
                                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria" height="22">
                            </span>
                            <span class="logo-lg">
                                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria" height="24">
                                <span class="monta-logo-word">Asteria</span>
                            </span>
                        </a>
                    </div>

                    <div class="monta-sidebar-caption">Backoffice</div>
                    <ul id="side-menu">
                        <?php foreach (backoffice_navigation() as $slug => $label): ?>
                            <?php $route = 'backoffice/' . $slug; ?>
                            <li>
                                <a class="tp-link <?= $currentRoute === $route ? 'active' : '' ?>" href="<?= htmlspecialchars(route_url($route), ENT_QUOTES, 'UTF-8') ?>">
                                    <?php if ($slug === 'dashboard'): ?><i data-feather="home"></i><?php endif; ?>
                                    <?php if ($slug === 'categories'): ?><i data-feather="layers"></i><?php endif; ?>
                                    <?php if ($slug === 'products'): ?><i data-feather="package"></i><?php endif; ?>
                                    <?php if ($slug === 'orders'): ?><i data-feather="shopping-cart"></i><?php endif; ?>
                                    <span><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="monta-sidebar-caption">Secondary</div>
                    <ul id="side-menu-secondary">
                        <li>
                            <a class="tp-link <?= $currentRoute === 'backoffice/follow' ? 'active' : '' ?>" href="<?= htmlspecialchars(route_url('backoffice/follow'), ENT_QUOTES, 'UTF-8') ?>">
                                <i data-feather="activity"></i>
                                <span>Follow</span>
                            </a>
                        </li>
                        <li>
                            <a class="tp-link <?= $currentRoute === 'backoffice/users' ? 'active' : '' ?>" href="<?= htmlspecialchars(route_url('backoffice/users'), ENT_QUOTES, 'UTF-8') ?>">
                                <i data-feather="users"></i>
                                <span>Users</span>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>

        <div class="content-page">
            <div class="content">
                <?= $content ?>
            </div>

            <footer class="footer">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col fs-13 text-muted text-center">
                            &copy; <script>document.write(new Date().getFullYear())</script> Asteria native PHP MVC backoffice.
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/libs/jquery/jquery.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/libs/bootstrap/js/bootstrap.bundle.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/libs/simplebar/simplebar.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/libs/node-waves/waves.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/libs/feather-icons/feather.min.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
    <script src="<?= htmlspecialchars(asset_url('assets/backoffice/zoyothemes.com/silva/html/assets/js/app.js'), ENT_QUOTES, 'UTF-8') ?>"></script>
</body>
</html>
