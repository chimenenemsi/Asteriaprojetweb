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
    .exercises-admin .search-card,.exercises-admin .search-result{border:1px solid #e2e8f0;border-radius:20px;background:#fff;box-shadow:0 18px 40px rgba(15,23,42,.06)}
    .exercises-admin .search-card .card-body,.exercises-admin .search-result .card-body{padding:22px}
    .exercises-admin .search-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:14px}
    .exercises-admin .search-results{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}
    .exercises-admin .result-top{display:flex;justify-content:space-between;align-items:flex-start;gap:12px;margin-bottom:14px}
    .exercises-admin .result-title{font-size:18px;font-weight:700;color:#0f172a}
    .exercises-admin .result-meta{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px}
    .exercises-admin .meta-pill{display:inline-flex;align-items:center;padding:7px 11px;border-radius:999px;background:#f8fafc;color:#475569;font-size:12px;font-weight:700}
    .exercises-admin .meta-pill.source-api{background:#dcfce7;color:#166534}
    .exercises-admin .meta-pill.source-fallback{background:#fef3c7;color:#92400e}
    .exercises-admin .result-copy{color:#475569;line-height:1.7}
    .exercises-admin .result-copy strong{color:#0f172a}
    .exercises-admin .result-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:18px}
    .exercises-admin .search-help{padding:14px 16px;border-radius:16px;background:#f8fafc;color:#475569}
    .exercises-admin .btn-outline-primary{border:1px solid #2563eb;color:#2563eb;background:#fff}
    .exercises-admin .btn-outline-primary:hover{background:#eff6ff}
    .exercises-admin .chart-card{padding:22px;border-radius:22px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 18px 40px rgba(15,23,42,.06);margin-bottom:20px}
    .exercises-admin .chart-wrap{position:relative;height:290px;margin-top:16px}
    @media (max-width:1200px){.exercises-admin .cards-grid{grid-template-columns:1fr}}
    @media (max-width:980px){.exercises-admin .topbar,.exercises-admin .form-footer,.exercises-admin .exercise-top{flex-direction:column;align-items:flex-start}.exercises-admin .card-actions{flex-direction:column;align-items:flex-start}.exercises-admin .search-grid,.exercises-admin .search-results{grid-template-columns:1fr}}
</style>

<div id="coaching-exercises-live-panel" class="container-fluid exercises-admin">
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
            <?php if ($selectedProgramId > 0): ?>
                <a class="btn btn-primary" data-pdf-preview="true" href="<?= htmlspecialchars(route_url('backoffice/exercises-list-pdf') . '&program=' . rawurlencode((string) $selectedProgramId) . '&exercise_search=' . rawurlencode((string) ($exerciseFilters['search'] ?? '')) . '&exercise_muscle_group=' . rawurlencode((string) ($exerciseFilters['muscle_group'] ?? '')) . '&exercise_sort=' . rawurlencode((string) ($exerciseFilters['sort'] ?? 'name_asc')), ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
            <?php endif; ?>
        </div>
    </div>

    <?php $exerciseFilters = $exerciseFilters ?? ['search' => '', 'muscle_group' => '', 'sort' => 'name_asc']; ?>
    <div class="card search-card mb-4">
        <div class="card-body">
            <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" novalidate class="row g-3 align-items-end" data-live-search="true">
                <input type="hidden" name="route" value="backoffice/exercises">
                <input type="hidden" name="program" value="<?= htmlspecialchars((string) $selectedProgramId, ENT_QUOTES, 'UTF-8') ?>">
                <div class="col-md-5">
                    <label class="form-label fw-semibold" for="exercise-filter-search">Search</label>
                    <input class="form-control" id="exercise-filter-search" name="exercise_search" type="text" placeholder="Search exercise name or description" value="<?= htmlspecialchars((string) $exerciseFilters['search'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold" for="exercise-filter-muscle">Muscle</label>
                    <input class="form-control" id="exercise-filter-muscle" name="exercise_muscle_group" type="text" placeholder="biceps" value="<?= htmlspecialchars((string) $exerciseFilters['muscle_group'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold" for="exercise-filter-sort">Sort</label>
                    <select class="form-select" id="exercise-filter-sort" name="exercise_sort">
                        <option value="name_asc" <?= $exerciseFilters['sort'] === 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                        <option value="name_desc" <?= $exerciseFilters['sort'] === 'name_desc' ? 'selected' : '' ?>>Name Z-A</option>
                        <option value="sets_desc" <?= $exerciseFilters['sort'] === 'sets_desc' ? 'selected' : '' ?>>Sets High-Low</option>
                        <option value="sets_asc" <?= $exerciseFilters['sort'] === 'sets_asc' ? 'selected' : '' ?>>Sets Low-High</option>
                        <option value="reps_desc" <?= $exerciseFilters['sort'] === 'reps_desc' ? 'selected' : '' ?>>Reps High-Low</option>
                        <option value="rest_asc" <?= $exerciseFilters['sort'] === 'rest_asc' ? 'selected' : '' ?>>Rest Low-High</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button class="btn btn-primary w-100" type="submit">Filter</button>
                    <a class="btn btn-light w-100" data-live-link="true" href="<?= htmlspecialchars(route_url('backoffice/exercises') . ($selectedProgramId > 0 ? '&program=' . rawurlencode((string) $selectedProgramId) : ''), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="activity"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) ($exerciseStats['total_exercises'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Exercises in Program</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="bar-chart-2"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) ($exerciseStats['average_sets'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Avg Sets</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="repeat"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) ($exerciseStats['average_reps'] ?? 0), ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Avg Reps</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="shield"></i></div><div><div class="fw-semibold text-dark"><?= htmlspecialchars((string) ($exerciseStats['top_muscle'] ?? 'General'), ENT_QUOTES, 'UTF-8') ?></div><p class="mb-0">Top Muscle Group</p></div></div></div></div>
    </div>

    <div class="chart-card">
        <h5 class="mb-2">Muscle Group Mix</h5>
        <p class="section-note mb-0">See which body areas dominate the current program after search and sorting are applied.</p>
        <div class="chart-wrap"><canvas id="exercise-muscle-chart"></canvas></div>
    </div>

    <div class="card search-card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                <div>
                    <span class="badge-soft">Exercise Search</span>
                    <h5 class="mt-3 mb-1">Search External Exercise Ideas</h5>
                    <p class="section-note mb-0">Search mainly by exercise type, then review the instructions, download a PDF, or copy the result into your local form. If the live API is unavailable, the page falls back to a built-in suggestion bank so the feature still works.</p>
                </div>
            </div>

            <?php if ($exerciseSearchError !== null): ?>
                <div class="alert alert-danger border-0 rounded-4 mb-3">
                    <?= htmlspecialchars($exerciseSearchError, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" novalidate class="mb-3" data-live-search="true">
                <input type="hidden" name="route" value="backoffice/exercises">
                <input type="hidden" name="program" value="<?= htmlspecialchars((string) $selectedProgramId, ENT_QUOTES, 'UTF-8') ?>">
                <div class="search-grid">
                    <div>
                        <label class="form-label fw-semibold" for="search-type">Exercise Type</label>
                        <select class="form-select" id="search-type" name="search_type">
                            <option value="">Choose a type</option>
                            <?php foreach (['strength', 'cardio', 'stretching', 'plyometrics', 'powerlifting', 'strongman', 'olympic_weightlifting', 'core'] as $type): ?>
                                <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>" <?= (string) ($exerciseSearchForm['type'] ?? '') === $type ? 'selected' : '' ?>><?= htmlspecialchars(ucwords(str_replace('_', ' ', $type)), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="form-label fw-semibold" for="search-name">Optional Name Filter</label>
                        <input class="form-control" id="search-name" name="search_name" type="text" value="<?= htmlspecialchars((string) ($exerciseSearchForm['name'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" placeholder="Bench, squat, lunge...">
                    </div>
                </div>
                <div class="d-flex flex-wrap gap-2 mt-3">
                    <button class="btn btn-primary px-4" type="submit">Search Exercises</button>
                    <a class="btn btn-light" data-live-link="true" href="<?= htmlspecialchars(route_url('backoffice/exercises') . ($selectedProgramId > 0 ? '&program=' . rawurlencode((string) $selectedProgramId) : ''), ENT_QUOTES, 'UTF-8') ?>">Clear Search</a>
                </div>
            </form>

            <?php if ($exerciseSearchResults === []): ?>
                <div class="search-help">
                    Use at least one search field above to pull in matching exercise ideas and generate a PDF summary for your coaching team.
                </div>
            <?php else: ?>
                <div class="search-results">
                    <?php foreach ($exerciseSearchResults as $result): ?>
                        <?php
                        $equipmentSummary = $result['equipments'] === [] ? 'No equipment specified' : implode(', ', $result['equipments']);
                        $pdfUrl = route_url('backoffice/exercises-pdf')
                            . '&token=' . rawurlencode((string) $result['token'])
                            . '&program=' . rawurlencode((string) $selectedProgramId);
                        ?>
                        <article class="search-result">
                            <div class="card-body">
                                <div class="result-top">
                                    <div class="result-title"><?= htmlspecialchars((string) $result['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                    <span class="badge-soft"><?= htmlspecialchars(ucfirst((string) $result['difficulty']), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="result-meta">
                                    <span class="meta-pill"><?= htmlspecialchars(str_replace('_', ' ', (string) $result['type']), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="meta-pill"><?= htmlspecialchars(str_replace('_', ' ', (string) $result['muscle']), ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="meta-pill"><?= htmlspecialchars($equipmentSummary, ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="meta-pill <?= (($result['source'] ?? '') === 'Fallback') ? 'source-fallback' : 'source-api' ?>"><?= htmlspecialchars((string) ($result['source'] ?? 'API'), ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="result-copy">
                                    <p><strong>Instructions:</strong> <?= htmlspecialchars((string) $result['instructions'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php if ((string) $result['safety_info'] !== ''): ?>
                                        <p class="mb-0"><strong>Safety:</strong> <?= htmlspecialchars((string) $result['safety_info'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="result-actions">
                                    <button
                                        class="btn btn-outline-primary btn-sm"
                                        type="button"
                                        data-exercise-name="<?= htmlspecialchars((string) $result['name'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-exercise-muscle="<?= htmlspecialchars((string) $result['muscle'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-exercise-description="<?= htmlspecialchars((string) $result['instructions'], ENT_QUOTES, 'UTF-8') ?>"
                                        onclick="applySearchExercise(this)"
                                    >
                                        Use In Form
                                    </button>
                                    <a class="btn btn-primary btn-sm" data-pdf-preview="true" href="<?= htmlspecialchars($pdfUrl, ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
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
                        <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" novalidate class="mb-4" data-live-search="true">
                            <input type="hidden" name="route" value="backoffice/exercises">
                            <label class="form-label fw-semibold" for="program-switch">Program</label>
                            <select class="form-select" id="program-switch" name="program">
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
                                    <input class="form-control <?= isset($exerciseErrors['sets']) ? 'is-invalid' : '' ?>" id="exercise-sets" name="sets" type="text" inputmode="decimal" value="<?= htmlspecialchars($exerciseForm['sets'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: 4">
                                    <?php if (isset($exerciseErrors['sets'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['sets'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="exercise-reps">Reps</label>
                                    <input class="form-control <?= isset($exerciseErrors['reps']) ? 'is-invalid' : '' ?>" id="exercise-reps" name="reps" type="text" inputmode="decimal" value="<?= htmlspecialchars($exerciseForm['reps'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: 12">
                                    <?php if (isset($exerciseErrors['reps'])): ?><div class="invalid-feedback"><?= htmlspecialchars($exerciseErrors['reps'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold" for="exercise-rest-seconds">Rest</label>
                                    <input class="form-control <?= isset($exerciseErrors['rest_seconds']) ? 'is-invalid' : '' ?>" id="exercise-rest-seconds" name="rest_seconds" type="text" inputmode="decimal" value="<?= htmlspecialchars($exerciseForm['rest_seconds'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: 60">
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
                                        <form method="post" action="<?= htmlspecialchars(route_url('backoffice/exercises'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
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
<script>
if (!window.AsteriaLiveUi) {
    window.AsteriaLiveUi = {
        liveDelayMs: 250,
        ensurePdfModal() {
            let modal = document.getElementById('asteria-pdf-modal');
            if (modal) return modal;
            modal = document.createElement('div');
            modal.id = 'asteria-pdf-modal';
            modal.innerHTML = '<div class="asteria-pdf-backdrop"></div><div class="asteria-pdf-dialog"><button type="button" class="asteria-pdf-close">Close</button><iframe title="PDF preview"></iframe></div>';
            document.body.appendChild(modal);
            const close = () => {
                modal.classList.remove('open');
                const frame = modal.querySelector('iframe');
                if (frame) frame.src = 'about:blank';
            };
            modal.querySelector('.asteria-pdf-backdrop')?.addEventListener('click', close);
            modal.querySelector('.asteria-pdf-close')?.addEventListener('click', close);
            return modal;
        },
        openPdf(url) {
            const modal = this.ensurePdfModal();
            const frame = modal.querySelector('iframe');
            if (frame) frame.src = url;
            modal.classList.add('open');
        },
        bindLiveForm(form, load) {
            if (form.dataset.liveBound === 'true') {
                return;
            }

            form.dataset.liveBound = 'true';
            let timer = 0;
            const submit = () => {
                const action = form.getAttribute('action') || window.location.pathname;
                const query = new URLSearchParams(new FormData(form)).toString();
                load(query ? action + '?' + query : action);
            };
            const schedule = () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(submit, this.liveDelayMs);
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                submit();
            });
            form.querySelectorAll('input[type="text"], input[type="search"]').forEach((field) => {
                field.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        submit();
                    }
                });
            });
            form.querySelectorAll('select').forEach((field) => field.addEventListener('change', submit));
        },
        bindPanel(panelId) {
            const panel = document.getElementById(panelId);
            if (!panel) return;
            const load = (url) => {
                panel.classList.add('is-loading');
                fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                    .then(response => response.text())
                    .then(html => {
                        const doc = new DOMParser().parseFromString(html, 'text/html');
                        const next = doc.getElementById(panelId);
                        if (!next) {
                            window.location.href = url;
                            return;
                        }
                        panel.replaceWith(next);
                        window.history.pushState({}, '', url);
                        this.bindPanel(panelId);
                        if (typeof window.initCoachingExercises === 'function') {
                            window.initCoachingExercises(true);
                        }
                    })
                    .catch(() => { window.location.href = url; });
            };
            panel.querySelectorAll('form[data-live-search]').forEach((form) => this.bindLiveForm(form, load));
            panel.querySelectorAll('a[data-live-link]').forEach((link) => {
                if (link.dataset.liveBound === 'true') return;
                link.dataset.liveBound = 'true';
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    load(link.href);
                });
            });
            panel.querySelectorAll('a[data-pdf-preview]').forEach((link) => {
                if (link.dataset.pdfBound === 'true') return;
                link.dataset.pdfBound = 'true';
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    window.AsteriaLiveUi.openPdf(link.href);
                });
            });
        }
    };
}

function applySearchExercise(button) {
    const nameField = document.getElementById('exercise-name');
    const muscleField = document.getElementById('exercise-muscle-group');
    const descriptionField = document.getElementById('exercise-description');

    if (nameField) {
        nameField.value = button.dataset.exerciseName || '';
    }

    if (muscleField) {
        muscleField.value = button.dataset.exerciseMuscle || '';
    }

    if (descriptionField) {
        descriptionField.value = button.dataset.exerciseDescription || '';
    }

    window.scrollTo({ top: nameField ? nameField.getBoundingClientRect().top + window.scrollY - 120 : 0, behavior: 'smooth' });
}

window.initCoachingExercises = function initCoachingExercises(fromRefresh) {
    if (!window.Chart) {
        if (!fromRefresh) {
            window.AsteriaLiveUi.bindPanel('coaching-exercises-live-panel');
        }
        return;
    }

    const muscleCanvas = document.getElementById('exercise-muscle-chart');
    const muscleData = <?= json_encode($exerciseStats['muscle_distribution'] ?? [], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) ?>;
    if (!muscleCanvas || muscleData.length === 0) {
        if (!fromRefresh) {
            window.AsteriaLiveUi.bindPanel('coaching-exercises-live-panel');
        }
        return;
    }

    new Chart(muscleCanvas, {
        type: 'pie',
        data: {
            labels: muscleData.map(item => item.label),
            datasets: [{
                data: muscleData.map(item => Number(item.total || 0)),
                backgroundColor: ['#2563eb', '#14b8a6', '#f59e0b', '#ef4444', '#8b5cf6', '#22c55e', '#f97316'],
                borderColor: '#ffffff',
                borderWidth: 3
            }]
        },
        options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {position: 'bottom'}}}
    });
    if (!fromRefresh) {
        window.AsteriaLiveUi.bindPanel('coaching-exercises-live-panel');
    }
};

document.addEventListener('DOMContentLoaded', function () {
    window.initCoachingExercises(false);
});
</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.is-loading{opacity:.65;pointer-events:none}
#asteria-pdf-modal{position:fixed;inset:0;display:none;z-index:9999}
#asteria-pdf-modal.open{display:block}
.asteria-pdf-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.6)}
.asteria-pdf-dialog{position:relative;width:min(960px,92vw);height:min(88vh,820px);margin:4vh auto;background:#fff;border-radius:24px;overflow:hidden}
.asteria-pdf-close{position:absolute;top:14px;right:14px;z-index:2;border:0;border-radius:999px;padding:10px 14px;background:#0f172a;color:#fff}
.asteria-pdf-dialog iframe{width:100%;height:100%;border:0}
</style>
