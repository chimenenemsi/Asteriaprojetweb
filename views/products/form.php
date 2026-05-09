<?php
$values = $values ?? [];
$errors = $errors ?? [];
$mode = $mode ?? 'create';
$product = $product ?? null;
$action = $mode === 'edit'
    ? route_url($area . '/products/update', ['id' => (int) ($product['id'] ?? 0)])
    : route_url($area . '/products/create');
?>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Product Form', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted">Set up the catalog details, stock, and pricing for this product.</p>
        </div>
        <a href="<?= htmlspecialchars(route_url($area . '/products'), ENT_QUOTES, 'UTF-8') ?>">Back to Products</a>
    </div>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" novalidate class="form-shell">
        <div class="form-section">
            <h2>Catalog Details</h2>
            <p>Choose the category and define the product identity clearly.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="product-category">Category</label>
                        <select id="product-category" name="product_category_id">
                            <option value="">Select category</option>
                            <?php foreach (($categories ?? []) as $category): ?>
                                <option value="<?= (int) $category['id'] ?>" <?= (string) ($values['product_category_id'] ?? '') === (string) $category['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['product_category_id'])): ?><div class="error"><?= htmlspecialchars($errors['product_category_id'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="product-name">Name</label>
                        <input id="product-name" type="text" name="name" placeholder="Product name" value="<?= htmlspecialchars((string) ($values['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['name'])): ?><div class="error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="product-sku">SKU</label>
                        <input id="product-sku" type="text" name="sku" placeholder="ABC-123" value="<?= htmlspecialchars((string) ($values['sku'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="field-help">Use letters, numbers, dashes, or underscores.</div>
                        <?php if (isset($errors['sku'])): ?><div class="error"><?= htmlspecialchars($errors['sku'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="product-status">Status</label>
                        <select id="product-status" name="status">
                            <?php foreach (['ACTIVE', 'DRAFT', 'OUT_OF_STOCK'] as $status): ?>
                                <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= ($values['status'] ?? 'ACTIVE') === $status ? 'selected' : '' ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['status'])): ?><div class="error"><?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Pricing and Stock</h2>
            <p>Keep the catalog price accurate and the stock count realistic.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="product-price">Price</label>
                        <input id="product-price" type="text" inputmode="decimal" name="price" placeholder="0.00" value="<?= htmlspecialchars((string) ($values['price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['price'])): ?><div class="error"><?= htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="product-stock">Stock Quantity</label>
                        <input id="product-stock" type="text" inputmode="decimal" name="stock_quantity" value="<?= htmlspecialchars((string) ($values['stock_quantity'] ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['stock_quantity'])): ?><div class="error"><?= htmlspecialchars($errors['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Description</h2>
            <p>Add useful details that help users understand what they are ordering.</p>
            <div class="field">
                <label for="product-description">Description</label>
                <textarea id="product-description" name="description" rows="5" placeholder="Describe the product, materials, size, flavor, or anything the buyer should know."><?= htmlspecialchars((string) ($values['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (isset($errors['description'])): ?><div class="error"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit"><?= $mode === 'edit' ? 'Save Product' : 'Create Product' ?></button>
        </div>
    </form>
</div>
