<style>
.table-actions {display:flex;gap:8px;align-items:center;flex-wrap:nowrap}
.btn-sm {padding:6px 12px;font-size:13px;border-radius:8px}
.btn-secondary {background:#64748b;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:4px}
.btn-secondary:hover {background:#475569}
.btn-danger {background:#dc2626;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px}
.btn-danger:hover {background:#b91c1c}
</style>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Progress Records', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'backoffice' ? 'View all progress records (read-only).' : 'Manage your progress records.' ?></p>
        </div>
        <div class="actions">
            <?php if ($area === 'frontoffice'): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/records/new'), ENT_QUOTES, 'UTF-8') ?>">New Record</a>
            <?php endif; ?>
            <a href="<?= htmlspecialchars(route_url($area . '/goals'), ENT_QUOTES, 'UTF-8') ?>">Goals</a>
        </div>
    </div>
</div>

<div class="card">
    <?php if (($records ?? []) === []): ?>
        <p class="muted" style="margin:0">No records yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Goal</th>
                    <th>Date</th>
                    <th>Value</th>
                    <th>Adherence</th>
                    <th>Mood</th>
                    <th>Type</th>
                    <?php if ($area === 'frontoffice'): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $record): ?>
                    <?php $recordId = (int) ($record->getId() ?? 0); ?>
                    <tr>
                        <td>
                            <div><?= htmlspecialchars((string) ($record->getGoalTitle() ?? ''), ENT_QUOTES, 'UTF-8') ?></div>
                            <a class="muted" href="<?= htmlspecialchars(route_url($area . '/goals/show', ['id' => (int) ($record->getProgressGoalId() ?? 0)]), ENT_QUOTES, 'UTF-8') ?>">Open goal</a>
                        </td>
                        <td><?= htmlspecialchars($record->getRecordDate(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format($record->getRecordedValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) ($record->getGoalUnit() ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($record->getAdherenceScore() === null ? '-' : ((string) $record->getAdherenceScore() . '%'), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($record->getMood(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge"><?= htmlspecialchars($record->getRecordType(), ENT_QUOTES, 'UTF-8') ?></span></td>
                        <?php if ($area === 'frontoffice'): ?>
                            <td>
                                <div class="table-actions">
                                    <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars(route_url($area . '/records/edit', ['id' => $recordId]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                    <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/records/delete', ['id' => $recordId]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this record?');">
                                        <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
