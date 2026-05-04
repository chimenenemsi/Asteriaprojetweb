<?php
declare(strict_types=1);

$statusMap = [
    'published' => ['label' => 'Published', 'badge' => 'bg-success-subtle text-success', 'accent' => '#16a34a'],
    'draft' => ['label' => 'Draft', 'badge' => 'bg-warning-subtle text-warning', 'accent' => '#d97706'],
    'archived' => ['label' => 'Archived', 'badge' => 'bg-secondary-subtle text-secondary', 'accent' => '#64748b'],
];

$formData = $editingConsultation ?? [
    'id' => '',
    'title' => '',
    'category' => '',
    'duration' => '',
    'format' => 'Online',
    'price' => '',
    'focus' => '',
    'description' => '',
    'status' => 'draft',
    'spots' => 1,
];

$flashClass = ($flash['type'] ?? '') === 'success' ? 'alert-success' : 'alert-info';
?>
<style>
    .consultation-admin .stat-card,.consultation-admin .editor-card,.consultation-admin .item-card{border:0;border-radius:24px;box-shadow:0 18px 40px rgba(15,23,42,.08)}
    .consultation-admin .stat-icon,.consultation-admin .editor-chip{display:inline-flex;align-items:center;justify-content:center}
    .consultation-admin .stat-icon{width:48px;height:48px;border-radius:16px;background:#eff6ff;color:#1d4ed8}
    .consultation-admin .stat-number{margin:0;font-size:34px;font-weight:800;color:#0f172a;line-height:1}
    .consultation-admin .stat-card p{margin:6px 0 0;color:#64748b}
    .consultation-admin .editor-card{background:linear-gradient(180deg,#fff 0%,#f8fafc 100%)}
    .consultation-admin .editor-chip{gap:8px;padding:8px 12px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-weight:600;font-size:13px}
    .consultation-admin .editor-note{padding:16px 18px;border-radius:18px;background:#0f172a;color:rgba(255,255,255,.82);line-height:1.7}
    .consultation-admin .editor-note strong{color:#fff}
    .consultation-admin .item-card{position:relative;overflow:hidden;background:#fff}
    .consultation-admin .item-card::before{content:"";position:absolute;inset:0 auto 0 0;width:5px;background:var(--accent,#1d4ed8)}
    .consultation-admin .chip{display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:999px;background:#f8fafc;color:#334155;font-size:13px;font-weight:600}
    .consultation-admin .focus-line{font-size:15px;font-weight:600;color:#1d4ed8}
    .consultation-admin .desc{color:#64748b;line-height:1.7}
    .consultation-admin .card-footer-line{margin-top:auto;padding-top:18px;border-top:1px solid #e2e8f0}
    .consultation-admin .updated-label{font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#94a3b8}
    .consultation-admin .empty-state{padding:42px;border-radius:24px;background:#fff;text-align:center;color:#64748b;box-shadow:0 18px 40px rgba(15,23,42,.08)}
</style>

<div class="container-fluid consultation-admin">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column gap-2">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Consultation Studio</h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Backoffice</a></li>
                <li class="breadcrumb-item active">Consultation</li>
            </ol>
        </div>
    </div>

    <?php if ($flash !== null): ?>
        <div class="alert <?= htmlspecialchars($flashClass, ENT_QUOTES, 'UTF-8') ?> border-0 rounded-4">
            <?= htmlspecialchars((string) ($flash['message'] ?? ''), ENT_QUOTES, 'UTF-8') ?>
        </div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6"><div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="layers"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['total'], ENT_QUOTES, 'UTF-8') ?></p><p>Total cards</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="globe"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['published'], ENT_QUOTES, 'UTF-8') ?></p><p>Published</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="file-text"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['draft'], ENT_QUOTES, 'UTF-8') ?></p><p>Drafts</p></div></div></div></div>
        <div class="col-xl-3 col-md-6"><div class="card stat-card h-100"><div class="card-body d-flex align-items-center gap-3"><div class="stat-icon"><i data-feather="calendar"></i></div><div><p class="stat-number"><?= htmlspecialchars((string) $stats['spots'], ENT_QUOTES, 'UTF-8') ?></p><p>Visible spots</p></div></div></div></div>
    </div>

    <div class="row g-4">
        <div class="col-xxl-4" id="consultation-form">
            <div class="card editor-card h-100">
                <div class="card-body p-4">
                    <div class="editor-chip mb-3"><i data-feather="star"></i><span><?= $editingConsultation === null ? 'Create card' : 'Edit card' ?></span></div>
                    <h5 class="mb-1"><?= $editingConsultation === null ? 'New consultation offer' : 'Refine consultation offer' ?></h5>
                    <p class="text-muted mb-4">Everything here updates the consultation grid below and the live cards in frontoffice.</p>

                    <form method="post" action="<?= htmlspecialchars(route_url('backoffice/consultation'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                        <input type="hidden" name="action" value="save-consultation">
                        <input type="hidden" name="consultation_id" value="<?= htmlspecialchars((string) $formData['id'], ENT_QUOTES, 'UTF-8') ?>">

                        <div class="mb-3">
                            <label class="form-label fw-semibold" for="consultation-title">Card title</label>
                            <input class="form-control form-control-lg" id="consultation-title" name="title" type="text" value="<?= htmlspecialchars((string) $formData['title'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Deep Nutrition Assessment">
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="consultation-category">Category</label>
                                <input class="form-control" id="consultation-category" name="category" type="text" value="<?= htmlspecialchars((string) $formData['category'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Personalized Coaching">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="consultation-status">Status</label>
                                <select class="form-select" id="consultation-status" name="status">
                                    <option value="draft" <?= (string) $formData['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                                    <option value="published" <?= (string) $formData['status'] === 'published' ? 'selected' : '' ?>>Published</option>
                                    <option value="archived" <?= (string) $formData['status'] === 'archived' ? 'selected' : '' ?>>Archived</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="consultation-duration">Duration</label>
                                <input class="form-control" id="consultation-duration" name="duration" type="text" value="<?= htmlspecialchars((string) $formData['duration'], ENT_QUOTES, 'UTF-8') ?>" placeholder="45 min">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="consultation-format">Format</label>
                                <select class="form-select" id="consultation-format" name="format">
                                    <?php foreach (['Online', 'Hybrid', 'In Clinic'] as $formatOption): ?>
                                        <option value="<?= htmlspecialchars($formatOption, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $formData['format'] === $formatOption ? 'selected' : '' ?>><?= htmlspecialchars($formatOption, ENT_QUOTES, 'UTF-8') ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="consultation-price">Price</label>
                                <input class="form-control" id="consultation-price" name="price" type="text" value="<?= htmlspecialchars((string) $formData['price'], ENT_QUOTES, 'UTF-8') ?>" placeholder="120 TND">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="consultation-spots">Visible spots</label>
                                <input class="form-control" id="consultation-spots" name="spots" type="text" inputmode="decimal" value="<?= htmlspecialchars((string) $formData['spots'], ENT_QUOTES, 'UTF-8') ?>">
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold" for="consultation-focus">Focus line</label>
                            <input class="form-control" id="consultation-focus" name="focus" type="text" value="<?= htmlspecialchars((string) $formData['focus'], ENT_QUOTES, 'UTF-8') ?>" placeholder="Habits, routine, and full plan review">
                        </div>
                        <div class="mt-3">
                            <label class="form-label fw-semibold" for="consultation-description">Description</label>
                            <textarea class="form-control" id="consultation-description" name="description" rows="5" placeholder="Explain who this consultation is for and why it helps."><?= htmlspecialchars((string) $formData['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mt-4">
                            <button class="btn btn-primary px-4" type="submit"><?= $editingConsultation === null ? 'Save new card' : 'Update card' ?></button>
                            <a class="btn btn-light px-4" href="<?= htmlspecialchars(route_url('backoffice/consultation'), ENT_QUOTES, 'UTF-8') ?>">Clear form</a>
                            <a class="btn btn-outline-dark px-4" href="<?= htmlspecialchars(route_url('frontoffice/consultation'), ENT_QUOTES, 'UTF-8') ?>">Preview frontoffice</a>
                        </div>
                    </form>

                    <div class="editor-note mt-4"><strong>Quick publishing tip:</strong> keep draft cards visible only in backoffice, then switch to <strong>Published</strong> when you want them to appear automatically for clients in frontoffice.</div>
                </div>
            </div>
        </div>

        <div class="col-xxl-8">
            <div class="row g-4">
                <?php if ($consultations === []): ?>
                    <div class="col-12"><div class="empty-state"><h5 class="mb-2">No consultation cards yet</h5><p class="mb-0">Use the editor to add your first offer and the card grid will populate here instantly.</p></div></div>
                <?php else: ?>
                    <?php foreach ($consultations as $consultation): ?>
                        <?php
                        $status = $statusMap[$consultation['status']] ?? $statusMap['draft'];
                        $editUrl = route_url('backoffice/consultation') . '&edit=' . rawurlencode((string) $consultation['id']) . '#consultation-form';
                        ?>
                        <div class="col-xl-6">
                            <article class="card item-card h-100" style="--accent: <?= htmlspecialchars($status['accent'], ENT_QUOTES, 'UTF-8') ?>;">
                                <div class="card-body p-4 d-flex flex-column">
                                    <div class="d-flex align-items-start justify-content-between gap-3 mb-3">
                                        <div>
                                            <span class="badge rounded-pill <?= htmlspecialchars($status['badge'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($status['label'], ENT_QUOTES, 'UTF-8') ?></span>
                                            <h5 class="mt-3 mb-1"><?= htmlspecialchars((string) $consultation['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                                            <p class="text-muted mb-0"><?= htmlspecialchars((string) $consultation['category'], ENT_QUOTES, 'UTF-8') ?></p>
                                        </div>
                                        <div class="text-end">
                                            <div class="fw-bold text-dark"><?= htmlspecialchars((string) $consultation['price'], ENT_QUOTES, 'UTF-8') ?></div>
                                            <div class="text-muted small"><?= htmlspecialchars((string) $consultation['spots'], ENT_QUOTES, 'UTF-8') ?> visible spots</div>
                                        </div>
                                    </div>

                                    <p class="focus-line mb-3"><?= htmlspecialchars((string) $consultation['focus'], ENT_QUOTES, 'UTF-8') ?></p>
                                    <div class="d-flex flex-wrap gap-2 mb-3">
                                        <span class="chip"><i data-feather="clock"></i><?= htmlspecialchars((string) $consultation['duration'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="chip"><i data-feather="monitor"></i><?= htmlspecialchars((string) $consultation['format'], ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="chip"><i data-feather="target"></i><?= htmlspecialchars((string) $consultation['category'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <p class="desc mb-0"><?= htmlspecialchars((string) $consultation['description'], ENT_QUOTES, 'UTF-8') ?></p>

                                    <div class="card-footer-line">
                                        <div class="updated-label mb-3">Updated <?= htmlspecialchars(date('M d, Y', strtotime((string) $consultation['updatedAt'])), ENT_QUOTES, 'UTF-8') ?></div>
                                        <div class="d-flex flex-wrap gap-2">
                                            <a class="btn btn-primary btn-sm" href="<?= htmlspecialchars($editUrl, ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                            <form method="post" action="<?= htmlspecialchars(route_url('backoffice/consultation'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                                <input type="hidden" name="action" value="toggle-status">
                                                <input type="hidden" name="consultation_id" value="<?= htmlspecialchars((string) $consultation['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                <input type="hidden" name="status" value="<?= htmlspecialchars($consultation['status'] === 'published' ? 'draft' : 'published', ENT_QUOTES, 'UTF-8') ?>">
                                                <button class="btn btn-light btn-sm" type="submit"><?= $consultation['status'] === 'published' ? 'Move to draft' : 'Publish now' ?></button>
                                            </form>
                                            <form method="post" action="<?= htmlspecialchars(route_url('backoffice/consultation'), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                                <input type="hidden" name="action" value="delete-consultation">
                                                <input type="hidden" name="consultation_id" value="<?= htmlspecialchars((string) $consultation['id'], ENT_QUOTES, 'UTF-8') ?>">
                                                <button class="btn btn-outline-danger btn-sm" type="submit">Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
