<?php
$values = $values ?? [];
$errors = $errors ?? [];
$mode = $mode ?? 'create';
$order = $order ?? null;
$selectedProduct = $selectedProduct ?? null;
$backUrl = $area === 'frontoffice'
    ? route_url('frontoffice/products')
    : route_url('backoffice/orders');
$backLabel = $area === 'frontoffice' ? 'Back to Products' : 'Back to Orders';
$statusOptions = $statusOptions ?? order_backoffice_status_options();
$currentStatus = (string) ($values['status'] ?? 'PENDING');
if (!array_key_exists($currentStatus, $statusOptions)) {
    $currentStatus = 'PENDING';
}
$placedAt = !empty($order['created_at'])
    ? date('Y-m-d H:i', strtotime((string) $order['created_at']))
    : 'Saved automatically on submit';
$action = $mode === 'edit'
    ? route_url($area . '/orders/update', ['id' => (int) ($order['id'] ?? 0)])
    : route_url($area . '/orders/create');
?>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Order Form', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'frontoffice' ? 'Place your order. The order date is automatic and delivery state is updated from backoffice.' : 'Create or update an order and manage its delivery state.' ?></p>
        </div>
        <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8') ?></a>
    </div>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" class="form-shell">
        <div class="form-section">
            <h2>Order Setup</h2>
            <p>Select the product and quantity. Price totals are calculated from the catalog automatically.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="order-product-select">Product</label>
                        <select name="product_id" id="order-product-select" required>
                            <option value="">Select product</option>
                            <?php foreach (($products ?? []) as $product): ?>
                                <option
                                    value="<?= (int) $product['id'] ?>"
                                    data-name="<?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-price="<?= htmlspecialchars((string) $product['price'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-sku="<?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-stock="<?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-status="<?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?>"
                                    data-category="<?= htmlspecialchars((string) ($product['category_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                    <?= (string) ($values['product_id'] ?? '') === (string) $product['id'] ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?> - $<?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['product_id'])): ?><div class="error"><?= htmlspecialchars($errors['product_id'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="order-quantity-input">Quantity</label>
                        <input type="number" id="order-quantity-input" name="quantity" min="1" step="1" required value="<?= htmlspecialchars((string) ($values['quantity'] ?? 1), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="field-help" id="order-stock-hint">Choose a quantity within the available stock.</div>
                        <?php if (isset($errors['quantity'])): ?><div class="error"><?= htmlspecialchars($errors['quantity'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <?php if ($area === 'backoffice'): ?>
                    <div class="col-3">
                        <div class="field">
                            <label for="order-status-select">Delivery State</label>
                            <select id="order-status-select" name="status" required>
                                <?php foreach ($statusOptions as $status => $label): ?>
                                    <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= $currentStatus === $status ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if (isset($errors['status'])): ?><div class="error"><?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="form-section">
            <h2>Customer Details</h2>
            <p>Collect the contact and delivery information for the order.</p>
            <div class="row">
                <div class="col-5">
                    <div class="field">
                        <label for="customer-name">Customer Name</label>
                        <input id="customer-name" type="text" name="customer_name" minlength="2" maxlength="150" required placeholder="Customer full name" value="<?= htmlspecialchars((string) ($values['customer_name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['customer_name'])): ?><div class="error"><?= htmlspecialchars($errors['customer_name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-7">
                    <div class="field">
                        <label for="customer-email">Customer Email</label>
                        <input id="customer-email" type="email" name="customer_email" maxlength="255" required placeholder="name@example.com" value="<?= htmlspecialchars((string) ($values['customer_email'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['customer_email'])): ?><div class="error"><?= htmlspecialchars($errors['customer_email'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <div class="field">
                        <label for="shipping-address">Shipping Address</label>
                        <textarea id="shipping-address" name="shipping_address" rows="4" maxlength="2000" required placeholder="Street, area, city, and anything needed for delivery"><?= htmlspecialchars((string) ($values['shipping_address'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (isset($errors['shipping_address'])): ?><div class="error"><?= htmlspecialchars($errors['shipping_address'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <div class="field">
                        <label for="order-notes">Notes</label>
                        <textarea id="order-notes" name="notes" rows="4" maxlength="2000" placeholder="Special delivery notes or order comments"><?= htmlspecialchars((string) ($values['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (isset($errors['notes'])): ?><div class="error"><?= htmlspecialchars($errors['notes'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Product Snapshot</h2>
            <p>These details come from the product record and refresh automatically when you choose a different item.</p>
            <div class="summary-grid">
                <div class="summary-item">
                    <strong>Category</strong>
                    <span id="order-category-display"><?= htmlspecialchars((string) (($selectedProduct['category_name'] ?? '') ?: 'Select a product'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>SKU</strong>
                    <span id="order-sku-display"><?= htmlspecialchars((string) (($selectedProduct['sku'] ?? '') ?: 'Select a product'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Available Stock</strong>
                    <span id="order-stock-display"><?= htmlspecialchars($selectedProduct !== null ? (string) ($selectedProduct['stock_quantity'] ?? 0) : 'Select a product', ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Product Status</strong>
                    <span id="order-product-status-display"><?= htmlspecialchars((string) (($selectedProduct['status'] ?? '') ?: 'Select a product'), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Unit Price</strong>
                    <span id="order-price-display"><?= htmlspecialchars(($values['unit_price'] ?? '') === '' ? 'Calculated from product' : ('$' . number_format((float) $values['unit_price'], 2)), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Total</strong>
                    <span id="order-total-display"><?= htmlspecialchars(($values['total_amount'] ?? '') === '' ? 'Calculated from quantity' : ('$' . number_format((float) $values['total_amount'], 2)), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Placed On</strong>
                    <span><?= htmlspecialchars($placedAt, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Delivery State</strong>
                    <span><?= htmlspecialchars($area === 'frontoffice' && $mode === 'create' ? 'Starts as Not delivered' : order_status_label((string) ($order['status'] ?? $currentStatus)), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
            <?php if (isset($errors['order_date'])): ?><div class="error" style="margin-top:12px"><?= htmlspecialchars($errors['order_date'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit"><?= $mode === 'edit' ? 'Save Order' : ($area === 'frontoffice' ? 'Place Order' : 'Create Order') ?></button>
        </div>
    </form>
</div>
<script>
(() => {
    const productSelect = document.getElementById('order-product-select');
    const quantityInput = document.getElementById('order-quantity-input');
    const categoryDisplay = document.getElementById('order-category-display');
    const skuDisplay = document.getElementById('order-sku-display');
    const stockDisplay = document.getElementById('order-stock-display');
    const statusDisplay = document.getElementById('order-product-status-display');
    const priceDisplay = document.getElementById('order-price-display');
    const totalDisplay = document.getElementById('order-total-display');
    const stockHint = document.getElementById('order-stock-hint');

    if (!productSelect || !quantityInput) {
        return;
    }

    const formatMoney = (value) => '$' + Number(value || 0).toFixed(2);

    const updateOrderPreview = () => {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const quantity = Math.max(0, Number(quantityInput.value || 0));

        if (!selectedOption || !selectedOption.value) {
            categoryDisplay.textContent = 'Select a product';
            skuDisplay.textContent = 'Select a product';
            stockDisplay.textContent = 'Select a product';
            statusDisplay.textContent = 'Select a product';
            priceDisplay.textContent = 'Calculated from product';
            totalDisplay.textContent = 'Calculated from quantity';
            quantityInput.removeAttribute('max');
            stockHint.textContent = 'Choose a quantity within the available stock.';
            stockHint.style.color = '';
            return;
        }

        const price = Number(selectedOption.dataset.price || 0);
        const stock = Number(selectedOption.dataset.stock || 0);
        const status = selectedOption.dataset.status || '';
        const category = selectedOption.dataset.category || '';
        const sku = selectedOption.dataset.sku || '';

        categoryDisplay.textContent = category || 'N/A';
        skuDisplay.textContent = sku || 'N/A';
        stockDisplay.textContent = String(stock);
        statusDisplay.textContent = status || 'N/A';
        priceDisplay.textContent = formatMoney(price);
        totalDisplay.textContent = formatMoney(price * quantity);
        if (stock > 0) {
            quantityInput.max = String(stock);
        } else {
            quantityInput.removeAttribute('max');
        }

        if (quantity > stock) {
            stockHint.textContent = 'Requested quantity is higher than available stock.';
            stockHint.style.color = '#b91c1c';
        } else if (status !== 'ACTIVE') {
            stockHint.textContent = 'Only active products can be ordered.';
            stockHint.style.color = '#b45309';
        } else {
            stockHint.textContent = 'Stock is available for this quantity.';
            stockHint.style.color = '#166534';
        }
    };

    productSelect.addEventListener('change', updateOrderPreview);
    quantityInput.addEventListener('input', updateOrderPreview);
    updateOrderPreview();
})();
</script>
