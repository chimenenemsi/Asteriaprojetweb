<?php $placedAt = !empty($order['created_at']) ? date('Y-m-d H:i', strtotime((string) $order['created_at'])) : (string) ($order['order_date'] ?? ''); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<style>
.order-map {height:260px;border-radius:16px;border:1px solid #cbd5e1;overflow:hidden;margin-top:16px}
</style>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0">Order #<?= htmlspecialchars((string) ($order['id'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= htmlspecialchars((string) ($order['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?> - <?= htmlspecialchars($placedAt, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="actions">
            <a href="<?= htmlspecialchars(route_url($area . '/orders'), ENT_QUOTES, 'UTF-8') ?>">Back</a>
            <?php if ($area === 'backoffice'): ?>
                <a href="<?= htmlspecialchars(route_url($area . '/orders/edit', ['id' => (int) $order['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/orders/delete', ['id' => (int) $order['id']]), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="grid">
    <div class="card">
        <h3 style="margin-top:0">Order Details</h3>
        <p><strong>Product:</strong> <?= htmlspecialchars((string) ($order['product_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Delivery State:</strong> <?= htmlspecialchars(order_status_label((string) ($order['status'] ?? 'PENDING')), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Quantity:</strong> <?= htmlspecialchars((string) ($order['quantity'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Unit Price:</strong> DT <?= htmlspecialchars(number_format((float) ($order['unit_price'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Total:</strong> DT <?= htmlspecialchars(number_format((float) ($order['total_amount'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Placed On:</strong> <?= htmlspecialchars($placedAt, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card">
        <h3 style="margin-top:0">Customer Details</h3>
        <p><strong>User ID:</strong> <?= !empty($order['user_id']) ? htmlspecialchars((string) $order['user_id'], ENT_QUOTES, 'UTF-8') : 'Guest / legacy order' ?></p>
        <?php if (!empty($order['user_id'])): ?>
            <p><strong>User Account:</strong> <?= htmlspecialchars((string) ($order['user_fullname'] ?? 'Unknown user'), ENT_QUOTES, 'UTF-8') ?><?= !empty($order['user_email']) ? ' - ' . htmlspecialchars((string) $order['user_email'], ENT_QUOTES, 'UTF-8') : '' ?></p>
        <?php endif; ?>
        <p><strong>Name:</strong> <?= htmlspecialchars((string) ($order['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars((string) ($order['customer_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!empty($order['shipping_address'])): ?>
            <p><strong>Shipping Address:</strong><br><?= nl2br(htmlspecialchars((string) $order['shipping_address'], ENT_QUOTES, 'UTF-8')) ?></p>
        <?php endif; ?>
        <?php if (!empty($order['delivery_location_name'])): ?>
            <p><strong>Delivery Location:</strong> <?= htmlspecialchars((string) $order['delivery_location_name'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <?php if (!empty($order['notes'])): ?>
            <p><strong>Notes:</strong><br><?= nl2br(htmlspecialchars((string) $order['notes'], ENT_QUOTES, 'UTF-8')) ?></p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3 style="margin-top:0">Catalog Snapshot</h3>
        <p><strong>Category:</strong> <?= htmlspecialchars((string) (($order['current_category_name'] ?? '') ?: 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>SKU:</strong> <?= htmlspecialchars((string) (($order['current_product_sku'] ?? '') ?: 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Current Stock:</strong> <?= htmlspecialchars((string) (($order['current_product_stock'] ?? '') ?: 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Current Product Status:</strong> <?= htmlspecialchars((string) (($order['current_product_status'] ?? '') ?: 'N/A'), ENT_QUOTES, 'UTF-8') ?></p>
    </div>
</div>
<?php if (($order['delivery_latitude'] ?? null) !== null && ($order['delivery_longitude'] ?? null) !== null): ?>
    <div class="card">
        <h3 style="margin-top:0">Pinned Delivery Point</h3>
        <p class="muted">The order stores a map location in addition to the written shipping address.</p>
        <div id="order-map" class="order-map" data-lat="<?= htmlspecialchars((string) $order['delivery_latitude'], ENT_QUOTES, 'UTF-8') ?>" data-lng="<?= htmlspecialchars((string) $order['delivery_longitude'], ENT_QUOTES, 'UTF-8') ?>" data-label="<?= htmlspecialchars((string) ($order['delivery_location_name'] ?? 'Delivery point'), ENT_QUOTES, 'UTF-8') ?>"></div>
    </div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>
    <script>
    (() => {
        const mapEl = document.getElementById('order-map');
        if (!mapEl || !window.L) {
            return;
        }

        const lat = Number(mapEl.dataset.lat || 0);
        const lng = Number(mapEl.dataset.lng || 0);
        const label = mapEl.dataset.label || 'Delivery point';
        const map = L.map(mapEl).setView([lat, lng], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);
        L.marker([lat, lng]).addTo(map).bindPopup(label).openPopup();
    })();
    </script>
<?php endif; ?>
