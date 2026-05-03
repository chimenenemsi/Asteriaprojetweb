<?php
$values = $values ?? [];
$errors = $errors ?? [];
$mode = $mode ?? 'create';
$category = $category ?? null;
$action = $mode === 'edit'
    ? route_url($area . '/categories/update', ['id' => (int) ($category['id'] ?? 0)])
    : route_url($area . '/categories/create');
?>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Category Form', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted">Organize the catalog with a clear category name and optional description.</p>
        </div>
        <a href="<?= htmlspecialchars(route_url($area . '/categories'), ENT_QUOTES, 'UTF-8') ?>">Back to Categories</a>
    </div>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" novalidate class="form-shell">
        <div class="form-section">
            <h2>Category Details</h2>
            <p>Keep category names short, recognizable, and easy to scan in the catalog.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="category-name">Name</label>
                        <input id="category-name" type="text" name="name" placeholder="Category name" value="<?= htmlspecialchars((string) ($values['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['name'])): ?><div class="error"><?= htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="category-status">Status</label>
                        <select id="category-status" name="status">
                            <?php foreach (['ACTIVE', 'INACTIVE'] as $status): ?>
                                <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= ($values['status'] ?? 'ACTIVE') === $status ? 'selected' : '' ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['status'])): ?><div class="error"><?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-12">
                    <div class="field">
                        <label for="category-description">Description</label>
                        <textarea id="category-description" name="description" rows="5" placeholder="Explain what belongs in this category."><?= htmlspecialchars((string) ($values['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (isset($errors['description'])): ?><div class="error"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit"><?= $mode === 'edit' ? 'Save Category' : 'Create Category' ?></button>
        </div>
    </form>
</div>
