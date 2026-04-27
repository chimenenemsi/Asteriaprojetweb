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
$goalSummary = $goalSummary ?? [
    'record_count' => 0,
    'current_value' => $goal->getStartValue(),
    'completion' => 0,
    'days_remaining' => 0,
    'latest_record_date' => null,
    'remaining_to_target' => 0,
    'weekly_target_pace' => 0,
    'progress_direction' => 'Increase',
];
$motivationUrl = route_url($area . '/goals/motivation', ['id' => $goalId]);
?>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($goal->getTitle(), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= htmlspecialchars($goal->getMetric(), ENT_QUOTES, 'UTF-8') ?> from <?= htmlspecialchars(number_format($goal->getStartValue(), 2), ENT_QUOTES, 'UTF-8') ?> to <?= htmlspecialchars(number_format($goal->getTargetValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="actions">
            <a href="<?= htmlspecialchars(route_url($area . '/goals'), ENT_QUOTES, 'UTF-8') ?>">Back</a>
            <a href="<?= htmlspecialchars(route_url($area . '/goals/edit', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>">Edit Goal</a>
            <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/records/new', ['goal_id' => $goalId, 'type' => 'REPORT']), ENT_QUOTES, 'UTF-8') ?>">Add Record</a>
            <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/goals/delete', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this goal?');">
                <button class="btn btn-danger" type="submit">Delete</button>
            </form>
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
        <h3 style="margin-top:0">Performance Snapshot</h3>
        <p><strong>Current Value:</strong> <?= htmlspecialchars(number_format((float) $goalSummary['current_value'], 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Completion:</strong> <?= htmlspecialchars(number_format((float) $goalSummary['completion'], 1), ENT_QUOTES, 'UTF-8') ?>%</p>
        <p><strong>Records Logged:</strong> <?= htmlspecialchars((string) $goalSummary['record_count'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Direction:</strong> <?= htmlspecialchars((string) $goalSummary['progress_direction'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Days Remaining:</strong> <?= htmlspecialchars((string) $goalSummary['days_remaining'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Latest Entry:</strong> <?= htmlspecialchars((string) ($goalSummary['latest_record_date'] ?? 'No entries yet'), ENT_QUOTES, 'UTF-8') ?></p>
    </div>
    <div class="card">
        <h3 style="margin-top:0">Next Win</h3>
        <p><strong>Remaining To Target:</strong> <?= htmlspecialchars(number_format((float) $goalSummary['remaining_to_target'], 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?></p>
        <p><strong>Suggested Weekly Pace:</strong> <?= htmlspecialchars(number_format((float) $goalSummary['weekly_target_pace'], 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?>/week</p>
        <p class="muted">Use this as your next checkpoint so the target stops feeling abstract.</p>
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
        <div>
            <h2 style="margin:0">Motivation Center</h2>
            <p class="muted" style="margin:8px 0 0">Ask for a quote based on the kind of push you need right now.</p>
        </div>
        <div class="actions">
            <select id="motivation-type" style="min-width:220px">
                <option value="subject">Subject-based</option>
                <option value="discipline">Discipline</option>
                <option value="confidence">Confidence</option>
                <option value="resilience">Resilience</option>
            </select>
            <button class="btn btn-primary" type="button" id="load-motivation">Get Motivation</button>
        </div>
    </div>
    <div style="margin-top:18px;padding:18px;border:1px solid #e5e7eb;border-radius:16px;background:#f8fafc">
        <div class="badge" id="motivation-badge">subject</div>
        <blockquote id="motivation-content" style="margin:16px 0 8px;font-size:20px;line-height:1.7;color:#0f172a">Choose a motivation type and load a quote for this goal.</blockquote>
        <div class="muted" id="motivation-author">Asteria</div>
        <div class="muted" id="motivation-summary" style="margin-top:10px">Your next win will appear here after loading motivation.</div>
    </div>
</div>

<script>
(() => {
    const button = document.getElementById('load-motivation');
    const select = document.getElementById('motivation-type');
    const badge = document.getElementById('motivation-badge');
    const content = document.getElementById('motivation-content');
    const author = document.getElementById('motivation-author');
    const summary = document.getElementById('motivation-summary');
    const baseUrl = <?= json_encode($motivationUrl, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

    if (!button || !select) {
        return;
    }

    button.addEventListener('click', async () => {
        const motivationType = select.value || 'subject';
        badge.textContent = motivationType;
        content.textContent = 'Loading motivation...';
        author.textContent = 'Asteria';
        summary.textContent = 'Building your next target snapshot...';

        try {
            const response = await fetch(`${baseUrl}&motivation_type=${encodeURIComponent(motivationType)}`);
            const payload = await response.json();
            content.textContent = payload.quote?.content || 'Keep going.';
            author.textContent = payload.quote?.author ? `- ${payload.quote.author}` : '- Asteria';
            if (payload.summary) {
                const remaining = Number(payload.summary.remaining_to_target || 0).toFixed(2);
                const pace = Number(payload.summary.weekly_target_pace || 0).toFixed(2);
                summary.textContent = `${payload.summary.progress_direction || 'Move'} ${remaining} <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?> more. Suggested pace: ${pace} <?= htmlspecialchars($goalUnit, ENT_QUOTES, 'UTF-8') ?>/week.`;
            } else {
                summary.textContent = 'Keep stacking progress one honest checkpoint at a time.';
            }
        } catch (error) {
            content.textContent = 'Progress gets stronger when you ask your next effort to beat your last one.';
            author.textContent = '- Asteria';
            summary.textContent = 'Keep stacking progress one honest checkpoint at a time.';
        }
    });
})();
</script>

<div class="card">
    <div class="header-line">
        <h2 style="margin:0">Progress Records</h2>
        <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/records/new', ['goal_id' => $goalId, 'type' => 'REPORT']), ENT_QUOTES, 'UTF-8') ?>">Add Record</a>
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
                        <td>
                            <div class="table-actions">
                                <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars(route_url($area . '/records/edit', ['id' => $recordId]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/records/delete', ['id' => $recordId]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this record?');">
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
