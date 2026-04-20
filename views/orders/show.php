<?php $placedAt = !empty($order['created_at']) ? date('Y-m-d H:i', strtotime((string) $order['created_at'])) : (string) ($order['order_date'] ?? ''); ?>
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
                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/orders/delete', ['id' => (int) $order['id']]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this order?');">
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
        <p><strong>Unit Price:</strong> $<?= htmlspecialchars(number_format((float) ($order['unit_price'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Total:</strong> $<?= htmlspecialchars(number_format((float) ($order['total_amount'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Placed On:</strong> <?= htmlspecialchars($placedAt, ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card">
        <h3 style="margin-top:0">Customer Details</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars((string) ($order['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars((string) ($order['customer_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <?php if (!empty($order['shipping_address'])): ?>
            <p><strong>Shipping Address:</strong><br><?= nl2br(htmlspecialchars((string) $order['shipping_address'], ENT_QUOTES, 'UTF-8')) ?></p>
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
