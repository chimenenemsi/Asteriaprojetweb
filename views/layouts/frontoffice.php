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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root{--monta-green:#6ca138;--monta-dark:#1b2115;--monta-muted:#60706a;--monta-offwhite:#f7f4ee}
        *{box-sizing:border-box}
        body{margin:0;font-family:Outfit,"Segoe UI",Arial,sans-serif;background:linear-gradient(180deg,#fefcf8 0%,#f7f4ee 100%);color:#153122}
        a{color:inherit; text-decoration: none;}
        .monta-shell{max-width:1180px;margin:0 auto;padding:0 20px}
        .monta-topbar{background:#181614;color:#fff;font-size:13px;line-height:1.2}
        .monta-topbar-inner,.monta-navbar,.monta-footer-inner{display:flex;align-items:center;justify-content:space-between;gap:16px}
        .monta-topbar-inner{min-height:20px;padding:0}
        .monta-topbar-left,.monta-topbar-right{display:flex;align-items:center;gap:28px;white-space:nowrap}
        .monta-navbar-wrap{position:sticky;top:0;z-index:1020;background:rgba(28,37,17,.9);backdrop-filter:blur(8px)}
        .monta-navbar{min-height:74px}
        .monta-brand{display:inline-flex;align-items:center;flex:0 0 auto;text-decoration:none;gap:12px}
        .monta-brand img{width:88px;height:auto;display:block}
        .monta-brand-text{color:#fff;font-size:24px;font-weight:700;line-height:1}
        .monta-nav{display:flex;flex:1 1 auto;flex-wrap:wrap;justify-content:center;gap:8px}
        .monta-nav a{padding:16px 14px;color:#fff;text-decoration:none;font-size:16px;font-weight:500;transition:opacity .2s ease}
        .monta-nav a:hover{opacity:.78}
        .monta-nav a.active{opacity:1;border-bottom:2px solid #6ca138;padding-bottom:14px}
        .monta-nav-icon{display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;color:#fff;text-decoration:none;flex:0 0 auto;font-size:14px}
        .monta-account-nav{display:flex;align-items:center;gap:8px;flex:0 0 auto}
        .monta-account-link{display:inline-flex;align-items:center;justify-content:center;min-height:36px;padding:8px 13px;border-radius:999px;color:#fff;text-decoration:none;font-size:13px;font-weight:700;border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.08)}
        .monta-account-link.primary{background:#6ca138;border-color:#6ca138;box-shadow:0 12px 24px rgba(108,161,56,.22)}
        .monta-main{padding:44px 0 70px}
        .monta-hero{display:grid;grid-template-columns:1.15fr .85fr;gap:28px;align-items:center;padding:42px;border-radius:34px;background:linear-gradient(135deg,rgba(255,255,255,.94),rgba(255,244,232,.88)),url("<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/banner-bg.jpg'), ENT_QUOTES, 'UTF-8') ?>") center/cover no-repeat;box-shadow:0 24px 60px rgba(21,49,34,.08)}
        .monta-eyebrow{display:inline-block;margin-bottom:14px;padding:8px 14px;border-radius:999px;background:rgba(108,161,56,.12);color:var(--monta-green);font-size:14px;font-weight:700;letter-spacing:.05em;text-transform:uppercase}
        .monta-hero h1,.monta-hero h2{margin:0 0 16px;line-height:1.05;font-size:clamp(34px,4vw,58px)}
        .monta-hero p{margin:0 0 14px;max-width:620px;color:var(--monta-muted);font-size:17px;line-height:1.7}
        .monta-actions{display:flex;flex-wrap:wrap;gap:12px;margin-top:26px}
        .monta-button,.btn{display:inline-flex;align-items:center;justify-content:center;text-decoration:none;border-radius:999px;padding:12px 18px;font-weight:600;background:var(--monta-green);color:#fff;box-shadow:0 16px 30px rgba(108,161,56,.24);border:none;cursor:pointer}
        .monta-button.secondary,.btn-secondary{background:#fff;color:#153122;box-shadow:inset 0 0 0 1px rgba(21,49,34,.12)}
        .btn-danger{background:#be123c;color:#fff;box-shadow:none}
        .monta-hero-visual{padding:18px;border-radius:28px;background:rgba(255,255,255,.78);box-shadow:inset 0 0 0 1px rgba(21,49,34,.06)}
        .monta-hero-visual img{width:100%;display:block;border-radius:20px;object-fit:cover}
        .monta-section{margin-top:28px}
        .monta-grid,.grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
        .monta-card,.card{padding:26px;border-radius:24px;background:#fff;box-shadow:0 18px 42px rgba(21,49,34,.07)}
        .monta-card h3,.card h3{margin:0 0 12px;font-size:22px}
        .monta-card p,.monta-strip span,.muted{color:var(--monta-muted);line-height:1.7}
        .monta-footer{margin-top:48px;padding:26px 0 40px;border-top:1px solid rgba(21,49,34,.1);color:var(--monta-muted)}
        .header-line{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap}
        .actions{display:flex;gap:12px;flex-wrap:wrap;align-items:center}
        .table{width:100%;border-collapse:collapse}
        .table th,.table td{padding:14px 12px;border-bottom:1px solid rgba(21,49,34,.08);text-align:left;vertical-align:top}
        .badge{display:inline-block;padding:6px 12px;border-radius:999px;background:rgba(108,161,56,.12);color:#476b26;font-size:12px;font-weight:700}
        input,select,textarea{width:100%;padding:12px 14px;border:1px solid rgba(21,49,34,.14);border-radius:14px;box-sizing:border-box;font:inherit;background:#fff}
        label{display:block;margin-bottom:6px;font-weight:600}
        /* Custom Grid overrides to prevent conflicts with Bootstrap */
        .monta-main .row{display:flex;flex-wrap:wrap;margin-right:-15px;margin-left:-15px}
        .monta-main .col-12,.monta-main .col-6,.monta-main .col-4,.monta-main .col-3{position:relative;width:100%;padding-right:15px;padding-left:15px}
        .monta-main .col-12{flex:0 0 100%;max-width:100%}
        .monta-main .col-6{flex:0 0 50%;max-width:50%}
        .monta-main .col-4{flex:0 0 33.333333%;max-width:33.333333%}
        .monta-main .col-3{flex:0 0 25%;max-width:25%}
        @media (max-width:900px){
            .monta-main .col-6,.monta-main .col-4,.monta-main .col-3{flex:0 0 100%;max-width:100%}
        }
        .error{color:#b91c1c;font-size:13px;margin-top:4px}
        .field{display:grid;gap:8px}
        .field label{margin:0;font-weight:700}
        .field-help{font-size:13px;color:var(--monta-muted);line-height:1.5}
        .form-shell{display:grid;gap:20px}
        .form-section{padding:22px;border:1px solid rgba(21,49,34,.08);border-radius:22px;background:linear-gradient(180deg,rgba(255,255,255,.98),rgba(249,246,239,.98))}
        .form-section h2{margin:0 0 6px;font-size:20px}
        .form-section p{margin:0 0 18px;color:var(--monta-muted);line-height:1.6}
        .summary-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(170px,1fr));gap:12px}
        .summary-item{padding:14px 16px;border-radius:18px;border:1px solid rgba(21,49,34,.08);background:#faf8f3}
        .summary-item strong{display:block;margin-bottom:4px;font-size:12px;text-transform:uppercase;color:var(--monta-muted)}
        .readonly-input{background:#f2f4ee}
        .catalog-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:18px}
        .catalog-card{padding:26px;border-radius:24px;background:#fff;box-shadow:0 18px 42px rgba(21,49,34,.07);display:grid;gap:18px}
        .catalog-top{display:flex;justify-content:space-between;gap:16px;align-items:flex-start}
        .catalog-price{font-size:28px;font-weight:700;line-height:1}
        .catalog-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
        .catalog-meta-item{padding:12px 14px;border:1px solid rgba(21,49,34,.08);border-radius:16px;background:#faf8f3}
        .catalog-meta-item strong{display:block;margin-bottom:4px;font-size:12px;text-transform:uppercase;color:var(--monta-muted)}
        .order-panel{padding-top:18px;border-top:1px solid rgba(21,49,34,.08);display:grid;gap:16px}
        .order-message{padding:12px 14px;border-radius:16px;background:rgba(108,161,56,.12);color:#36521f;line-height:1.5}
        .error-summary{padding:12px 14px;border-radius:16px;background:rgba(185,28,28,.08);color:#991b1b;line-height:1.5}
        form.inline{display:inline}
        @media (max-width:900px){.monta-hero,.monta-grid,.grid{grid-template-columns:1fr}.monta-navbar,.monta-topbar-inner,.monta-footer-inner{flex-direction:column;align-items:flex-start}.monta-topbar-left,.monta-topbar-right,.monta-nav,.monta-account-nav{flex-wrap:wrap;justify-content:flex-start}}
    </style>
</head>
<body>
    <?php
    $currentRoute = trim((string) ($_GET['route'] ?? 'frontoffice/home'), '/');
    $section = $currentSection ?? 'home';
    $switchRoute = in_array($section, ['categories', 'products', 'orders', 'goals', 'records'], true) ? 'backoffice/' . $section : 'backoffice/dashboard';
    ?>
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
            <a class="monta-brand" href="<?= htmlspecialchars(route_url('frontoffice/home'), ENT_QUOTES, 'UTF-8') ?>">
                <img src="<?= htmlspecialchars(asset_url('assets/imgs/logo.png'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria logo">
                <span class="monta-brand-text">Asteria</span>
            </a>

            <div class="monta-nav">
                <?php foreach (frontoffice_navigation() as $slug => $label): ?>
                    <?php $route = 'frontoffice/' . $slug; ?>
                    <a href="<?= htmlspecialchars(route_url($route), ENT_QUOTES, 'UTF-8') ?>" class="<?= $currentRoute === $route ? 'active' : '' ?>">
                        <?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </div>
            <div class="monta-account-nav">
                <?php if (isset($_SESSION['user'])): ?>
                    <a class="monta-account-link primary" href="<?= htmlspecialchars(action_url('user_dashboard'), ENT_QUOTES, 'UTF-8') ?>">My Space</a>
                    <a class="monta-account-link" href="<?= htmlspecialchars(action_url('logout'), ENT_QUOTES, 'UTF-8') ?>">Logout</a>
                <?php else: ?>
                    <a class="monta-account-link" href="<?= htmlspecialchars(action_url('login'), ENT_QUOTES, 'UTF-8') ?>">Login</a>
                    <a class="monta-account-link primary" href="<?= htmlspecialchars(action_url('register'), ENT_QUOTES, 'UTF-8') ?>">Register</a>
                <?php endif; ?>
                <?php if (isset($_SESSION['user']) && ($_SESSION['user']['role'] ?? 'user') === 'admin'): ?>
                    <a class="monta-nav-icon" href="<?= htmlspecialchars(route_url($switchRoute), ENT_QUOTES, 'UTF-8') ?>" aria-label="Backoffice">BO</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <main class="monta-main">
        <div class="monta-shell">
            <?= $content ?>
        </div>
    </main>

    <footer class="monta-footer">
        <div class="monta-shell monta-footer-inner"></div>
    </footer>

    <style>
        .product-ai-bubble{position:fixed;right:24px;bottom:24px;z-index:9999;display:flex;flex-direction:column;align-items:flex-end;gap:12px;font-family:Outfit,"Segoe UI",Arial,sans-serif}
        .product-ai-toggle{min-width:98px;height:60px;border:0;border-radius:999px;background:linear-gradient(135deg,#6ca138,#2d4a1e);color:#fff;font-size:16px;font-weight:900;letter-spacing:.02em;box-shadow:0 18px 42px rgba(45,74,30,.32);cursor:pointer;padding:0 20px}
        .product-ai-panel{width:min(380px,calc(100vw - 32px));display:none;overflow:hidden;border-radius:24px;background:#fff;box-shadow:0 24px 70px rgba(21,49,34,.22);border:1px solid rgba(21,49,34,.08)}
        .product-ai-panel.open{display:block}
        .product-ai-head{padding:16px 18px;background:#1b2115;color:#fff;display:flex;justify-content:space-between;gap:12px;align-items:center}
        .product-ai-head strong{display:block;font-size:15px}.product-ai-head span{display:block;font-size:12px;color:rgba(255,255,255,.68);margin-top:2px}
        .product-ai-close{border:0;background:rgba(255,255,255,.12);color:#fff;border-radius:999px;width:30px;height:30px;cursor:pointer}
        .product-ai-messages{height:310px;overflow-y:auto;padding:16px;display:flex;flex-direction:column;gap:10px;background:linear-gradient(180deg,#faf8f3,#fff)}
        .product-ai-msg{max-width:88%;padding:11px 14px;border-radius:16px;font-size:14px;line-height:1.55;white-space:pre-wrap}
        .product-ai-msg.bot{align-self:flex-start;background:#eef6e7;color:#23351c;border-bottom-left-radius:5px}
        .product-ai-msg.user{align-self:flex-end;background:#6ca138;color:#fff;border-bottom-right-radius:5px}
        .product-ai-msg.rich{max-width:94%;background:#fff;border:1px solid rgba(108,161,56,.18);box-shadow:0 10px 24px rgba(21,49,34,.08);white-space:normal;padding:12px}
        .product-ai-title{display:flex;align-items:center;gap:8px;margin:0 0 9px;font-size:14px;font-weight:900;color:#1b2115}
        .product-ai-section{margin-top:10px;padding:10px 11px;border-radius:15px;background:#f5faef;border:1px solid rgba(108,161,56,.18)}
        .product-ai-section strong{display:block;margin-bottom:4px;color:#2d4a1e;font-size:13px}
        .product-ai-products{display:grid;gap:8px;margin-top:8px}
        .product-ai-card{border-radius:16px;border:1px solid rgba(21,49,34,.08);background:linear-gradient(180deg,#fff,#fbfaf5);padding:11px 12px;box-shadow:0 8px 18px rgba(21,49,34,.06)}
        .product-ai-card-name{font-weight:900;color:#1b2115;margin-bottom:6px}
        .product-ai-card-meta{display:flex;flex-wrap:wrap;gap:6px;font-size:12px;color:#60706a}
        .product-ai-pill{display:inline-flex;align-items:center;border-radius:999px;padding:4px 8px;background:#eef6e7;color:#385820;font-weight:700}
        .product-ai-pill.price{background:#1b2115;color:#fff}.product-ai-pill.stock{background:#f5faef;color:#2d4a1e}
        .product-ai-list{margin:7px 0 0 18px;padding:0}.product-ai-list li{margin:4px 0}
        .product-ai-typing{display:none;padding:0 16px 10px;color:#60706a;font-size:13px;background:#fff}
        .product-ai-row{display:flex;gap:8px;padding:12px;border-top:1px solid rgba(21,49,34,.08);background:#fff}
        .product-ai-row input{flex:1;border-radius:14px;border:1px solid rgba(21,49,34,.14);padding:11px 12px;font-size:14px}
        .product-ai-row button{border:0;border-radius:14px;background:#6ca138;color:#fff;padding:0 15px;font-weight:700;cursor:pointer}
        .product-ai-suggestions{display:flex;gap:7px;flex-wrap:wrap;padding:12px 12px 0;background:#fff}
        .product-ai-chip{border:1px solid rgba(108,161,56,.25);background:#f5faef;color:#34511f;border-radius:999px;padding:7px 10px;font-size:12px;cursor:pointer}
    </style>
    <div class="product-ai-bubble" data-product-ai>
        <div class="product-ai-panel" id="product-ai-panel">
            <div class="product-ai-head">
                <div><strong>Asteria Product Assistant</strong><span>Ask Gemini about the live product catalog.</span></div>
                <button class="product-ai-close" type="button" aria-label="Close product assistant">x</button>
            </div>
            <div class="product-ai-suggestions">
                <button class="product-ai-chip" type="button" data-ai-suggest="What is the cheapest in-stock product?">Cheapest in stock</button>
                <button class="product-ai-chip" type="button" data-ai-suggest="Suggest products for weight loss under 30 DT.">Weight loss under 30 DT</button>
                <button class="product-ai-chip" type="button" data-ai-suggest="Which protein products do you recommend?">Protein picks</button>
            </div>
            <div class="product-ai-messages" id="product-ai-messages">
                <div class="product-ai-msg bot">Hi! Tell me your goal or budget and I will recommend products from the Asteria catalog.</div>
            </div>
            <div class="product-ai-typing" id="product-ai-typing">Asteria is thinking...</div>
            <div class="product-ai-row">
                <input id="product-ai-input" type="text" placeholder="Ask about products, budget, stock...">
                <button id="product-ai-send" type="button">Send</button>
            </div>
        </div>
        <button class="product-ai-toggle" id="product-ai-toggle" type="button" aria-label="Open product assistant">AI Chat</button>
    </div>
    <script>
    (() => {
        const panel = document.getElementById('product-ai-panel');
        const bubble = document.querySelector('[data-product-ai]');
        const toggle = document.getElementById('product-ai-toggle');
        const close = document.querySelector('.product-ai-close');
        const messages = document.getElementById('product-ai-messages');
        const input = document.getElementById('product-ai-input');
        const send = document.getElementById('product-ai-send');
        const typing = document.getElementById('product-ai-typing');

        const appendMessage = (text, role, isRich = false) => {
            const msg = document.createElement('div');
            msg.className = 'product-ai-msg ' + role + (isRich ? ' rich' : '');
            if (isRich) {
                msg.innerHTML = text;
            } else {
                msg.innerText = text;
            }
            messages.appendChild(msg);
            messages.scrollTop = messages.scrollHeight;
        };

        const askAi = async (prompt) => {
            if (!prompt.trim()) return;
            appendMessage(prompt, 'user');
            input.value = '';
            typing.style.display = 'block';

            try {
                const response = await fetch('<?= htmlspecialchars(route_url('frontoffice/ai-chat'), ENT_QUOTES, 'UTF-8') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt })
                });
                const data = await response.json();
                if (data.response) {
                    appendMessage(data.response, 'bot', data.is_rich || false);
                } else {
                    appendMessage('Sorry, I encountered an error.', 'bot');
                }
            } catch (err) {
                appendMessage('Connection error.', 'bot');
            } finally {
                typing.style.display = 'none';
            }
        };

        toggle.onclick = () => panel.classList.toggle('open');
        close.onclick = () => panel.classList.remove('open');
        send.onclick = () => askAi(input.value);
        input.onkeydown = (e) => { if (e.key === 'Enter') askAi(input.value); };
        document.querySelectorAll('[data-ai-suggest]').forEach(btn => {
            btn.onclick = () => askAi(btn.getAttribute('data-ai-suggest'));
        });
    })();
    </script>
</body>
</html>
