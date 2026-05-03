<div id="category-products-live-panel">
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars((string) ($category['name'] ?? 'Category'), ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= htmlspecialchars((string) ($category['status'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        </div>
        <div class="actions">
            <a href="<?= htmlspecialchars(route_url($area . '/categories'), ENT_QUOTES, 'UTF-8') ?>">Back</a>
            <a data-pdf-preview="true" href="<?= htmlspecialchars(route_url($area . '/categories/products-pdf', ['id' => (int) $category['id'], 'product_search' => (string) ($productFilters['search'] ?? ''), 'product_status' => (string) ($productFilters['status'] ?? ''), 'product_sort' => (string) ($productFilters['sort'] ?? 'created_desc')]), ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
            <?php if ($area === 'backoffice'): ?>
                <a href="<?= htmlspecialchars(route_url($area . '/categories/edit', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/products/new', ['category_id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">Add Product</a>
                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/categories/delete', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                    <button class="btn btn-danger" type="submit">Delete</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($category['description'])): ?>
    <div class="card"><p class="muted" style="margin:0"><?= nl2br(htmlspecialchars((string) $category['description'], ENT_QUOTES, 'UTF-8')) ?></p></div>
<?php endif; ?>

<div class="card">
    <div class="header-line">
        <h2 style="margin:0">Products</h2>
        <a href="<?= htmlspecialchars(route_url($area === 'frontoffice' ? 'frontoffice/products' : ($area . '/orders')), ENT_QUOTES, 'UTF-8') ?>"><?= $area === 'frontoffice' ? 'Open Catalog' : 'Orders' ?></a>
    </div>
    <?php $productFilters = $productFilters ?? ['search' => '', 'status' => '', 'sort' => 'created_desc']; ?>
    <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" novalidate class="form-shell" style="margin:18px 0 20px" data-live-search="true">
        <input type="hidden" name="route" value="<?= htmlspecialchars($area . '/categories/show', ENT_QUOTES, 'UTF-8') ?>">
        <input type="hidden" name="id" value="<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>">
        <div class="row">
            <div class="col-4">
                <div class="field">
                    <label for="category-product-search">Search Products</label>
                    <input id="category-product-search" type="text" name="product_search" placeholder="Search name, SKU, or description" value="<?= htmlspecialchars((string) $productFilters['search'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-4">
                <div class="field">
                    <label for="category-product-status">Status</label>
                    <select id="category-product-status" name="product_status">
                        <option value="">All statuses</option>
                        <?php foreach (['ACTIVE', 'DRAFT', 'OUT_OF_STOCK'] as $status): ?>
                            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $productFilters['status'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-4">
                <div class="field">
                    <label for="category-product-sort">Sort</label>
                    <select id="category-product-sort" name="product_sort">
                        <option value="created_desc" <?= $productFilters['sort'] === 'created_desc' ? 'selected' : '' ?>>Newest</option>
                        <option value="name_asc" <?= $productFilters['sort'] === 'name_asc' ? 'selected' : '' ?>>Name A-Z</option>
                        <option value="name_desc" <?= $productFilters['sort'] === 'name_desc' ? 'selected' : '' ?>>Name Z-A</option>
                        <option value="price_asc" <?= $productFilters['sort'] === 'price_asc' ? 'selected' : '' ?>>Price Low-High</option>
                        <option value="price_desc" <?= $productFilters['sort'] === 'price_desc' ? 'selected' : '' ?>>Price High-Low</option>
                        <option value="stock_desc" <?= $productFilters['sort'] === 'stock_desc' ? 'selected' : '' ?>>Stock High-Low</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="actions">
            <span class="muted">Filters update automatically while you type or change sort.</span>
            <a data-live-link="true" href="<?= htmlspecialchars(route_url($area . '/categories/show', ['id' => (int) $category['id']]), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
        </div>
    </form>
    <?php if (($products ?? []) === []): ?>
        <p class="muted">No products in this category yet.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>SKU</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>DT <?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <?php if ($area === 'backoffice'): ?>
                                <a href="<?= htmlspecialchars(route_url($area . '/products/edit', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                                <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/products/delete', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>" novalidate>
                                    <button class="btn btn-danger" type="submit">Delete</button>
                                </form>
                                <a href="<?= htmlspecialchars(route_url($area . '/orders/new', ['product_id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">New Order</a>
                            <?php else: ?>
                                <a href="<?= htmlspecialchars(route_url('frontoffice/products'), ENT_QUOTES, 'UTF-8') ?>">Order from Catalog</a>
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
            const load = (url) => fetch(url, {headers: {'X-Requested-With': 'XMLHttpRequest'}}).then(response => response.text()).then(html => {
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
document.addEventListener('DOMContentLoaded', () => {
    window.AsteriaLiveUi.bindPanel('category-products-live-panel');
});
</script>
<style>
#asteria-pdf-modal{position:fixed;inset:0;display:none;z-index:9999}.open#asteria-pdf-modal{display:block}
.asteria-pdf-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.6)}
.asteria-pdf-dialog{position:relative;width:min(960px,92vw);height:min(88vh,820px);margin:4vh auto;background:#fff;border-radius:24px;overflow:hidden}
.asteria-pdf-close{position:absolute;top:14px;right:14px;z-index:2;border:0;border-radius:999px;padding:10px 14px;background:#0f172a;color:#fff}
.asteria-pdf-dialog iframe{width:100%;height:100%;border:0}
</style>
