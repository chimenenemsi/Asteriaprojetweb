<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <span class="badge bg-primary-subtle text-primary mb-2">Backoffice</span>
            <h4 class="fs-18 fw-semibold m-0">Progress Dashboard</h4>
            <p class="text-muted mb-0 mt-1">Manage goals and combined progress records (reports + records).</p>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/goals'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Module</span>
                    <h5 class="card-title text-dark mb-2">Progress Goals</h5>
                    <p class="text-muted mb-0">Create, update, and manage measurable goals.</p>
                </div>
            </a>
        </div>
        <div class="col-md-6">
            <a class="card h-100 text-decoration-none border-0 shadow-sm" href="<?= htmlspecialchars(route_url('backoffice/records'), ENT_QUOTES, 'UTF-8') ?>">
                <div class="card-body">
                    <span class="badge bg-light text-dark mb-3">Module</span>
                    <h5 class="card-title text-dark mb-2">Progress Records</h5>
                    <p class="text-muted mb-0">Combined entity with adherence, mood, checkpoints, milestones, measurements, and notes.</p>
                </div>
            </a>
        </div>
    </div>
</div>
