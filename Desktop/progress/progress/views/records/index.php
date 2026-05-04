<?php
$recordFilters = $recordFilters ?? ['search' => '', 'goal_id' => '', 'record_type' => '', 'mood' => '', 'sort' => 'record_date_desc'];
$records = $records ?? [];
$goals = $goals ?? [];

$totalRecords = count($records);
$moodCounts = ['LOW' => 0, 'STEADY' => 0, 'HIGH' => 0];
$typeCounts = [];
$adherenceTotal = 0;
$adherenceCount = 0;
$milestoneCount = 0;
$reportCount = 0;

foreach ($records as $record) {
    $mood = (string) $record->getMood();
    $type = (string) $record->getRecordType();
    $moodCounts[$mood] = ($moodCounts[$mood] ?? 0) + 1;
    $typeCounts[$type] = ($typeCounts[$type] ?? 0) + 1;

    if ($type === 'MILESTONE') {
        $milestoneCount++;
    }
    if ($type === 'REPORT') {
        $reportCount++;
    }

    if ($record->getAdherenceScore() !== null) {
        $adherenceTotal += (int) $record->getAdherenceScore();
        $adherenceCount++;
    }
}

$averageAdherence = $adherenceCount > 0 ? round($adherenceTotal / $adherenceCount, 1) : 0;
arsort($typeCounts);
$topRecordType = $typeCounts === [] ? 'None yet' : (string) array_key_first($typeCounts);
?>
<style>
.records-dashboard-shell{display:grid;gap:20px}
.records-hero{padding:28px;border-radius:28px;background:radial-gradient(circle at top right,#fef9c3 0%,#ecfdf5 35%,#ffffff 75%);border:1px solid #c8e0be;box-shadow:0 22px 60px rgba(15,23,42,.08)}
.records-hero h1{margin:0;color:#153122}
.records-hero-copy{max-width:760px;color:#60706a;line-height:1.7;margin-top:10px}
.records-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.records-stat-card{padding:20px;border-radius:22px;background:linear-gradient(180deg,#ffffff 0%,#f7fbf4 100%);border:1px solid #d4e8cc;box-shadow:0 16px 40px rgba(15,23,42,.06)}
.records-stat-card strong{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#60706a}
.records-stat-card span{display:block;font-size:32px;font-weight:800;color:#153122;margin-top:8px}
.records-stat-card small{display:block;margin-top:8px;color:#60706a;line-height:1.5}
.records-filters{padding:22px;border-radius:24px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 16px 36px rgba(15,23,42,.05)}
.records-chart-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.records-chart-card{padding:22px;border-radius:24px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 16px 36px rgba(15,23,42,.05)}
.records-chart-card h2{margin:0;color:#153122;font-size:20px}
.records-chart-card p{margin:10px 0 0;color:#60706a;line-height:1.6}
.records-chart-wrap{position:relative;height:300px;margin-top:18px}
.record-list-card{padding:0;border-radius:24px;overflow:hidden;border:1px solid #e2e8f0;background:#fff;box-shadow:0 16px 36px rgba(15,23,42,.05)}
.record-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;padding:22px}
.record-card{padding:22px;border-radius:22px;background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);border:1px solid #e2e8f0;display:grid;gap:14px}
.record-card-top{display:flex;justify-content:space-between;gap:16px;align-items:flex-start}
.record-card h3{margin:0;color:#153122;font-size:20px;line-height:1.3}
.record-card p{margin:0;color:#60706a;line-height:1.6}
.record-chip{display:inline-flex;align-items:center;padding:7px 11px;border-radius:999px;background:rgba(108,161,56,.12);color:#3a6b2a;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em}
.record-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.record-meta-item{padding:12px 14px;border-radius:16px;background:#f8fafc;border:1px solid #e2e8f0}
.record-meta-item strong{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#60706a}
.record-meta-item span{display:block;margin-top:6px;font-weight:700;color:#153122}
.record-card .actions{display:flex;gap:10px;flex-wrap:wrap}
.record-empty{padding:28px;color:#60706a}
@media (max-width:1100px){.records-stat-grid,.records-chart-grid,.record-grid{grid-template-columns:1fr 1fr}}
@media (max-width:800px){.records-stat-grid,.records-chart-grid,.record-grid{grid-template-columns:1fr}}
.progress-ai-inline-card{display:flex;align-items:center;justify-content:space-between;gap:18px;padding:20px 22px;border-radius:24px;background:linear-gradient(135deg,#1b2115,#2d4a1e);color:#fff;box-shadow:0 22px 60px rgba(15,23,42,.12);margin-bottom:20px}.progress-ai-inline-card strong{display:block;font-size:20px;margin-bottom:6px}.progress-ai-inline-card span{display:block;color:rgba(255,255,255,.75);line-height:1.6}.progress-ai-inline-card .btn{background:#6ca138;color:#fff;white-space:nowrap}@media(max-width:800px){.progress-ai-inline-card{flex-direction:column;align-items:flex-start}}
</style>

<div id="records-live-panel" class="records-dashboard-shell">
    <div class="records-hero">
        <div class="header-line">
            <div>
                <h1><?= htmlspecialchars($pageTitle ?? 'Progress Records', ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="records-hero-copy">This view is now a record dashboard instead of a plain table: quick stat cards, pie charts for mood and record types, and cleaner record cards that are easier to read at a glance.</p>
            </div>
            <div class="actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/records/new'), ENT_QUOTES, 'UTF-8') ?>">New Record</a>
                <a href="<?= htmlspecialchars(route_url($area . '/goals'), ENT_QUOTES, 'UTF-8') ?>">Goals</a>
                <a class="btn btn-secondary" data-pdf-preview="true" href="<?= htmlspecialchars(route_url($area . '/records/pdf', $recordFilters), ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
            </div>
        </div>
    </div>

    <div class="records-stat-grid">
        <div class="records-stat-card">
            <strong>Total Records</strong>
            <span><?= htmlspecialchars((string) $totalRecords, ENT_QUOTES, 'UTF-8') ?></span>
            <small>All records in the current filtered view.</small>
        </div>
        <div class="records-stat-card">
            <strong>Average Adherence</strong>
            <span><?= htmlspecialchars(number_format($averageAdherence, 1), ENT_QUOTES, 'UTF-8') ?>%</span>
            <small>Average adherence score across records that have one.</small>
        </div>
        <div class="records-stat-card">
            <strong>Milestones</strong>
            <span><?= htmlspecialchars((string) $milestoneCount, ENT_QUOTES, 'UTF-8') ?></span>
            <small>Major progress jumps captured as milestones.</small>
        </div>
        <div class="records-stat-card">
            <strong>Top Record Type</strong>
            <span style="font-size:20px"><?= htmlspecialchars($topRecordType, ENT_QUOTES, 'UTF-8') ?></span>
            <small>The most common record type in this filtered set.</small>
        </div>
    </div>

    <div class="records-filters">
        <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" novalidate class="form-shell" data-live-search="true">
            <input type="hidden" name="route" value="<?= htmlspecialchars($area . '/records', ENT_QUOTES, 'UTF-8') ?>">
            <div class="row">
                <div class="col-3">
                    <div class="field">
                        <label for="record-search">Search</label>
                        <input id="record-search" type="text" name="search" placeholder="Search goal title or notes" value="<?= htmlspecialchars((string) $recordFilters['search'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="goal-filter">Goal</label>
                        <select id="goal-filter" name="goal_id">
                            <option value="">All goals</option>
                            <?php foreach ($goals as $goal): ?>
                                <?php $goalId = (int) ($goal->getId() ?? 0); ?>
                                <option value="<?= htmlspecialchars((string) $goalId, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $recordFilters['goal_id'] === (string) $goalId ? 'selected' : '' ?>><?= htmlspecialchars($goal->getTitle(), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="record-type-filter">Type</label>
                        <select id="record-type-filter" name="record_type">
                            <option value="">All types</option>
                            <?php foreach (['CHECKPOINT','MILESTONE','MEASUREMENT','NOTE','REPORT'] as $type): ?>
                                <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>" <?= $recordFilters['record_type'] === $type ? 'selected' : '' ?>><?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="record-mood-filter">Mood</label>
                        <select id="record-mood-filter" name="mood">
                            <option value="">All moods</option>
                            <?php foreach (['LOW','STEADY','HIGH'] as $mood): ?>
                                <option value="<?= htmlspecialchars($mood, ENT_QUOTES, 'UTF-8') ?>" <?= $recordFilters['mood'] === $mood ? 'selected' : '' ?>><?= htmlspecialchars($mood, ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="record-sort-filter">Sort</label>
                        <select id="record-sort-filter" name="sort">
                            <option value="record_date_desc" <?= $recordFilters['sort'] === 'record_date_desc' ? 'selected' : '' ?>>Newest Date</option>
                            <option value="record_date_asc" <?= $recordFilters['sort'] === 'record_date_asc' ? 'selected' : '' ?>>Oldest Date</option>
                            <option value="value_desc" <?= $recordFilters['sort'] === 'value_desc' ? 'selected' : '' ?>>Highest Value</option>
                            <option value="value_asc" <?= $recordFilters['sort'] === 'value_asc' ? 'selected' : '' ?>>Lowest Value</option>
                            <option value="adherence_desc" <?= $recordFilters['sort'] === 'adherence_desc' ? 'selected' : '' ?>>Best Adherence</option>
                            <option value="adherence_asc" <?= $recordFilters['sort'] === 'adherence_asc' ? 'selected' : '' ?>>Lowest Adherence</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="actions">
                <span class="muted">Filters update automatically while you type or change sort.</span>
                <button class="btn btn-primary" type="submit">Search records</button>
                <a class="btn btn-secondary" data-live-link="true" href="<?= htmlspecialchars(route_url($area . '/records'), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if ($records !== []): ?>
        <div class="records-chart-grid">
            <div class="records-chart-card">
                <h2>Mood Distribution</h2>
                <p>Pie chart showing how the selected records are distributed across low, steady, and high moods.</p>
                <div class="records-chart-wrap">
                    <canvas id="record-mood-chart"></canvas>
                </div>
            </div>
            <div class="records-chart-card">
                <h2>Record Type Distribution</h2>
                <p>Pie chart showing whether this filtered view is mostly checkpoints, milestones, reports, or notes.</p>
                <div class="records-chart-wrap">
                    <canvas id="record-type-chart"></canvas>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="record-list-card">
        <?php if ($records === []): ?>
            <div class="record-empty">No records match the current filters yet.</div>
        <?php else: ?>
            <div class="record-grid">
                <?php foreach ($records as $record): ?>
                    <?php $recordId = (int) ($record->getId() ?? 0); ?>
                    <article class="record-card">
                        <div class="record-card-top">
                            <div>
                                <h3><?= htmlspecialchars((string) ($record->getGoalTitle() ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars($record->getRecordDate(), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <span class="record-chip"><?= htmlspecialchars($record->getRecordType(), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="record-meta">
                            <div class="record-meta-item">
                                <strong>Recorded Value</strong>
                                <span><?= htmlspecialchars(number_format($record->getRecordedValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars((string) ($record->getGoalUnit() ?? ''), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="record-meta-item">
                                <strong>Mood</strong>
                                <span><?= htmlspecialchars($record->getMood(), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="record-meta-item">
                                <strong>Adherence</strong>
                                <span><?= htmlspecialchars($record->getAdherenceScore() === null ? '-' : ((string) $record->getAdherenceScore() . '%'), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="record-meta-item">
                                <strong>Goal Link</strong>
                                <span><a href="<?= htmlspecialchars(route_url($area . '/goals/show', ['id' => (int) ($record->getProgressGoalId() ?? 0)]), ENT_QUOTES, 'UTF-8') ?>">Open goal</a></span>
                            </div>
                        </div>
                        <?php if (($record->getNotes() ?? '') !== null && trim((string) $record->getNotes()) !== ''): ?>
                            <p><?= htmlspecialchars((string) $record->getNotes(), ENT_QUOTES, 'UTF-8') ?></p>
                        <?php endif; ?>
                        <div class="actions">
                            <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars(route_url($area . '/records/edit', ['id' => $recordId]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/records/delete', ['id' => $recordId]), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                            </form>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
if (!window.AsteriaLiveUi) {
    window.AsteriaLiveUi = {
        liveDelayMs: 250,
        ensurePdfModal() {
            let modal = document.getElementById('asteria-pdf-modal');
            if (modal) return modal;
            modal = document.createElement('div');
            modal.id = 'asteria-pdf-modal';
            modal.innerHTML = '<div class="asteria-pdf-backdrop"></div><div class="asteria-pdf-dialog"><button type="button" class="asteria-pdf-close" aria-label="Close PDF preview">Close</button><iframe title="PDF preview"></iframe></div>';
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
            const submit = () => {
                const action = form.getAttribute('action') || window.location.pathname;
                const params = new URLSearchParams(new FormData(form)).toString();
                load(params ? action + '?' + params : action);
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
            // Select filters no longer auto-refresh. Use Enter in search or the Search/Apply button.
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
                        if (typeof window.initRecordsDashboard === 'function') {
                            window.initRecordsDashboard(true);
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

window.initRecordsDashboard = function initRecordsDashboard(fromRefresh) {
    if (window.Chart) {
        const moodCanvas = document.getElementById('record-mood-chart');
        const typeCanvas = document.getElementById('record-type-chart');
        const moodData = <?= json_encode([
            'labels' => array_keys($moodCounts),
            'values' => array_values($moodCounts),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const typeData = <?= json_encode([
            'labels' => array_keys($typeCounts),
            'values' => array_values($typeCounts),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {legend: {position: 'bottom', labels: {padding: 18, usePointStyle: true}}}
        };

        if (moodCanvas) {
            new Chart(moodCanvas, {
                type: 'pie',
                data: {labels: moodData.labels, datasets: [{data: moodData.values, backgroundColor: ['#ef4444', '#3b82f6', '#22c55e'], borderColor: '#ffffff', borderWidth: 3}]},
                options: baseOptions
            });
        }
        if (typeCanvas) {
            new Chart(typeCanvas, {
                type: 'pie',
                data: {labels: typeData.labels, datasets: [{data: typeData.values, backgroundColor: ['#14b8a6', '#8b5cf6', '#2563eb', '#f59e0b', '#ec4899'], borderColor: '#ffffff', borderWidth: 3}]},
                options: baseOptions
            });
        }
    }

    if (!fromRefresh) {
        window.AsteriaLiveUi.bindPanel('records-live-panel');
    }
};

document.addEventListener('DOMContentLoaded', () => window.initRecordsDashboard(false));
</script>
<style>
.is-loading{opacity:.65;pointer-events:none;transition:opacity .2s ease}
#asteria-pdf-modal{position:fixed;inset:0;display:none;z-index:9999}
#asteria-pdf-modal.open{display:block}
.asteria-pdf-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.6)}
.asteria-pdf-dialog{position:relative;width:min(960px,92vw);height:min(88vh,820px);margin:4vh auto;background:#fff;border-radius:24px;overflow:hidden;box-shadow:0 30px 80px rgba(15,23,42,.25)}
.asteria-pdf-close{position:absolute;top:14px;right:14px;z-index:2;border:0;border-radius:999px;padding:10px 14px;background:#0f172a;color:#fff;cursor:pointer}
.asteria-pdf-dialog iframe{width:100%;height:100%;border:0;background:#fff}
</style>
