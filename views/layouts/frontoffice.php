<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(($pageTitle ?? 'Monta') . ' | Frontoffice', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/themes/hello-elementor/style.min0875.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/themes/hello-elementor/theme.min0875.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/plugins/template-kit-export/public/assets/css/template-kit-export-public.min365c.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/plugins/header-footer-elementor/assets/css/header-footer-elementora242.css'), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/elementor/css/global975d.css'), ENT_QUOTES, 'UTF-8') ?>">
    <style>
        :root {
            --monta-green: #6ca138;
            --monta-dark: #1b2115;
            --monta-muted: #60706a;
            --monta-offwhite: #f7f4ee;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Outfit, "Segoe UI", Arial, sans-serif;
            background: linear-gradient(180deg, #fefcf8 0%, #f7f4ee 100%);
            color: #153122;
        }

        a {
            color: inherit;
        }

        .monta-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .monta-topbar {
            background: #181614;
            color: #ffffff;
            font-size: 13px;
            line-height: 1.2;
        }

        .monta-topbar-inner,
        .monta-navbar,
        .monta-footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .monta-topbar-inner {
            min-height: 20px;
            padding: 0;
        }

        .monta-topbar-left,
        .monta-topbar-right {
            display: flex;
            align-items: center;
            gap: 28px;
            white-space: nowrap;
        }

        .monta-navbar-wrap {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(28, 37, 17, 0.9);
            backdrop-filter: blur(8px);
        }

        .monta-navbar {
            min-height: 74px;
        }

        .monta-brand {
            display: inline-flex;
            align-items: center;
            flex: 0 0 auto;
            text-decoration: none;
            gap: 12px;
        }

        .monta-brand img {
            width: 88px;
            height: auto;
            display: block;
        }

        .monta-brand-text {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
        }

        .monta-nav {
            display: flex;
            flex: 1 1 auto;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .monta-nav a {
            padding: 16px 14px;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }

        .monta-nav a:hover,
        .monta-nav a.active {
            opacity: 0.78;
        }

        .monta-nav-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            border: 1px solid rgba(255, 255, 255, 0.9);
            color: #ffffff;
            font-size: 0;
            text-decoration: none;
            flex: 0 0 auto;
        }

        .monta-main {
            padding: 44px 0 70px;
        }

        .monta-hero {
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 28px;
            align-items: center;
            padding: 42px;
            border-radius: 34px;
            background:
                linear-gradient(135deg, rgba(255, 255, 255, 0.94), rgba(255, 244, 232, 0.88)),
                url("<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/banner-bg.jpg'), ENT_QUOTES, 'UTF-8') ?>") center/cover no-repeat;
            box-shadow: 0 24px 60px rgba(21, 49, 34, 0.08);
        }

        .monta-eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(108, 161, 56, 0.12);
            color: var(--monta-green);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .monta-hero h1,
        .monta-hero h2 {
            margin: 0 0 16px;
            line-height: 1.05;
            font-size: clamp(34px, 4vw, 58px);
        }

        .monta-hero p {
            margin: 0 0 14px;
            max-width: 620px;
            color: var(--monta-muted);
            font-size: 17px;
            line-height: 1.7;
        }

        .monta-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .monta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 600;
            background: var(--monta-green);
            color: #ffffff;
            box-shadow: 0 16px 30px rgba(108, 161, 56, 0.24);
        }

        .monta-button.secondary {
            background: #ffffff;
            color: #153122;
            box-shadow: inset 0 0 0 1px rgba(21, 49, 34, 0.12);
        }

        .monta-hero-visual {
            padding: 18px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.78);
            box-shadow: inset 0 0 0 1px rgba(21, 49, 34, 0.06);
        }

        .monta-hero-visual img {
            width: 100%;
            display: block;
            border-radius: 20px;
            object-fit: cover;
        }

        .monta-section {
            margin-top: 28px;
        }

        .monta-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .monta-card {
            padding: 26px;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 18px 42px rgba(21, 49, 34, 0.07);
        }

        .monta-card h3 {
            margin: 0 0 12px;
            font-size: 22px;
        }

        .monta-card p,
        .monta-strip span {
            color: var(--monta-muted);
            line-height: 1.7;
        }

        .monta-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            padding: 24px;
            border-radius: 28px;
            background: #153122;
            color: #ffffff;
        }

        .monta-strip div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
        }

        .monta-strip strong {
            display: block;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .monta-footer {
            margin-top: 48px;
            padding: 26px 0 40px;
            border-top: 1px solid rgba(21, 49, 34, 0.1);
            color: var(--monta-muted);
        }

        @media (max-width: 900px) {
            .monta-hero,
            .monta-grid,
            .monta-strip {
                grid-template-columns: 1fr;
            }

            .monta-navbar,
            .monta-topbar-inner,
            .monta-footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .monta-topbar-left,
            .monta-topbar-right,
            .monta-nav {
                flex-wrap: wrap;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="monta-topbar">
        <div class="monta-shell monta-topbar-inner">
            <div class="monta-topbar-left">
                <span>We're 24/7 Hours Service Provider!</span>
            </div>
            <div class="monta-topbar-right">
                <span>info@example.com</span>
                <span>+1(888)1234-5678</span>
            </div>
        </div>
    </div>

    <div class="monta-navbar-wrap">
        <div class="monta-shell monta-navbar">
            <a class="monta-brand" href="<?= htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria logo">
                <span class="monta-brand-text">Asteria</span>
            </a>

            <nav class="monta-nav">
                <?php foreach ($navigation as $slug => $label): ?>
                    <?php
                    if ($slug === 'home') {
                        $route = 'home';
                    } elseif ($slug === 'about-us') {
                        $route = 'about-us';
                    } else {
                        $route = 'frontoffice/' . $slug;
                    }
                    $isActive = $currentRoute === $route || ($slug === 'home' && $currentRoute === 'frontoffice/home');
                    ?>
                    <a class="<?= $isActive ? 'active' : '' ?>" href="<?= htmlspecialchars(route_url($route), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            <a class="monta-nav-icon" href="#" aria-label="Search"></a>
        </div>
    </div>

    <main class="monta-main">
        <div class="monta-shell">
            <?= $content ?>
        </div>
    </main>

    <footer class="monta-footer">
        <div class="monta-shell monta-footer-inner">
             </div>
    </footer>
</body>
</html>
