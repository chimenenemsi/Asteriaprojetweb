<?php
declare(strict_types=1);
?>
<section style="max-width:720px;margin:60px auto;padding:32px;border-radius:24px;background:#fff;box-shadow:0 18px 42px rgba(15,23,42,.08);text-align:center;">
    <p style="margin:0 0 10px;color:#6ca138;font-weight:700;letter-spacing:.08em;text-transform:uppercase;">404</p>
    <h1 style="margin:0 0 14px;color:#153122;">Page not found</h1>
    <p style="margin:0 0 24px;color:#60706a;line-height:1.7;">The page you requested is not available in this Asteria module anymore.</p>
    <a href="<?= htmlspecialchars(route_url('home'), ENT_QUOTES, 'UTF-8') ?>" style="display:inline-flex;align-items:center;justify-content:center;padding:12px 18px;border-radius:999px;background:#6ca138;color:#fff;text-decoration:none;font-weight:600;">Return home</a>
</section>
