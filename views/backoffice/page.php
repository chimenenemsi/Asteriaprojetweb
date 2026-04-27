<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0"><?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?></h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') ?>">Frontoffice</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <span class="badge bg-primary-subtle text-primary mb-3"><?= htmlspecialchars($page['badge'], ENT_QUOTES, 'UTF-8') ?></span>
                    <h5 class="card-title mb-2"><?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                    <p class="text-muted mb-2"><?= htmlspecialchars($page['intro'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-muted mb-0"><?= htmlspecialchars($page['description'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <?php foreach ($page['cards'] as $card): ?>
            <div class="col-md-4">
                <div class="card h-100">
                    <div class="card-body">
                        <span class="badge bg-light text-dark mb-3">Static Section</span>
                        <h5 class="card-title"><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h5>
                        <p class="text-muted mb-0"><?= htmlspecialchars($card['text'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Ready Highlights</h5>
                    <div class="row">
                        <?php foreach ($page['highlights'] as $item): ?>
                            <div class="col-md-4">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="text-muted mb-0"><?= htmlspecialchars($item, ENT_QUOTES, 'UTF-8') ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
