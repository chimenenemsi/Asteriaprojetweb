<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Product Categories', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'backoffice' ? 'Manage product categories.' : 'Browse product categories.' ?></p>
        </div>
        <div class="actions">
            <?php if ($area === 'backoffice'): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/categories/new'), ENT_QUOTES, 'UTF-8') ?>">New Category</a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars(route_url($area . '/products'), ENT_QUOTES, 'UTF-8') ?>">Products</a>
        </div>
    </div>
</div>

<div class="card">
    <?php if (($categories ?? []) === []): ?>
        <p class="muted" style="margin:0">No categories yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge"><?= htmlspecialchars((string) $category['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars((string) ($category['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars(route_url($area . '/categories/show', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">View</a>
                            <?php if ($area === 'backoffice'): ?>
                                <a href="<?= htmlspecialchars(route_url($area . '/categories/edit', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/categories/delete', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this category?');">
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
