<?php
declare(strict_types=1);

$flashClass = match ((string) ($flash['type'] ?? 'info')) {
    'success' => 'alert-success',
    'danger' => 'alert-danger',
    default => 'alert-info',
};

$shouldAutoOpenProgramModal = $programErrors !== [] || $programForm['id'] !== '';
?>
<style>
    .programs-admin .panel,.programs-admin .stat-card,.programs-admin .cards-wrap,.programs-admin .program-card{border:0;border-radius:22px;box-shadow:0 18px 40px rgba(15,23,42,.08)}
    .programs-admin .panel,.programs-admin .cards-wrap,.programs-admin .program-card{background:#fff}
    .programs-admin .stat-card{height:100%}
    .programs-admin .stat-icon,.programs-admin .badge-soft,#programModal .badge-soft{display:inline-flex;align-items:center;justify-content:center}
    .programs-admin .stat-icon{width:48px;height:48px;border-radius:16px;background:#ecfeff;color:#0f766e}
    .programs-admin .stat-number{margin:0;font-size:32px;font-weight:800;line-height:1;color:#0f172a}
    .programs-admin .stat-card p,.programs-admin .section-note,.programs-admin .muted{color:#64748b}
    .programs-admin .hero-bar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:24px 28px;background:linear-gradient(135deg,#ffffff 0%,#f7fbff 55%,#f1fcf8 100%)}
    .programs-admin .hero-bar h5{margin:0;color:#0f172a}
    .programs-admin .badge-soft,#programModal .badge-soft{padding:8px 12px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:800;letter-spacing:.06em;text-transform:uppercase}
    .programs-admin .cards-wrap{padding:22px}
    .programs-admin .cards-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:18px}
    .programs-admin .program-card{position:relative;overflow:hidden;height:100%;border:1px solid #eef2f7}
    .programs-admin .program-card::before{content:"";position:absolute;inset:0 auto 0 0;width:5px;background:var(--accent,#1d4ed8)}
    .programs-admin .program-card.active{box-shadow:0 24px 54px rgba(37,99,235,.14)}
    .programs-admin .program-card .card-body{padding:22px}
    .programs-admin .program-top{display:flex;align-items:flex-start;justify-content:space-between;gap:12px;margin-bottom:18px}
    .programs-admin .program-title{font-weight:700;color:#0f172a;font-size:18px;line-height:1.3}
    .programs-admin .program-meta{display:grid;gap:10px;margin-bottom:18px}
    .programs-admin .meta-row{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border-radius:14px;background:#f8fafc;color:#475569}
    .programs-admin .card-actions{display:flex;flex-wrap:wrap;gap:10px;margin-top:auto}
    .programs-admin .empty-state{padding:40px;text-align:center;color:#64748b}
    .programs-admin .form-control,#programModal .form-control{border-radius:14px;padding:12px 14px;border:1px solid #dbe3ee;box-shadow:none}
    .programs-admin .form-control:focus,#programModal .form-control:focus{border-color:rgba(37,99,235,.5);box-shadow:0 0 0 .25rem rgba(37,99,235,.08)}
    .programs-admin textarea.form-control,#programModal textarea.form-control{min-height:120px}
    .programs-admin .form-footer{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:12px;margin-top:22px}
    .programs-admin .form-hint{padding:14px 16px;border-radius:16px;background:#eef6ff;color:#1e3a8a}
    #programModal .modal-dialog{max-width:720px}
    #programModal .modal-content{background:#fff;border:1px solid #dbe3ee;box-shadow:0 32px 90px rgba(15,23,42,.22);border-radius:28px;overflow:hidden}
    #programModal .modal-panel{padding:0;background:#fff}
    #programModal .modal-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding:24px 28px 20px;margin:0;border-bottom:1px solid #e2e8f0;background:linear-gradient(180deg,#fbfdff 0%,#f6fbff 100%)}
    #programModal .modal-header h5{margin:12px 0 6px;color:#0f172a;font-size:24px;font-weight:700}
    #programModal .modal-header p{margin:0;color:#64748b}
    #programModal .modal-header .btn-close{margin:0;flex-shrink:0;width:40px;height:40px;padding:0;border:1px solid #dbe3ee;border-radius:999px;background-size:12px;opacity:.9}
    #programModal form{padding:24px 28px 28px}
    #programModal .form-label{margin-bottom:8px;color:#334155}
    #programModal .form-footer{display:flex;justify-content:space-between;align-items:center;gap:16px;margin-top:24px;padding-top:18px;border-top:1px solid #e2e8f0}
    #programModal .form-hint{max-width:320px;padding:12px 14px;border-radius:16px;background:#f8fafc;color:#475569;font-size:13px;line-height:1.5}
    #programModal .modal-actions{display:flex;flex-wrap:wrap;justify-content:flex-end;gap:10px}
    @media (max-width:1200px){.programs-admin .cards-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
    @media (max-width:980px){.programs-admin .hero-bar,.programs-admin .form-footer,.programs-admin .program-top{flex-direction:column;align-items:flex-start}.programs-admin .card-actions{flex-direction:column;align-items:flex-start}.programs-admin .cards-grid{grid-template-columns:1fr}}
    @media (max-width:767px){#programModal .modal-header,#programModal form{padding-left:20px;padding-right:20px}#programModal .form-footer{flex-direction:column;align-items:stretch}#programModal .form-hint{max-width:none}#programModal .modal-actions{justify-content:stretch}#programModal .modal-actions .btn{width:100%}}
</style>

<div class="container-fluid programs-admin">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Programs</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Backoffice</a></li>
                <li class="breadcrumb-item active">Programs</li>
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

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="layers"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['total_programs'], ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Programs</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="activity"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['total_exercises'], ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Exercises</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="calendar"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['total_weeks'], ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Weeks</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="bar-chart-2"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['average_exercises'], ENT_QUOTES, 'UTF-8') ?></p><p class="mb-0">Avg / Program</p></div></div></div></div>
    </div>

    <div class="card panel hero-bar mb-4">
        <div>
            <span class="badge-soft">Program CRUD</span>
            <h5 class="mt-3">Program Management</h5>
            <p class="section-note mb-0">Create, update, delete, and open exercises per program.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary px-4" type="button" data-bs-toggle="modal" data-bs-target="#programModal">Create Program</button>
        </div>
    </div>

    <div class="card cards-wrap">
        <?php if ($programs === []): ?>
            <div class="empty-state">
                <h5 class="mb-2">No programs found</h5>
                <p class="mb-0">Create the first program to start managing exercises by program.</p>
            </div>
        <?php else: ?>
            <div class="cards-grid">
                <?php foreach ($programs as $program): ?>
                    <?php
                    $programId = (int) ($program->getId() ?? 0);
                    $programTitle = $program->getTitle();
                    $programDescription = (string) ($program->getDescription() ?? '');
                    $programGoalType = (string) ($program->getGoalType() ?? '');
                    $programDurationWeeks = $program->getDurationWeeks();
                    $programExerciseCount = $program->getExerciseCount();
                    $exercisePageUrl = route_url('backoffice/exercises') . '&program=' . rawurlencode((string) $programId);
                    $editUrl = route_url('backoffice/programs') . '&program=' . rawurlencode((string) $programId) . '&editProgram=' . rawurlencode((string) $programId);
                    $isActive = (int) $selectedProgramId === $programId;
                    ?>
                    <article class="card program-card <?= $isActive ? 'active' : '' ?>" style="--accent: <?= $isActive ? '#2563eb' : '#14b8a6' ?>;">
                        <div class="card-body d-flex flex-column">
                            <div class="program-top">
                                <div>
                                    <div class="program-title"><?= htmlspecialchars($programTitle, ENT_QUOTES, 'UTF-8') ?></div>
                                    <?php if ($programDescription !== ''): ?>
                                        <p class="muted mt-2 mb-0"><?= htmlspecialchars($programDescription, ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>
                                </div>
                                <span class="badge-soft"><?= htmlspecialchars($programGoalType !== '' ? $programGoalType : 'General', ENT_QUOTES, 'UTF-8') ?></span>
                            </div>

                            <div class="program-meta">
                                <div class="meta-row"><span>Weeks</span><strong><?= htmlspecialchars((string) $programDurationWeeks, ENT_QUOTES, 'UTF-8') ?></strong></div>
                                <div class="meta-row"><span>Exercises</span><strong><?= htmlspecialchars((string) $programExerciseCount, ENT_QUOTES, 'UTF-8') ?></strong></div>
                                <div class="meta-row"><span>Program ID</span><strong>#<?= htmlspecialchars((string) $programId, ENT_QUOTES, 'UTF-8') ?></strong></div>
                            </div>

                            <div class="card-actions">
                                <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars($exercisePageUrl, ENT_QUOTES, 'UTF-8') ?>">Exercises</a>
                                <a class="btn btn-light btn-sm" href="<?= htmlspecialchars($editUrl, ENT_QUOTES, 'UTF-8') ?>">Update</a>
                                <form method="post" action="<?= htmlspecialchars(route_url('backoffice/programs'), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this program and all related exercises?');">
                                    <input type="hidden" name="action" value="delete-program">
                                    <input type="hidden" name="program_id" value="<?= htmlspecialchars((string) $programId, ENT_QUOTES, 'UTF-8') ?>">
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

<div class="modal fade" id="programModal" tabindex="-1" aria-labelledby="programModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-panel">
                <div class="modal-header">
                    <div>
                        <span class="badge-soft"><?= $programForm['id'] === '' ? 'Create' : 'Update' ?></span>
                        <h5 id="programModalLabel"><?= $programForm['id'] === '' ? 'Create program' : 'Update program' ?></h5>
                        <p>Fill the essential information for this training program.</p>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form method="post" action="<?= htmlspecialchars(route_url('backoffice/programs'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                    <input type="hidden" name="action" value="save-program">
                    <input type="hidden" name="program_id" value="<?= htmlspecialchars($programForm['id'], ENT_QUOTES, 'UTF-8') ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold" for="program-title">Title</label>
                        <input class="form-control <?= isset($programErrors['title']) ? 'is-invalid' : '' ?>" id="program-title" name="title" type="text" value="<?= htmlspecialchars($programForm['title'], ENT_QUOTES, 'UTF-8') ?>">
                        <?php if (isset($programErrors['title'])): ?><div class="invalid-feedback"><?= htmlspecialchars($programErrors['title'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label class="form-label fw-semibold" for="program-goal-type">Goal Type</label>
                            <input class="form-control <?= isset($programErrors['goal_type']) ? 'is-invalid' : '' ?>" id="program-goal-type" name="goal_type" type="text" value="<?= htmlspecialchars($programForm['goal_type'], ENT_QUOTES, 'UTF-8') ?>">
                            <?php if (isset($programErrors['goal_type'])): ?><div class="invalid-feedback"><?= htmlspecialchars($programErrors['goal_type'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-semibold" for="program-duration-weeks">Weeks</label>
                            <input class="form-control <?= isset($programErrors['duration_weeks']) ? 'is-invalid' : '' ?>" id="program-duration-weeks" name="duration_weeks" type="text" value="<?= htmlspecialchars($programForm['duration_weeks'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Example: 8">
                            <?php if (isset($programErrors['duration_weeks'])): ?><div class="invalid-feedback"><?= htmlspecialchars($programErrors['duration_weeks'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label fw-semibold" for="program-description">Description</label>
                        <textarea class="form-control <?= isset($programErrors['description']) ? 'is-invalid' : '' ?>" id="program-description" name="description" rows="4" placeholder="Describe the focus and structure of this program."><?= htmlspecialchars($programForm['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        <?php if (isset($programErrors['description'])): ?><div class="invalid-feedback"><?= htmlspecialchars($programErrors['description'], ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
                    </div>

                    <div class="form-footer">
                        <div class="form-hint">Validation is handled in PHP, and exercises are managed in the dedicated page.</div>
                        <div class="modal-actions">
                            <a class="btn btn-light" href="<?= htmlspecialchars(route_url('backoffice/programs') . ($selectedProgramId > 0 ? '&program=' . rawurlencode((string) $selectedProgramId) : ''), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
                            <button class="btn btn-primary px-4" type="submit"><?= $programForm['id'] === '' ? 'Save' : 'Update' ?></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const modalEl = document.getElementById('programModal');

    if (modalEl) {
        modalEl.addEventListener('hidden.bs.modal', function () {
            const url = new URL(window.location.href);
            url.searchParams.delete('editProgram');
            window.history.replaceState({}, '', url.toString());
        });
    }

    <?php if ($shouldAutoOpenProgramModal): ?>
    if (modalEl && window.bootstrap) {
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }
    <?php endif; ?>
});
</script>
