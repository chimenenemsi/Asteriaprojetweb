<section class="monta-section">
    <div class="monta-hero">
        <div>
            <span class="monta-eyebrow">Asteria Unified Home</span>
            <h1>Manage products, programs, and progress in one place.</h1>
            <p>Explore our products, join coaching programs, and track your fitness goals with our integrated AI-powered platform.</p>
            <div class="monta-actions">
                <a class="monta-button" href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>">Browse Products</a>
                <a class="monta-button" href="<?= htmlspecialchars(route_url('frontoffice/programs'), ENT_QUOTES, 'UTF-8') ?>">Coaching Programs</a>
                <a class="monta-button secondary" href="<?= htmlspecialchars(route_url('frontoffice/goals'), ENT_QUOTES, 'UTF-8') ?>">Track Progress</a>
                <?php if (!isset($_SESSION['user'])): ?>
                    <a class="monta-button secondary" href="<?= htmlspecialchars(action_url('login'), ENT_QUOTES, 'UTF-8') ?>">Login</a>
                <?php else: ?>
                    <a class="monta-button secondary" href="<?= htmlspecialchars(action_url('user_dashboard'), ENT_QUOTES, 'UTF-8') ?>">My Space</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="monta-hero-visual">
            <img src="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria Platform">
        </div>
    </div>
</section>

<section class="monta-section">
    <div class="monta-grid">
        <div class="monta-card">
            <h3>Products & Shop</h3>
            <p>Order supplements, equipment, and nutrition guides from our curated catalog.</p>
        </div>
        <div class="monta-card">
            <h3>Coaching & AI</h3>
            <p>Follow expert training plans with our Gemini-powered AI coach available 24/7.</p>
        </div>
        <div class="monta-card">
            <h3>Progress & Goals</h3>
            <p>Set measurable targets, record your metrics, and visualize your success journey.</p>
        </div>
    </div>
</section>

<style>
.unified-polish-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:22px;margin-top:28px}
.unified-polish-card{padding:28px;border-radius:28px;background:#fff;box-shadow:0 22px 54px rgba(21,49,34,.08);border:1px solid rgba(21,49,34,.06)}
.unified-polish-card h2{margin:0 0 12px;font-size:30px;line-height:1.1;color:#153122}
.unified-polish-card p{color:#60706a;line-height:1.7}
.unified-feature-list{display:grid;gap:12px;margin-top:18px}
.unified-feature-list div{padding:14px 16px;border-radius:18px;background:#f7fbf4;color:#315021;border:1px solid rgba(108,161,56,.16)}
.unified-mini-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.unified-mini-stat{display:block;text-decoration:none;padding:18px;border-radius:20px;background:linear-gradient(180deg,#f8fafc,#fff);border:1px solid rgba(21,49,34,.08)}
.unified-mini-stat strong{display:block;font-size:26px;color:#6ca138}
.unified-mini-stat span{display:block;color:#60706a;margin-top:5px}
@media (max-width:900px){.unified-polish-grid,.unified-mini-stats{grid-template-columns:1fr}}
</style>

<section class="unified-polish-grid">
    <div class="unified-polish-card">
        <span class="monta-eyebrow">Integrated Experience</span>
        <h2>Your journey, our technology, shared results.</h2>
        <p>The Asteria platform now brings everything together. Whether you are shopping for products, following a training program, or tracking your body metrics, everything works as one.</p>
        <div class="unified-feature-list">
            <div>Catalog-aware AI coach using the products and programs from our database.</div>
            <div>Progress tracking linked to your training goals and nutrition plans.</div>
            <div>Seamless transition between shopping, coaching, and monitoring.</div>
        </div>
    </div>
    <div class="unified-polish-card">
        <h2>Quick Shortcuts</h2>
        <div class="unified-mini-stats">
            <a class="unified-mini-stat" href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>"><strong>Shop</strong><span>Products</span></a>
            <a class="unified-mini-stat" href="<?= htmlspecialchars(route_url('frontoffice/diet-client'), ENT_QUOTES, 'UTF-8') ?>"><strong>Diet</strong><span>Nutrition</span></a>
            <a class="unified-mini-stat" href="<?= htmlspecialchars(route_url('frontoffice/goals'), ENT_QUOTES, 'UTF-8') ?>"><strong>Track</strong><span>Goals</span></a>
            <a class="unified-mini-stat" href="<?= htmlspecialchars(route_url('frontoffice/records'), ENT_QUOTES, 'UTF-8') ?>"><strong>Log</strong><span>Progress</span></a>
        </div>
    </div>
</section>
