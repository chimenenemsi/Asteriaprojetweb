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
        :root {
            --site-green: #6ca138;
            --site-muted: #60706a;
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

        .btn, .monta-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 600;
            background: var(--site-green);
            color: #fff;
            box-shadow: 0 16px 30px rgba(108, 161, 56, .24);
            border: none;
            cursor: pointer;
        }

        .btn-secondary, .monta-button.secondary {
            background: #fff;
            color: #153122;
            box-shadow: inset 0 0 0 1px rgba(21, 49, 34, .12);
        }

        .btn-danger {
            background: #be123c;
            color: #fff;
            box-shadow: none;
        }

        .site-shell {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .site-topbar {
            background: #181614;
            color: #ffffff;
            font-size: 13px;
            line-height: 1.2;
        }

        .site-topbar-inner,
        .site-navbar,
        .site-footer-inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .site-topbar-inner {
            min-height: 20px;
            padding: 0;
        }

        .site-topbar-left,
        .site-topbar-right {
            display: flex;
            align-items: center;
            gap: 28px;
            white-space: nowrap;
        }

        .site-navbar-wrap {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(28, 37, 17, 0.9);
            backdrop-filter: blur(8px);
        }

        .site-navbar {
            min-height: 74px;
        }

        .site-brand {
            display: inline-flex;
            align-items: center;
            flex: 0 0 auto;
            text-decoration: none;
            gap: 12px;
        }

        .site-brand img {
            width: 88px;
            height: auto;
            display: block;
        }

        .site-brand-text {
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
        }

        .site-nav {
            display: flex;
            flex: 1 1 auto;
            flex-wrap: wrap;
            justify-content: center;
            gap: 8px;
        }

        .site-nav a {
            padding: 16px 14px;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            font-weight: 500;
            transition: opacity 0.2s ease;
        }

        .site-nav a:hover,
        .site-nav a.active {
            opacity: 0.78;
        }

        .site-nav-icon {
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

        .site-main {
            padding: 44px 0 70px;
        }

        .site-hero {
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

        .site-eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            padding: 8px 14px;
            border-radius: 999px;
            background: rgba(108, 161, 56, 0.12);
            color: var(--site-green);
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .site-hero h1,
        .site-hero h2 {
            margin: 0 0 16px;
            line-height: 1.05;
            font-size: clamp(34px, 4vw, 58px);
        }

        .site-hero p {
            margin: 0 0 14px;
            max-width: 620px;
            color: var(--site-muted);
            font-size: 17px;
            line-height: 1.7;
        }

        .site-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 26px;
        }

        .site-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            border-radius: 999px;
            padding: 12px 18px;
            font-weight: 600;
            background: var(--site-green);
            color: #ffffff;
            box-shadow: 0 16px 30px rgba(108, 161, 56, 0.24);
        }

        .site-button.secondary {
            background: #ffffff;
            color: #153122;
            box-shadow: inset 0 0 0 1px rgba(21, 49, 34, 0.12);
        }

        .site-hero-visual {
            padding: 18px;
            border-radius: 28px;
            background: rgba(255, 255, 255, 0.78);
            box-shadow: inset 0 0 0 1px rgba(21, 49, 34, 0.06);
        }

        .site-hero-visual img {
            width: 100%;
            display: block;
            border-radius: 20px;
            object-fit: cover;
        }

        .site-section {
            margin-top: 28px;
        }

        .site-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .site-card {
            padding: 26px;
            border-radius: 24px;
            background: #ffffff;
            box-shadow: 0 18px 42px rgba(21, 49, 34, 0.07);
        }

        .site-card h3 {
            margin: 0 0 12px;
            font-size: 22px;
        }

        .site-card p,
        .site-strip span {
            color: var(--site-muted);
            line-height: 1.7;
        }

        .site-strip {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            padding: 24px;
            border-radius: 28px;
            background: #153122;
            color: #ffffff;
        }

        .site-strip div {
            padding: 16px;
            border-radius: 20px;
            background: rgba(255, 255, 255, 0.08);
        }

        .site-strip strong {
            display: block;
            margin-bottom: 8px;
            font-size: 18px;
        }

        .site-footer {
            margin-top: 48px;
            padding: 26px 0 40px;
            border-top: 1px solid rgba(21, 49, 34, 0.1);
            color: var(--site-muted);
        }

        @media (max-width: 900px) {
            .site-hero,
            .site-grid,
            .site-strip {
                grid-template-columns: 1fr;
            }

            .site-navbar,
            .site-topbar-inner,
            .site-footer-inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .site-topbar-left,
            .site-topbar-right,
            .site-nav {
                flex-wrap: wrap;
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="site-topbar">
        <div class="site-shell site-topbar-inner">
            <div class="site-topbar-left">
                <span>We're 24/7 Hours Service Provider!</span>
            </div>
            <div class="site-topbar-right">
                <span>info@example.com</span>
                <span>+1(888)1234-5678</span>
            </div>
        </div>
    </div>

    <div class="site-navbar-wrap">
        <div class="site-shell site-navbar">
            <a class="site-brand" href="<?= htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria logo">
                <span class="site-brand-text">Asteria</span>
            </a>

            <nav class="site-nav">
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
            <a class="site-nav-icon" href="#" aria-label="Search"></a>
        </div>
    </div>

    <main class="site-main">
        <div class="site-shell">
            <?= $content ?>
        </div>
    </main>

    <footer class="site-footer">
        <div class="site-shell site-footer-inner">
             </div>
    </footer>

    <style>
        .ai-summary-button{position:fixed;left:22px;bottom:22px;z-index:9998;border:0;border-radius:999px;padding:13px 18px;background:#1b2115;color:#fff;font-weight:800;box-shadow:0 18px 42px rgba(21,49,34,.28);cursor:pointer}.ai-coach-float{position:fixed;right:22px;bottom:22px;z-index:9998;display:inline-flex;align-items:center;text-decoration:none;border-radius:999px;padding:14px 20px;background:linear-gradient(135deg,#6ca138,#2d4a1e);color:#fff;font-weight:900;box-shadow:0 18px 42px rgba(45,74,30,.32)}
        .ai-summary-panel{position:fixed;left:22px;bottom:78px;z-index:9998;width:min(430px,calc(100vw - 44px));display:none;border-radius:24px;background:#fff;box-shadow:0 24px 70px rgba(21,49,34,.22);border:1px solid rgba(21,49,34,.08);overflow:hidden}
        .ai-summary-panel.open{display:block}.ai-summary-head{display:flex;justify-content:space-between;align-items:center;gap:12px;padding:16px 18px;background:#6ca138;color:#fff}.ai-summary-head strong{display:block}.ai-summary-close{border:0;border-radius:999px;width:30px;height:30px;background:rgba(255,255,255,.18);color:#fff;cursor:pointer}
        .ai-summary-content{padding:18px;color:#153122;line-height:1.65;white-space:pre-wrap;max-height:360px;overflow:auto}.ai-summary-status{color:#60706a}
    </style>
    <div class="ai-summary-panel" id="ai-summary-panel">
        <div class="ai-summary-head"><strong>Résumé IA</strong><button class="ai-summary-close" type="button" aria-label="Close AI summary">x</button></div>
        <div class="ai-summary-content" id="ai-summary-content"><span class="ai-summary-status">Cliquez sur le bouton pour résumer cette page.</span></div>
    </div>
    <button class="ai-summary-button" id="ai-summary-button" type="button">Résumer cette page</button>
    <a class="ai-coach-float" href="<?= htmlspecialchars(route_url('frontoffice/programs'), ENT_QUOTES, 'UTF-8') ?>#coach-ai">Open AI Coach</a>
    <script>
    (() => {
        const panel = document.getElementById('ai-summary-panel');
        const content = document.getElementById('ai-summary-content');
        const button = document.getElementById('ai-summary-button');
        const close = document.querySelector('.ai-summary-close');
        const endpoint = '<?= htmlspecialchars(route_url('frontoffice/ai-summary'), ENT_QUOTES, 'UTF-8') ?>';
        let lastText = '';
        button?.addEventListener('click', async () => {
            const main = document.querySelector('main.site-main') || document.body;
            const text = (main.innerText || '').replace(/\s+/g, ' ').trim().slice(0, 7000);
            panel?.classList.add('open');
            if (content) content.textContent = 'Résumé en cours...';
            if (text === lastText && content && !content.textContent.includes('cours')) return;
            lastText = text;
            try {
                const response = await fetch(endpoint, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({title: document.title, text})});
                const data = await response.json();
                if (content) content.textContent = data.summary || data.error || 'Résumé indisponible.';
            } catch (error) {
                if (content) content.textContent = 'Erreur de connexion. Veuillez réessayer.';
            }
        });
        close?.addEventListener('click', () => panel?.classList.remove('open'));
    })();
    </script>

</body>
</html>
