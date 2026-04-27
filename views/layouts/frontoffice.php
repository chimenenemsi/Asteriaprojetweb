<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(($pageTitle ?? 'Asteria') . ' | Frontoffice', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/themes/hello-elementor/style.min0875.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/themes/hello-elementor/theme.min0875.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/plugins/template-kit-export/public/assets/css/template-kit-export-public.min365c.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/plugins/header-footer-elementor/assets/css/header-footer-elementora242.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/elementor/css/global975d.css'), ENT_QUOTES, 'UTF-8') ?>">
    <style>
        :root{--chimene-green:#6ca138;--chimene-dark:#1b2115;--chimene-muted:#60706a;--chimene-offwhite:#f7f4ee}
        *{box-sizing:border-box}
        body{margin:0;font-family:Outfit,"Segoe UI",Arial,sans-serif;background:linear-gradient(180deg,#fefcf8 0%,#f7f4ee 100%);color:#153122}
        a{color:inherit}
        .chimene-shell{max-width:1180px;margin:0 auto;padding:0 20px}
        .chimene-topbar{background:#181614;color:#fff;font-size:13px;line-height:1.2}
        .chimene-topbar-inner,.chimene-navbar,.chimene-footer-inner{display:flex;align-items:center;justify-content:space-between;gap:16px}
        .chimene-topbar-inner{min-height:20px;padding:0}
        .chimene-topbar-left,.chimene-topbar-right{display:flex;align-items:center;gap:28px;white-space:nowrap}
        .chimene-navbar-wrap{position:sticky;top:0;z-index:20;background:rgba(28,37,17,.9);backdrop-filter:blur(8px)}
        .chimene-navbar{min-height:74px}
        .chimene-brand{display:inline-flex;align-items:center;flex:0 0 auto;text-decoration:none;gap:12px}
        .chimene-brand img{width:88px;height:auto;display:block}
        .chimene-brand-text{color:#fff;font-size:24px;font-weight:700;line-height:1}
        .chimene-nav{display:flex;flex:1 1 auto;flex-wrap:wrap;justify-content:center;gap:8px}
        .chimene-nav a{padding:16px 14px;color:#fff;text-decoration:none;font-size:16px;font-weight:500;transition:opacity .2s ease}
        .chimene-nav a:hover,.chimene-nav a.active{opacity:.78}
        .chimene-nav-icon{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;color:#fff;text-decoration:none;flex:0 0 auto;font-size:14px}
        .chimene-main{padding:44px 0 70px}
        .chimene-hero{display:grid;grid-template-columns:1.15fr .85fr;gap:28px;align-items:center;padding:42px;border-radius:34px;background:linear-gradient(135deg,rgba(255,255,255,.94),rgba(255,244,232,.88)),url("<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/banner-bg.jpg'), ENT_QUOTES, 'UTF-8') ?>") center/cover no-repeat;box-shadow:0 24px 60px rgba(21,49,34,.08)}
        .chimene-eyebrow{display:inline-block;margin-bottom:14px;padding:8px 14px;border-radius:999px;background:rgba(108,161,56,.12);color:var(--chimene-green);font-size:14px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}
        .chimene-hero h1,.chimene-hero h2{margin:0 0 16px;line-height:1.05;font-size:clamp(34px,4vw,58px)}
        .chimene-hero p{margin:0 0 14px;max-width:620px;color:var(--chimene-muted);font-size:17px;line-height:1.7}
        .chimene-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:26px}
        .chimene-button,.btn{display:inline-flex;align-items:center;justify-content:center;text-decoration:none;border-radius:999px;padding:12px 18px;font-weight:600;background:var(--chimene-green);color:#fff;box-shadow:0 16px 30px rgba(108,161,56,.24);border:none;cursor:pointer}
        .chimene-button.secondary,.btn-secondary{background:#fff;color:#153122;box-shadow:inset 0 0 0 1px rgba(21,49,34,.12)}
        .btn-danger{background:#be123c;color:#fff;box-shadow:none}
        .chimene-hero-visual{padding:18px;border-radius:28px;background:rgba(255,255,255,.78);box-shadow:inset 0 0 0 1px rgba(21,49,34,.06)}
        .chimene-hero-visual img{width:100%;display:block;border-radius:20px;object-fit:cover}
        .chimene-section{margin-top:28px}
        .chimene-grid,.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
        .chimene-card,.card{padding:26px;border-radius:24px;background:#fff;box-shadow:0 18px 42px rgba(21,49,34,.07)}
        .chimene-card h3,.card h3{margin:0 0 12px;font-size:22px}
        .chimene-card p,.chimene-strip span,.muted{color:var(--chimene-muted);line-height:1.7}
        .chimene-footer{margin-top:48px;padding:26px 0 40px;border-top:1px solid rgba(21,49,34,.1);color:var(--chimene-muted)}
        .header-line{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap}
        .actions{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
        .table{width:100%;border-collapse:collapse}
        .table th,.table td{padding:14px 12px;border-bottom:1px solid rgba(21,49,34,.08);text-align:left;vertical-align:top}
        .badge{display:inline-block;padding:6px 12px;border-radius:999px;background:rgba(108,161,56,.12);color:#476b26;font-size:12px;font-weight:700}
        input,select,textarea{width:100%;padding:12px 14px;border:1px solid rgba(21,49,34,.14);border-radius:14px;box-sizing:border-box;font:inherit;background:#fff}
        label{display:block;margin-bottom:6px;font-weight:600}
        .row{display:grid;grid-template-columns:repeat(12,1fr);gap:16px}
        .col-12{grid-column:span 12}.col-6{grid-column:span 6}.col-4{grid-column:span 4}.col-3{grid-column:span 3}
        .error{color:#b91c1c;font-size:13px;margin-top:4px}
        .field{display:grid;gap:8px}
        .field label{margin:0;font-weight:700}
        .field-help{font-size:13px;color:var(--chimene-muted);line-height:1.5}
        .form-shell{display:grid;gap:20px}
        .form-section{padding:22px;border:1px solid rgba(21,49,34,.08);border-radius:22px;background:linear-gradient(180deg,rgba(255,255,255,.98),rgba(249,246,239,.98))}
        .form-section h2{margin:0 0 6px;font-size:20px}
        .form-section p{margin:0 0 18px;color:var(--chimene-muted);line-height:1.6}
        .summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px}
        .summary-item{padding:14px 16px;border-radius:18px;border:1px solid rgba(21,49,34,.08);background:#faf8f3}
        .summary-item strong{display:block;margin-bottom:4px;font-size:12px;text-transform:uppercase;color:var(--chimene-muted)}
        .readonly-input{background:#f2f4ee}
        .catalog-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px}
        .catalog-card{padding:26px;border-radius:24px;background:#fff;box-shadow:0 18px 42px rgba(21,49,34,.07);display:grid;gap:18px}
        .catalog-top{display:flex;justify-content:space-between;gap:16px;align-items:flex-start}
        .catalog-price{font-size:28px;font-weight:700;line-height:1}
        .catalog-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .catalog-meta-item{padding:12px 14px;border:1px solid rgba(21,49,34,.08);border-radius:16px;background:#faf8f3}
        .catalog-meta-item strong{display:block;margin-bottom:4px;font-size:12px;text-transform:uppercase;color:var(--chimene-muted)}
        .order-panel{padding-top:18px;border-top:1px solid rgba(21,49,34,.08);display:grid;gap:16px}
        .order-message{padding:12px 14px;border-radius:16px;background:rgba(108,161,56,.12);color:#36521f;line-height:1.5}
        .error-summary{padding:12px 14px;border-radius:16px;background:rgba(185,28,28,.08);color:#991b1b;line-height:1.5}
        form.inline{display:inline}
        @media (max-width:900px){.chimene-hero,.chimene-grid,.grid{grid-template-columns:1fr}.chimene-navbar,.chimene-topbar-inner,.chimene-footer-inner{flex-direction:column;align-items:flex-start}.chimene-topbar-left,.chimene-topbar-right,.chimene-nav{flex-wrap:wrap;justify-content:flex-start}.col-6,.col-4,.col-3{grid-column:span 12}}
    </style>
</head>
<body>
    <?php
    $currentRoute = trim((string) ($_GET['route'] ?? 'frontoffice/home'), '/');
    $section = $currentSection ?? 'home';
    $switchRoute = in_array($section, ['categories', 'products', 'orders'], true) ? 'backoffice/' . $section : 'backoffice/dashboard';
    ?>
    <div class="chimene-topbar">
        <div class="chimene-shell chimene-topbar-inner">
            <div class="chimene-topbar-left">
                <span>We're 24/7 Hours Service Provider!</span>
            </div>
            <div class="chimene-topbar-right">
                <span>info@example.com</span>
                <span>+1(888)1234-5678</span>
            </div>
        </div>
    </div>

    <div class="chimene-navbar-wrap">
        <div class="chimene-shell chimene-navbar">
            <a class="chimene-brand" href="<?= htmlspecialchars(route_url('frontoffice/home'), ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria logo">
                <span class="chimene-brand-text">Asteria</span>
            </a>

            <nav class="chimene-nav">
                <?php foreach (frontoffice_navigation() as $slug => $label): ?>
                    <?php $route = 'frontoffice/' . $slug; ?>
                    <a class="<?= $currentRoute === $route ? 'active' : '' ?>" href="<?= htmlspecialchars(route_url($route), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <a class="chimene-nav-icon" href="<?= htmlspecialchars(route_url($switchRoute), ENT_QUOTES, 'UTF-8') ?>" aria-label="Backoffice">BO</a>
        </div>
    </div>

    <main class="chimene-main">
        <div class="chimene-shell">
            <?= $content ?>
        </div>
    </main>

    <footer class="chimene-footer">
        <div class="chimene-shell chimene-footer-inner"></div>
    </footer>
</body>
</html>
