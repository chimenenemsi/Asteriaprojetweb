<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Orders', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'backoffice' ? 'Manage orders and update their delivery state.' : 'Review placed orders and follow their delivery state.' ?></p>
        </div>
        <div class="actions">
            <?php if ($area === 'backoffice'): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/orders/new'), ENT_QUOTES, 'UTF-8') ?>">New Order</a>
            <?php else: ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>">Order Products</a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars(route_url($area . '/products'), ENT_QUOTES, 'UTF-8') ?>">Products</a>
        </div>
    </div>
</div>

<div class="card">
    <?php if (($orders ?? []) === []): ?>
        <p class="muted" style="margin:0">No orders yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Product</th>
                    <th>Placed On</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Delivery State</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <?php $placedAt = !empty($order['created_at']) ? date('Y-m-d H:i', strtotime((string) $order['created_at'])) : (string) $order['order_date']; ?>
                    <tr>
                        <td>
                            <div><?= htmlspecialchars((string) $order['customer_name'], ENT_QUOTES, 'UTF-8') ?></div>
                            <div class="muted"><?= htmlspecialchars((string) $order['customer_email'], ENT_QUOTES, 'UTF-8') ?></div>
                        </td>
                        <td><?= htmlspecialchars((string) $order['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($placedAt, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $order['quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>$<?= htmlspecialchars(number_format((float) $order['total_amount'], 2), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(order_status_label((string) $order['status']), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars(route_url($area . '/orders/show', ['id' => (int) $order['id']]), ENT_QUOTES, 'UTF-8') ?>">View</a>
                            <?php if ($area === 'backoffice'): ?>
                                <a href="<?= htmlspecialchars(route_url($area . '/orders/edit', ['id' => (int) $order['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/orders/delete', ['id' => (int) $order['id']]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this order?');">
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
