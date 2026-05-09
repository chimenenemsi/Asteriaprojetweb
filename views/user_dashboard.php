<?php
if (!isset($_SESSION['user'])) {
    header('Location: ' . action_url('login'));
    exit;
}

$currentUser = $_SESSION['user'];
?>
<section class="monta-section">
    <div class="monta-hero">
        <div>
            <span class="monta-eyebrow">User Space</span>
            <h1>Welcome back, <?= htmlspecialchars((string) $currentUser['fullname'], ENT_QUOTES, 'UTF-8') ?>.</h1>
            <p>Your account now lands in the same Asteria frontoffice experience as the produits homepage, so browsing products and ordering stays visually consistent.</p>
            <div class="monta-actions">
                <a class="monta-button" href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>">Browse Products</a>
                <a class="monta-button secondary" href="<?= htmlspecialchars(route_url('frontoffice/orders'), ENT_QUOTES, 'UTF-8') ?>">Open Orders</a>
                <a class="monta-button secondary" href="<?= htmlspecialchars(action_url('logout'), ENT_QUOTES, 'UTF-8') ?>">Logout</a>
            </div>
        </div>
        <div class="monta-hero-visual">
            <img src="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/banner-bg.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria catalog">
        </div>
    </div>
</section>

<section class="monta-section">
    <div class="monta-grid">
        <div class="monta-card">
            <h3>Catalog</h3>
            <p>Find products by category, stock state, price, and status from the same storefront flow.</p>
        </div>
        <div class="monta-card">
            <h3>Orders</h3>
            <p>Create an order with customer details, quantity, delivery address, and map location.</p>
        </div>
        <div class="monta-card">
            <h3>Assistant</h3>
            <p>Use the floating product assistant to get recommendations from the live product database.</p>
        </div>
    </div>
</section>
