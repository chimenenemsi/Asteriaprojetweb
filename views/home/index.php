<section class="monta-section">
    <div class="monta-hero">
        <div>
            <span class="monta-eyebrow">Asteria Produits</span>
            <h1>Browse products and place orders from one clean frontoffice.</h1>
            <p>Explore the catalog, compare stock and prices, create delivery orders, and keep the produits module as the real homepage for visitors and signed-in users.</p>
            <div class="monta-actions">
                <a class="monta-button" href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>">Open Products</a>
                <a class="monta-button secondary" href="<?= htmlspecialchars(route_url('frontoffice/categories'), ENT_QUOTES, 'UTF-8') ?>">Open Categories</a>
                <?php if (!isset($_SESSION['user'])): ?>
                    <a class="monta-button secondary" href="<?= htmlspecialchars(action_url('login'), ENT_QUOTES, 'UTF-8') ?>">Login</a>
                <?php else: ?>
                    <a class="monta-button secondary" href="<?= htmlspecialchars(action_url('user_dashboard'), ENT_QUOTES, 'UTF-8') ?>">My Space</a>
                <?php endif; ?>
            </div>
        </div>
        <div class="monta-hero-visual">
            <img src="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria produits">
        </div>
    </div>
</section>

<section class="monta-section">
    <div class="monta-grid">
        <div class="monta-card">
            <h3>Product Categories</h3>
            <p>Organize the catalog into clean sections and keep related products grouped together.</p>
        </div>
        <div class="monta-card">
            <h3>Products</h3>
            <p>Handle SKU, price, stock, and status inside a standalone product flow.</p>
        </div>
        <div class="monta-card">
            <h3>Orders</h3>
            <p>Place orders from the product catalog while backoffice handles delivery state changes later.</p>
        </div>
    </div>
</section>

<style>
.produits-polish-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:22px;margin-top:28px}
.produits-polish-card{padding:28px;border-radius:28px;background:#fff;box-shadow:0 22px 54px rgba(21,49,34,.08);border:1px solid rgba(21,49,34,.06)}
.produits-polish-card h2{margin:0 0 12px;font-size:30px;line-height:1.1;color:#153122}
.produits-polish-card p{color:#60706a;line-height:1.7}
.produits-feature-list{display:grid;gap:12px;margin-top:18px}
.produits-feature-list div{padding:14px 16px;border-radius:18px;background:#f7fbf4;color:#315021;border:1px solid rgba(108,161,56,.16)}
.produits-mini-stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.produits-mini-stat{display:block;text-decoration:none;padding:18px;border-radius:20px;background:linear-gradient(180deg,#f8fafc,#fff);border:1px solid rgba(21,49,34,.08)}
.produits-mini-stat strong{display:block;font-size:26px;color:#6ca138}
.produits-mini-stat span{display:block;color:#60706a;margin-top:5px}
@media (max-width:900px){.produits-polish-grid,.produits-mini-stats{grid-template-columns:1fr}}
</style>

<section class="produits-polish-grid">
    <div class="produits-polish-card">
        <span class="monta-eyebrow">Smarter Shopping</span>
        <h2>Cleaner catalog, faster decisions, and Gemini help.</h2>
        <p>The frontoffice now acts as the shared home for the application: guests can discover the catalog, users can return to their space, and admins can still manage the backoffice.</p>
        <div class="produits-feature-list">
            <div>Catalog-aware chatbot using the products stored in your database.</div>
            <div>Direct routes to categories, products, orders, login, and user dashboard.</div>
            <div>Server-side PHP validation stays inside the existing controllers.</div>
        </div>
    </div>
    <div class="produits-polish-card">
        <h2>Storefront shortcuts</h2>
        <div class="produits-mini-stats">
            <a class="produits-mini-stat" href="<?= htmlspecialchars(route_url('frontoffice/categories'), ENT_QUOTES, 'UTF-8') ?>"><strong>01</strong><span>Browse categories</span></a>
            <a class="produits-mini-stat" href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>"><strong>02</strong><span>Compare products</span></a>
            <a class="produits-mini-stat" href="<?= htmlspecialchars(route_url('frontoffice/orders/new'), ENT_QUOTES, 'UTF-8') ?>"><strong>03</strong><span>Create an order</span></a>
            <a class="produits-mini-stat" href="#product-ai-toggle"><strong>AI</strong><span>Ask Gemini</span></a>
        </div>
    </div>
</section>
