<div class="container py-5">
    <div class="row align-items-center mb-5">
        <div class="col-lg-6">
            <span class="badge bg-primary mb-2"><?= htmlspecialchars($page['eyebrow'] ?? '') ?></span>
            <h1 class="display-4 fw-bold mb-3"><?= htmlspecialchars($page['title'] ?? '') ?></h1>
            <p class="lead mb-4"><?= htmlspecialchars($page['intro'] ?? '') ?></p>
            <p class="mb-4"><?= htmlspecialchars($page['summary'] ?? '') ?></p>
        </div>
        <div class="col-lg-6">
            <img src="<?= asset_url($page['image'] ?? 'assets/imgs/hero.jpg') ?>" alt="Hero" class="img-fluid rounded-4 shadow">
        </div>
    </div>

    <div class="row g-4">
        <?php foreach ($page['cards'] ?? [] as $card): ?>
            <div class="col-md-4">
                <div class="card h-100 border-0 shadow-sm p-4">
                    <h3 class="h5 fw-bold"><?= htmlspecialchars($card['title']) ?></h3>
                    <p class="text-muted mb-0"><?= htmlspecialchars($card['text']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
