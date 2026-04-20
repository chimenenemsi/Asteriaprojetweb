<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars((string) ($category['name'] ?? 'Category'), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= htmlspecialchars((string) ($category['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="actions">
            <a href="<?= htmlspecialchars(route_url($area . '/categories'), ENT_QUOTES, 'UTF-8') ?>">Back</a>
            <?php if ($area === 'backoffice'): ?>
                <a href="<?= htmlspecialchars(route_url($area . '/categories/edit', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/products/new', ['category_id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">Add Product</a>
                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/categories/delete', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this category?');">
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($category['description'])): ?>
    <div class="card"><p class="muted" style="margin:0"><?= nl2br(htmlspecialchars((string) $category['description'], ENT_QUOTES, 'UTF-8')) ?></p></div>
<?php endif; ?>

<div class="card">
    <div class="header-line">
        <h2 style="margin:0">Products</h2>
        <a href="<?= htmlspecialchars(route_url($area === 'frontoffice' ? 'frontoffice/products' : ($area . '/orders')), ENT_QUOTES, 'UTF-8') ?>"><?= $area === 'frontoffice' ? 'Open Catalog' : 'Orders' ?></a>
    </div>
    <?php if (($products ?? []) === []): ?>
        <p class="muted">No products in this category yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
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
                        <td><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>$<?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($area === 'backoffice'): ?>
                                <a href="<?= htmlspecialchars(route_url($area . '/products/edit', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/products/delete', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this product?');">
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                                <a href="<?= htmlspecialchars(route_url($area . '/orders/new', ['product_id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">New Order</a>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>">Order from Catalog</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
