<?php
declare(strict_types=1);

$palette = [
    ['accent' => '#2f8f83', 'surface' => 'linear-gradient(180deg,#ffffff 0%,#f2fbf8 100%)', 'soft' => 'rgba(47,143,131,.12)'],
    ['accent' => '#d97706', 'surface' => 'linear-gradient(180deg,#ffffff 0%,#fff8ee 100%)', 'soft' => 'rgba(217,119,6,.12)'],
    ['accent' => '#4f46e5', 'surface' => 'linear-gradient(180deg,#ffffff 0%,#f4f4ff 100%)', 'soft' => 'rgba(79,70,229,.12)'],
    ['accent' => '#6ca138', 'surface' => 'linear-gradient(180deg,#ffffff 0%,#f6fbef 100%)', 'soft' => 'rgba(108,161,56,.14)'],
];
?>
<style>
    .programs-front{display:grid;gap:28px}
    .programs-front .showcase{position:relative;overflow:hidden;padding:28px 30px;border-radius:34px;background:linear-gradient(135deg,#fffdf8 0%,#f7f4ee 48%,#eef8ef 100%);box-shadow:0 24px 60px rgba(21,49,34,.08)}
    .programs-front .showcase::before,.programs-front .showcase::after{content:"";position:absolute;border-radius:999px;pointer-events:none}
    .programs-front .showcase::before{width:280px;height:280px;top:-120px;right:-70px;background:radial-gradient(circle,rgba(108,161,56,.22) 0%,rgba(108,161,56,0) 70%)}
    .programs-front .showcase::after{width:220px;height:220px;bottom:-110px;left:-80px;background:radial-gradient(circle,rgba(47,143,131,.18) 0%,rgba(47,143,131,0) 72%)}
    .programs-front .showcase-head{position:relative;z-index:1;display:flex;align-items:flex-end;justify-content:space-between;gap:18px}
    .programs-front .showcase h1{margin:0;font-size:clamp(34px,4vw,54px);line-height:1;color:#153122}
    .programs-front .showcase-pillbar{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:12px;margin-top:22px}
    .programs-front .showcase-pill{display:inline-flex;align-items:center;justify-content:center;padding:11px 16px;border-radius:999px;background:rgba(255,255,255,.9);border:1px solid rgba(21,49,34,.08);box-shadow:0 10px 24px rgba(21,49,34,.06);text-decoration:none;font-size:14px;font-weight:700;color:#264032}
    .programs-front .program-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
    .programs-front .program-card{position:relative;overflow:hidden;padding:24px;border-radius:30px;background:var(--card-surface,#fff);border:1px solid rgba(21,49,34,.08);box-shadow:0 22px 52px rgba(21,49,34,.08)}
    .programs-front .program-card::before{content:"";position:absolute;inset:0 auto 0 0;width:6px;background:var(--card-accent,#6ca138)}
    .programs-front .program-card::after{content:"";position:absolute;top:-60px;right:-50px;width:170px;height:170px;border-radius:999px;background:var(--card-soft,rgba(108,161,56,.14))}
    .programs-front .program-head,.programs-front .exercise-head{position:relative;z-index:1;display:flex;align-items:flex-start;justify-content:space-between;gap:14px}
    .programs-front .program-kicker{display:inline-flex;align-items:center;padding:8px 12px;border-radius:999px;background:rgba(255,255,255,.84);border:1px solid rgba(21,49,34,.08);font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--card-accent,#6ca138)}
    .programs-front .program-title{margin:16px 0 0;font-size:28px;line-height:1.08;color:#153122}
    .programs-front .program-meta{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:10px;margin:20px 0}
    .programs-front .meta-pill,.programs-front .exercise-pill{display:inline-flex;align-items:center;justify-content:center;padding:10px 13px;border-radius:999px;font-size:13px;font-weight:700}
    .programs-front .meta-pill{background:rgba(255,255,255,.86);border:1px solid rgba(21,49,34,.07);color:#335145}
    .programs-front .exercise-stack{position:relative;z-index:1;display:grid;gap:12px}
    .programs-front .exercise-card{padding:16px 18px;border-radius:22px;background:rgba(255,255,255,.74);border:1px solid rgba(21,49,34,.08);backdrop-filter:blur(6px)}
    .programs-front .exercise-title{margin:0;font-size:18px;line-height:1.2;color:#153122}
    .programs-front .exercise-number{display:inline-flex;align-items:center;justify-content:center;min-width:38px;height:38px;padding:0 12px;border-radius:999px;background:var(--card-soft,rgba(108,161,56,.14));color:var(--card-accent,#6ca138);font-size:13px;font-weight:800}
    .programs-front .exercise-pills{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}
    .programs-front .exercise-pill{background:#fff;color:#486256;border:1px solid rgba(21,49,34,.07)}
    .programs-front .empty-card{padding:34px;border-radius:30px;background:#fff;box-shadow:0 20px 48px rgba(21,49,34,.08);text-align:center}
    .programs-front .empty-card h3,.programs-front .empty-card h4{margin:0;color:#153122}
    .programs-front .empty-card p{margin:10px 0 0;color:#60706a}
    .programs-front .alert-box{padding:18px 20px;border-radius:22px;background:#fff1f2;color:#9f1239;box-shadow:0 18px 42px rgba(159,18,57,.08)}
    @media (max-width:980px){.programs-front .showcase-head,.programs-front .program-head,.programs-front .exercise-head{flex-direction:column;align-items:flex-start}.programs-front .program-grid{grid-template-columns:1fr}}
</style>

<div class="programs-front">
    <section class="showcase">
        <div class="showcase-head">
            <div>
                <span class="monta-eyebrow">Programs</span>
                <h1>Programs & Exercises</h1>
            </div>
        </div>

        <?php if ($programs !== []): ?>
            <div class="showcase-pillbar">
                <?php foreach ($programs as $program): ?>
                    <a class="showcase-pill" href="#program-<?= htmlspecialchars((string) ($program->getId() ?? 0), ENT_QUOTES, 'UTF-8') ?>">
                        <?= htmlspecialchars($program->getTitle(), ENT_QUOTES, 'UTF-8') ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
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
        <section class="program-grid">
            <?php foreach ($programs as $index => $program): ?>
                <?php
                $theme = $palette[$index % count($palette)];
                $programId = (int) ($program->getId() ?? 0);
                $programTitle = $program->getTitle();
                $programGoalType = (string) ($program->getGoalType() ?? '');
                $programDescription = (string) ($program->getDescription() ?? '');
                $programDurationWeeks = $program->getDurationWeeks();
                $programExercises = $program->getExercises();
                $exerciseCount = $program->getExerciseCount() > 0 ? $program->getExerciseCount() : count($programExercises);
                ?>
                <article
                    class="program-card"
                    id="program-<?= htmlspecialchars((string) $programId, ENT_QUOTES, 'UTF-8') ?>"
                    style="--card-accent: <?= htmlspecialchars($theme['accent'], ENT_QUOTES, 'UTF-8') ?>; --card-surface: <?= htmlspecialchars($theme['surface'], ENT_QUOTES, 'UTF-8') ?>; --card-soft: <?= htmlspecialchars($theme['soft'], ENT_QUOTES, 'UTF-8') ?>;"
                >
                    <div class="program-head">
                        <div>
                            <span class="program-kicker"><?= htmlspecialchars($programGoalType !== '' ? $programGoalType : 'General', ENT_QUOTES, 'UTF-8') ?></span>
                            <h2 class="program-title"><?= htmlspecialchars($programTitle, ENT_QUOTES, 'UTF-8') ?></h2>
                            <?php if ($programDescription !== ''): ?>
                                <p class="mt-3 mb-0" style="position:relative;z-index:1;color:#486256;max-width:52ch;"><?= htmlspecialchars($programDescription, ENT_QUOTES, 'UTF-8') ?></p>
                            <?php endif; ?>
                        </div>
                        <span class="meta-pill"><?= htmlspecialchars((string) $programDurationWeeks, ENT_QUOTES, 'UTF-8') ?> weeks</span>
                    </div>

                    <div class="program-meta">
                        <span class="meta-pill"><?= htmlspecialchars((string) $exerciseCount, ENT_QUOTES, 'UTF-8') ?> exercises</span>
                        <span class="meta-pill">Program #<?= htmlspecialchars((string) $programId, ENT_QUOTES, 'UTF-8') ?></span>
                    </div>

                    <?php if ($programExercises === []): ?>
                        <div class="empty-card">
                            <h4>No exercises yet</h4>
                            <p>This program has no exercises for now.</p>
                        </div>
                    <?php else: ?>
                        <div class="exercise-stack">
                            <?php foreach ($programExercises as $exerciseIndex => $exercise): ?>
                                <?php
                                $exerciseDescription = (string) ($exercise->getDescription() ?? '');
                                $exerciseMuscleGroup = (string) ($exercise->getMuscleGroup() ?? '');
                                ?>
                                <div class="exercise-card">
                                    <div class="exercise-head">
                                        <h3 class="exercise-title"><?= htmlspecialchars($exercise->getName(), ENT_QUOTES, 'UTF-8') ?></h3>
                                        <span class="exercise-number"><?= htmlspecialchars((string) ($exerciseIndex + 1), ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>

                                    <?php if ($exerciseDescription !== ''): ?>
                                        <p class="mb-0 mt-2" style="color:#5a6e64;"><?= htmlspecialchars($exerciseDescription, ENT_QUOTES, 'UTF-8') ?></p>
                                    <?php endif; ?>

                                    <div class="exercise-pills">
                                        <span class="exercise-pill"><?= htmlspecialchars($exerciseMuscleGroup !== '' ? $exerciseMuscleGroup : 'General', ENT_QUOTES, 'UTF-8') ?></span>
                                        <span class="exercise-pill"><?= htmlspecialchars((string) $exercise->getSets(), ENT_QUOTES, 'UTF-8') ?> sets</span>
                                        <span class="exercise-pill"><?= htmlspecialchars((string) $exercise->getReps(), ENT_QUOTES, 'UTF-8') ?> reps</span>
                                        <span class="exercise-pill"><?= htmlspecialchars((string) $exercise->getRestSeconds(), ENT_QUOTES, 'UTF-8') ?> sec rest</span>
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
