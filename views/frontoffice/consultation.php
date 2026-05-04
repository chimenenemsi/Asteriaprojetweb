<?php
declare(strict_types=1);

$steps = [
    ['title' => 'Pick your card', 'text' => 'Choose the consultation format that matches your goals, comfort level, and weekly routine.'],
    ['title' => 'Share your context', 'text' => 'Tell us what matters most right now, from energy and food balance to family routines and long-term habits.'],
    ['title' => 'Leave with direction', 'text' => 'Each session is designed to end with practical next steps, not vague advice.'],
];

$clientBenefits = [
    ['title' => 'Simple choices', 'text' => 'Clear offers, no crowded layouts, and quick details on duration, format, and focus.'],
    ['title' => 'Realistic support', 'text' => 'Sessions are shaped for daily life, so advice feels usable after the call instead of theoretical.'],
    ['title' => 'Warm guidance', 'text' => 'Asteria keeps the tone supportive and practical from the first message to the next follow-up.'],
];
?>
<style>
    .consultation-front .hero{display:grid;grid-template-columns:1.1fr .9fr;gap:24px;padding:34px;border-radius:32px;background:radial-gradient(circle at top right,rgba(108,161,56,.22),transparent 34%),linear-gradient(135deg,rgba(255,255,255,.96),rgba(244,236,223,.9));box-shadow:0 24px 60px rgba(21,49,34,.09)}
    .consultation-front .hero h1{margin:0 0 16px;font-size:clamp(34px,4vw,58px);line-height:1.02}
    .consultation-front .hero p{margin:0 0 14px;color:#60706a;line-height:1.8;font-size:17px}
    .consultation-front .stats,.consultation-front .package-grid,.consultation-front .step-grid,.consultation-front .benefit-grid{display:grid;gap:18px}
    .consultation-front .stats{grid-template-columns:repeat(3,minmax(0,1fr));margin-top:28px}
    .consultation-front .stat{padding:18px;border-radius:22px;background:rgba(255,255,255,.84);box-shadow:inset 0 0 0 1px rgba(21,49,34,.07)}
    .consultation-front .stat strong{display:block;font-size:28px;color:#153122}
    .consultation-front .panel-stack{display:grid;gap:14px}
    .consultation-front .panel-card,.consultation-front .package-card,.consultation-front .step-card,.consultation-front .benefit-card,.consultation-front .empty-card{border-radius:26px;background:#fff;box-shadow:0 18px 42px rgba(21,49,34,.08)}
    .consultation-front .panel-card{padding:24px;background:linear-gradient(180deg,#153122 0%,#214530 100%);color:#fff}
    .consultation-front .panel-card p{color:rgba(255,255,255,.76);font-size:15px}
    .consultation-front .panel-card ul{margin:18px 0 0;padding-left:18px;color:rgba(255,255,255,.88);line-height:1.8}
    .consultation-front .section-head{display:flex;align-items:end;justify-content:space-between;gap:16px;margin-bottom:18px}
    .consultation-front .section-head p{margin:6px 0 0;max-width:600px;color:#60706a;line-height:1.7}
    .consultation-front .package-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
    .consultation-front .package-card{padding:24px;display:flex;flex-direction:column}
    .consultation-front .package-top{display:flex;align-items:center;justify-content:space-between;gap:14px;margin-bottom:18px}
    .consultation-front .pill{display:inline-flex;align-items:center;padding:8px 14px;border-radius:999px;background:rgba(108,161,56,.12);color:#6ca138;font-weight:700;font-size:13px}
    .consultation-front .price{font-size:24px;font-weight:800;color:#153122}
    .consultation-front .package-card h3{margin:0 0 10px;font-size:24px;line-height:1.2}
    .consultation-front .focus-line{margin:0 0 16px;color:#6ca138;font-weight:700}
    .consultation-front .tag-row{display:flex;flex-wrap:wrap;gap:10px;margin-bottom:16px}
    .consultation-front .tag{padding:8px 12px;border-radius:999px;background:#f7f4ee;color:#485853;font-size:13px;font-weight:600}
    .consultation-front .package-card p:last-of-type,.consultation-front .step-card p,.consultation-front .benefit-card p,.consultation-front .empty-card p{color:#60706a;line-height:1.75}
    .consultation-front .package-footer{margin-top:auto;padding-top:18px;display:flex;align-items:center;justify-content:space-between;gap:16px}
    .consultation-front .package-footer span{color:#60706a;font-size:14px}
    .consultation-front .step-grid,.consultation-front .benefit-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
    .consultation-front .step-card,.consultation-front .benefit-card{padding:24px}
    .consultation-front .step-number{display:inline-flex;align-items:center;justify-content:center;width:48px;height:48px;margin-bottom:18px;border-radius:16px;background:#153122;color:#fff;font-weight:800}
    .consultation-front .benefit-card{background:linear-gradient(180deg,#fff 0%,#f7f4ee 100%)}
    .consultation-front .empty-card{padding:28px;text-align:center}
    @media (max-width:980px){.consultation-front .hero,.consultation-front .package-grid,.consultation-front .step-grid,.consultation-front .benefit-grid,.consultation-front .stats{grid-template-columns:1fr}.consultation-front .section-head,.consultation-front .package-footer{flex-direction:column;align-items:flex-start}}
</style>

<div class="consultation-front">
    <section class="hero">
        <div>
            <span class="site-eyebrow">Client Consultation</span>
            <h1>Friendly consultation cards that help clients choose the right support fast.</h1>
            <p>Explore a calmer, cleaner consultation space where every card explains the session clearly, highlights who it is for, and keeps booking decisions easy.</p>
            <p>Published consultation cards from backoffice appear here automatically, so the client side always reflects the latest offers you choose to keep live.</p>

            <div class="stats">
                <div class="stat"><strong><?= htmlspecialchars((string) $consultationStats['published'], ENT_QUOTES, 'UTF-8') ?></strong><span>ready consultation cards</span></div>
                <div class="stat"><strong><?= htmlspecialchars((string) $consultationStats['formats'], ENT_QUOTES, 'UTF-8') ?></strong><span>available session formats</span></div>
                <div class="stat"><strong><?= htmlspecialchars((string) $consultationStats['spots'], ENT_QUOTES, 'UTF-8') ?></strong><span>visible spots across live offers</span></div>
            </div>

            <div class="site-actions">
                <a class="site-button secondary" href="mailto:info@example.com?subject=I%20want%20a%20consultation">Talk with Asteria</a>
            </div>
        </div>

        <div class="panel-stack">
            <div class="panel-card">
                <h3 class="text-white mb-2">What clients can expect</h3>
                <p>A consultation should feel simple, human, and useful from the first click.</p>
                <ul>
                    <li>Clear card layouts with no clutter</li>
                    <li>Visible timing, pricing, and format details</li>
                    <li>A practical focus line before booking</li>
                    <li>Supportive next steps after the session</li>
                </ul>
            </div>
            <div class="panel-card">
                <h3 class="text-white mb-2">Best for</h3>
                <p>New clients, busy families, follow-up clients, and anyone who wants structure without a confusing process.</p>
            </div>
        </div>
    </section>

    <section class="site-section" id="consultation-cards">
        <div class="section-head">
            <div>
                <span class="site-eyebrow">Live Offers</span>
                <h2>Choose the consultation style that fits your rhythm.</h2>
                <p>Each card below answers the questions clients usually ask first: what it covers, how long it takes, how it happens, and why it helps.</p>
            </div>
        </div>

        <?php if ($consultations === []): ?>
            <div class="empty-card">
                <h3>No published consultation cards yet</h3>
                <p>As soon as a consultation offer is marked as published in backoffice, it will appear here for clients.</p>
            </div>
        <?php else: ?>
            <div class="package-grid">
                <?php foreach ($consultations as $consultation): ?>
                    <?php $mailSubject = 'I want to book ' . $consultation['title']; ?>
                    <article class="package-card">
                        <div class="package-top">
                            <span class="pill"><?= htmlspecialchars((string) $consultation['category'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="price"><?= htmlspecialchars((string) $consultation['price'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <h3><?= htmlspecialchars((string) $consultation['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p class="focus-line"><?= htmlspecialchars((string) $consultation['focus'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="tag-row">
                            <span class="tag"><?= htmlspecialchars((string) $consultation['duration'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="tag"><?= htmlspecialchars((string) $consultation['format'], ENT_QUOTES, 'UTF-8') ?></span>
                            <span class="tag"><?= htmlspecialchars((string) $consultation['spots'], ENT_QUOTES, 'UTF-8') ?> spots visible</span>
                        </div>
                        <p><?= htmlspecialchars((string) $consultation['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="package-footer">
                            <span>Friendly, clear, and designed for real routines.</span>
                            <a class="site-button" href="mailto:info@example.com?subject=<?= htmlspecialchars(rawurlencode($mailSubject), ENT_QUOTES, 'UTF-8') ?>">Request this session</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <section class="site-section">
        <div class="section-head">
            <div>
                <span class="site-eyebrow">How It Works</span>
                <h2>A booking journey that stays easy to follow.</h2>
            </div>
        </div>
        <div class="step-grid">
            <?php foreach ($steps as $index => $step): ?>
                <article class="step-card">
                    <span class="step-number"><?= htmlspecialchars((string) ($index + 1), ENT_QUOTES, 'UTF-8') ?></span>
                    <h3><?= htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($step['text'], ENT_QUOTES, 'UTF-8') ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="site-section">
        <div class="section-head">
            <div>
                <span class="site-eyebrow">Client Experience</span>
                <h2>Support that feels organized and welcoming.</h2>
            </div>
        </div>
        <div class="benefit-grid">
            <?php foreach ($clientBenefits as $benefit): ?>
                <article class="benefit-card">
                    <h3><?= htmlspecialchars($benefit['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                    <p><?= htmlspecialchars($benefit['text'], ENT_QUOTES, 'UTF-8') ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>
