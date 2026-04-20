<section class="monta-section">
    <div class="monta-hero">
        <div>
            <span class="monta-eyebrow">Produits Module</span>
            <h1>Browse products and place orders directly from frontoffice with the same Asteria style.</h1>
            <p>Explore the catalog from the user side, place orders with quantity and delivery details, then leave status changes to backoffice.</p>
            <div class="monta-actions">
                <a class="monta-button" href="<?= htmlspecialchars(route_url('frontoffice/categories'), ENT_QUOTES, 'UTF-8') ?>">Open Categories</a>
                <a class="monta-button secondary" href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>">Open Products</a>
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
