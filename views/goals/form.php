<?php
$values = $values ?? [];
$errors = $errors ?? [];
$mode = $mode ?? 'create';
$goal = $goal ?? null;
$goalId = (int) ($goal?->getId() ?? 0);
$action = $mode === 'edit'
    ? route_url($area . '/goals/update', ['id' => $goalId])
    : route_url($area . '/goals/create');
?>
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Goal Form', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'frontoffice' ? 'Create or update a progress goal from frontoffice.' : 'Create or update a progress goal from backoffice.' ?></p>
        </div>
        <a href="<?= htmlspecialchars(route_url($area . '/goals'), ENT_QUOTES, 'UTF-8') ?>">Back to Goals</a>
    </div>
</div>

<div class="card">
    <form method="post" action="<?= htmlspecialchars($action, ENT_QUOTES, 'UTF-8') ?>" class="form-shell">
        <div class="form-section">
            <h2>Goal Identity</h2>
            <p>Give the goal a clear title, the metric you track, and the unit you use every time you update it.</p>
            <div class="row">
                <div class="col-6">
                    <div class="field">
                        <label for="goal-title">Title</label>
                        <input id="goal-title" type="text" name="title" required minlength="2" maxlength="150" placeholder="Goal title" value="<?= htmlspecialchars((string) ($values['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['title'])): ?><div class="error"><?= htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="field">
                        <label for="goal-metric">Metric</label>
                        <input id="goal-metric" type="text" name="metric" required minlength="2" maxlength="100" placeholder="Weight, sales, steps, water intake..." value="<?= htmlspecialchars((string) ($values['metric'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['metric'])): ?><div class="error"><?= htmlspecialchars($errors['metric'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="goal-unit">Unit</label>
                        <input id="goal-unit" type="text" name="unit" required maxlength="30" placeholder="kg, %, liters..." value="<?= htmlspecialchars((string) ($values['unit'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['unit'])): ?><div class="error"><?= htmlspecialchars($errors['unit'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="goal-type">Goal Type</label>
                        <select id="goal-type" name="goal_type">
                            <?php foreach ([
                                'WEIGHT_LOSS' => 'Weight Loss',
                                'FITNESS' => 'Fitness',
                                'CAREER' => 'Career',
                                'FINANCE' => 'Finance',
                                'LEARNING' => 'Learning',
                                'HEALTH' => 'Health',
                                'PRODUCTIVITY' => 'Productivity',
                                'OTHER' => 'Other'
                            ] as $type => $label): ?>
                                <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>" <?= ($values['goal_type'] ?? 'OTHER') === $type ? 'selected' : '' ?>><?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['goal_type'])): ?><div class="error"><?= htmlspecialchars($errors['goal_type'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-4">
                    <div class="field">
                        <label for="goal-status">Status</label>
                        <select id="goal-status" name="status">
                            <?php foreach (['ACTIVE', 'COMPLETED', 'ON_HOLD'] as $status): ?>
                                <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= ($values['status'] ?? 'ACTIVE') === $status ? 'selected' : '' ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['status'])): ?><div class="error"><?= htmlspecialchars($errors['status'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Values and Timeline</h2>
            <p>Set where the goal starts, where it should end, and the time range you want to work within.</p>
            <div class="row">
                <div class="col-3">
                    <div class="field">
                        <label for="goal-start-value">Start Value</label>
                        <input id="goal-start-value" type="number" name="start_value" required step="0.01" min="0" max="9999999" placeholder="Example: 90.5" value="<?= htmlspecialchars((string) ($values['start_value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['start_value'])): ?><div class="error"><?= htmlspecialchars($errors['start_value'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="goal-target-value">Target Value</label>
                        <input id="goal-target-value" type="number" name="target_value" required step="0.01" min="0" max="9999999" placeholder="Example: 75.0" value="<?= htmlspecialchars((string) ($values['target_value'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($errors['target_value'])): ?><div class="error"><?= htmlspecialchars($errors['target_value'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="goal-start-date">Start Date</label>
                        <input id="goal-start-date" type="date" name="start_date" required value="<?= htmlspecialchars((string) ($values['start_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="field-help">Use the `YYYY-MM-DD` format. Validation is checked in PHP.</div>
                        <?php if (isset($errors['start_date'])): ?><div class="error"><?= htmlspecialchars($errors['start_date'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="goal-target-date">Target Date</label>
                        <input id="goal-target-date" type="date" name="target_date" required value="<?= htmlspecialchars((string) ($values['target_date'] ?? ''), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="field-help">Use the `YYYY-MM-DD` format. Validation is checked in PHP.</div>
                        <?php if (isset($errors['target_date'])): ?><div class="error"><?= htmlspecialchars($errors['target_date'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2>Description</h2>
            <p>Add context that explains what success looks like or what should be remembered while tracking it.</p>
            <div class="field">
                <label for="goal-description">Description</label>
                <textarea id="goal-description" name="description" rows="5" maxlength="2000" placeholder="Add context, milestones, or any useful notes for this goal."><?= htmlspecialchars((string) ($values['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></textarea>
                <?php if (isset($errors['description'])): ?><div class="error"><?= htmlspecialchars($errors['description'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
            </div>
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit"><?= $mode === 'edit' ? 'Save Goal' : 'Create Goal' ?></button>
            <?php if ($mode === 'edit'): ?>
                <a class="btn btn-secondary" href="<?= htmlspecialchars(route_url($area . '/records/new', ['goal_id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>">Add Record</a>
            <?php endif; ?>
        </div>
    </form>
</div>
