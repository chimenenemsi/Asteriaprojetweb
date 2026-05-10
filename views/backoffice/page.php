<div class="header-line mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1"><?= htmlspecialchars($page['title'] ?? '') ?></h1>
        <p class="text-muted mb-0"><?= htmlspecialchars($page['intro'] ?? '') ?></p>
    </div>
    <span class="badge"><?= htmlspecialchars($page['badge'] ?? 'Backoffice') ?></span>
</div>

<div class="card mb-4">
    <p class="mb-0"><?= htmlspecialchars($page['description'] ?? '') ?></p>
</div>

<div class="row g-4 mb-4">
    <?php foreach ($page['cards'] ?? [] as $card): ?>
        <div class="col-md-4">
            <div class="card h-100 p-4 border-0 shadow-sm" style="background: #f8fafc;">
                <h3 class="h5 fw-bold mb-2"><?= htmlspecialchars($card['title']) ?></h3>
                <p class="text-muted small mb-0"><?= htmlspecialchars($card['text']) ?></p>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="card">
    <h3 class="h5 fw-bold mb-3">Highlights</h3>
    <ul class="mb-0">
        <?php foreach ($page['highlights'] ?? [] as $highlight): ?>
            <li class="mb-2"><?= htmlspecialchars($highlight) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
