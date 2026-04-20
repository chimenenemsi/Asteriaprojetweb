<?php
$orderErrors = $orderErrors ?? [];
$orderValues = $orderValues ?? [];
$orderProductId = (int) ($orderProductId ?? 0);
?>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Products', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'backoffice' ? 'Manage products.' : 'Browse products and place orders directly from the catalog.' ?></p>
        </div>
        <div class="actions">
            <?php if ($area === 'backoffice'): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/products/new'), ENT_QUOTES, 'UTF-8') ?>">New Product</a>
                <a href="<?= htmlspecialchars(route_url($area . '/categories'), ENT_QUOTES, 'UTF-8') ?>">Categories</a>
                <a href="<?= htmlspecialchars(route_url($area . '/orders'), ENT_QUOTES, 'UTF-8') ?>">Orders</a>
            <?php else: ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url('frontoffice/orders'), ENT_QUOTES, 'UTF-8') ?>">Open Orders</a>
                <a href="<?= htmlspecialchars(route_url('frontoffice/categories'), ENT_QUOTES, 'UTF-8') ?>">Categories</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (($products ?? []) === []): ?>
    <div class="card">
        <p class="muted" style="margin:0">No products yet.</p>
    </div>
<?php elseif ($area === 'backoffice'): ?>
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><a href="<?= htmlspecialchars(route_url($area . '/categories/show', ['id' => (int) $product['product_category_id']]), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>$<?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars(route_url($area . '/products/edit', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/products/delete', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this product?');">
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                            <a href="<?= htmlspecialchars(route_url($area . '/orders/new', ['product_id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">New Order</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="catalog-grid">
        <?php foreach ($products as $product): ?>
            <?php
            $productId = (int) $product['id'];
            $stock = (int) ($product['stock_quantity'] ?? 0);
            $isOrderable = (string) ($product['status'] ?? '') === 'ACTIVE' && $stock > 0;
            $isSelected = $orderProductId === $productId;
            $values = $isSelected
                ? $orderValues
                : [
                    'quantity' => 1,
                    'customer_name' => '',
                    'customer_email' => '',
                    'shipping_address' => '',
                    'notes' => '',
                ];
            ?>
            <div class="catalog-card">
                <div class="catalog-top">
                    <div>
                        <h3><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="muted" style="margin:6px 0 0"><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="catalog-price">$<?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <p class="muted" style="margin:0">
                    <?= htmlspecialchars((string) ($product['description'] ?: 'Order straight from the catalog and let backoffice handle the delivery state.'), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <div class="catalog-meta">
                    <div class="catalog-meta-item">
                        <strong>SKU</strong>
                        <span><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="catalog-meta-item">
                        <strong>Status</strong>
                        <span><?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="catalog-meta-item">
                        <strong>Stock</strong>
                        <span><?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="catalog-meta-item">
                        <strong>Order State</strong>
                        <span>Starts as Not delivered</span>
                    </div>
                </div>

                <div class="order-panel">
                    <?php if ($isSelected && $orderErrors !== []): ?>
                        <div class="error-summary">Please correct the highlighted order fields for this product.</div>
                    <?php endif; ?>

                    <?php if ($isOrderable): ?>
                        <div class="order-message">Order date is saved automatically when you submit. Delivery state is updated later from backoffice.</div>
                        <form method="post" action="<?= htmlspecialchars(route_url('frontoffice/orders/create'), ENT_QUOTES, 'UTF-8') ?>" class="form-shell">
                            <input type="hidden" name="product_id" value="<?= $productId ?>">
                            <input type="hidden" name="inline_order" value="1">
                            <div class="row">
                                <div class="col-2">
                                    <div class="field">
                                        <label for="order-quantity-<?= $productId ?>">Quantity</label>
                                        <input id="order-quantity-<?= $productId ?>" type="number" name="quantity" min="1" max="<?= $stock ?>" step="1" required value="<?= htmlspecialchars((string) ($values['quantity'] ?? 1), ENT_QUOTES, 'UTF-8') ?>">
                                        <div class="field-help">Available stock: <?= htmlspecialchars((string) $stock, ENT_QUOTES, 'UTF-8') ?></div>
                                        <?php if ($isSelected && isset($orderErrors['quantity'])): ?><div class="error"><?= htmlspecialchars($orderErrors['quantity'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="field">
                                        <label for="order-name-<?= $productId ?>">Your Name</label>
                                        <input id="order-name-<?= $productId ?>" type="text" name="customer_name" minlength="2" maxlength="150" required placeholder="Your full name" value="<?= htmlspecialchars((string) ($values['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                        <?php if ($isSelected && isset($orderErrors['customer_name'])): ?><div class="error"><?= htmlspecialchars($orderErrors['customer_name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="field">
                                        <label for="order-email-<?= $productId ?>">Email</label>
                                        <input id="order-email-<?= $productId ?>" type="email" name="customer_email" maxlength="255" required placeholder="name@example.com" value="<?= htmlspecialchars((string) ($values['customer_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                                        <?php if ($isSelected && isset($orderErrors['customer_email'])): ?><div class="error"><?= htmlspecialchars($orderErrors['customer_email'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="field">
                                        <label for="order-address-<?= $productId ?>">Shipping Address</label>
                                        <textarea id="order-address-<?= $productId ?>" name="shipping_address" rows="3" maxlength="2000" required placeholder="Where should this order be delivered?"><?= htmlspecialchars((string) ($values['shipping_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                        <?php if ($isSelected && isset($orderErrors['shipping_address'])): ?><div class="error"><?= htmlspecialchars($orderErrors['shipping_address'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="field">
                                        <label for="order-notes-<?= $productId ?>">Notes</label>
                                        <textarea id="order-notes-<?= $productId ?>" name="notes" rows="3" maxlength="2000" placeholder="Any special instructions?"><?= htmlspecialchars((string) ($values['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                                        <?php if ($isSelected && isset($orderErrors['notes'])): ?><div class="error"><?= htmlspecialchars($orderErrors['notes'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php if ($isSelected && isset($orderErrors['product_id'])): ?><div class="error"><?= htmlspecialchars($orderErrors['product_id'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                            <div class="actions">
                                <button class="btn btn-primary" type="submit">Place Order</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="order-message">
                            <?= $stock < 1 ? 'This product is currently out of stock.' : 'This product is not available for ordering right now.' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
