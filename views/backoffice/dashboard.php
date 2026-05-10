<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <span class="badge bg-primary-subtle text-primary mb-2">Backoffice</span>
            <h4 class="fs-18 fw-semibold m-0">Asteria Dashboard</h4>
            <p class="text-muted mb-0 mt-1">Manage your store and coaching programs from one centralized administration area.</p>
        </div>
    </div>

    <h5 class="mb-3">Produits & Orders</h5>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/categories'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Module</span>
                    <h5 class="card-title text-dark mb-2">Product Categories</h5>
                    <p class="text-muted mb-0">Create and organize product category records.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/products'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Module</span>
                    <h5 class="card-title text-dark mb-2">Products</h5>
                    <p class="text-muted mb-0">Update catalog items, stock, prices, and statuses.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/orders'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Module</span>
                    <h5 class="card-title text-dark mb-2">Orders</h5>
                    <p class="text-muted mb-0">Review customer orders and update delivery states.</p>
                </div>
            </a>
        </div>
    </div>

    <h5 class="mb-3">Coaching & Training</h5>
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/programs'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-info-subtle text-info mb-3">Coaching</span>
                    <h5 class="card-title text-dark mb-2">Programs</h5>
                    <p class="text-muted mb-0">Manage training programs and track goal distributions.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/exercises'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-info-subtle text-info mb-3">Coaching</span>
                    <h5 class="card-title text-dark mb-2">Exercises</h5>
                    <p class="text-muted mb-0">Build exercise catalogs and search for new training ideas.</p>
                </div>
            </a>
        </div>
        <div class="col-md-4">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/users'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-secondary-subtle text-secondary mb-3">Admin</span>
                    <h5 class="card-title text-dark mb-2">User Management</h5>
                    <p class="text-muted mb-0">Review user profiles and administrative roles.</p>
                </div>
            </a>
        </div>
    </div>
</div>
