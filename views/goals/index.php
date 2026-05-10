<style>
.btn-sm {padding:6px 12px;font-size:13px;border-radius:8px}
.btn-secondary {background:#64748b;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:4px}
.btn-secondary:hover {background:#475569}
</style>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Progress Goals', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'backoffice' ? 'View all progress goals (read-only).' : 'Create and manage your progress goals.' ?></p>
        </div>
        <div class="actions">
            <?php if ($area === 'frontoffice'): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url('frontoffice/goals/new'), ENT_QUOTES, 'UTF-8') ?>">New Goal</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card">
    <?php if (($goals ?? []) === []): ?>
        <p class="muted" style="margin:0">No goals yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Metric</th>
                    <th>Target</th>
                    <th>Dates</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($goals as $goal): ?>
                    <?php $goalId = (int) ($goal->getId() ?? 0); ?>
                    <tr>
                        <td><?= htmlspecialchars($goal->getTitle(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($goal->getMetric(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format($goal->getTargetValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goal->getUnit(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($goal->getStartDate(), ENT_QUOTES, 'UTF-8') ?> to <?= htmlspecialchars($goal->getTargetDate(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge"><?= htmlspecialchars($goal->getStatus(), ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td>
                            <a href="<?= htmlspecialchars(route_url($area . '/goals/show', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>"><?= $area === 'frontoffice' ? 'View & Update' : 'View' ?></a>
                            <?php if ($area === 'frontoffice'): ?>
                                <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars(route_url('frontoffice/goals/edit', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
