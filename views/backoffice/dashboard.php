<?php
declare(strict_types=1);

$page = $page ?? [
    'title' => 'Dashboard',
    'badge' => 'Backoffice',
    'intro' => 'Asteria backoffice overview.',
    'description' => 'Native PHP dashboard using the Silva assets and layout without loading the original template page file.',
    'cards' => [],
    'highlights' => [],
];
?>
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0"><?= htmlspecialchars((string) ($page['title'] ?? 'Dashboard'), ENT_QUOTES, 'UTF-8') ?></h4>
        </div>
        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="<?= htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') ?>">Frontoffice</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars((string) ($page['title'] ?? 'Dashboard'), ENT_QUOTES, 'UTF-8') ?></li>
            </ol>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <span class="badge bg-primary-subtle text-primary mb-3"><?= htmlspecialchars((string) ($page['badge'] ?? 'Backoffice'), ENT_QUOTES, 'UTF-8') ?></span>
                    <h5 class="card-title mb-2"><?= htmlspecialchars((string) ($page['title'] ?? 'Dashboard'), ENT_QUOTES, 'UTF-8') ?></h5>
                    <p class="text-muted mb-2"><?= htmlspecialchars((string) ($page['intro'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                    <p class="text-muted mb-0"><?= htmlspecialchars((string) ($page['description'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($page['cards']) && is_array($page['cards'])): ?>
        <div class="row">
            <?php foreach ($page['cards'] as $card): ?>
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <span class="badge bg-light text-dark mb-3">Module</span>
                            <h5 class="card-title"><?= htmlspecialchars((string) ($card['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h5>
                            <p class="text-muted mb-0"><?= htmlspecialchars((string) ($card['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($page['highlights']) && is_array($page['highlights'])): ?>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Ready Highlights</h5>
                        <div class="row">
                            <?php foreach ($page['highlights'] as $item): ?>
                                <div class="col-md-4">
                                    <div class="border rounded-3 p-3 h-100">
                                        <p class="text-muted mb-0"><?= htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
