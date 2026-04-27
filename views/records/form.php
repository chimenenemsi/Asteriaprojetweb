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
$insightsUrl = route_url($area . '/records/insights');

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
                'goal_type' => $g->getGoalType(),
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
.goal-preview {background:radial-gradient(circle at top right,#dcfce7 0%,#eff6ff 35%,#f8fafc 75%);border-radius:24px;padding:24px;margin-bottom:22px;border:1px solid #dbe7f3;box-shadow:0 20px 50px rgba(15,23,42,.07)}
.goal-preview h3 {margin:0 0 12px;font-size:22px;color:#0f172a}
.goal-preview-stats {display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:12px}
.goal-preview-stat {background:rgba(255,255,255,.92);padding:14px 16px;border-radius:16px;text-align:center;border:1px solid #dfe9f5}
.goal-preview-stat strong {display:block;font-size:11px;text-transform:uppercase;color:#64748b;margin-bottom:4px}
.goal-preview-stat span {font-size:18px;font-weight:700;color:#0f172a}
.goal-preview-copy {display:flex;justify-content:space-between;gap:16px;align-items:flex-start;flex-wrap:wrap;margin-bottom:16px}
.goal-preview-copy p {margin:8px 0 0;color:#475569;max-width:620px;line-height:1.6}
.mood-selector {display:flex;gap:12px;flex-wrap:wrap}
.mood-option {flex:1;min-width:100px;cursor:pointer;padding:16px 12px;border:2px solid #e2e8f0;border-radius:16px;text-align:center;transition:all .2s;background:#fff}
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
.quick-value-btn {padding:8px 14px;border:1px solid #cbd5e1;border-radius:10px;background:#fff;cursor:pointer;font-size:13px;transition:all .2s}
.quick-value-btn:hover {background:#f1f5f9;border-color:#94a3b8}
.quick-value-btn.primary {background:#0f172a;border-color:#0f172a;color:#fff}
.quick-value-btn.primary:hover {background:#1e293b;border-color:#1e293b}
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
.goal-progress-bar {height:12px;background:#e2e8f0;border-radius:999px;margin-top:10px;overflow:hidden}
.goal-progress-fill {height:100%;background:linear-gradient(90deg,#16a34a,#22c55e);border-radius:999px;transition:width .3s}
.date-quick-links {display:flex;gap:8px;margin-top:8px}
.date-quick-link {font-size:12px;color:#16a34a;cursor:pointer;text-decoration:underline}
.motivation-card {background:linear-gradient(135deg,#eff6ff 0%,#f8fafc 100%);border:1px solid #dbeafe;border-radius:18px;padding:22px;margin-bottom:20px}
.motivation-card blockquote {margin:0;font-size:18px;line-height:1.7;color:#0f172a;font-weight:600}
.motivation-card footer {margin-top:12px;color:#475569;font-size:14px}
.ghost-bar-wrap {margin-top:14px;padding:14px 16px;border-radius:14px;background:#f8fafc;border:1px solid #e2e8f0}
.ghost-bar-track {position:relative;height:14px;border-radius:999px;background:#e2e8f0;overflow:hidden}
.ghost-bar-baseline,.ghost-bar-current {position:absolute;top:0;left:0;height:100%;border-radius:999px;transition:width .25s ease}
.ghost-bar-baseline {background:rgba(59,130,246,.22);border:1px dashed rgba(37,99,235,.4)}
.ghost-bar-current {background:linear-gradient(90deg,#16a34a,#22c55e)}
.ghost-bar-copy {display:flex;justify-content:space-between;align-items:center;gap:14px;margin-top:10px;font-size:13px;color:#475569;flex-wrap:wrap}
.ghost-bar-delta {font-weight:700;color:#0f172a}
.preview-insights {display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:12px;margin-top:12px}
.preview-insight {padding:12px 14px;border-radius:14px;background:#fff;border:1px solid #e2e8f0}
.preview-insight strong {display:block;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b}
.preview-insight span {display:block;margin-top:6px;font-size:16px;font-weight:700;color:#0f172a}
.autofill-copy {display:block;margin-top:10px;color:#475569;line-height:1.5}
@media (max-width:820px){.preview-insights{grid-template-columns:1fr}}
</style>

<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Progress Record', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'frontoffice' ? 'Track your progress with a server-validated record form.' : 'Create or update a progress record with detailed backoffice tracking.' ?></p>
        </div>
        <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($backLabel, ENT_QUOTES, 'UTF-8') ?></a>
    </div>
</div>

<div id="goal-preview" class="goal-preview" style="<?= $selectedGoal ? '' : 'display:none' ?>">
    <div class="goal-preview-copy">
        <div>
            <h3>Selected Goal</h3>
            <p id="goal-preview-copy">Choose a goal to see a cleaner progress breakdown, the direction of travel, and how far today’s value is from the target.</p>
        </div>
    </div>
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

<div class="motivation-card" id="motivation-card" style="display:none">
    <div class="d-flex justify-content-between align-items-start gap-3 flex-wrap">
        <div>
            <div class="section-badge report" id="motivation-tag">Motivation</div>
            <h3 style="margin:14px 0 10px;color:#0f172a">Subject-Based Motivation</h3>
            <blockquote id="motivation-quote">Loading a motivational quote for this goal...</blockquote>
            <footer id="motivation-author">Asteria</footer>
        </div>
    </div>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" class="form-shell" id="record-form">
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
                        <select id="record-goal" name="progress_goal_id" required data-goals='<?= $goalsJson ?>'>
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
                        <input id="record-date" type="date" name="record_date" required value="<?= htmlspecialchars((string) ($values['record_date'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8') ?>">
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
                        <input id="record-value" type="number" name="recorded_value" required step="0.01" min="0" max="9999999"
                               value="<?= htmlspecialchars((string) ($values['recorded_value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>"
                               placeholder="Enter value...">
                        <p class="help-text" id="value-help">Enter your current measurement.</p>
                        <div class="ghost-bar-wrap" id="ghost-bar-wrap" style="display:none">
                            <div class="ghost-bar-track">
                                <div class="ghost-bar-baseline" id="ghost-bar-baseline" style="width:0%"></div>
                                <div class="ghost-bar-current" id="ghost-bar-current" style="width:0%"></div>
                            </div>
                            <div class="ghost-bar-copy">
                                <span id="ghost-bar-label">Last week baseline will appear here.</span>
                                <span class="ghost-bar-delta" id="ghost-bar-delta"></span>
                            </div>
                        </div>
                        <?php if (isset($errors['recorded_value'])): ?><div class="error"><?= htmlspecialchars($errors['recorded_value'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field" id="progress-preview-field" style="opacity:0.5">
                        <label>Progress Preview</label>
                        <div style="padding:16px;background:#f8fafc;border-radius:12px;border:1px solid #e2e8f0">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                                <span style="font-size:13px;color:#64748b">Goal Progress</span>
                                <span style="font-size:20px;font-weight:700;color:#16a34a" id="progress-percent">--%</span>
                            </div>
                            <div class="goal-progress-bar">
                                <div class="goal-progress-fill" id="progress-bar" style="width:0%"></div>
                            </div>
                            <div class="preview-insights">
                                <div class="preview-insight">
                                    <strong>Current</strong>
                                    <span id="preview-current">--</span>
                                </div>
                                <div class="preview-insight">
                                    <strong>Remaining</strong>
                                    <span id="preview-remaining">--</span>
                                </div>
                                <div class="preview-insight">
                                    <strong>Direction</strong>
                                    <span id="preview-direction">--</span>
                                </div>
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
                            <input id="adherence-score" type="number" name="adherence_score" min="0" max="100" step="1"
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
                                <button type="button" class="quick-value-btn primary" id="auto-complete-progress">Auto-Complete From Mood</button>
                            </div>
                            <span class="autofill-copy" id="autofill-copy">Pick a mood, then use auto-complete to fill the form with a reasonable suggestion you can adjust.</span>
                        </div>
                        <?php if (isset($errors['adherence_score'])): ?><div class="error"><?= htmlspecialchars($errors['adherence_score'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="form-section">
            <h2>Notes & Reflection</h2>
            <p>Document what happened, challenges faced, or insights gained.</p>
            <div class="field">
                <label for="record-notes">Notes</label>
                <textarea id="record-notes" name="notes" rows="6" maxlength="2000"
                          placeholder="What challenges did you face? What went well? Any insights to share?"
                          oninput="updateCharCounter(this)"><?= htmlspecialchars((string) ($values['notes'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <div class="char-counter" id="char-counter">0 / 2000 characters</div>
                <?php if (isset($errors['notes'])): ?><div class="error"><?= htmlspecialchars($errors['notes'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit"><?= $mode === 'edit' ? 'Save Changes' : 'Create Record' ?></button>
            <a href="<?= htmlspecialchars($backUrl, ENT_QUOTES, 'UTF-8') ?>" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
(function() {
    const goalsSelect = document.getElementById('record-goal');
    const recordDateInput = document.getElementById('record-date');
    const goalsData = JSON.parse(goalsSelect.dataset.goals || '[]');
    const previewEl = document.getElementById('goal-preview');
    const statsEl = document.getElementById('goal-stats');
    const previewCopy = document.getElementById('goal-preview-copy');
    const valueInput = document.getElementById('record-value');
    const progressPreview = document.getElementById('progress-preview-field');
    const progressPercent = document.getElementById('progress-percent');
    const progressBar = document.getElementById('progress-bar');
    const previewCurrent = document.getElementById('preview-current');
    const previewRemaining = document.getElementById('preview-remaining');
    const previewDirection = document.getElementById('preview-direction');
    const valueHelp = document.getElementById('value-help');
    const motivationCard = document.getElementById('motivation-card');
    const motivationTag = document.getElementById('motivation-tag');
    const motivationQuote = document.getElementById('motivation-quote');
    const motivationAuthor = document.getElementById('motivation-author');
    const ghostWrap = document.getElementById('ghost-bar-wrap');
    const ghostBaselineBar = document.getElementById('ghost-bar-baseline');
    const ghostCurrentBar = document.getElementById('ghost-bar-current');
    const ghostLabel = document.getElementById('ghost-bar-label');
    const ghostDelta = document.getElementById('ghost-bar-delta');
    const autoCompleteButton = document.getElementById('auto-complete-progress');
    const autoFillCopy = document.getElementById('autofill-copy');
    const insightsUrl = <?= json_encode($insightsUrl, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;

    let currentGoal = null;
    let currentBaseline = null;

    function getGoalState(goal, rawValue) {
        const start = Number(goal.start_value || 0);
        const target = Number(goal.target_value || 0);
        const hasValue = rawValue !== null && rawValue !== '' && !Number.isNaN(Number(rawValue));
        const current = hasValue ? Number(rawValue) : start;
        const directionFactor = target >= start ? 1 : -1;
        const totalDistance = Math.max(Math.abs(target - start), 0.0001);
        const traveled = Math.max(0, Math.min(totalDistance, (current - start) * directionFactor));
        const remaining = Math.max(0, (target - current) * directionFactor);
        const percent = Math.max(0, Math.min(100, (traveled / totalDistance) * 100));
        const today = new Date();
        const deadline = new Date(`${goal.target_date}T00:00:00`);
        const daysLeft = Math.ceil((deadline - today) / 86400000);

        return {
            start,
            target,
            current,
            directionFactor,
            directionLabel: directionFactor === 1 ? 'Increase' : 'Reduce',
            totalDistance,
            remaining,
            percent,
            daysLeft,
        };
    }

    function updateGoalPreview() {
        const goalId = parseInt(goalsSelect.value, 10) || 0;
        currentGoal = goalsData.find(g => g.id === goalId) || null;

        if (!currentGoal) {
            previewEl.style.display = 'none';
            progressPreview.style.opacity = '0.5';
            motivationCard.style.display = 'none';
            ghostWrap.style.display = 'none';
            return;
        }

        previewEl.style.display = 'block';
        const state = getGoalState(currentGoal, valueInput.value || null);
        previewCopy.textContent = `This is a ${state.directionLabel.toLowerCase()} goal for ${currentGoal.metric.toLowerCase()}. You started at ${formatNumber(state.start)} ${currentGoal.unit}, your target is ${formatNumber(state.target)} ${currentGoal.unit}, and this entry updates how much is still left.`;
        statsEl.innerHTML = `
            <div class="goal-preview-stat">
                <strong>Metric</strong>
                <span>${escapeHtml(currentGoal.metric)}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Goal Type</strong>
                <span>${escapeHtml(String(currentGoal.goal_type || 'OTHER').replaceAll('_', ' '))}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Start</strong>
                <span>${formatNumber(currentGoal.start_value)} ${escapeHtml(currentGoal.unit)}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Target</strong>
                <span>${formatNumber(currentGoal.target_value)} ${escapeHtml(currentGoal.unit)}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Remaining</strong>
                <span>${formatNumber(state.remaining)} ${escapeHtml(currentGoal.unit)}</span>
            </div>
            <div class="goal-preview-stat">
                <strong>Deadline</strong>
                <span>${escapeHtml(currentGoal.target_date)}</span>
            </div>
        `;
        valueHelp.textContent = `Enter your ${escapeHtml(currentGoal.metric.toLowerCase())} in ${escapeHtml(currentGoal.unit)}.`;
        updateProgressPreview();
        progressPreview.style.opacity = '1';
        loadInsights();
    }

    function updateProgressPreview() {
        if (!currentGoal) {
            progressPercent.textContent = '--%';
            progressBar.style.width = '0%';
            previewCurrent.textContent = '--';
            previewRemaining.textContent = '--';
            previewDirection.textContent = '--';
            return;
        }

        const state = getGoalState(currentGoal, valueInput.value || null);
        const percent = state.percent;

        progressPercent.textContent = percent.toFixed(1) + '%';
        progressBar.style.width = percent + '%';
        progressBar.style.background = percent >= 100
            ? 'linear-gradient(90deg,#16a34a,#22c55e)'
            : percent >= 55
                ? 'linear-gradient(90deg,#0284c7,#22c55e)'
                : 'linear-gradient(90deg,#f59e0b,#f97316)';
        previewCurrent.textContent = `${formatNumber(state.current)} ${currentGoal.unit}`;
        previewRemaining.textContent = `${formatNumber(state.remaining)} ${currentGoal.unit}`;
        previewDirection.textContent = `${state.directionLabel}${state.daysLeft >= 0 ? ` · ${state.daysLeft}d left` : ' · overdue'}`;

        updateGhostBar();
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function formatNumber(num) {
        return Number(num).toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function updateGhostBar() {
        if (!currentBaseline || !currentGoal) {
            ghostWrap.style.display = 'none';
            return;
        }

        const currentValue = parseFloat(valueInput.value || '0');
        const baselineValue = Number(currentBaseline.recorded_value || 0);
        const directionFactor = Number(currentGoal.target_value) >= Number(currentGoal.start_value) ? 1 : -1;
        const chartMin = Math.min(Number(currentGoal.start_value), Number(currentGoal.target_value), baselineValue, currentValue);
        const chartMax = Math.max(Number(currentGoal.start_value), Number(currentGoal.target_value), baselineValue, currentValue);
        const denominator = Math.max(chartMax - chartMin, 0.0001);
        const baselineWidth = ((baselineValue - chartMin) / denominator) * 100;
        const currentWidth = ((currentValue - chartMin) / denominator) * 100;
        const improvement = (currentValue - baselineValue) * directionFactor;

        ghostBaselineBar.style.width = baselineWidth + '%';
        ghostCurrentBar.style.width = currentWidth + '%';
        ghostLabel.textContent = `Last week you logged ${formatNumber(baselineValue)} ${currentGoal.unit} on ${currentBaseline.record_date}.`;
        if (!valueInput.value) {
            ghostDelta.textContent = 'Enter today\'s value to compare.';
        } else if (improvement > 0) {
            ghostDelta.textContent = directionFactor === 1
                ? `You are up ${formatNumber(improvement)} ${currentGoal.unit} from last week.`
                : `You are down ${formatNumber(improvement)} ${currentGoal.unit} from last week.`;
        } else if (improvement < 0) {
            ghostDelta.textContent = `You need ${formatNumber(Math.abs(improvement))} ${currentGoal.unit} more to beat last week.`;
        } else {
            ghostDelta.textContent = 'You are matching last week exactly.';
        }

        ghostWrap.style.display = 'block';
    }

    async function loadInsights() {
        const goalId = parseInt(goalsSelect.value, 10) || 0;
        const recordDate = recordDateInput.value || '';

        if (!goalId || !recordDate) {
            motivationCard.style.display = 'none';
            ghostWrap.style.display = 'none';
            currentBaseline = null;
            return;
        }

        motivationCard.style.display = 'block';
        motivationQuote.textContent = 'Loading a motivational quote for this goal...';
        motivationAuthor.textContent = 'Asteria';

        try {
            const response = await fetch(`${insightsUrl}&goal_id=${encodeURIComponent(goalId)}&record_date=${encodeURIComponent(recordDate)}`);
            const payload = await response.json();

            if (payload.quote) {
                motivationTag.textContent = payload.quote.tag ? String(payload.quote.tag).replaceAll('|', ' / ') : 'Motivation';
                motivationQuote.textContent = payload.quote.content || 'Keep going.';
                motivationAuthor.textContent = payload.quote.author ? `- ${payload.quote.author}` : '- Asteria';
            }

            currentBaseline = payload.baseline || null;
            updateGhostBar();
        } catch (error) {
            motivationTag.textContent = 'Motivation';
            motivationQuote.textContent = 'Visualizing your progress is the fastest way to pressure yourself to beat last week.';
            motivationAuthor.textContent = '- Asteria';
            currentBaseline = null;
            ghostWrap.style.display = 'none';
        }
    }

    goalsSelect.addEventListener('change', updateGoalPreview);
    valueInput.addEventListener('input', updateProgressPreview);
    recordDateInput.addEventListener('change', loadInsights);

    if (autoCompleteButton) {
        autoCompleteButton.addEventListener('click', () => {
            if (!currentGoal) {
                autoFillCopy.textContent = 'Select a goal before using auto-complete.';
                return;
            }

            const mood = document.getElementById('mood-input').value || 'STEADY';
            const baselineValue = currentBaseline ? Number(currentBaseline.recorded_value || 0) : Number(currentGoal.start_value || 0);
            const directionFactor = Number(currentGoal.target_value) >= Number(currentGoal.start_value) ? 1 : -1;
            const totalDistance = Math.max(Math.abs(Number(currentGoal.target_value) - Number(currentGoal.start_value)), 0.0001);
            const step = totalDistance >= 20 ? Math.max(1, totalDistance * 0.04) : Math.max(0.1, totalDistance * 0.06);

            let suggestedValue = baselineValue;
            let suggestedAdherence = 70;
            let suggestedType = 'CHECKPOINT';
            let noteIntro = 'Steady day';

            if (mood === 'HIGH') {
                suggestedValue = baselineValue + (directionFactor * step);
                suggestedAdherence = 88;
                suggestedType = 'MILESTONE';
                noteIntro = 'High-energy day';
            } else if (mood === 'LOW') {
                suggestedValue = baselineValue - (directionFactor * (step / 2));
                suggestedAdherence = 45;
                suggestedType = 'NOTE';
                noteIntro = 'Low-energy day';
            }

            valueInput.value = Number(suggestedValue).toFixed(2).replace(/\.00$/, '');
            updateAdherence(suggestedAdherence);
            document.getElementById('record-type').value = suggestedType;
            updateTypeBadge(suggestedType);

            const notesField = document.getElementById('record-notes');
            if (notesField && notesField.value.trim() === '') {
                notesField.value = `${noteIntro}. Logged progress for ${currentGoal.metric.toLowerCase()} and ${mood === 'HIGH' ? 'pushed past the previous baseline.' : mood === 'LOW' ? 'kept the habit alive even with lower energy.' : 'kept momentum without forcing a huge jump.'}`;
                updateCharCounter(notesField);
            }

            updateProgressPreview();
            autoFillCopy.textContent = `Auto-complete used the ${mood.toLowerCase()} mood to suggest a ${suggestedType.toLowerCase()} entry with ${suggestedAdherence}% adherence. Adjust anything you want before saving.`;
        });
    }

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
    const event = new Event('change');
    document.getElementById('record-date').dispatchEvent(event);
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
