<style>
.table-actions {display:flex;gap:8px;align-items:center;flex-wrap:nowrap}
.btn-sm {padding:6px 12px;font-size:13px;border-radius:8px}
.btn-secondary {background:#64748b;color:#fff;text-decoration:none;display:inline-flex;align-items:center;gap:4px}
.btn-secondary:hover {background:#475569}
.btn-danger {background:#dc2626;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:4px}
.btn-danger:hover {background:#b91c1c}
</style>
<?php
$goalId = (int) ($goal->getId() ?? 0);
$goalUnit = $goal->getUnit();
$goalDescription = (string) ($goal->getDescription() ?? '');
?>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($goal->getTitle(), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= htmlspecialchars($goal->getMetric(), ENT_QUOTES, 'UTF-8') ?> from <?= htmlspecialchars(number_format($goal->getStartValue(), 2), ENT_QUOTES, 'UTF-8') ?> to <?= htmlspecialchars(number_format($goal->getTargetValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="actions">
            <a href="<?= htmlspecialchars(route_url($area . '/goals'), ENT_QUOTES, 'UTF-8') ?>">Back</a>
            <?php if ($area === 'frontoffice'): ?>
                <a href="<?= htmlspecialchars(route_url($area . '/goals/edit', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>">Edit Goal</a>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/records/new', ['goal_id' => $goalId, 'type' => 'REPORT']), ENT_QUOTES, 'UTF-8') ?>">Add Record</a>
                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/goals/delete', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this goal?');">
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="grid">
    <div class="card">
        <h3 style="margin-top:0">Goal Details</h3>
        <p><strong>Status:</strong> <span class="badge"><?= htmlspecialchars($goal->getStatus(), ENT_QUOTES, 'UTF-8') ?></span></p>
        <p><strong>Timeline:</strong> <?= htmlspecialchars($goal->getStartDate(), ENT_QUOTES, 'UTF-8') ?> to <?= htmlspecialchars($goal->getTargetDate(), ENT_QUOTES, 'UTF-8') ?></p>
        <?php if ($goalDescription !== ''): ?>
            <p class="muted"><?= nl2br(htmlspecialchars($goalDescription, ENT_QUOTES, 'UTF-8')) ?></p>
        <?php endif; ?>
    </div>
    <div class="card">
        <h3 style="margin-top:0">Latest Record</h3>
        <?php if (empty($records)): ?>
            <p class="muted">No progress records for this goal yet.</p>
        <?php else: ?>
            <?php $latest = $records[0]; ?>
            <p><strong>Date:</strong> <?= htmlspecialchars($latest->getRecordDate(), ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Value:</strong> <?= htmlspecialchars(number_format($latest->getRecordedValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Type:</strong> <?= htmlspecialchars($latest->getRecordType(), ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Mood:</strong> <?= htmlspecialchars($latest->getMood() !== '' ? $latest->getMood() : '-', ENT_QUOTES, 'UTF-8') ?></p>
            <p><strong>Adherence:</strong> <?= htmlspecialchars($latest->getAdherenceScore() === null ? '-' : ((string) $latest->getAdherenceScore() . '%'), ENT_QUOTES, 'UTF-8') ?></p>
            <?php if (($latest->getNotes() ?? '') !== ''): ?>
                <p class="muted"><?= nl2br(htmlspecialchars((string) $latest->getNotes(), ENT_QUOTES, 'UTF-8')) ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="header-line">
        <h2 style="margin:0">Progress Records</h2>
        <?php if ($area === 'frontoffice'): ?>
            <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/records/new', ['goal_id' => $goalId, 'type' => 'REPORT']), ENT_QUOTES, 'UTF-8') ?>">Add Record</a>
        <?php endif; ?>
    </div>
    <?php if (empty($records)): ?>
        <p class="muted">No progress records for this goal yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Mood</th>
                    <th>Adherence</th>
                    <?php if ($area === 'frontoffice'): ?><th>Actions</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($records as $rec): ?>
                    <?php $recordId = (int) ($rec->getId() ?? 0); ?>
                    <tr>
                        <td><?= htmlspecialchars($rec->getRecordDate(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars(number_format($rec->getRecordedValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?></td>
                        <td><span class="badge"><?= htmlspecialchars($rec->getRecordType(), ENT_QUOTES, 'UTF-8') ?></span></td>
                        <td><?= htmlspecialchars($rec->getMood(), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars($rec->getAdherenceScore() === null ? '-' : ((string) $rec->getAdherenceScore() . '%'), ENT_QUOTES, 'UTF-8') ?></td>
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
