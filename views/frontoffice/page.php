<section class="site-hero">
    <div>
        <span class="site-eyebrow"><?= htmlspecialchars($page['eyebrow'], ENT_QUOTES, 'UTF-8') ?></span>
        <h2><?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($page['intro'], ENT_QUOTES, 'UTF-8') ?></p>
        <p><?= htmlspecialchars($page['summary'], ENT_QUOTES, 'UTF-8') ?></p>

        <div class="site-actions">
            <a class="site-button" href="<?= htmlspecialchars(route_url('backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>">Open Backoffice</a>
            <a class="site-button secondary" href="<?= htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') ?>">Return Home</a>
        </div>
    </div>

    <div class="site-hero-visual">
        <img src="<?= htmlspecialchars(asset_url($page['image']), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($page['title'], ENT_QUOTES, 'UTF-8') ?>">
    </div>
</section>

<section class="site-section">
    <div class="site-grid">
        <?php foreach ($page['cards'] as $card): ?>
            <article class="site-card">
                <h3><?= htmlspecialchars($card['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars($card['text'], ENT_QUOTES, 'UTF-8') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
