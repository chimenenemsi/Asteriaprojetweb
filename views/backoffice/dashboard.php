<?php
declare(strict_types=1);

$templatePath = ROOT_PATH . '/assets/backoffice/zoyothemes.com/silva/html/index.html';
$templateHtml = @file_get_contents($templatePath);

if ($templateHtml === false) {
    ?>
    <section>
        <h1>Dashboard template unavailable</h1>
        <p>The Silva dashboard reference file could not be loaded from the local assets directory.</p>
    </section>
    <?php
    return;
}

echo rewrite_backoffice_dashboard_template($templateHtml);
