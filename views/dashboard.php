<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <span class="badge bg-primary-subtle text-primary mb-2">Backoffice</span>
            <h4 class="fs-18 fw-semibold m-0">Asteria Admin Dashboard</h4>
            <p class="text-muted mb-0 mt-1">Manage products, coaching programs, and progress tracking in one unified control panel.</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Products & Orders Section -->
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/categories'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Produits</span>
                    <h5 class="card-title text-dark mb-2">Categories</h5>
                    <p class="text-muted mb-0">Organize product catalog sections.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/products'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Produits</span>
                    <h5 class="card-title text-dark mb-2">Products</h5>
                    <p class="text-muted mb-0">Manage items, stock, and pricing.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/orders'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Produits</span>
                    <h5 class="card-title text-dark mb-2">Orders</h5>
                    <p class="text-muted mb-0">Track customer orders and delivery states.</p>
                </div>
            </a>
        </div>

        <!-- Coaching Section -->
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/programs'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Coaching</span>
                    <h5 class="card-title text-dark mb-2">Programs</h5>
                    <p class="text-muted mb-0">Manage training plans and exercise sets.</p>
                </div>
            </a>
        </div>

        <!-- Progress Tracking Section -->
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/goals'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Progress</span>
                    <h5 class="card-title text-dark mb-2">Goals</h5>
                    <p class="text-muted mb-0">Monitor user measurable goals and targets.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/records'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Progress</span>
                    <h5 class="card-title text-dark mb-2">Records</h5>
                    <p class="text-muted mb-0">Review progress snapshots, adherence, and reports.</p>
                </div>
            </a>
        </div>
    </div>
</div>
