<?php
$goalFilters = $goalFilters ?? ['search' => '', 'goal_type' => '', 'status' => '', 'sort' => 'target_date_asc'];
$goals = $goals ?? [];

$totalGoals = count($goals);
$activeGoals = 0;
$completedGoals = 0;
$onHoldGoals = 0;
$goalTypeCounts = [];
$goalStatusCounts = ['ACTIVE' => 0, 'COMPLETED' => 0, 'ON_HOLD' => 0];

foreach ($goals as $goal) {
    $status = (string) $goal->getStatus();
    $type = (string) $goal->getGoalType();
    $goalStatusCounts[$status] = ($goalStatusCounts[$status] ?? 0) + 1;
    $goalTypeCounts[$type] = ($goalTypeCounts[$type] ?? 0) + 1;

    if ($status === 'ACTIVE') {
        $activeGoals++;
    } elseif ($status === 'COMPLETED') {
        $completedGoals++;
    } elseif ($status === 'ON_HOLD') {
        $onHoldGoals++;
    }
}

arsort($goalTypeCounts);
$mostCommonGoalType = $goalTypeCounts === [] ? 'None yet' : str_replace('_', ' ', (string) array_key_first($goalTypeCounts));
?>
<style>
.progress-dashboard-shell{display:grid;gap:20px}
.progress-hero{padding:28px;border-radius:28px;background:radial-gradient(circle at top right,#dcfce7 0%,#e0f2fe 35%,#ffffff 75%);border:1px solid #d7e8f6;box-shadow:0 22px 60px rgba(15,23,42,.08)}
.progress-hero h1{margin:0;color:#0f172a}
.progress-hero-copy{max-width:760px;color:#475569;line-height:1.7;margin-top:10px}
.progress-stat-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.progress-stat-card{padding:20px;border-radius:22px;background:linear-gradient(180deg,#ffffff 0%,#f8fbff 100%);border:1px solid #dbe7f3;box-shadow:0 16px 40px rgba(15,23,42,.06)}
.progress-stat-card strong{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#64748b}
.progress-stat-card span{display:block;font-size:32px;font-weight:800;color:#0f172a;margin-top:8px}
.progress-stat-card small{display:block;margin-top:8px;color:#475569;line-height:1.5}
.progress-filters{padding:22px;border-radius:24px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 16px 36px rgba(15,23,42,.05)}
.progress-chart-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}
.progress-chart-card{padding:22px;border-radius:24px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 16px 36px rgba(15,23,42,.05)}
.progress-chart-card h2{margin:0;color:#0f172a;font-size:20px}
.progress-chart-card p{margin:10px 0 0;color:#64748b;line-height:1.6}
.progress-chart-wrap{position:relative;height:300px;margin-top:18px}
.goal-list-card{padding:0;border-radius:24px;overflow:hidden;border:1px solid #e2e8f0;background:#fff;box-shadow:0 16px 36px rgba(15,23,42,.05)}
.goal-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;padding:22px}
.goal-card{padding:22px;border-radius:22px;background:linear-gradient(180deg,#ffffff 0%,#f8fafc 100%);border:1px solid #e2e8f0;display:grid;gap:14px}
.goal-card-top{display:flex;justify-content:space-between;gap:16px;align-items:flex-start}
.goal-card h3{margin:0;color:#0f172a;font-size:20px;line-height:1.3}
.goal-card p{margin:0;color:#475569;line-height:1.6}
.goal-chip{display:inline-flex;align-items:center;padding:7px 11px;border-radius:999px;background:#eff6ff;color:#1d4ed8;font-size:12px;font-weight:800;text-transform:uppercase;letter-spacing:.06em}
.goal-meta{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
.goal-meta-item{padding:12px 14px;border-radius:16px;background:#f8fafc;border:1px solid #e2e8f0}
.goal-meta-item strong{display:block;font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#64748b}
.goal-meta-item span{display:block;margin-top:6px;font-weight:700;color:#0f172a}
.goal-card .actions{display:flex;gap:10px;flex-wrap:wrap}
.goal-empty{padding:28px;color:#64748b}
@media (max-width:1100px){.progress-stat-grid,.progress-chart-grid,.goal-grid{grid-template-columns:1fr 1fr}}
@media (max-width:800px){.progress-stat-grid,.progress-chart-grid,.goal-grid{grid-template-columns:1fr}}
</style>

<div id="goals-live-panel" class="progress-dashboard-shell">
    <div class="progress-hero">
        <div class="header-line">
            <div>
                <h1><?= htmlspecialchars($pageTitle ?? 'Progress Goals', ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="progress-hero-copy">This dashboard gives your goals a cleaner overview: quick status counts, pie charts for distribution, and goal cards that are easier to scan than the older table layout.</p>
            </div>
            <div class="actions">
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/goals/new'), ENT_QUOTES, 'UTF-8') ?>">New Goal</a>
                <a class="btn btn-secondary" data-pdf-preview="true" href="<?= htmlspecialchars(route_url($area . '/goals/pdf', $goalFilters), ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
            </div>
        </div>
    </div>

    <div class="progress-stat-grid">
        <div class="progress-stat-card">
            <strong>Total Goals</strong>
            <span><?= htmlspecialchars((string) $totalGoals, ENT_QUOTES, 'UTF-8') ?></span>
            <small>All goals in the current filtered view.</small>
        </div>
        <div class="progress-stat-card">
            <strong>Active</strong>
            <span><?= htmlspecialchars((string) $activeGoals, ENT_QUOTES, 'UTF-8') ?></span>
            <small>Goals that are currently being worked on.</small>
        </div>
        <div class="progress-stat-card">
            <strong>Completed</strong>
            <span><?= htmlspecialchars((string) $completedGoals, ENT_QUOTES, 'UTF-8') ?></span>
            <small>Goals already finished inside the filtered set.</small>
        </div>
        <div class="progress-stat-card">
            <strong>Top Goal Type</strong>
            <span style="font-size:20px"><?= htmlspecialchars($mostCommonGoalType, ENT_QUOTES, 'UTF-8') ?></span>
            <small>The most common goal type in the current view.</small>
        </div>
    </div>

    <div class="progress-filters">
        <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" class="form-shell" data-live-search="true">
            <input type="hidden" name="route" value="<?= htmlspecialchars($area . '/goals', ENT_QUOTES, 'UTF-8') ?>">
            <div class="row">
                <div class="col-3">
                    <div class="field">
                        <label for="goal-search">Search</label>
                        <input id="goal-search" type="text" name="search" placeholder="Search title, metric, or description" value="<?= htmlspecialchars((string) $goalFilters['search'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="goal-type-filter">Goal Type</label>
                        <select id="goal-type-filter" name="goal_type">
                            <option value="">All goal types</option>
                            <?php foreach (['WEIGHT_LOSS','FITNESS','CAREER','FINANCE','LEARNING','HEALTH','PRODUCTIVITY','OTHER'] as $type): ?>
                                <option value="<?= htmlspecialchars($type, ENT_QUOTES, 'UTF-8') ?>" <?= $goalFilters['goal_type'] === $type ? 'selected' : '' ?>><?= htmlspecialchars(str_replace('_', ' ', $type), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="goal-status-filter">Status</label>
                        <select id="goal-status-filter" name="status">
                            <option value="">All statuses</option>
                            <?php foreach (['ACTIVE','COMPLETED','ON_HOLD'] as $status): ?>
                                <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= $goalFilters['status'] === $status ? 'selected' : '' ?>><?= htmlspecialchars(str_replace('_', ' ', $status), ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-3">
                    <div class="field">
                        <label for="goal-sort-filter">Sort</label>
                        <select id="goal-sort-filter" name="sort">
                            <option value="target_date_asc" <?= $goalFilters['sort'] === 'target_date_asc' ? 'selected' : '' ?>>Target Date Asc</option>
                            <option value="target_date_desc" <?= $goalFilters['sort'] === 'target_date_desc' ? 'selected' : '' ?>>Target Date Desc</option>
                            <option value="title_asc" <?= $goalFilters['sort'] === 'title_asc' ? 'selected' : '' ?>>Title A-Z</option>
                            <option value="title_desc" <?= $goalFilters['sort'] === 'title_desc' ? 'selected' : '' ?>>Title Z-A</option>
                            <option value="created_desc" <?= $goalFilters['sort'] === 'created_desc' ? 'selected' : '' ?>>Newest</option>
                            <option value="created_asc" <?= $goalFilters['sort'] === 'created_asc' ? 'selected' : '' ?>>Oldest</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="actions">
                <span class="muted">Filters update automatically while you type or change sort.</span>
                <a class="btn btn-secondary" data-live-link="true" href="<?= htmlspecialchars(route_url($area . '/goals'), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
            </div>
        </form>
    </div>

    <?php if ($goals !== []): ?>
        <div class="progress-chart-grid">
            <div class="progress-chart-card">
                <h2>Goal Status Mix</h2>
                <p>Pie chart showing how your filtered goals are split between active, completed, and on hold.</p>
                <div class="progress-chart-wrap">
                    <canvas id="goal-status-chart"></canvas>
                </div>
            </div>
            <div class="progress-chart-card">
                <h2>Goal Type Mix</h2>
                <p>Pie chart showing what kinds of goals currently dominate this view.</p>
                <div class="progress-chart-wrap">
                    <canvas id="goal-type-chart"></canvas>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="goal-list-card">
        <?php if ($goals === []): ?>
            <div class="goal-empty">No goals match the current filters yet.</div>
        <?php else: ?>
            <div class="goal-grid">
                <?php foreach ($goals as $goal): ?>
                    <?php $goalId = (int) ($goal->getId() ?? 0); ?>
                    <article class="goal-card">
                        <div class="goal-card-top">
                            <div>
                                <h3><?= htmlspecialchars($goal->getTitle(), ENT_QUOTES, 'UTF-8') ?></h3>
                                <p><?= htmlspecialchars($goal->getMetric(), ENT_QUOTES, 'UTF-8') ?></p>
                            </div>
                            <span class="goal-chip"><?= htmlspecialchars(str_replace('_', ' ', $goal->getStatus()), ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="goal-meta">
                            <div class="goal-meta-item">
                                <strong>Target</strong>
                                <span><?= htmlspecialchars(number_format($goal->getTargetValue(), 2), ENT_QUOTES, 'UTF-8') ?> <?= htmlspecialchars($goal->getUnit(), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="goal-meta-item">
                                <strong>Type</strong>
                                <span><?= htmlspecialchars(str_replace('_', ' ', $goal->getGoalType()), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="goal-meta-item">
                                <strong>Start Date</strong>
                                <span><?= htmlspecialchars($goal->getStartDate(), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                            <div class="goal-meta-item">
                                <strong>Target Date</strong>
                                <span><?= htmlspecialchars($goal->getTargetDate(), ENT_QUOTES, 'UTF-8') ?></span>
                            </div>
                        </div>
                        <div class="actions">
                            <a href="<?= htmlspecialchars(route_url($area . '/goals/show', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>">View</a>
                            <a class="btn btn-sm btn-secondary" href="<?= htmlspecialchars(route_url($area . '/goals/edit', ['id' => $goalId]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
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
            if (modal) {
                return modal;
            }

            modal = document.createElement('div');
            modal.id = 'asteria-pdf-modal';
            modal.innerHTML = '<div class="asteria-pdf-backdrop"></div><div class="asteria-pdf-dialog"><button type="button" class="asteria-pdf-close" aria-label="Close PDF preview">Close</button><iframe title="PDF preview"></iframe></div>';
            document.body.appendChild(modal);

            const close = () => {
                modal.classList.remove('open');
                const frame = modal.querySelector('iframe');
                if (frame) {
                    frame.src = 'about:blank';
                }
            };

            modal.querySelector('.asteria-pdf-backdrop')?.addEventListener('click', close);
            modal.querySelector('.asteria-pdf-close')?.addEventListener('click', close);
            return modal;
        },
        openPdf(url) {
            const modal = this.ensurePdfModal();
            const frame = modal.querySelector('iframe');
            if (frame) {
                frame.src = url;
            }
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
                const params = new URLSearchParams(new FormData(form)).toString();
                load(params ? action + '?' + params : action);
            };
            const schedule = () => {
                window.clearTimeout(timer);
                timer = window.setTimeout(submit, this.liveDelayMs);
            };

            form.addEventListener('submit', (event) => {
                event.preventDefault();
                submit();
            });

            form.querySelectorAll('input[type="text"], input[type="search"], input:not([type])').forEach((field) => {
                field.addEventListener('input', schedule);
            });

            form.querySelectorAll('select').forEach((field) => {
                field.addEventListener('change', submit);
            });
        },
        bindPanel(panelId) {
            const panel = document.getElementById(panelId);
            if (!panel) {
                return;
            }

            const load = (url) => {
                panel.classList.add('is-loading');
                fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        const next = doc.getElementById(panelId);
                        if (!next) {
                            window.location.href = url;
                            return;
                        }

                        panel.replaceWith(next);
                        window.history.pushState({}, '', url);
                        this.bindPanel(panelId);
                        if (typeof window.initGoalsDashboard === 'function') {
                            window.initGoalsDashboard(true);
                        }
                    })
                    .catch(() => {
                        window.location.href = url;
                    });
            };

            panel.querySelectorAll('form[data-live-search]').forEach((form) => this.bindLiveForm(form, load));

            panel.querySelectorAll('a[data-live-link]').forEach((link) => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    load(link.href);
                });
            });

            panel.querySelectorAll('a[data-pdf-preview]').forEach((link) => {
                link.addEventListener('click', (event) => {
                    event.preventDefault();
                    this.openPdf(link.href);
                });
            });
        }
    };
}

window.initGoalsDashboard = function initGoalsDashboard(fromRefresh) {
    if (window.Chart) {
        const statusCanvas = document.getElementById('goal-status-chart');
        const typeCanvas = document.getElementById('goal-type-chart');
        const statusData = <?= json_encode([
            'labels' => array_map(static fn(string $item): string => str_replace('_', ' ', $item), array_keys($goalStatusCounts)),
            'values' => array_values($goalStatusCounts),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
        const typeData = <?= json_encode([
            'labels' => array_map(static fn(string $item): string => str_replace('_', ' ', $item), array_keys($goalTypeCounts)),
            'values' => array_values($goalTypeCounts),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        const baseOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {padding: 18, usePointStyle: true}
                }
            }
        };

        if (statusCanvas) {
            new Chart(statusCanvas, {
                type: 'pie',
                data: {labels: statusData.labels, datasets: [{data: statusData.values, backgroundColor: ['#16a34a', '#2563eb', '#f59e0b'], borderColor: '#ffffff', borderWidth: 3}]},
                options: baseOptions
            });
        }

        if (typeCanvas) {
            new Chart(typeCanvas, {
                type: 'pie',
                data: {labels: typeData.labels, datasets: [{data: typeData.values, backgroundColor: ['#14b8a6', '#3b82f6', '#8b5cf6', '#ec4899', '#f97316', '#22c55e', '#eab308', '#64748b'], borderColor: '#ffffff', borderWidth: 3}]},
                options: baseOptions
            });
        }
    }

    if (!fromRefresh) {
        window.AsteriaLiveUi.bindPanel('goals-live-panel');
    }
};

document.addEventListener('DOMContentLoaded', () => window.initGoalsDashboard(false));
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
