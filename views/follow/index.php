<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <span class="badge bg-primary-subtle text-primary mb-2">Secondary</span>
            <h4 class="fs-18 fw-semibold m-0">Follow</h4>
            <p class="text-muted mb-0 mt-1">Track the order workflow from placed to delivered inside the same produits backoffice shell.</p>
        </div>
        <div class="text-sm-end mt-3 mt-sm-0">
            <a class="btn btn-primary" href="<?= htmlspecialchars(route_url('backoffice/orders'), ENT_QUOTES, 'UTF-8') ?>">Open Orders</a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-warning-subtle text-warning">Step 01</span>
                        <h5 class="card-title mb-0">Placed</h5>
                    </div>
                    <p class="text-muted mb-0">Orders begin as not delivered. Customer, product, quantity, user account, and delivery location are saved here.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-info-subtle text-info">Step 02</span>
                        <h5 class="card-title mb-0">Prepared</h5>
                    </div>
                    <p class="text-muted mb-0">Backoffice reviews stock, product details, and map pin before delivery status is changed.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <span class="badge bg-success-subtle text-success">Step 03</span>
                        <h5 class="card-title mb-0">Delivered</h5>
                    </div>
                    <p class="text-muted mb-0">Admins mark orders as delivered from the order edit page once the delivery is complete.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="header-line mb-4">
                <div>
                    <h5 class="card-title mb-1">Follow Menu</h5>
                    <p class="text-muted mb-0">This page uses the same sidebar, topbar, cards, buttons, and typography as the produits backoffice.</p>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-3 col-md-6">
                    <a class="card h-100 text-decoration-none border shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/orders', ['status' => 'PENDING']), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-body">
                            <span class="badge bg-warning-subtle text-warning mb-3">Not delivered</span>
                            <h6 class="text-dark mb-2">Pending Orders</h6>
                            <p class="text-muted mb-0">Review orders still waiting for delivery.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a class="card h-100 text-decoration-none border shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/orders', ['location' => 'missing']), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-body">
                            <span class="badge bg-danger-subtle text-danger mb-3">Map</span>
                            <h6 class="text-dark mb-2">Missing Location</h6>
                            <p class="text-muted mb-0">Find orders that still need a pinned delivery point.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a class="card h-100 text-decoration-none border shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/orders', ['status' => 'SHIPPED']), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-body">
                            <span class="badge bg-success-subtle text-success mb-3">Delivered</span>
                            <h6 class="text-dark mb-2">Delivered Orders</h6>
                            <p class="text-muted mb-0">See orders already marked as delivered.</p>
                        </div>
                    </a>
                </div>
                <div class="col-lg-3 col-md-6">
                    <a class="card h-100 text-decoration-none border shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/products'), ENT_QUOTES, 'UTF-8') ?>">
                        <div class="card-body">
                            <span class="badge bg-primary-subtle text-primary mb-3">Catalog</span>
                            <h6 class="text-dark mb-2">Product Stock</h6>
                            <p class="text-muted mb-0">Check stock before updating delivery progress.</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
