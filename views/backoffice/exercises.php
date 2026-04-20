<?php
declare(strict_types=1);

$flashClass = match ((string) ($flash['type'] ?? 'info')) {
    'success' => 'alert-success',
    'danger' => 'alert-danger',
    default => 'alert-info',
};
?>
<style>
    .exercises-admin .panel,.exercises-admin .stat-card,.exercises-admin .cards-wrap,.exercises-admin .exercise-card,.exercises-admin .form-card{border:0;border-radius:22px;box-shadow:0 18px 40px rgba(15,23,42,.08);background:#fff}
    .exercises-admin .stat-card{height:100%}
    .exercises-admin .stat-icon,.exercises-admin .badge-soft{display:inline-flex;align-items:center;justify-content:center}
    .exercises-admin .stat-icon{width:48px;height:48px;border-radius:16px;background:#ecfeff;color:#0f766e}
    .exercises-admin .stat-number{margin:0;font-size:32px;font-weight:800;line-height:1;color:#0f172a}
    .exercises-admin .badge-soft{padding:8px 12px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
    .exercises-admin .topbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:24px 28px;background:linear-gradient(135deg,#ffffff 0%,#f7fbff 55%,#f1fcf8 100%)}
    .exercises-admin .section-note,.exercises-admin .muted,.exercises-admin .stat-card p{color:#64748b}
    .exercises-admin .cards-wrap{padding:22px}
    .exercises-admin .cards-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
    .exercises-admin .exercise-card{position:relative;overflow:hidden;height:100%;border:1px solid #eef2f7}
    .exercises-admin .exercise-card::before{content:"";position:absolute;inset:0 auto 0 0;width:5px;background:var(--accent,#1d4ed8)}
    .exercises-admin .exercise-card.active{box-shadow:0 24px 54px rgba(37,99,235,.14)}
    .exercises-admin .exercise-card .card-body{padding:22px}
    .exercises-admin .exercise-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:18px}
    .exercises-admin .exercise-title{font-weight:700;color:#0f172a;font-size:18px;line-height:1.3}
    .exercises-admin .exercise-meta{display:grid;gap:10px;margin-bottom:18px}
    .exercises-admin .meta-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border-radius:14px;background:#f8fafc;color:#475569}
    .exercises-admin .card-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:auto}
    .exercises-admin .form-control,.exercises-admin .form-select{border-radius:14px;padding:12px 14px;border:1px solid #dbe3ee;box-shadow:none}
    .exercises-admin .form-control:focus,.exercises-admin .form-select:focus{border-color:rgba(37,99,235,.5);box-shadow:0 0 0 .25rem rgba(37,99,235,.08)}
    .exercises-admin textarea.form-control{min-height:120px}
    .exercises-admin .form-hint{padding:14px 16px;border-radius:16px;background:#eef6ff;color:#1e3a8a}
    .exercises-admin .empty-state{padding:40px;text-align:center;color:#64748b}
    .exercises-admin .form-footer{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px;margin-top:22px}
    @media (max-width:1200px){.exercises-admin .cards-grid{grid-template-columns:1fr}}
    @media (max-width:980px){.exercises-admin .topbar,.exercises-admin .form-footer,.exercises-admin .exercise-top{flex-direction:column;align-items:flex-start}.exercises-admin .card-actions{flex-direction:column;align-items:flex-start}}
</style>

<div class="container-fluid exercises-admin">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Exercises</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Backoffice</a></li>
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(route_url('backoffice/programs'), ENT_QUOTES, 'UTF-8') ?>">Programs</a></li>
                <li class="breadcrumb-item active">Exercises</li>
            </ol>
        </div>
    </div>

    <?php if ($flash !== null): ?>
        <div class="alert <?= htmlspecialchars($flashClass, ENT_QUOTES, 'UTF-8') ?> border-0 rounded-4">
            <?= htmlspecialchars((string) ($flash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <?php if ($dbError !== null): ?>
        <div class="alert alert-danger border-0 rounded-4">
            <?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="card panel topbar mb-4">
        <div>
            <span class="badge-soft">Exercise CRUD</span>
            <h5 class="mt-3 mb-1"><?= htmlspecialchars($selectedProgram?->getTitle() ?? 'No Program Selected', ENT_QUOTES, 'UTF-8') ?></h5>
            <p class="section-note mb-0">Exercise management for the selected program.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a class="btn btn-light" href="<?= htmlspecialchars(route_url('backoffice/programs'), ENT_QUOTES, 'UTF-8') ?>">Back to Programs</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-4 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="activity"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) count($selectedProgram?->getExercises() ?? []), ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Exercises in Program</p></div></div></div></div>
        <div class="col-xl-4 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="clock"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) ($selectedProgram?->getDurationWeeks() ?? 0), ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Program Weeks</p></div></div></div></div>
        <div class="col-xl-4 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="target"></i></div><div><div class="fw-semibold text-dark"><?= htmlspecialchars((($selectedProgram?->getGoalType() ?? '') !== '') ? (string) ($selectedProgram?->getGoalType() ?? '') : '-', ENT_QUOTES, 'UTF-8') ?></div><p class="mb-0">Goal Type</p></div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-xxl-4">
            <div class="card form-card">
                <div class="card-body p-4">
                    <h5 class="mb-3"><?= $exerciseForm['id'] === '' ? 'Add Exercise' : 'Update Exercise' ?></h5>

                    <?php if ($programs === []): ?>
                        <div class="empty-state">
                            <p class="mb-0">Create a program first before adding exercises.</p>
                        </div>
                    <?php else: ?>
                        <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" class="mb-4">
                            <input type="hidden" name="route" value="backoffice/exercises">
                            <label class="form-label fw-semibold" for="program-switch">Program</label>
                            <select class="form-select" id="program-switch" name="program" onchange="this.form.submit()">
                                <?php foreach ($programs as $program): ?>
                                    <option value="<?= htmlspecialchars((string) ($program->getId() ?? 0), ENT_QUOTES, 'UTF-8') ?>" <?= (int) $selectedProgramId === (int) ($program->getId() ?? 0) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($program->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </form>

                        <form method="post" action="<?= htmlspecialchars(route_url('backoffice/exercises'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                            <input type="hidden" name="action" value="save-exercise">
                            <input type="hidden" name="exercise_id" value="<?= htmlspecialchars($exerciseForm['id'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="program_id" value="<?= htmlspecialchars($exerciseForm['program_id'], ENT_QUOTES, 'UTF-8') ?>">

                            <?php if (isset($exerciseErrors['program_id'])): ?>
                                <div class="alert alert-danger border-0 rounded-4">
                                    <?= htmlspecialchars($exerciseErrors['program_id'], ENT_QUOTES, 'UTF-8') ?>
                                </div>
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="exercise-name">Name</label>
                                <input class="form-control <?= isset($exerciseErrors['name']) ? 'is-invalid' : '' ?>" id="exercise-name" name="name" type="text" value="<?= htmlspecialchars($exerciseForm['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php if (isset($exerciseErrors['name'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['name'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="exercise-muscle-group">Muscle Group</label>
                                <input class="form-control <?= isset($exerciseErrors['muscle_group']) ? 'is-invalid' : '' ?>" id="exercise-muscle-group" name="muscle_group" type="text" value="<?= htmlspecialchars($exerciseForm['muscle_group'], ENT_QUOTES, 'UTF-8') ?>">
                                <?php if (isset($exerciseErrors['muscle_group'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['muscle_group'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold" for="exercise-description">Description</label>
                                <textarea class="form-control <?= isset($exerciseErrors['description']) ? 'is-invalid' : '' ?>" id="exercise-description" name="description" rows="4" placeholder="Describe the movement, variation, or coaching note."><?= htmlspecialchars($exerciseForm['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                                <?php if (isset($exerciseErrors['description'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['description'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="exercise-sets">Sets</label>
                                    <input class="form-control <?= isset($exerciseErrors['sets']) ? 'is-invalid' : '' ?>" id="exercise-sets" name="sets" type="text" value="<?= htmlspecialchars($exerciseForm['sets'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: 4">
                                    <?php if (isset($exerciseErrors['sets'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['sets'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="exercise-reps">Reps</label>
                                    <input class="form-control <?= isset($exerciseErrors['reps']) ? 'is-invalid' : '' ?>" id="exercise-reps" name="reps" type="text" value="<?= htmlspecialchars($exerciseForm['reps'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: 12">
                                    <?php if (isset($exerciseErrors['reps'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['reps'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="exercise-rest-seconds">Rest</label>
                                    <input class="form-control <?= isset($exerciseErrors['rest_seconds']) ? 'is-invalid' : '' ?>" id="exercise-rest-seconds" name="rest_seconds" type="text" value="<?= htmlspecialchars($exerciseForm['rest_seconds'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: 60">
                                    <?php if (isset($exerciseErrors['rest_seconds'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['rest_seconds'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                </div>
                            </div>

                            <div class="form-footer">
                                <div class="form-hint">This form affects only the selected program, and validation is handled in PHP.</div>
                                <div class="d-flex flex-wrap gap-2">
                                    <a class="btn btn-light" href="<?= htmlspecialchars(route_url('backoffice/exercises') . ($selectedProgramId > 0 ? '&program=' . rawurlencode((string) $selectedProgramId) : ''), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
                                    <button class="btn btn-primary px-4" type="submit"><?= $exerciseForm['id'] === '' ? 'Save' : 'Update' ?></button>
                                </div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-xxl-8">
            <div class="card cards-wrap">
                <?php if ($selectedProgram === null): ?>
                    <div class="empty-state">
                        <h5 class="mb-2">No program available</h5>
                        <p class="mb-0">Create a program first, then come back here to manage exercises.</p>
                    </div>
                <?php elseif ($selectedProgram->getExercises() === []): ?>
                    <div class="empty-state">
                        <h5 class="mb-2">No exercises in this program</h5>
                        <p class="mb-0">Use the form to create the first exercise for <?= htmlspecialchars($selectedProgram->getTitle(), ENT_QUOTES, 'UTF-8') ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="cards-grid">
                        <?php foreach ($selectedProgram->getExercises() as $exercise): ?>
                            <?php
                            $selectedProgramIdValue = (int) ($selectedProgram->getId() ?? 0);
                            $exerciseId = (int) ($exercise->getId() ?? 0);
                            $exerciseName = $exercise->getName();
                            $exerciseDescription = (string) ($exercise->getDescription() ?? '');
                            $exerciseMuscleGroup = (string) ($exercise->getMuscleGroup() ?? '');
                            $editUrl = route_url('backoffice/exercises')
                                . '&program=' . rawurlencode((string) $selectedProgramIdValue)
                                . '&editExercise=' . rawurlencode((string) $exerciseId);
                            $isActive = (int) ($exerciseForm['id'] ?: 0) === $exerciseId;
                            ?>
                            <article class="card exercise-card <?= $isActive ? 'active' : '' ?>" style="--accent: <?= $isActive ? '#2563eb' : '#14b8a6' ?>;">
                                <div class="card-body d-flex flex-column">
                                    <div class="exercise-top">
                                        <div class="exercise-title"><?= htmlspecialchars($exerciseName, ENT_QUOTES, 'UTF-8') ?></div>
                                        <span class="badge-soft"><?= htmlspecialchars($exerciseMuscleGroup !== '' ? $exerciseMuscleGroup : 'General', ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>

                                    <?php if ($exerciseDescription !== ''): ?>
                                        <p class="muted mb-3"><?= htmlspecialchars($exerciseDescription, ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>

                                    <div class="exercise-meta">
                                        <div class="meta-row"><span>Sets</span><strong><?= htmlspecialchars((string) $exercise->getSets(), ENT_QUOTES, 'UTF-8') ?></strong></div>
                                        <div class="meta-row"><span>Reps</span><strong><?= htmlspecialchars((string) $exercise->getReps(), ENT_QUOTES, 'UTF-8') ?></strong></div>
                                        <div class="meta-row"><span>Rest</span><strong><?= htmlspecialchars((string) $exercise->getRestSeconds(), ENT_QUOTES, 'UTF-8') ?> sec</strong></div>
                                        <div class="meta-row"><span>Exercise ID</span><strong>#<?= htmlspecialchars((string) $exerciseId, ENT_QUOTES, 'UTF-8') ?></strong></div>
                                    </div>

                                    <div class="card-actions">
                                        <a class="btn btn-light btn-sm" href="<?= htmlspecialchars($editUrl, ENT_QUOTES, 'UTF-8') ?>">Update</a>
                                        <form method="post" action="<?= htmlspecialchars(route_url('backoffice/exercises'), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this exercise?');">
                                            <input type="hidden" name="action" value="delete-exercise">
                                            <input type="hidden" name="exercise_id" value="<?= htmlspecialchars((string) $exerciseId, ENT_QUOTES, 'UTF-8') ?>">
                                            <input type="hidden" name="program_id" value="<?= htmlspecialchars((string) $selectedProgramIdValue, ENT_QUOTES, 'UTF-8') ?>">
                                            <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
