<?php
declare(strict_types=1);
?>
<section class="site-hero">
    <div>
        <span class="site-eyebrow"><?= htmlspecialchars($page['eyebrow'] ?? 'Frontoffice', ENT_QUOTES, 'UTF-8') ?></span>
        <h1><?= htmlspecialchars($page['title'] ?? 'Asteria Coaching', ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars($page['intro'] ?? 'Native PHP frontoffice page.', ENT_QUOTES, 'UTF-8') ?></p>
        <p><?= htmlspecialchars($page['summary'] ?? 'The page keeps the Nutrio styling assets without loading the original HTML template file.', ENT_QUOTES, 'UTF-8') ?></p>
        <div class="site-actions">
            <a class="site-button" href="<?= htmlspecialchars(route_url($page['primaryRoute'] ?? 'frontoffice/programs'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($page['primaryLabel'] ?? 'Browse Programs', ENT_QUOTES, 'UTF-8') ?></a>
            <a class="site-button secondary" href="<?= htmlspecialchars(route_url($page['secondaryRoute'] ?? 'backoffice/dashboard'), ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($page['secondaryLabel'] ?? 'Open Backoffice', ENT_QUOTES, 'UTF-8') ?></a>
        </div>
    </div>
    <div class="site-hero-visual">
        <img src="<?= htmlspecialchars(asset_url($page['image'] ?? 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($page['title'] ?? 'Asteria Coaching', ENT_QUOTES, 'UTF-8') ?>">
    </div>
</section>

<?php if (!empty($page['cards']) && is_array($page['cards'])): ?>
<section class="site-section">
    <div class="site-grid">
        <?php foreach ($page['cards'] as $card): ?>
            <article class="site-card">
                <h3><?= htmlspecialchars((string) ($card['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars((string) ($card['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($page['highlights']) && is_array($page['highlights'])): ?>
<section class="site-section">
    <div class="site-strip">
        <?php foreach ($page['highlights'] as $index => $item): ?>
            <div>
                <strong><?= htmlspecialchars('0' . ((int) $index + 1), ENT_QUOTES, 'UTF-8') ?></strong>
                <span><?= htmlspecialchars((string) $item, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>
