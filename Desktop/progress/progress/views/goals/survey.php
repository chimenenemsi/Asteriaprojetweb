<?php
declare(strict_types=1);
?>
<style>
.survey-wrap{max-width:820px;margin:0 auto;display:grid;gap:22px}.survey-hero{padding:36px;border-radius:30px;background:linear-gradient(135deg,#1b2115,#2d4a1e);color:#fff;box-shadow:0 24px 60px rgba(21,49,34,.18)}.survey-hero h1{margin:0 0 10px;font-size:clamp(30px,4vw,46px);line-height:1.08}.survey-hero p{margin:0;color:rgba(255,255,255,.76);line-height:1.7}.survey-card{padding:24px;border-radius:24px;background:#fff;border:1px solid #e2e8f0;box-shadow:0 18px 42px rgba(15,23,42,.06)}.survey-card h3{margin:0 0 6px;color:#153122}.survey-card p{margin:0 0 14px;color:#60706a}.option-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px}.opt-btn{border:2px solid rgba(21,49,34,.1);border-radius:16px;background:#fff;color:#153122;padding:13px 11px;font-weight:700;cursor:pointer;transition:all .18s}.opt-btn:hover,.opt-btn.selected{border-color:#6ca138;background:#f1f8ea;color:#31501d}.survey-text{min-height:82px;resize:vertical}.survey-submit{width:100%;border:0;border-radius:18px;background:#6ca138;color:#fff;padding:16px 18px;font-size:17px;font-weight:800;cursor:pointer}.survey-loading{display:none;text-align:center;padding:30px;color:#60706a}.survey-spinner{width:42px;height:42px;border-radius:999px;border:4px solid rgba(108,161,56,.2);border-top-color:#6ca138;display:inline-block;animation:survey-spin .9s linear infinite}@keyframes survey-spin{to{transform:rotate(360deg)}}.survey-result{display:none;overflow:hidden;border-radius:28px;background:#fff;box-shadow:0 24px 60px rgba(15,23,42,.1)}.survey-result-head{padding:26px 30px;background:linear-gradient(135deg,#1b2115,#2d4a1e);color:#fff}.survey-result-head h2{margin:0 0 8px;font-size:32px}.survey-result-head p{margin:0;color:rgba(255,255,255,.72)}.survey-result-body{padding:26px 30px;display:grid;gap:16px}.result-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px}.result-item{padding:14px 16px;border-radius:18px;background:#faf8f3;border:1px solid rgba(21,49,34,.08)}.result-item strong{display:block;font-size:11px;text-transform:uppercase;color:#60706a;letter-spacing:.06em}.result-item span{display:block;margin-top:5px;font-size:20px;font-weight:800;color:#153122}.result-explanation{padding:16px;border-radius:18px;background:rgba(108,161,56,.1);color:#2d4a1e;line-height:1.7}.survey-actions{display:flex;flex-wrap:wrap;gap:12px}.survey-actions a,.survey-actions button{display:inline-flex;align-items:center;justify-content:center;border-radius:999px;padding:12px 18px;text-decoration:none;font-weight:800}.survey-actions a{background:#6ca138;color:#fff}.survey-actions button{border:1px solid rgba(21,49,34,.14);background:#fff;color:#153122;cursor:pointer}
</style>
<div class="survey-wrap">
    <section class="survey-hero">
        <span style="display:inline-block;margin-bottom:10px;color:#9bd46a;font-weight:800;text-transform:uppercase;letter-spacing:.08em;font-size:12px">Gemini Survey</span>
        <h1>Tell Asteria about you. Gemini will recommend a goal.</h1>
        <p>Answer six quick questions. The recommendation can pre-fill the PHP goal creation form while validation stays server-side.</p>
    </section>

    <div id="survey-form">
        <div class="survey-card"><h3>1. Main focus</h3><p>Pick the goal that matters most right now.</p><div class="option-grid" data-q="focus"><button class="opt-btn" type="button" data-val="lose weight">Lose weight</button><button class="opt-btn" type="button" data-val="build muscle">Build muscle</button><button class="opt-btn" type="button" data-val="improve endurance">Improve endurance</button><button class="opt-btn" type="button" data-val="get stronger">Get stronger</button><button class="opt-btn" type="button" data-val="improve aesthetics">Aesthetics</button><button class="opt-btn" type="button" data-val="general fitness">General fitness</button></div></div>
        <div class="survey-card"><h3>2. Days per week</h3><p>Choose a realistic commitment.</p><div class="option-grid" data-q="days_per_week"><button class="opt-btn" type="button" data-val="1-2 days">1-2 days</button><button class="opt-btn" type="button" data-val="3 days">3 days</button><button class="opt-btn" type="button" data-val="4 days">4 days</button><button class="opt-btn" type="button" data-val="5+ days">5+ days</button></div></div>
        <div class="survey-card"><h3>3. Current level</h3><div class="option-grid" data-q="level"><button class="opt-btn" type="button" data-val="beginner">Beginner</button><button class="opt-btn" type="button" data-val="intermediate">Intermediate</button><button class="opt-btn" type="button" data-val="advanced">Advanced</button></div></div>
        <div class="survey-card"><h3>4. Limitations or injuries</h3><div class="option-grid" data-q="limitations"><button class="opt-btn" type="button" data-val="none">None</button><button class="opt-btn" type="button" data-val="knee issue">Knee</button><button class="opt-btn" type="button" data-val="back issue">Back</button><button class="opt-btn" type="button" data-val="shoulder issue">Shoulder</button><button class="opt-btn" type="button" data-val="other limitation">Other</button></div></div>
        <div class="survey-card"><h3>5. Timeframe</h3><div class="option-grid" data-q="timeframe"><button class="opt-btn" type="button" data-val="4 weeks">4 weeks</button><button class="opt-btn" type="button" data-val="6-8 weeks">6-8 weeks</button><button class="opt-btn" type="button" data-val="3 months">3 months</button><button class="opt-btn" type="button" data-val="6 months">6 months</button><button class="opt-btn" type="button" data-val="long term">Long term</button></div></div>
        <div class="survey-card"><h3>6. Intensity</h3><div class="option-grid" data-q="intensity"><button class="opt-btn" type="button" data-val="easy and sustainable">Easy</button><button class="opt-btn" type="button" data-val="moderate and manageable">Moderate</button><button class="opt-btn" type="button" data-val="intense and challenging">Intense</button></div></div>
        <div class="survey-card"><h3>Extra notes <span style="font-size:14px;color:#60706a;font-weight:400">optional</span></h3><textarea id="survey-extra" class="survey-text" placeholder="Example: home workouts, travel, wedding in 3 months..."></textarea></div>
        <button id="survey-submit" class="survey-submit" type="button">Get My AI Goal Recommendation</button>
    </div>

    <div id="survey-loading" class="survey-loading"><span class="survey-spinner"></span><p>Analysing your answers...</p></div>

    <section id="survey-result" class="survey-result">
        <div class="survey-result-head"><span style="color:#9bd46a;font-weight:800;text-transform:uppercase;letter-spacing:.08em;font-size:12px">Your AI Recommendation</span><h2 id="res-title"></h2><p id="res-weekly"></p></div>
        <div class="survey-result-body">
            <div class="result-grid"><div class="result-item"><strong>Metric</strong><span id="res-metric"></span></div><div class="result-item"><strong>Target</strong><span id="res-target"></span></div><div class="result-item"><strong>Timeframe</strong><span id="res-timeframe"></span></div><div class="result-item"><strong>Type</strong><span id="res-type"></span></div></div>
            <div class="result-explanation" id="res-explanation"></div>
            <div class="survey-actions"><a id="create-goal-link" href="#">Create this Goal</a><button type="button" onclick="location.reload()">Start Over</button></div>
        </div>
    </section>
</div>
<script>
(() => {
    const answers = {};
    const recommendEndpoint = '<?= htmlspecialchars(route_url($area . '/ai-survey/recommend'), ENT_QUOTES, 'UTF-8') ?>';
    const goalFormUrl = '<?= htmlspecialchars(route_url($area . '/goals/new'), ENT_QUOTES, 'UTF-8') ?>';
    document.querySelectorAll('.option-grid').forEach((grid) => {
        const key = grid.dataset.q;
        grid.querySelectorAll('.opt-btn').forEach((button) => button.addEventListener('click', () => {
            grid.querySelectorAll('.opt-btn').forEach((item) => item.classList.remove('selected'));
            button.classList.add('selected');
            answers[key] = button.dataset.val || '';
        }));
    });

    document.getElementById('survey-submit')?.addEventListener('click', async () => {
        const required = ['focus','days_per_week','level','limitations','timeframe','intensity'];
        const missing = required.filter((key) => !answers[key]);
        if (missing.length) {
            alert('Please answer all survey questions.');
            return;
        }
        const extra = document.getElementById('survey-extra')?.value.trim();
        if (extra) answers.extra = extra;
        document.getElementById('survey-form').style.display = 'none';
        document.getElementById('survey-loading').style.display = 'block';
        try {
            const response = await fetch(recommendEndpoint, {method: 'POST', headers: {'Content-Type': 'application/json'}, body: JSON.stringify(answers)});
            const data = await response.json();
            if (data.error) throw new Error(data.error);
            showResult(data.goal || {});
        } catch (error) {
            document.getElementById('survey-loading').style.display = 'none';
            document.getElementById('survey-form').style.display = 'grid';
            alert(error.message || 'Could not generate recommendation.');
        }
    });

    function showResult(goal) {
        document.getElementById('survey-loading').style.display = 'none';
        document.getElementById('res-title').textContent = goal.title || 'Recommended Goal';
        document.getElementById('res-weekly').textContent = goal.weekly_plan || '';
        document.getElementById('res-metric').textContent = goal.metric || '';
        document.getElementById('res-target').textContent = (goal.target_value ?? '') + (goal.unit ? ' ' + goal.unit : '');
        document.getElementById('res-timeframe').textContent = (goal.timeframe_weeks || '?') + ' weeks';
        document.getElementById('res-type').textContent = String(goal.goal_type || '').replace('_', ' ');
        document.getElementById('res-explanation').textContent = goal.explanation || '';
        const params = new URLSearchParams({
            prefill_title: goal.title || '',
            prefill_metric: goal.metric || '',
            prefill_unit: goal.unit || '',
            prefill_goal_type: goal.goal_type || 'FITNESS',
            prefill_start_value: goal.start_value ?? 0,
            prefill_target_value: goal.target_value ?? '',
            prefill_target_date: goal.target_date || '',
            prefill_description: ((goal.explanation || '') + ' ' + (goal.weekly_plan || '')).trim()
        });
        document.getElementById('create-goal-link').href = goalFormUrl + '&' + params.toString();
        document.getElementById('survey-result').style.display = 'block';
    }
})();
</script>
