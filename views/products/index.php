<?php
$productStats = $productStats ?? [];
$topProducts = $topProducts ?? [];
$productFilters = $productFilters ?? ['search' => '', 'category_id' => '', 'status' => '', 'stock' => '', 'sort' => 'created_desc'];
$categories = $categories ?? [];
?>
<style>
.product-analytics-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;margin-bottom:20px}
.product-analytics-card{padding:20px;border-radius:20px;background:linear-gradient(180deg,#fff 0%,#f8fbff 100%);border:1px solid #dbe7f3;box-shadow:0 18px 40px rgba(15,23,42,.06)}
.product-analytics-card strong{display:block;font-size:12px;letter-spacing:.08em;text-transform:uppercase;color:#64748b}
.product-analytics-card .metric{display:block;font-size:31px;font-weight:800;color:#0f172a;margin-top:8px}
.product-analytics-card .meta{display:block;font-size:13px;color:#475569;margin-top:8px;line-height:1.5}
.product-dashboard-grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-bottom:20px}
.product-chart-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-bottom:20px}
.product-chart-card{padding:22px;border-radius:24px;background:linear-gradient(180deg,#fff 0%,#f8fbff 100%);border:1px solid #dbe7f3;box-shadow:0 18px 40px rgba(15,23,42,.06)}
.product-chart-card h3{margin:0;color:#0f172a}
.product-chart-card p{margin:10px 0 0;color:#64748b;line-height:1.6}
.product-chart-wrap{position:relative;height:320px;margin-top:18px}
.sales-hero{padding:24px;border-radius:24px;background:radial-gradient(circle at top right,#dbeafe 0%,#eff6ff 30%,#fff 75%);border:1px solid #c7d8ef}
.sales-hero h2{margin:0;color:#0f172a}
.sales-hero .summary{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-top:18px}
.sales-hero .summary-item{padding:16px;border-radius:18px;background:rgba(255,255,255,.9);border:1px solid #dbeafe}
.sales-hero .summary-item strong{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.08em;color:#64748b}
.sales-hero .summary-item span{display:block;font-size:26px;font-weight:800;color:#0f172a;margin-top:6px}
.health-list{display:grid;gap:12px}
.health-item{padding:16px 18px;border-radius:18px;background:#fff;border:1px solid #e2e8f0}
.health-item strong{display:block;color:#0f172a}
.health-item span{display:block;color:#475569;margin-top:6px}
@media (max-width:960px){.product-dashboard-grid,.product-chart-grid{grid-template-columns:1fr}.sales-hero .summary{grid-template-columns:1fr}}
</style>
<div id="products-live-panel">
<div class="card">
    <div class="header-line">
        <div>
            <h1 style="margin:0"><?= htmlspecialchars($pageTitle ?? 'Products', ENT_QUOTES, 'UTF-8') ?></h1>
            <p class="muted"><?= $area === 'backoffice' ? 'Manage products.' : 'Browse products and place orders directly from the catalog.' ?></p>
        </div>
        <div class="actions">
            <?php if ($area === 'backoffice'): ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url($area . '/products/new'), ENT_QUOTES, 'UTF-8') ?>">New Product</a>
                <a data-pdf-preview="true" href="<?= htmlspecialchars(route_url($area . '/products/pdf', ['search' => (string) $productFilters['search'], 'category_id' => (string) $productFilters['category_id'], 'status' => (string) $productFilters['status'], 'stock' => (string) $productFilters['stock'], 'sort' => (string) $productFilters['sort']]), ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
                <a href="<?= htmlspecialchars(route_url($area . '/categories'), ENT_QUOTES, 'UTF-8') ?>">Categories</a>
                <a href="<?= htmlspecialchars(route_url($area . '/orders'), ENT_QUOTES, 'UTF-8') ?>">Orders</a>
            <?php else: ?>
                <a class="btn btn-primary" href="<?= htmlspecialchars(route_url('frontoffice/orders'), ENT_QUOTES, 'UTF-8') ?>">Open Orders</a>
                <a data-pdf-preview="true" href="<?= htmlspecialchars(route_url($area . '/products/pdf', ['search' => (string) $productFilters['search'], 'category_id' => (string) $productFilters['category_id'], 'status' => (string) $productFilters['status'], 'stock' => (string) $productFilters['stock'], 'sort' => (string) $productFilters['sort']]), ENT_QUOTES, 'UTF-8') ?>">Open PDF</a>
                <a href="<?= htmlspecialchars(route_url('frontoffice/categories'), ENT_QUOTES, 'UTF-8') ?>">Categories</a>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom:20px">
    <form method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" class="form-shell" data-live-search="true">
        <input type="hidden" name="route" value="<?= htmlspecialchars($area . '/products', ENT_QUOTES, 'UTF-8') ?>">
        <div class="row">
            <div class="col-3">
                <div class="field">
                    <label for="product-search">Search</label>
                    <input id="product-search" type="text" name="search" placeholder="Search name, SKU, or description" value="<?= htmlspecialchars((string) $productFilters['search'], ENT_QUOTES, 'UTF-8') ?>">
                </div>
            </div>
            <div class="col-3">
                <div class="field">
                    <label for="product-category">Category</label>
                    <select id="product-category" name="category_id">
                        <option value="">All categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?= htmlspecialchars((string) $category['id'], ENT_QUOTES, 'UTF-8') ?>" <?= (string) $productFilters['category_id'] === (string) $category['id'] ? 'selected' : '' ?>><?= htmlspecialchars((string) $category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="field">
                    <label for="product-status">Status</label>
                    <select id="product-status" name="status">
                        <option value="">All statuses</option>
                        <?php foreach (['ACTIVE', 'DRAFT', 'OUT_OF_STOCK'] as $status): ?>
                            <option value="<?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?>" <?= (string) $productFilters['status'] === $status ? 'selected' : '' ?>><?= htmlspecialchars($status, ENT_QUOTES, 'UTF-8') ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-2">
                <div class="field">
                    <label for="product-stock">Stock</label>
                    <select id="product-stock" name="stock">
                        <option value="">Any</option>
                        <option value="in_stock" <?= $productFilters['stock'] === 'in_stock' ? 'selected' : '' ?>>In Stock</option>
                        <option value="low_stock" <?= $productFilters['stock'] === 'low_stock' ? 'selected' : '' ?>>Low Stock</option>
                        <option value="out_of_stock" <?= $productFilters['stock'] === 'out_of_stock' ? 'selected' : '' ?>>Out of Stock</option>
                    </select>
                </div>
            </div>
            <div class="col-1">
                <div class="field">
                    <label for="product-sort">Sort</label>
                    <select id="product-sort" name="sort">
                        <option value="created_desc" <?= $productFilters['sort'] === 'created_desc' ? 'selected' : '' ?>>New</option>
                        <option value="name_asc" <?= $productFilters['sort'] === 'name_asc' ? 'selected' : '' ?>>A-Z</option>
                        <option value="price_asc" <?= $productFilters['sort'] === 'price_asc' ? 'selected' : '' ?>>$+</option>
                        <option value="price_desc" <?= $productFilters['sort'] === 'price_desc' ? 'selected' : '' ?>>$-</option>
                        <option value="stock_desc" <?= $productFilters['sort'] === 'stock_desc' ? 'selected' : '' ?>>Stock</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="actions">
            <span class="muted">Filters update automatically while you type or change sort.</span>
            <a data-live-link="true" href="<?= htmlspecialchars(route_url($area . '/products'), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
        </div>
    </form>
</div>

<?php if (($products ?? []) === []): ?>
    <div class="card">
        <p class="muted" style="margin:0">No products yet.</p>
    </div>
<?php elseif ($area === 'backoffice'): ?>
    <div class="product-dashboard-grid">
        <div class="sales-hero">
            <h2>Backoffice Sales Snapshot</h2>
            <p class="muted" style="margin:10px 0 0">This combines catalog health with what your orders are actually doing, so you can see what is selling, what is stuck, and where inventory is tied up.</p>
            <div class="summary">
                <div class="summary-item">
                    <strong>Total Revenue</strong>
                    <span>$<?= htmlspecialchars(number_format((float) ($productStats['total_revenue'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Units Sold</strong>
                    <span><?= htmlspecialchars((string) ($productStats['total_units_sold'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
                <div class="summary-item">
                    <strong>Orders</strong>
                    <span><?= htmlspecialchars((string) ($productStats['total_orders'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            </div>
        </div>
        <div class="health-list">
            <div class="health-item">
                <strong>Inventory Value</strong>
                <span>$<?= htmlspecialchars(number_format((float) ($productStats['inventory_value'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?> currently sitting in stock.</span>
            </div>
            <div class="health-item">
                <strong>Average Order Value</strong>
                <span>$<?= htmlspecialchars(number_format((float) ($productStats['average_order_value'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?> per completed non-cancelled order line.</span>
            </div>
            <div class="health-item">
                <strong>Average Product Price</strong>
                <span>$<?= htmlspecialchars(number_format((float) ($productStats['average_price'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?> across the filtered catalog.</span>
            </div>
        </div>
    </div>

    <div class="product-analytics-grid">
        <div class="product-analytics-card">
            <strong>Total Products</strong>
            <span class="metric"><?= htmlspecialchars((string) ($productStats['total_products'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="meta">All products currently stored in the catalog.</span>
        </div>
        <div class="product-analytics-card">
            <strong>Active Products</strong>
            <span class="metric"><?= htmlspecialchars((string) ($productStats['active_products'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="meta">Products available to be ordered right now.</span>
        </div>
        <div class="product-analytics-card">
            <strong>Low Stock</strong>
            <span class="metric"><?= htmlspecialchars((string) ($productStats['low_stock_products'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="meta">Products at five units or fewer that need attention.</span>
        </div>
        <div class="product-analytics-card">
            <strong>Out Of Stock</strong>
            <span class="metric"><?= htmlspecialchars((string) ($productStats['out_of_stock_products'] ?? 0), ENT_QUOTES, 'UTF-8') ?></span>
            <span class="meta">Products that need replenishment before they can be sold again.</span>
        </div>
    </div>

    <div class="product-chart-grid">
        <div class="product-chart-card">
            <h3>Catalog Status Mix</h3>
            <p>Pie chart of how the filtered catalog is split between active, low-stock, and out-of-stock products.</p>
            <div class="product-chart-wrap">
                <canvas id="product-status-chart"></canvas>
            </div>
        </div>
        <div class="product-chart-card">
            <h3>Revenue Vs Inventory</h3>
            <p>Pie chart comparing what is currently tied up in inventory against the revenue already generated by the filtered products.</p>
            <div class="product-chart-wrap">
                <canvas id="product-value-chart"></canvas>
            </div>
        </div>
    </div>

    <?php if ($topProducts !== []): ?>
        <div class="card" style="margin-bottom:20px">
            <div class="header-line">
                <div>
                    <h2 style="margin:0">Top Performing Products</h2>
                    <p class="muted" style="margin:8px 0 0">A quick sales snapshot based on order quantity and revenue.</p>
                </div>
                <div class="badge">Avg Price $<?= htmlspecialchars(number_format((float) ($productStats['average_price'] ?? 0), 2), ENT_QUOTES, 'UTF-8') ?></div>
            </div>
            <table class="table" style="margin-top:18px">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Units Sold</th>
                        <th>Revenue</th>
                        <th>Stock Left</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topProducts as $topProduct): ?>
                        <tr>
                            <td>
                                <div><?= htmlspecialchars((string) $topProduct['name'], ENT_QUOTES, 'UTF-8') ?></div>
                                <div class="muted"><?= htmlspecialchars((string) $topProduct['sku'], ENT_QUOTES, 'UTF-8') ?></div>
                            </td>
                            <td><?= htmlspecialchars((string) $topProduct['total_units_sold'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>$<?= htmlspecialchars(number_format((float) $topProduct['total_revenue'], 2), ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $topProduct['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars((string) $topProduct['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Category</th>
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
                        <td><a href="<?= htmlspecialchars(route_url($area . '/categories/show', ['id' => (int) $product['product_category_id']]), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8') ?></a></td>
                        <td><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>$<?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td>
                            <a href="<?= htmlspecialchars(route_url($area . '/products/edit', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">Edit</a>
                            <form class="inline" method="post" action="<?= htmlspecialchars(route_url($area . '/products/delete', ['id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>" onsubmit="return confirm('Delete this product?');">
                                <button class="btn btn-danger" type="submit">Delete</button>
                            </form>
                            <a href="<?= htmlspecialchars(route_url($area . '/orders/new', ['product_id' => (int) $product['id']]), ENT_QUOTES, 'UTF-8') ?>">New Order</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php else: ?>
    <div class="catalog-grid">
        <?php foreach ($products as $product): ?>
            <?php
            $productId = (int) $product['id'];
            $stock = (int) ($product['stock_quantity'] ?? 0);
            $isOrderable = (string) ($product['status'] ?? '') === 'ACTIVE' && $stock > 0;
            ?>
            <div class="catalog-card">
                <div class="catalog-top">
                    <div>
                        <h3><?= htmlspecialchars((string) $product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="muted" style="margin:6px 0 0"><?= htmlspecialchars((string) $product['category_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <div class="catalog-price">$<?= htmlspecialchars(number_format((float) $product['price'], 2), ENT_QUOTES, 'UTF-8') ?></div>
                </div>

                <p class="muted" style="margin:0">
                    <?= htmlspecialchars((string) ($product['description'] ?: 'Order straight from the catalog and let backoffice handle the delivery state.'), ENT_QUOTES, 'UTF-8') ?>
                </p>

                <div class="catalog-meta">
                    <div class="catalog-meta-item">
                        <strong>SKU</strong>
                        <span><?= htmlspecialchars((string) $product['sku'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="catalog-meta-item">
                        <strong>Status</strong>
                        <span><?= htmlspecialchars((string) $product['status'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="catalog-meta-item">
                        <strong>Stock</strong>
                        <span><?= htmlspecialchars((string) $product['stock_quantity'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                    <div class="catalog-meta-item">
                        <strong>Order State</strong>
                        <span>Starts as Not delivered</span>
                    </div>
                </div>

                <div class="order-panel">
                    <?php if ($isOrderable): ?>
                        <div class="order-message">Open the detailed order page to choose quantity, set the address, and pin the delivery point on the Leaflet map.</div>
                        <div class="actions">
                            <a class="btn btn-primary" href="<?= htmlspecialchars(route_url('frontoffice/orders/new', ['product_id' => $productId]), ENT_QUOTES, 'UTF-8') ?>">Order With Delivery Map</a>
                        </div>
                    <?php else: ?>
                        <div class="order-message">
                            <?= $stock < 1 ? 'This product is currently out of stock.' : 'This product is not available for ordering right now.' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
<?php if ($area === 'backoffice' && ($products ?? []) !== []): ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    window.initProductsDashboard = function initProductsDashboard() {
        if (!window.Chart) {
            return;
        }

        const statusCanvas = document.getElementById('product-status-chart');
        const valueCanvas = document.getElementById('product-value-chart');
        if (!statusCanvas || !valueCanvas) {
            return;
        }

        const productStats = <?= json_encode([
            'active_products' => (int) ($productStats['active_products'] ?? 0),
            'low_stock_products' => (int) ($productStats['low_stock_products'] ?? 0),
            'out_of_stock_products' => (int) ($productStats['out_of_stock_products'] ?? 0),
            'inventory_value' => (float) ($productStats['inventory_value'] ?? 0),
            'total_revenue' => (float) ($productStats['total_revenue'] ?? 0),
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

        new Chart(statusCanvas, {
            type: 'pie',
            data: {
                labels: ['Active', 'Low Stock', 'Out Of Stock'],
                datasets: [{
                    data: [
                        productStats.active_products,
                        productStats.low_stock_products,
                        productStats.out_of_stock_products
                    ],
                    backgroundColor: ['#16a34a', '#f59e0b', '#dc2626'],
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 18,
                            usePointStyle: true
                        }
                    }
                }
            }
        });

        new Chart(valueCanvas, {
            type: 'pie',
            data: {
                labels: ['Inventory Value', 'Revenue Earned'],
                datasets: [{
                    data: [
                        productStats.inventory_value,
                        productStats.total_revenue
                    ],
                    backgroundColor: ['#2563eb', '#14b8a6'],
                    borderColor: '#ffffff',
                    borderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 18,
                            usePointStyle: true
                        }
                    }
                }
            }
        });
    };
    </script>
<?php endif; ?>
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

            form.querySelectorAll('input[type="text"], input[type="search"], input:not([type])').forEach((field) => {
                field.addEventListener('input', schedule);
            });

            form.querySelectorAll('select').forEach((field) => {
                field.addEventListener('change', submit);
            });
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
                    if (typeof window.initProductsDashboard === 'function') {
                        window.initProductsDashboard();
                    }
                }).catch(() => { window.location.href = url; });
            panel.querySelectorAll('form[data-live-search]').forEach((form) => this.bindLiveForm(form, load));
            panel.querySelectorAll('a[data-live-link]').forEach((link) => link.addEventListener('click', (event) => { event.preventDefault(); load(link.href); }));
            panel.querySelectorAll('a[data-pdf-preview]').forEach((link) => link.addEventListener('click', (event) => { event.preventDefault(); this.openPdf(link.href); }));
        }
    };
}
document.addEventListener('DOMContentLoaded', () => {
    window.AsteriaLiveUi.bindPanel('products-live-panel');
    if (typeof window.initProductsDashboard === 'function') {
        window.initProductsDashboard();
    }
});
</script>
<style>
#asteria-pdf-modal{position:fixed;inset:0;display:none;z-index:9999}.open#asteria-pdf-modal{display:block}
.asteria-pdf-backdrop{position:absolute;inset:0;background:rgba(15,23,42,.6)}
.asteria-pdf-dialog{position:relative;width:min(960px,92vw);height:min(88vh,820px);margin:4vh auto;background:#fff;border-radius:24px;overflow:hidden}
.asteria-pdf-close{position:absolute;top:14px;right:14px;z-index:2;border:0;border-radius:999px;padding:10px 14px;background:#0f172a;color:#fff}
.asteria-pdf-dialog iframe{width:100%;height:100%;border:0}
</style>
