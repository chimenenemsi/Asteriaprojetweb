<style>
.category-stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin:20px 0}
.category-stat-card{padding:20px;border-radius:20px;background:linear-gradient(180deg,#fff 0%,#f8fbff 100%);border:1px solid #dbe7f3;box-shadow:0 18px 40px rgba(15,23,42,.06)}
.category-stat-card strong{display:block;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#64748b}
.category-stat-card .metric{display:block;font-size:31px;font-weight:800;color:#0f172a;margin-top:8px}
.category-stat-card .meta{display:block;font-size:13px;color:#475569;margin-top:8px;line-height:1.5}
</style>
<?php $categoryFilters = $categoryFilters ?? ['search' => '', 'status' => '', 'sort' => 'created_desc']; ?>
<div id="product-categories-live-panel">
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Product Categories', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'backoffice' ? 'Manage product categories.' : 'Browse product categories.' ?></p>
        </div>
        <div class="actions">
            <?php if ($area === 'backoffice'): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/categories/new'), ENT_QUOTES, 'UTF-8') ?>">New Category</a>
            <?php endif; ?>
            <a data-pdf-preview="true" href="<?= htmlspecialchars(route_url($area . '/categories/pdf', ['search' => (string) $categoryFilters['search'], 'status' => (string) $categoryFilters['status'], 'sort' => (string) $categoryFilters['sort']]), ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
            <a href="<?= htmlspecialchars(route_url($area . '/products'), ENT_QUOTES, 'UTF-8') ?>">Products</a>
        </div>
    </div>
</div>

<?php $categoryStats = $categoryStats ?? ['total_categories' => 0, 'active_categories' => 0, 'inactive_categories' => 0]; ?>
<div class="category-stats-grid">
    <div class="category-stat-card"><strong>Total Categories</strong><span class="metric"><?= htmlspecialchars((string) $categoryStats['total_categories'], ENT_QUOTES, 'UTF-8') ?></span><span class="meta">All matching categories in the catalog.</span></div>
    <div class="category-stat-card"><strong>Active</strong><span class="metric"><?= htmlspecialchars((string) $categoryStats['active_categories'], ENT_QUOTES, 'UTF-8') ?></span><span class="meta">Visible categories ready to receive products.</span></div>
    <div class="category-stat-card"><strong>Inactive</strong><span class="metric"><?= htmlspecialchars((string) $categoryStats['inactive_categories'], ENT_QUOTES, 'UTF-8') ?></span><span class="meta">Paused categories that can be reactivated later.</span></div>
</div>

<div class="card">
    <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" novalidate class="form-shell" style="margin-bottom:20px" data-live-search="true">
        <input type="hidden" name="route" value="<?= htmlspecialchars($area . '/categories', ENT_QUOTES, 'UTF-8') ?>">
        <div class="row">
            <div class="col-5">
                <div class="field">
                    <label for="category-search">Search</label>
                    <input id="category-search" type="text" name="search" placeholder="Search category name or description" value="<?= htmlspecialchars((string) $categoryFilters['search'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-3">
                <div class="field">
                    <label for="category-status">Status</label>
                    <select id="category-status" name="status">
                        <option value="">All statuses</option>
                        <?php foreach (['ACTIVE', 'INACTIVE'] as $status): ?>
                            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $categoryFilters['status'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="field">
                    <label for="category-sort">Sort</label>
                    <select id="category-sort" name="sort">
                        <option value="created_desc" <?= $categoryFilters['sort'] === 'created_desc' ? 'selected' : '' ?>>Newest</option>
                        <option value="name_asc" <?= $categoryFilters['sort'] === 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                        <option value="name_desc" <?= $categoryFilters['sort'] === 'name_desc' ? 'selected' : '' ?>>Name Z-A</option>
                        <option value="status_asc" <?= $categoryFilters['sort'] === 'status_asc' ? 'selected' : '' ?>>Status</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="actions">
            <span class="muted">Filters update automatically while you type or change sort.</span>
            <a data-live-link="true" href="<?= htmlspecialchars(route_url($area . '/categories'), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
        </div>
    </form>

    <?php if (($categories ?? []) === []): ?>
        <p class="muted" style="margin:0">No categories yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= product_status_badge((string) $category['status']) ?></td>
                        <td><?= htmlspecialchars((string) ($category['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars(route_url($area . '/categories/show', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">View</a>
                            <?php if ($area === 'backoffice'): ?>
                                <a href="<?= htmlspecialchars(route_url($area . '/categories/edit', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/categories/delete', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
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
            const close = () => { modal.classList.remove('open'); const frame = modal.querySelector('iframe'); if (frame) frame.src = 'about:blank'; };
            modal.querySelector('.asteria-pdf-backdrop')?.addEventListener('click', close);
            modal.querySelector('.asteria-pdf-close')?.addEventListener('click', close);
            return modal;
        },
        openPdf(url) { const modal = this.ensurePdfModal(); const frame = modal.querySelector('iframe'); if (frame) frame.src = url; modal.classList.add('open'); },
        bindLiveForm(form, load) {
            if (form.dataset.liveBound === 'true') {
                return;
            }

            form.dataset.liveBound = 'true';
            let timer = 0;
            const submit = () => {
                const action = form.getAttribute('action') || location.pathname;
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
            const load = (url) => fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}})
                .then(response => response.text())
                .then(html => {
                    const next = new DOMParser().parseFromString(html, 'text/html').getElementById(panelId);
                    if (!next) { window.location.href = url; return; }
                    panel.replaceWith(next);
                    history.pushState({}, '', url);
                    this.bindPanel(panelId);
                }).catch(() => { window.location.href = url; });
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
document.addEventListener('DOMContentLoaded', () => window.AsteriaLiveUi.bindPanel('product-categories-live-panel'));
</script>
<style>
#asteria-pdf-modal{position:fixed;inset:0;display:none;z-index:9999}.open#asteria-pdf-modal{display:block}
.asteria-pdf-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.6)}
.asteria-pdf-dialog{position:relative;width:min(960px,92vw);height:min(88vh,820px);margin:4vh auto;background:#fff;border-radius:24px;overflow:hidden}
.asteria-pdf-close{position:absolute;top:14px;right:14px;z-index:2;border:0;border-radius:999px;padding:10px 14px;background:#0f172a;color:#fff}
.asteria-pdf-dialog iframe{width:100%;height:100%;border:0}
</style>
