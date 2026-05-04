<?php
declare(strict_types=1);

$page = $page ?? [
    'title' => 'About Asteria',
    'eyebrow' => 'About',
    'intro' => 'Asteria keeps coaching, nutrition, and progress support in one simple PHP MVC experience.',
    'summary' => 'This native view keeps the same Nutrio asset styling without reading the removed HTML template file.',
    'image' => 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg',
    'cards' => [
        ['title' => 'Personal Guidance', 'text' => 'Explain each plan with clear context and achievable next steps.'],
        ['title' => 'Admin Ready', 'text' => 'Backoffice pages stay connected to the same routing and layout system.'],
        ['title' => 'Asset Friendly', 'text' => 'CSS, JavaScript, and images can stay in your assets folder without depending on template HTML pages.'],
    ],
];
?>
<section class="site-hero">
    <div>
        <span class="site-eyebrow"><?= htmlspecialchars((string) ($page['eyebrow'] ?? 'About'), ENT_QUOTES, 'UTF-8') ?></span>
        <h1><?= htmlspecialchars((string) ($page['title'] ?? 'About Asteria'), ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= htmlspecialchars((string) ($page['intro'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <p><?= htmlspecialchars((string) ($page['summary'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
        <div class="site-actions">
            <a class="site-button" href="<?= htmlspecialchars(route_url('frontoffice/programs'), ENT_QUOTES, 'UTF-8') ?>">View Programs</a>
            <a class="site-button secondary" href="<?= htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') ?>">Back Home</a>
        </div>
    </div>
    <div class="site-hero-visual">
        <img src="<?= htmlspecialchars(asset_url((string) ($page['image'] ?? 'assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg')), ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars((string) ($page['title'] ?? 'About Asteria'), ENT_QUOTES, 'UTF-8') ?>">
    </div>
</section>

<section class="site-section">
    <div class="site-grid">
        <?php foreach (($page['cards'] ?? []) as $card): ?>
            <article class="site-card">
                <h3><?= htmlspecialchars((string) ($card['title'] ?? ''), ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars((string) ($card['text'] ?? ''), ENT_QUOTES, 'UTF-8') ?></p>
            </article>
        <?php endforeach; ?>
    </div>
</section>
