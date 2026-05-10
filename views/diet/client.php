<?php
/**
 * View for Diet Management Client
 */
?>
<style>
    :root { --primary: #2e7d32; --accent: #ffb300; --bg: #f8faf8; }
    
    .diet-client-wrap { color: #1a3a1a; padding: 20px; }
    
    .hero-diet { background: linear-gradient(135deg, #1b5e20 0%, #2e7d32 100%); color: white; padding: 60px 40px; text-align: center; border-radius: 30px; margin-bottom: 40px; }
    .hero-diet h1 { font-size: 2.8rem; margin-bottom: 15px; font-weight: 800; }
    .hero-diet p { font-size: 1.1rem; opacity: 0.9; max-width: 700px; margin: 0 auto; }

    .grid-diet { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px; }
    .card-diet-client { background: white; border-radius: 25px; overflow: hidden; box-shadow: 0 15px 40px rgba(0,0,0,0.05); transition: 0.4s; border: 1px solid rgba(0,0,0,0.02); }
    .card-diet-client:hover { transform: translateY(-10px); box-shadow: 0 25px 60px rgba(0,0,0,0.1); }
    
    .card-img-diet { height: 180px; background: #e8f5e9; display: flex; align-items: center; justify-content: center; font-size: 4rem; position: relative; }
    .level-tag-diet { position: absolute; top: 15px; right: 15px; background: white; padding: 5px 12px; border-radius: 12px; font-size: 11px; font-weight: 800; color: var(--primary); box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    
    .card-content-diet { padding: 25px; }
    .card-content-diet h3 { font-size: 1.4rem; margin-bottom: 10px; font-weight: 800; }
    .card-meta-diet { display: flex; gap: 15px; margin-bottom: 15px; color: #666; font-size: 13px; font-weight: 600; }
    .card-meta-diet span { display: flex; align-items: center; gap: 5px; }

    .btn-view-diet { width: 100%; padding: 14px; border-radius: 15px; border: none; background: var(--primary); color: white; font-weight: 700; cursor: pointer; transition: 0.3s; font-size: 14px; }
    .btn-view-diet:hover { background: #1b5e20; box-shadow: 0 8px 20px rgba(46, 125, 50, 0.2); }

    /* Modal Details */
    .modal-diet-client { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.6); backdrop-filter: blur(8px); }
    .modal-diet-client.show { display: flex; align-items: center; justify-content: center; }
    .modal-content-diet-client { background: white; width: 95%; max-width: 800px; border-radius: 30px; padding: 35px; max-height: 80vh; overflow-y: auto; }
    .modal-header-diet-client { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; }
    .modal-header-diet-client h2 { font-size: 1.8rem; font-weight: 800; color: var(--primary); }

    .recipe-item-diet { display: flex; gap: 15px; padding: 15px; background: #f9fdf9; border-radius: 18px; margin-bottom: 12px; align-items: center; }
    .recipe-day-diet { background: var(--primary); color: white; width: 50px; height: 50px; border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; font-weight: 800; font-size: 10px; }
    .recipe-day-diet span { font-size: 18px; }
    .recipe-info-diet { flex: 1; }
    .recipe-info-diet h4 { margin-bottom: 4px; font-weight: 700; }
    .recipe-macros-diet { font-size: 12px; color: #666; font-weight: 600; }
    .macro-badge-diet { background: #e8f5e9; padding: 2px 6px; border-radius: 6px; color: var(--primary); margin-right: 4px; }
    
    .tab-btn-diet { padding: 8px 20px; border: none; background: #eee; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.3s; }
    .tab-btn-diet.active { background: var(--primary); color: white; }
    
    .client-calendar-grid-diet { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 12px; margin-top: 15px; }
    .client-calendar-day-diet { background: #f9fdf9; border: 1px solid #edf5ed; border-radius: 15px; padding: 12px; text-align: center; transition: 0.3s; cursor: pointer; }
    .client-calendar-day-diet:hover { background: #e8f5e9; border-color: var(--primary); transform: translateY(-3px); }
    .client-calendar-day-diet h5 { color: var(--primary); margin-bottom: 6px; font-weight: 800; }
    .client-calendar-meal-dots-diet { display: flex; justify-content: center; gap: 4px; }
    .meal-dot-diet { width: 6px; height: 6px; border-radius: 50%; background: #ccc; }
    .meal-dot-diet.active { background: var(--primary); }
</style>

<div class="diet-client-wrap">
    <div class="hero-diet">
        <h1>Libérez votre plein potentiel bio</h1>
        <p>Des programmes de nutrition sur mesure, conçus pour votre santé et le respect de notre planète.</p>
    </div>

    <div style="margin-bottom: 30px;">
        <h2 style="font-size: 1.8rem; font-weight: 800; color: var(--primary); margin-bottom: 20px;">Nos Programmes Diététiques</h2>
        <div id="plans-grid-diet" class="grid-diet"></div>
    </div>
</div>

<!-- DETAILS MODAL -->
<div id="details-modal-client" class="modal-diet-client">
    <div class="modal-content-diet-client">
        <div class="modal-header-diet-client">
            <h2 id="modal-title-diet">Détails du Programme</h2>
            <button style="background:none; border:none; font-size:32px; cursor:pointer;" onclick="closeDietClientModal()">&times;</button>
        </div>
        <div id="modal-body-diet">
            <!-- Content injected here -->
        </div>
    </div>
</div>

<script>
    const CLIENT_DIET_API = 'index.php';
    let clientPlans = [];
    let clientRecipes = [];

    async function initDietClient() {
        try {
            const [pRes, rRes] = await Promise.all([
                fetch(`${CLIENT_DIET_API}?controller=DietPlan&action=obtenirTous`),
                fetch(`${CLIENT_DIET_API}?controller=Recipe&action=obtenirTous`)
            ]);
            const pData = await pRes.json();
            const rData = await rRes.json();
            
            if (pData.success) clientPlans = pData.plans;
            if (rData.success) clientRecipes = rData.recipes;
            
            renderDietClientPlans();
        } catch (e) { console.error(e); }
    }

    function renderDietClientPlans() {
        const grid = document.getElementById('plans-grid-diet');
        grid.innerHTML = clientPlans.map(p => {
            const emoji = p.goal && p.goal.includes('Poids') ? '⚖️' : (p.goal && p.goal.includes('Muscle') ? '💪' : '🍏');
            return `
            <div class="card-diet-client">
                <div class="card-img-diet">
                    ${emoji}
                    <span class="level-tag-diet">${p.level}</span>
                </div>
                <div class="card-content-diet">
                    <h3>${p.title}</h3>
                    <p style="margin-bottom: 15px; color: #666; font-size: 14px;">${p.description || ''}</p>
                    <div class="card-meta-diet">
                        <span>⏱️ ${p.duration_days} jours</span>
                        <span>🔥 ${p.target_calories_per_day} kcal/j</span>
                    </div>
                    <button class="btn-view-diet" onclick="openDietDetails(${p.id})">VOIR LE PROGRAMME</button>
                </div>
            </div>
        `}).join('');
    }

    function openDietDetails(id) {
        const p = clientPlans.find(x => x.id == id);
        const recipes = clientRecipes.filter(r => r.diet_plan_id == id);
        
        let html = `
            <div style="margin-bottom: 20px;">
                <p style="font-size: 1rem; color: #444; margin-bottom: 15px;">${p.description || ''}</p>
                <span class="badge" style="background: #e8f5e9; color: #2e7d32; padding: 5px 10px; border-radius: 8px;">🎯 Objectif: ${p.goal}</span>
            </div>
            <h3>Planning des Repas</h3>
            <div style="display: flex; gap: 10px; margin: 15px 0;">
                <button class="tab-btn-diet active" id="tab-list-diet" onclick="switchDietView('list', ${id})">📋 Liste</button>
                <button class="tab-btn-diet" id="tab-calendar-diet" onclick="switchDietView('calendar', ${id})">📅 Calendrier</button>
            </div>

            <div id="view-list-diet" style="margin-top: 15px;">
                ${recipes.length ? recipes.map(r => `
                    <div class="recipe-item-diet">
                        <div class="recipe-day-diet">JOUR<span>${r.day_number}</span></div>
                        <div class="recipe-info-diet">
                            <span style="background: #fff3e0; color: #e65100; padding: 2px 8px; border-radius: 8px; font-size: 11px; font-weight: 700;">${r.meal_type}</span>
                            <h4 style="margin-top: 5px;">${r.name}</h4>
                            <div class="recipe-macros-diet">
                                <span class="macro-badge-diet">🔥 ${r.calories} kcal</span>
                                <span class="macro-badge-diet">🥩 ${r.proteins}g Prot</span>
                                <span class="macro-badge-diet">🍞 ${r.carbs}g Gluc</span>
                                <span class="macro-badge-diet">🥑 ${r.fats}g Lip</span>
                            </div>
                        </div>
                    </div>
                `).join('') : '<p style="padding: 20px; text-align: center; background: #f5f5f5; border-radius: 15px;">Aucune recette associée.</p>'}
            </div>
            <div id="view-calendar-diet" style="display: none; margin-top: 15px;"></div>
        `;
        
        document.getElementById('modal-title-diet').textContent = p.title;
        document.getElementById('modal-body-diet').innerHTML = html;
        document.getElementById('details-modal-client').classList.add('show');
    }

    function closeDietClientModal() { document.getElementById('details-modal-client').classList.remove('show'); }
    
    async function switchDietView(view, planId) {
        document.getElementById('tab-list-diet').classList.toggle('active', view === 'list');
        document.getElementById('tab-calendar-diet').classList.toggle('active', view === 'calendar');
        document.getElementById('view-list-diet').style.display = view === 'list' ? 'block' : 'none';
        document.getElementById('view-calendar-diet').style.display = view === 'calendar' ? 'block' : 'none';
        
        if (view === 'calendar') {
            const grid = document.getElementById('view-calendar-diet');
            grid.innerHTML = '<div style="text-align:center; padding:20px;">Chargement...</div>';
            
            try {
                const resp = await fetch(`${CLIENT_DIET_API}?controller=DietPlan&action=obtenirCalendrier&id=${planId}`);
                const data = await resp.json();
                if (data.success) {
                    let html = `<div class="client-calendar-grid-diet">`;
                    data.calendar.forEach(day => {
                        const mealTypes = day.recipes.map(r => r.meal_type);
                        html += `
                            <div class="client-calendar-day-diet">
                                <h5>Jour ${day.day}</h5>
                                <div class="client-calendar-meal-dots-diet">
                                    <div class="meal-dot-diet ${mealTypes.includes('BREAKFAST') ? 'active' : ''}"></div>
                                    <div class="meal-dot-diet ${mealTypes.includes('LUNCH') ? 'active' : ''}"></div>
                                    <div class="meal-dot-diet ${mealTypes.includes('DINNER') ? 'active' : ''}"></div>
                                    <div class="meal-dot-diet ${mealTypes.includes('SNACK') ? 'active' : ''}"></div>
                                </div>
                            </div>
                        `;
                    });
                    grid.innerHTML = html + `</div>`;
                }
            } catch (e) { grid.innerHTML = 'Erreur.'; }
        }
    }

    initDietClient();
</script>
