<?php
/**
 * Dashboard Client - Catalogue Nutrition et Régimes
 * Style asteria (Vert écologique)
 * Point d'accès: http://localhost/gestion-allergies/app/views/client-dashboard.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>asteria - Nutrition durable</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <style>
        :root { --primary: #2e7d32; --accent: #ffb300; --bg: #f8faf8; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--bg); color: #1a3a1a; }
        
        header { background: #2e7d32; color: white; padding: 20px 60px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .logo { font-size: 28px; font-weight: 800; letter-spacing: -1.5px; }
        .nav-link { color: white; text-decoration: none; font-weight: 600; background: rgba(255,255,255,0.1); padding: 12px 24px; border-radius: 15px; transition: 0.3s; }
        .nav-link:hover { background: rgba(255,255,255,0.2); transform: translateY(-2px); }

        .hero { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%); color: white; padding: 80px 60px; text-align: center; }
        .hero h1 { font-size: 3.5rem; margin-bottom: 20px; font-weight: 800; }
        .hero p { font-size: 1.3rem; opacity: 0.9; max-width: 800px; margin: 0 auto; }

        .container { max-width: 1400px; margin: 0 auto; padding: 60px; }
        
        .section-header { margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-end; }
        .section-header h2 { font-size: 2.2rem; font-weight: 800; color: var(--primary); }

        .grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 40px; }
        .card { background: white; border-radius: 30px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.05); transition: 0.4s; border: 1px solid rgba(0,0,0,0.02); }
        .card:hover { transform: translateY(-12px); box-shadow: 0 25px 60px rgba(0,0,0,0.1); }
        
        .card-img { height: 220px; background: #e8f5e9; display: flex; align-items: center; justify-content: center; font-size: 5rem; position: relative; }
        .level-tag { position: absolute; top: 20px; right: 20px; background: white; padding: 6px 15px; border-radius: 15px; font-size: 12px; font-weight: 800; color: var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        .card-content { padding: 30px; }
        .card-content h3 { font-size: 1.6rem; margin-bottom: 15px; font-weight: 800; }
        .card-meta { display: flex; gap: 20px; margin-bottom: 20px; color: #666; font-size: 14px; font-weight: 600; }
        .card-meta span { display: flex; align-items: center; gap: 6px; }

        .btn-view { width: 100%; padding: 16px; border-radius: 18px; border: none; background: var(--primary); color: white; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 15px; }
        .btn-view:hover { background: #1b5e20; box-shadow: 0 8px 20px rgba(46, 125, 50, 0.2); }

        /* Modal Details */
        .modal { display: none; position: fixed; z-index: 200; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; width: 95%; max-width: 900px; border-radius: 35px; padding: 45px; max-height: 85vh; overflow-y: auto; }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .modal-header h2 { font-size: 2.2rem; font-weight: 800; color: var(--primary); }

        .recipe-item { display: flex; gap: 20px; padding: 20px; background: #f9fdf9; border-radius: 20px; margin-bottom: 15px; align-items: center; }
        .recipe-day { background: var(--primary); color: white; width: 60px; height: 60px; border-radius: 15px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 800; font-size: 12px; }
        .recipe-day span { font-size: 20px; }
        .recipe-info { flex: 1; }
        .recipe-info h4 { margin-bottom: 5px; font-weight: 700; }
        .recipe-macros { font-size: 13px; color: #666; font-weight: 600; }
        .macro-badge { background: #e8f5e9; padding: 2px 8px; border-radius: 8px; color: var(--primary); margin-right: 5px; }
        
        .tab-btn { padding: 10px 25px; border: none; background: #eee; border-radius: 12px; cursor: pointer; font-weight: 600; font-family: inherit; transition: 0.3s; }
        .tab-btn.active { background: var(--primary); color: white; }
        
        .client-calendar-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 15px; margin-top: 20px; }
        .client-calendar-day { background: #f9fdf9; border: 1px solid #edf5ed; border-radius: 18px; padding: 15px; text-align: center; transition: 0.3s; cursor: pointer; }
        .client-calendar-day:hover { background: #e8f5e9; border-color: var(--primary); transform: translateY(-5px); }
        .client-calendar-day h5 { color: var(--primary); margin-bottom: 8px; font-weight: 800; }
        .client-calendar-meal-dots { display: flex; justify-content: center; gap: 4px; }
        .meal-dot { width: 8px; height: 8px; border-radius: 50%; background: #ccc; }
        .meal-dot.active { background: var(--primary); }

        footer { background: #1a3a1a; color: white; padding: 80px 60px; text-align: center; }
    </style>
</head>
<body>
    <header>
        <div class="logo">🌱 ASTERIA</div>
        <a href="admin.php" class="nav-link">⚙️ Accès Admin</a>
    </header>

    <div class="hero">
        <div class="container" style="padding: 0">
            <h1>Libérez votre plein potentiel bio</h1>
            <p>Des programmes de nutrition sur mesure, conçus pour votre santé et le respect de notre planète.</p>
        </div>
    </div>

    <div class="container">
        <div class="section-header">
            <h2>Nos Programmes Diététiques</h2>
        </div>

        <div id="plans-grid" class="grid"></div>
    </div>

    <footer>
        <div class="container" style="padding:0">
            <p style="font-weight: 800; font-size: 24px; margin-bottom: 20px;">ASTERIA</p>
            <p style="opacity: 0.6; font-weight: 400;">La plateforme leader de la nutrition durable et responsable.</p>
        </div>
    </footer>

    <!-- DETAILS MODAL -->
    <div id="details-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Détails du Programme</h2>
                <button style="background:none; border:none; font-size:40px; cursor:pointer;" onclick="closeModal()">&times;</button>
            </div>
            <div id="modal-body">
                <!-- Content injected here -->
            </div>
        </div>
    </div>

    <script>
        const API_BASE = '../../index.php';
        let allPlans = [];
        let allRecipes = [];

        async function init() {
            try {
                const [pRes, rRes] = await Promise.all([
                    fetch(`${API_BASE}?controller=DietPlan&action=obtenirTous`),
                    fetch(`${API_BASE}?controller=Recipe&action=obtenirTous`)
                ]);
                const pData = await pRes.json();
                const rData = await rRes.json();
                
                if (pData.success) allPlans = pData.plans;
                if (rData.success) allRecipes = rData.recipes;
                
                renderPlans();
            } catch (e) { console.error(e); }
        }

        function renderPlans() {
            const grid = document.getElementById('plans-grid');
            grid.innerHTML = allPlans.map(p => {
                const emoji = p.goal.includes('Poids') ? '⚖️' : p.goal.includes('Muscle') ? '💪' : '🍏';
                return `
                <div class="card">
                    <div class="card-img">
                        ${emoji}
                        <span class="level-tag">${p.level}</span>
                    </div>
                    <div class="card-content">
                        <h3>${p.title}</h3>
                        <p style="margin-bottom: 20px; color: #666; font-weight: 500;">${p.description}</p>
                        <div class="card-meta">
                            <span>⏱️ ${p.duration_days} jours</span>
                            <span>🔥 ${p.target_calories_per_day} kcal/j</span>
                        </div>
                        <button class="btn-view" onclick="openDetails(${p.id})">VOIR LE PROGRAMME</button>
                    </div>
                </div>
            `}).join('');
        }

        function openDetails(id) {
            const p = allPlans.find(x => x.id == id);
            const recipes = allRecipes.filter(r => r.diet_plan_id == id);
            
            let html = `
                <div style="margin-bottom: 30px; display: flex; gap: 40px; align-items: center;">
                    <div style="flex: 1">
                        <p style="font-size: 1.1rem; color: #444; margin-bottom: 20px;">${p.description}</p>
                        <div class="card-meta">
                             <span class="badge bg-success" style="font-size: 14px">🎯 Objectif: ${p.goal}</span>
                        </div>
                    </div>
                </div>
                <h3>Planning des Repas</h3>
                <div style="display: flex; gap: 10px; margin: 15px 0;">
                    <button class="tab-btn active" id="tab-list" onclick="switchView('list')">📋 Liste</button>
                    <button class="tab-btn" id="tab-calendar" onclick="switchView('calendar', ${id})">📅 Calendrier</button>
                </div>

                <div id="view-list" style="margin-top: 20px;">
                    ${recipes.length ? recipes.map(r => `
                        <div class="recipe-item">
                            <div class="recipe-day">JOUR<span>${r.day_number}</span></div>
                            <div class="recipe-info">
                                <span class="badge bg-warning" style="margin-bottom: 5px; display: inline-block;">${r.meal_type}</span>
                                <h4>${r.name}</h4>
                                <div class="recipe-macros">
                                    <span class="macro-badge">🔥 ${r.calories} kcal</span>
                                    <span class="macro-badge">🥩 ${r.proteins}g Prot</span>
                                    <span class="macro-badge">🍞 ${r.carbs}g Gluc</span>
                                    <span class="macro-badge">🥑 ${r.fats}g Lip</span>
                                </div>
                            </div>
                        </div>
                    `).join('') : '<p style="padding: 20px; text-align: center; background: #f5f5f5; border-radius: 15px;">Aucune recette associée pour le moment.</p>'}
                </div>
                <div id="view-calendar" style="display: none; margin-top: 20px;">
                    <!-- Calendar injected here -->
                </div>
            `;
            
            document.getElementById('modal-title').textContent = p.title;
            document.getElementById('modal-body').innerHTML = html;
            document.getElementById('details-modal').classList.add('show');
        }

        function closeModal() { document.getElementById('details-modal').classList.remove('show'); }
        
        async function switchView(view, planId) {
            document.getElementById('tab-list').classList.toggle('active', view === 'list');
            document.getElementById('tab-calendar').classList.toggle('active', view === 'calendar');
            document.getElementById('view-list').style.display = view === 'list' ? 'block' : 'none';
            document.getElementById('view-calendar').style.display = view === 'calendar' ? 'block' : 'none';
            
            if (view === 'calendar') {
                const grid = document.getElementById('view-calendar');
                grid.innerHTML = '<div style="text-align:center; padding:40px;">Chargement du calendrier...</div>';
                
                try {
                    const resp = await fetch(`${API_BASE}?controller=DietPlan&action=obtenirCalendrier&id=${planId}`);
                    const data = await resp.json();
                    if (data.success) {
                        let html = `<div class="client-calendar-grid">`;
                        data.calendar.forEach(day => {
                            const hasMeals = day.recipes.length > 0;
                            const mealTypes = day.recipes.map(r => r.meal_type);
                            
                            html += `
                                <div class="client-calendar-day">
                                    <h5>Jour ${day.day}</h5>
                                    <div class="client-calendar-meal-dots">
                                        <div class="meal-dot ${mealTypes.includes('BREAKFAST') ? 'active' : ''}" title="Petit-déjeuner"></div>
                                        <div class="meal-dot ${mealTypes.includes('LUNCH') ? 'active' : ''}" title="Déjeuner"></div>
                                        <div class="meal-dot ${mealTypes.includes('DINNER') ? 'active' : ''}" title="Dîner"></div>
                                        <div class="meal-dot ${mealTypes.includes('SNACK') ? 'active' : ''}" title="Snack"></div>
                                    </div>
                                    ${hasMeals ? `<div style="font-size:10px; margin-top:5px; color:#2e7d32; font-weight:700;">${day.recipes.length} repas</div>` : ''}
                                </div>
                            `;
                        });
                        grid.innerHTML = html + `</div>`;
                    }
                } catch (e) { grid.innerHTML = 'Erreur lors du chargement.'; }
            }
        }

        window.onclick = function(e) { if(e.target.classList.contains('modal')) closeModal(); }
        
        init();
    </script>
</body>
</html>
