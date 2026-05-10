<section class="monta-section">
    <div class="monta-hero">
        <div>
            <span class="monta-eyebrow">Progress Module</span>
            <h1>Track goals and update progress from frontoffice with the same Asteria style.</h1>
            <p>Create goals and add combined progress records from frontoffice, and keep backoffice focused on monitoring.</p>
            <div class="monta-actions">
                <a class="monta-button" href="<?= htmlspecialchars(route_url('frontoffice/goals'), ENT_QUOTES, 'UTF-8') ?>">Open Goals</a>
                <a class="monta-button secondary" href="<?= htmlspecialchars(route_url('frontoffice/records'), ENT_QUOTES, 'UTF-8') ?>">Open Records</a>
                <a class="monta-button progress-ai-hero-btn" href="<?= htmlspecialchars(route_url('frontoffice/ai-survey'), ENT_QUOTES, 'UTF-8') ?>">AI Goal Survey</a>
            </div>
        </div>
        <div class="monta-hero-visual">
            <img src="<?= htmlspecialchars(asset_url('assets/frontoffice/nutrio.radiantthemes.com/wp-content/uploads/2022/05/about-us-image.jpg'), ENT_QUOTES, 'UTF-8') ?>" alt="Asteria progress">
        </div>
    </div>
</section>

<section class="monta-section">
    <div class="monta-grid">
        <div class="monta-card">
            <h3>Progress Goals</h3>
            <p>Browse target values, units, dates, and statuses inside a focused goal workflow.</p>
        </div>
        <div class="monta-card">
            <h3>Progress Records</h3>
            <p>Combined entity for reports and records with adherence, mood, checkpoints, and milestones.</p>
        </div>
        <div class="monta-card">
            <h3>Backoffice Monitoring</h3>
            <p>Review the same goal activity from backoffice without moving the update workflow away from frontoffice.</p>
        </div>
    </div>
</section>


<style>
.progress-ai-survey-card{background:linear-gradient(135deg,#1b2115,#2d4a1e)!important;color:#fff;position:relative;overflow:hidden}
.progress-ai-survey-card h3{color:#fff!important}.progress-ai-survey-card p{color:rgba(255,255,255,.75)!important}
.progress-ai-survey-card .monta-button{margin-top:14px;background:#6ca138;color:#fff}
.progress-ai-hero-btn{box-shadow:0 16px 30px rgba(108,161,56,.24)!important}
</style>
<section class="monta-section">
    <div class="monta-card progress-ai-survey-card">
        <span class="monta-eyebrow">Gemini AI</span>
        <h3>AI Goal Survey</h3>
        <p>Answer 6 quick questions and Gemini recommends a realistic goal, metric, target, and timeframe. The result can pre-fill the goal creation form.</p>
        <a class="monta-button" href="<?= htmlspecialchars(route_url('frontoffice/ai-survey'), ENT_QUOTES, 'UTF-8') ?>">Start AI Goal Survey</a>
    </div>
</section>
