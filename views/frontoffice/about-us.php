<?php
declare(strict_types=1);

$templatePath = ROOT_PATH . '/assets/frontoffice/nutrio.radiantthemes.com/template-kit/about-us/index.html';
$templateHtml = @file_get_contents($templatePath);

if ($templateHtml === false) {
    ?>
    <section>
        <h1>About Us template unavailable</h1>
        <p>The Nutrio About Us reference file could not be loaded from the local assets directory.</p>
    </section>
    <?php
    return;
}

echo rewrite_frontoffice_template($templateHtml, 'about-us');
