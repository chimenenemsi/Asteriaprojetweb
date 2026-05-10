<?php
$values = $values ?? [];
$errors = $errors ?? [];
$mode = $mode ?? 'create';
$record = $record ?? null;
$recordId = (int) ($record?->getId() ?? 0);
$backUrl = route_url($area . '/records');
$backLabel = 'Back to Records';
$action = $mode === 'edit'
    ? route_url($area . '/records/update', ['id' => $recordId])
    : route_url($area . '/records/create');

$selectedGoalId = (int) ($values['progress_goal_id'] ?? 0);
$selectedGoal = null;
foreach (($goals ?? []) as $g) {
    if ((int) ($g->getId() ?? 0) === $selectedGoalId) {
        $selectedGoal = $g;
        break;
    }
}

$goalsJson = htmlspecialchars(
    (string) json_encode(
        array_map(
            static fn($g) => [
                'id' => (int) ($g->getId() ?? 0),
                'title' => $g->getTitle(),
                'metric' => $g->getMetric(),
                'target_value' => $g->getTargetValue(),
                'start_value' => $g->getStartValue(),
                'unit' => $g->getUnit(),
                'target_date' => $g->getTargetDate(),
            ],
            $goals ?? []
        ),
        JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ),
    ENT_QUOTES,
    'UTF-8'
);
?>
<style>
.goal-preview {background: linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);border-radius:16px;padding:20px;margin-bottom:20px;border:1px solid #e2e8f0}
.goal-preview h3 {margin:0 0 12px;font-size:18px;color:#1e293b}
.goal-preview-stats {display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px}
.goal-preview-stat {background:#fff;padding:12px 16px;border-radius:12px;text-align:center;border:1px solid #e2e8f0}
.goal-preview-stat strong {display:block;font-size:11px;text-transform:uppercase;color:#64748b;margin-bottom:4px}
.goal-preview-stat span {font-size:18px;font-weight:700;color:#0f172a}
.mood-selector {display:flex;gap:12px;flex-wrap:wrap}
.mood-option {flex:1;min-width:100px;cursor:pointer;padding:16px 12px;border:2px solid #e2e8f0;border-radius:14px;text-align:center;transition:all .2s;background:#fff}
.mood-option:hover {border-color:#94a3b8;transform:translateY(-2px)}
.mood-option.selected {border-color:#16a34a;background:#f0fdf4}
.mood-option .emoji {font-size:24px;display:block;margin-bottom:6px}
.mood-option .label {font-size:13px;font-weight:600;color:#475569}
.adherence-wrapper {background:#f8fafc;border-radius:12px;padding:20px;border:1px solid #e2e8f0}
.adherence-header {display:flex;justify-content:space-between;align-items:center;margin-bottom:12px}
.adherence-value {font-size:28px;font-weight:700;color:#16a34a}
.adherence-value.low {color:#dc2626}
.adherence-value.medium {color:#d97706}
.adherence-labels {display:flex;justify-content:space-between;margin-top:8px;font-size:12px;color:#64748b}
.quick-values {display:flex;gap:8px;flex-wrap:wrap;margin-top:12px}
.quick-value-btn {padding:8px 14px;border:1px solid #cbd5e1;border-radius:8px;background:#fff;cursor:pointer;font-size:13px;transition:all .2s}
.quick-value-btn:hover {background:#f1f5f9;border-color:#94a3b8}
.char-counter {text-align:right;font-size:12px;color:#64748b;margin-top:4px}
.char-counter.warning {color:#d97706}
.char-counter.danger {color:#dc2626}
.section-badge {display:inline-block;padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;text-transform:uppercase;margin-left:10px}
.section-badge.report {background:#dbeafe;color:#1d4ed8}
.section-badge.checkpoint {background:#dcfce7;color:#166534}
.section-badge.milestone {background:#fef3c7;color:#92400e}
.form-section h2 {display:flex;align-items:center;flex-wrap:wrap;gap:8px}
.help-text {font-size:13px;color:#64748b;margin-top:4px;font-style:italic}
.required::after {content:' *';color:#dc2626}
.goal-progress-bar {height:8px;background:#e2e8f0;border-radius:4px;margin-top:8px;overflow:hidden}
.goal-progress-fill {height:100%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:4px;transition:width .3s}
.date-quick-links {display:flex;gap:8px;margin-top:8px}
.date-quick-link {font-size:12px;color:#16a34a;cursor:pointer;text-decoration:underline}
</style>

<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Progress Record', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'frontoffice' ? 'Track your progress with a server-validated record form.' : 'Create or update a progress record with detailed tracking.' ?></p>
        </div>
        <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8') ?></a>
    </div>
</div>

<div id="goal-preview" class="goal-preview" style="<?= $selectedGoal ? '' : 'display:none' ?>">
    <h3>Selected Goal</h3>
    <div class="goal-preview-stats" id="goal-stats">
        <?php if ($selectedGoal): ?>
        <div class="goal-preview-stat">
            <strong>Metric</strong>
            <span><?= htmlspecialchars($selectedGoal->getMetric(), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="goal-preview-stat">
            <strong>Target</strong>
            <span><?= htmlspecialchars(number_format($selectedGoal->getTargetValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($selectedGoal->getUnit(), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="goal-preview-stat">
            <strong>Start</strong>
            <span><?= htmlspecialchars(number_format($selectedGoal->getStartValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($selectedGoal->getUnit(), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <div class="goal-preview-stat">
            <strong>Deadline</strong>
            <span><?= htmlspecialchars($selectedGoal->getTargetDate(), ENT_QUOTES, 'UTF-8') ?></span>
        </div>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" class="form-shell" id="record-form" novalidate>
        <div class="form-section">
            <h2>
                Goal Selection
                <span class="section-badge <?= ($values['record_type'] ?? 'CHECKPOINT') === 'REPORT' ? 'report' : (($values['record_type'] ?? 'CHECKPOINT') === 'MILESTONE' ? 'milestone' : 'checkpoint') ?>" id="type-badge">
                    <?= htmlspecialchars($values['record_type'] ?? 'CHECKPOINT', ENT_QUOTES, 'UTF-8') ?>
                </span>
            </h2>
            <p>Choose the goal you're tracking progress for.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="record-goal" class="required">Goal</label>
                        <select id="record-goal" name="progress_goal_id" data-goals='<?= $goalsJson ?>'>
                            <option value="">Select a goal...</option>
                            <?php foreach (($goals ?? []) as $goal): ?>
                                <?php $goalId = (int) ($goal->getId() ?? 0); ?>
                                <option value="<?= $goalId ?>" <?= (string) ($values['progress_goal_id'] ?? '') === (string) $goalId ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($goal->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="help-text">Select the goal to see its details and track progress.</p>
                        <?php if (isset($errors['progress_goal_id'])): ?><div class="error"><?= htmlspecialchars($errors['progress_goal_id'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="record-date" class="required">Record Date</label>
                        <input id="record-date" type="text" name="record_date" placeholder="YYYY-MM-DD" value="<?= htmlspecialchars((string) ($values['record_date'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="date-quick-links">
                            <span class="date-quick-link" onclick="setDate('<?= date('Y-m-d') ?>')">Today</span>
                            <span class="date-quick-link" onclick="setDate('<?= date('Y-m-d', strtotime('-1 day')) ?>')">Yesterday</span>
                            <span class="date-quick-link" onclick="setDate('<?= date('Y-m-d', strtotime('-7 days')) ?>')">Last week</span>
                        </div>
                        <?php if (isset($errors['record_date'])): ?><div class="error"><?= htmlspecialchars($errors['record_date'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Progress Value</h2>
            <p>Record your current achievement toward this goal.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="record-value" class="required">Recorded Value</label>
                        <input id="record-value" type="text" name="recorded_value"
                               value="<?= htmlspecialchars((string) ($values['recorded_value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                               placeholder="Enter value...">
                        <p class="help-text" id="value-help">Enter your current measurement.</p>
                        <?php if (isset($errors['recorded_value'])): ?><div class="error"><?= htmlspecialchars($errors['recorded_value'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field" id="progress-preview-field" style="opacity:0.5">
                        <label>Progress Preview</label>
                        <div style="padding:16px;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                                <span style="font-size:13px;color:#64748b">Completion</span>
                                <span style="font-size:20px;font-weight:700;color:#16a34a" id="progress-percent">--%</span>
                            </div>
                            <div class="goal-progress-bar">
                                <div class="goal-progress-fill" id="progress-bar" style="width:0%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Wellness & Adherence</h2>
            <p>How did you feel and how well did you stick to your plan?</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label>Current Mood</label>
                        <input type="hidden" name="mood" id="mood-input" value="<?= htmlspecialchars($values['mood'] ?? 'STEADY', ENT_QUOTES, 'UTF-8') ?>">
                        <div class="mood-selector">
                            <div class="mood-option <?= ($values['mood'] ?? 'STEADY') === 'LOW' ? 'selected' : '' ?>" data-mood="LOW" onclick="selectMood('LOW')">
                                <span class="emoji">:(</span>
                                <span class="label">Low</span>
                            </div>
                            <div class="mood-option <?= ($values['mood'] ?? 'STEADY') === 'STEADY' ? 'selected' : '' ?>" data-mood="STEADY" onclick="selectMood('STEADY')">
                                <span class="emoji">:|</span>
                                <span class="label">Steady</span>
                            </div>
                            <div class="mood-option <?= ($values['mood'] ?? 'STEADY') === 'HIGH' ? 'selected' : '' ?>" data-mood="HIGH" onclick="selectMood('HIGH')">
                                <span class="emoji">:)</span>
                                <span class="label">High</span>
                            </div>
                        </div>
                        <p class="help-text">Select the mood that best represents how you felt.</p>
                        <?php if (isset($errors['mood'])): ?><div class="error"><?= htmlspecialchars($errors['mood'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="adherence-score">Adherence Score</label>
                        <div class="adherence-wrapper">
                            <div class="adherence-header">
                                <span style="font-size:13px;color:#64748b">How well did you follow your plan?</span>
                                <span class="adherence-value" id="adherence-display"><?= ($values['adherence_score'] ?? '') === '' ? '--' : htmlspecialchars((string) $values['adherence_score'], ENT_QUOTES, 'UTF-8') . '%' ?></span>
                            </div>
                            <input id="adherence-score" type="text" name="adherence_score"
                                   value="<?= htmlspecialchars((string) ($values['adherence_score'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                                   class="form-control" placeholder="0 to 100" oninput="updateAdherence(this.value)">
                            <div class="adherence-labels">
                                <span>Enter a whole number from 0 to 100.</span>
                            </div>
                            <div class="quick-values">
                                <button type="button" class="quick-value-btn" onclick="updateAdherence(0)">Missed</button>
                                <button type="button" class="quick-value-btn" onclick="updateAdherence(25)">Struggled</button>
                                <button type="button" class="quick-value-btn" onclick="updateAdherence(50)">Partial</button>
                                <button type="button" class="quick-value-btn" onclick="updateAdherence(75)">Good</button>
                                <button type="button" class="quick-value-btn" onclick="updateAdherence(100)">Perfect</button>
                            </div>
                        </div>
                        <?php if (isset($errors['adherence_score'])): ?><div class="error"><?= htmlspecialchars($errors['adherence_score'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($area === 'backoffice'): ?>
        <div class="form-section">
            <h2>Record Classification</h2>
            <p>Categorize this entry for better organization.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="record-type" class="required">Record Type</label>
                        <select id="record-type" name="record_type" onchange="updateTypeBadge(this.value)">
                            <?php foreach (['CHECKPOINT' => 'Regular Checkpoint', 'MILESTONE' => 'Major Milestone', 'MEASUREMENT' => 'Simple Measurement', 'NOTE' => 'Quick Note', 'REPORT' => 'Progress Report'] as $type => $label): ?>
                                <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>" <?= ($values['record_type'] ?? 'CHECKPOINT') === $type ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="help-text">Validation is handled in PHP for every record type.</p>
                        <?php if (isset($errors['record_type'])): ?><div class="error"><?= htmlspecialchars($errors['record_type'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <input type="hidden" name="record_type" value="REPORT">
        <?php endif; ?>

        <div class="form-section">
            <h2>Notes & Reflection</h2>
            <p>Document what happened, challenges faced, or insights gained.</p>
            <div class="field">
                <label for="record-notes">Notes</label>
                <textarea id="record-notes" name="notes" rows="6"
                          placeholder="What challenges did you face? What went well? Any insights to share?"
                          oninput="updateCharCounter(this)"><?= htmlspecialchars((string) ($values['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <div class="char-counter" id="char-counter">0 / 2000 characters</div>
                <?php if (isset($errors['notes'])): ?><div class="error"><?= htmlspecialchars($errors['notes'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit">
                <?= $area === 'frontoffice' ? 'Submit Record' : ($mode === 'edit' ? 'Save Changes' : 'Create Record') ?>
            </button>
            <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
(function() {
    const goalsSelect = document.getElementById('record-goal');
    const goalsData = JSON.parse(goalsSelect.dataset.goals || '[]');
    const previewEl = document.getElementById('goal-preview');
    const statsEl = document.getElementById('goal-stats');
    const valueInput = document.getElementById('record-value');
    const progressPreview = document.getElementById('progress-preview-field');
    const progressPercent = document.getElementById('progress-percent');
    const progressBar = document.getElementById('progress-bar');
    const valueHelp = document.getElementById('value-help');

    let currentGoal = null;

    function updateGoalPreview() {
        const goalId = parseInt(goalsSelect.value, 10) || 0;
        currentGoal = goalsData.find(g => g.id === goalId) || null;

        if (!currentGoal) {
            previewEl.style.display = 'none';
            progressPreview.style.opacity = '0.5';
            return;
        }

        previewEl.style.display = 'block';
        statsEl.innerHTML = `
            <div class="goal-preview-stat">
                <strong>Metric</strong>
                <span>${escapeHtml(currentGoal.metric)}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Target</strong>
                <span>${formatNumber(currentGoal.target_value)} ${escapeHtml(currentGoal.unit)}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Start</strong>
                <span>${formatNumber(currentGoal.start_value)} ${escapeHtml(currentGoal.unit)}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Deadline</strong>
                <span>${escapeHtml(currentGoal.target_date)}</span>
            </div>
        `;
        valueHelp.textContent = `Enter your ${escapeHtml(currentGoal.metric.toLowerCase())} in ${escapeHtml(currentGoal.unit)}.`;
        updateProgressPreview();
        progressPreview.style.opacity = '1';
    }

    function updateProgressPreview() {
        if (!currentGoal || !valueInput.value) {
            progressPercent.textContent = '--%';
            progressBar.style.width = '0%';
            return;
        }

        const value = parseFloat(valueInput.value) || 0;
        const start = currentGoal.start_value;
        const target = currentGoal.target_value;
        const range = target - start;
        const progress = value - start;
        const percent = range !== 0 ? Math.max(0, Math.min(100, (progress / range) * 100)) : 0;

        progressPercent.textContent = percent.toFixed(1) + '%';
        progressBar.style.width = percent + '%';
        progressBar.style.background = percent >= 100
            ? 'linear-gradient(90deg,#16a34a,#22c55e)'
            : percent >= 50
                ? 'linear-gradient(90deg,#d97706,#f59e0b)'
                : 'linear-gradient(90deg,#dc2626,#ef4444)';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatNumber(num) {
        return Number(num).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    goalsSelect.addEventListener('change', updateGoalPreview);
    valueInput.addEventListener('input', updateProgressPreview);

    if (goalsSelect.value) {
        updateGoalPreview();
    }
})();

function selectMood(mood) {
    document.getElementById('mood-input').value = mood;
    document.querySelectorAll('.mood-option').forEach(el => {
        el.classList.toggle('selected', el.dataset.mood === mood);
    });
}

function updateAdherence(value) {
    const input = document.getElementById('adherence-score');
    const display = document.getElementById('adherence-display');
    const normalized = String(value).trim();
    input.value = normalized;

    if (!/^\d+$/.test(normalized)) {
        display.textContent = '--';
        display.className = 'adherence-value';
        return;
    }

    const numericValue = Math.max(0, Math.min(100, parseInt(normalized, 10)));
    input.value = String(numericValue);
    display.textContent = numericValue + '%';
    display.className = 'adherence-value' + (numericValue < 50 ? ' low' : numericValue < 80 ? ' medium' : '');
}

function updateTypeBadge(type) {
    const badge = document.getElementById('type-badge');
    badge.textContent = type;
    badge.className = 'section-badge ' + (type === 'REPORT' ? 'report' : type === 'MILESTONE' ? 'milestone' : 'checkpoint');
}

function updateCharCounter(textarea) {
    const counter = document.getElementById('char-counter');
    const length = textarea.value.length;
    const max = 2000;
    counter.textContent = length + ' / ' + max + ' characters';
    counter.className = 'char-counter' + (length > max * 0.9 ? ' danger' : length > max * 0.8 ? ' warning' : '');
}

function setDate(dateStr) {
    document.getElementById('record-date').value = dateStr;
}

document.addEventListener('DOMContentLoaded', function() {
    const notes = document.getElementById('record-notes');
    const adherence = document.getElementById('adherence-score');

    if (notes) {
        updateCharCounter(notes);
    }

    if (adherence) {
        updateAdherence(adherence.value);
    }
});
</script>
