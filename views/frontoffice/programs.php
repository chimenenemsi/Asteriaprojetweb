<?php
declare(strict_types=1);

$programFilters = $programFilters ?? ['search' => '', 'goal_type' => '', 'sort' => 'created_desc'];
?>
<style>
    .programs-page{display:grid;gap:28px}
    .programs-panel{padding:28px 30px;border-radius:30px;background:rgba(255,255,255,.82);box-shadow:0 20px 48px rgba(21,49,34,.08)}
    .programs-panel h1{margin:0;font-size:clamp(34px,4vw,54px);line-height:1.05}
    .programs-panel p{margin:14px 0 0;color:#60706a;line-height:1.7}
    .program-links{display:flex;flex-wrap:wrap;gap:10px;margin-top:22px}
    .program-links a,.filter-reset,.program-badge,.exercise-badge{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;text-decoration:none;font-weight:700}
    .program-links a,.filter-reset{padding:10px 15px;background:#fff;color:#264032;border:1px solid rgba(21,49,34,.12);transition:background .18s}.filter-reset:hover{background:#f0f4ee}
    .filter-form{display:flex;flex-wrap:wrap;gap:14px;align-items:flex-end;margin-top:24px}
    .filter-field{display:grid;gap:8px;flex:1 1 180px}.filter-field:first-child{flex:2 1 260px}
    .filter-field label{font-size:13px;font-weight:700;color:#335145}
    .filter-field input,.filter-field select{width:100%;padding:12px 14px;border-radius:16px;border:1px solid rgba(21,49,34,.12);background:#fff;color:#153122}
    .filter-note{font-size:13px;color:#4b6358}
    .program-list{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
    .program-card{padding:24px;border-radius:28px;background:#fff;border:1px solid rgba(21,49,34,.08);box-shadow:0 18px 42px rgba(21,49,34,.08)}
    .program-head,.exercise-head{display:flex;align-items:flex-start;justify-content:space-between;gap:14px}
    .program-label,.program-badge,.exercise-badge{padding:9px 13px;font-size:13px}
    .program-label{display:inline-flex;align-items:center;border-radius:999px;background:rgba(108,161,56,.12);color:#6ca138;font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase}
    .program-title{margin:16px 0 0;font-size:28px;line-height:1.08;color:#153122}
    .program-description,.exercise-description{color:#5a6e64;line-height:1.7}
    .program-meta{display:flex;flex-wrap:wrap;gap:10px;margin:20px 0}
    .program-badge{background:#f7f4ee;color:#335145}
    .exercise-list{display:grid;gap:12px}
    .exercise-card{padding:16px 18px;border-radius:22px;background:#fdfcf9;border:1px solid rgba(21,49,34,.08)}
    .exercise-title{margin:0;font-size:18px;line-height:1.2;color:#153122}
    .exercise-count{display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:38px;padding:0 12px;border-radius:999px;background:rgba(108,161,56,.12);color:#6ca138;font-size:13px;font-weight:800}
    .exercise-badges{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
    .exercise-badge{background:#fff;color:#486256;border:1px solid rgba(21,49,34,.07)}
    .empty-card{padding:32px;border-radius:28px;background:#fff;box-shadow:0 18px 42px rgba(21,49,34,.08);text-align:center}
    .empty-card h3,.empty-card h4{margin:0;color:#153122}
    .empty-card p{margin:10px 0 0;color:#60706a}
    .alert-box{padding:18px 20px;border-radius:22px;background:#fff1f2;color:#9f1239;box-shadow:0 18px 42px rgba(159,18,57,.08)}
    .coach-ai-wrap{padding:28px 30px;border-radius:30px;background:linear-gradient(135deg,#1b2115,#2d4a1e);color:#fff;box-shadow:0 24px 60px rgba(21,49,34,.18);position:relative;overflow:hidden}
    .coach-ai-wrap::before{content:"";position:absolute;right:-80px;top:-90px;width:260px;height:260px;border-radius:999px;background:rgba(108,161,56,.18)}
    .coach-ai-wrap h2{margin:8px 0 8px;font-size:clamp(26px,3vw,38px);line-height:1.1;color:#fff}.coach-ai-wrap p{margin:0 0 18px;color:rgba(255,255,255,.76);line-height:1.7}
    .coach-suggestion-row{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px}.coach-chip{border:1px solid rgba(255,255,255,.18);background:rgba(255,255,255,.1);color:#fff;border-radius:999px;padding:8px 12px;cursor:pointer}
    #coach-messages{max-height:260px;overflow-y:auto;display:flex;flex-direction:column;gap:10px;margin-bottom:12px}.coach-msg{padding:11px 14px;border-radius:16px;max-width:88%;white-space:pre-wrap;line-height:1.55}.coach-msg.user{align-self:flex-end;background:#6ca138;color:#fff;border-bottom-right-radius:4px}.coach-msg.bot{align-self:flex-start;background:rgba(255,255,255,.12);color:#fff;border-bottom-left-radius:4px}
    #coach-input-row{display:flex;gap:10px}#coach-input{flex:1;border:1px solid rgba(255,255,255,.2);background:rgba(255,255,255,.12);color:#fff;border-radius:16px;padding:12px 14px}#coach-input::placeholder{color:rgba(255,255,255,.5)}#coach-send{border:0;border-radius:16px;background:#6ca138;color:#fff;padding:0 20px;font-weight:800;cursor:pointer}#coach-typing{display:none;margin:8px 0 0;color:rgba(255,255,255,.6);font-size:13px}

    @media (max-width:980px){.filter-form,.program-list{grid-template-columns:1fr}.program-head,.exercise-head{flex-direction:column;align-items:flex-start}}
</style>

<div class="programs-page">

    <section class="coach-ai-wrap" id="coach-ai" aria-label="Asteria AI Coach">
        <span class="site-eyebrow">AI Coach</span>
        <h2>Tell me your goal. Gemini will recommend a program.</h2>
        <p>Describe your training goal and the assistant will use the available programs and exercises from your database.</p>
        <div class="coach-suggestion-row">
            <button class="coach-chip" type="button" data-coach-prompt="I want progressive overload and better muscle definition">Progressive overload + aesthetics</button>
            <button class="coach-chip" type="button" data-coach-prompt="I want to get stronger, I can train 3 days a week">Get stronger 3 days/week</button>
            <button class="coach-chip" type="button" data-coach-prompt="I want to improve endurance and lose weight">Endurance + fat loss</button>
        </div>
        <div id="coach-messages"><div class="coach-msg bot">Tell me your goal, schedule, and level. I will recommend from the available Asteria programs.</div></div>
        <div id="coach-typing">Coach AI is thinking...</div>
        <div id="coach-input-row">
            <input id="coach-input" type="text" placeholder="Example: I want bigger legs and a stronger back...">
            <button id="coach-send" type="button">Ask Coach</button>
        </div>
    </section>

    <section class="programs-panel">
        <span class="site-eyebrow">Programs</span>
        <h1>Programs & Exercises</h1>
        <p>Browse the latest coaching plans, search by goal, and see the exercises included in each program.</p>

        <?php if ($programs !== []): ?>
            <div class="program-links">
                <?php foreach ($programs as $program): ?>
                    <a href="#program-<?= htmlspecialchars((string) ($program->getId() ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($program->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form id="coaching-front-program-search" method="get" action="<?= htmlspecialchars(base_url() . '/index.php', ENT_QUOTES, 'UTF-8') ?>" novalidate class="filter-form">
            <input type="hidden" name="route" value="frontoffice/programs">
            <div class="filter-field">
                <label for="front-program-search">Search</label>
                <input id="front-program-search" type="text" name="search" placeholder="Search programs, goals, or exercises" value="<?= htmlspecialchars((string) $programFilters['search'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="filter-field">
                <label for="front-program-goal-type">Goal Type</label>
                <input id="front-program-goal-type" type="text" name="goal_type" placeholder="Strength, fat loss..." value="<?= htmlspecialchars((string) $programFilters['goal_type'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <div class="filter-field">
                <label for="front-program-sort">Sort</label>
                <select id="front-program-sort" name="sort">
                    <option value="created_desc" <?= $programFilters['sort'] === 'created_desc' ? 'selected' : '' ?>>Newest</option>
                    <option value="title_asc" <?= $programFilters['sort'] === 'title_asc' ? 'selected' : '' ?>>Title A-Z</option>
                    <option value="title_desc" <?= $programFilters['sort'] === 'title_desc' ? 'selected' : '' ?>>Title Z-A</option>
                    <option value="weeks_asc" <?= $programFilters['sort'] === 'weeks_asc' ? 'selected' : '' ?>>Weeks Low-High</option>
                    <option value="weeks_desc" <?= $programFilters['sort'] === 'weeks_desc' ? 'selected' : '' ?>>Weeks High-Low</option>
                </select>
            </div>
            <button class="filter-reset" type="submit">Apply filters</button>
            <a class="filter-reset" href="<?= htmlspecialchars(route_url('frontoffice/programs'), ENT_QUOTES, 'UTF-8') ?>">Reset</a>
        </form>
        <div class="filter-note">Search updates automatically while you type or change sort.</div>
    </section>

    <?php if ($dbError !== null): ?>
        <div class="alert-box"><?= htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if ($programs === []): ?>
        <div class="empty-card">
            <h3>No programs available yet</h3>
            <p>Programs created in backoffice will appear here.</p>
        </div>
    <?php else: ?>
        <section class="program-list">
            <?php foreach ($programs as $program): ?>
                <?php
                $programId = (int) ($program->getId() ?? 0);
                $programTitle = $program->getTitle();
                $programGoalType = (string) ($program->getGoalType() ?? '');
                $programDescription = (string) ($program->getDescription() ?? '');
                $programDurationWeeks = $program->getDurationWeeks();
                $programExercises = $program->getExercises();
                $exerciseCount = $program->getExerciseCount() > 0 ? $program->getExerciseCount() : count($programExercises);
                ?>
                <article class="program-card" id="program-<?= htmlspecialchars((string) $programId, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="program-head">
                        <div>
                            <span class="program-label"><?= htmlspecialchars($programGoalType !== '' ? $programGoalType : 'General', ENT_QUOTES, 'UTF-8') ?></span>
                            <h2 class="program-title"><?= htmlspecialchars($programTitle, ENT_QUOTES, 'UTF-8') ?></h2>
                            <?php if ($programDescription !== ''): ?>
                                <p class="program-description"><?= htmlspecialchars($programDescription, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="program-badge"><?= htmlspecialchars((string) $programDurationWeeks, ENT_QUOTES, 'UTF-8') ?> weeks</span>
                    </div>

                    <div class="program-meta">
                        <span class="program-badge"><?= htmlspecialchars((string) $exerciseCount, ENT_QUOTES, 'UTF-8') ?> exercises</span>
                        <span class="program-badge">Program #<?= htmlspecialchars((string) $programId, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <?php if ($programExercises === []): ?>
                        <div class="empty-card">
                            <h4>No exercises yet</h4>
                            <p>This program has no exercises for now.</p>
                        </div>
                    <?php else: ?>
                        <div class="exercise-list">
                            <?php foreach ($programExercises as $exerciseIndex => $exercise): ?>
                                <?php
                                $exerciseDescription = (string) ($exercise->getDescription() ?? '');
                                $exerciseMuscleGroup = (string) ($exercise->getMuscleGroup() ?? '');
                                ?>
                                <div class="exercise-card">
                                    <div class="exercise-head">
                                        <h3 class="exercise-title"><?= htmlspecialchars($exercise->getName(), ENT_QUOTES, 'UTF-8') ?></h3>
                                        <span class="exercise-count"><?= htmlspecialchars((string) ($exerciseIndex + 1), ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>

                                    <?php if ($exerciseDescription !== ''): ?>
                                        <p class="exercise-description"><?= htmlspecialchars($exerciseDescription, ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>

                                    <div class="exercise-badges">
                                        <span class="exercise-badge"><?= htmlspecialchars($exerciseMuscleGroup !== '' ? $exerciseMuscleGroup : 'General', ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="exercise-badge"><?= htmlspecialchars((string) $exercise->getSets(), ENT_QUOTES, 'UTF-8') ?> sets</span>
                                        <span class="exercise-badge"><?= htmlspecialchars((string) $exercise->getReps(), ENT_QUOTES, 'UTF-8') ?> reps</span>
                                        <span class="exercise-badge"><?= htmlspecialchars((string) $exercise->getRestSeconds(), ENT_QUOTES, 'UTF-8') ?> sec rest</span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>
</div>
<script>
(() => {
    const form = document.getElementById('coaching-front-program-search');
    if (form) {
        const submit = () => form.requestSubmit ? form.requestSubmit() : form.submit();
        form.querySelectorAll('input[type="text"], input[type="search"]').forEach((field) => {
            field.addEventListener('keydown', (event) => {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    submit();
                }
            });
        });
        // Select filters no longer auto-refresh. Use Enter in search or the Search button.
    }

    const endpoint = '<?= htmlspecialchars(route_url('frontoffice/ai-coach'), ENT_QUOTES, 'UTF-8') ?>';
    const messages = document.getElementById('coach-messages');
    const input = document.getElementById('coach-input');
    const typing = document.getElementById('coach-typing');
    const send = document.getElementById('coach-send');
    let history = [];

    function addMessage(text, role) {
        if (!messages) return;
        const item = document.createElement('div');
        item.className = 'coach-msg ' + role;
        item.textContent = text;
        messages.appendChild(item);
        messages.scrollTop = messages.scrollHeight;
    }

    async function ask(raw) {
        const message = (raw || (input ? input.value : '') || '').trim();
        if (!message) return;
        if (input) input.value = '';
        addMessage(message, 'user');
        history.push({role: 'user', text: message});
        if (typing) typing.style.display = 'block';
        try {
            const response = await fetch(endpoint, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify({message, history: history.slice(-8)})});
            const data = await response.json();
            const reply = data.reply || data.error || 'Coach AI could not answer right now.';
            history.push({role: 'model', text: reply});
            addMessage(reply, 'bot');
        } catch (error) {
            addMessage('Connection error. Please try again.', 'bot');
        } finally {
            if (typing) typing.style.display = 'none';
        }
    }

    send?.addEventListener('click', () => ask());
    input?.addEventListener('keydown', (event) => { if (event.key === 'Enter') ask(); });
    document.querySelectorAll('[data-coach-prompt]').forEach((chip) => chip.addEventListener('click', () => ask(chip.dataset.coachPrompt || '')));
})();
</script>
