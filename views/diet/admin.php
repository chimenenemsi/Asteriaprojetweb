<?php
/**
 * View for Diet Management Admin
 */
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

<style>
    :root { --primary: #2e7d32; --primary-light: #66bb6a; --bg: #f4f9f4; --card-bg: #ffffff; --text: #1a3a1a; }
    
    .diet-admin-wrap { color: var(--text); padding: 20px; }
    .navbar-diet { background: white; padding: 20px 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; border-radius: 20px; }
    .navbar-brand-diet { color: var(--primary); font-weight: 800; font-size: 26px; }
    
    .page-section { display: none; animation: slideUp 0.5s ease-out; }
    .page-section.active { display: block; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; border-radius: 24px; padding: 25px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); text-align: center; border-bottom: 6px solid var(--primary); transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); }
    .stat-card h3 { color: var(--primary); font-size: 2.5rem; margin: 10px 0; font-weight: 800; }
    .stat-card p { color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; }

    .card-diet { background: white; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); margin-bottom: 30px; overflow: hidden; border: 1px solid rgba(0,0,0,0.02); }
    .card-header-diet { background: var(--primary-light); color: white; padding: 15px 25px; display: flex; justify-content: space-between; align-items: center; }
    .card-body-diet { padding: 20px; }
    
    .diet-tabs { display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap; }
    .diet-tab { padding: 10px 20px; background: #eee; border-radius: 10px; cursor: pointer; font-weight: 600; transition: 0.3s; }
    .diet-tab.active { background: var(--primary); color: white; }

    .table-diet { width: 100%; border-collapse: collapse; }
    .table-diet th { background: #fcfdfc; padding: 15px; text-align: left; color: var(--primary); font-weight: 700; border-bottom: 2px solid #edf5ed; font-size: 13px; }
    .table-diet td { padding: 15px; border-bottom: 1px solid #f8f8f8; vertical-align: middle; font-size: 13px; color: #444; }

    .btn-diet { padding: 10px 20px; border: none; border-radius: 12px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-size: 13px; }
    .btn-primary-diet { background: var(--primary); color: white; }
    .btn-info-diet { background: #e3f2fd; color: #1976d2; }
    .btn-danger-diet { background: #ffebee; color: #c62828; }
    
    .modal-diet { display: none; position: fixed; z-index: 2000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(26,58,26,0.6); backdrop-filter: blur(8px); }
    .modal-diet.show { display: flex; align-items: center; justify-content: center; }
    .modal-content-diet { background: white; width: 90%; max-width: 600px; border-radius: 25px; box-shadow: 0 40px 100px rgba(0,0,0,0.3); }
    .modal-header-diet { padding: 20px 30px; background: var(--primary); color: white; border-radius: 25px 25px 0 0; display: flex; justify-content: space-between; align-items: center; }
    .modal-body-diet { padding: 30px; max-height: 70vh; overflow-y: auto; }
    
    .form-group-diet { margin-bottom: 15px; }
    .form-group-diet label { display: block; margin-bottom: 8px; font-weight: 600; font-size: 13px; }
    .form-control-diet { width: 100%; padding: 12px 15px; border: 2px solid #edf2ed; border-radius: 12px; font-size: 14px; }

    .chart-container-diet { background: white; border-radius: 24px; padding: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); margin-bottom: 20px; height: 300px; }
</style>

<div class="diet-admin-wrap">
    <div class="navbar-diet">
        <div class="navbar-brand-diet" id="section-title-diet">Diet Dashboard</div>
        <div class="diet-tabs">
            <div class="diet-tab active" onclick="showDietSection('dashboard', this)">Dashboard</div>
            <div class="diet-tab" onclick="showDietSection('plans', this)">Programmes</div>
            <div class="diet-tab" onclick="showDietSection('recipes', this)">Recettes</div>
            <div class="diet-tab" onclick="showDietSection('calendar', this)">Calendrier</div>
        </div>
    </div>

    <!-- DASHBOARD -->
    <div id="diet-dashboard" class="page-section active">
        <div class="stat-grid">
            <div class="stat-card">
                <p>Programmes Actifs</p>
                <h3 id="stat-active-plans">0</h3>
            </div>
            <div class="stat-card">
                <p>Total Recettes</p>
                <h3 id="stat-total-recipes">0</h3>
            </div>
            <div class="stat-card">
                <p>Niveau Moyen</p>
                <h3>INT.</h3>
            </div>
        </div>

        <div class="row" style="display: flex; gap: 20px; margin-bottom: 30px;">
            <div style="flex: 1;">
                <div class="chart-container-diet">
                    <h5 style="margin-bottom: 15px; color: var(--primary);">📊 Répartition par Niveau</h5>
                    <canvas id="chart-levels"></canvas>
                </div>
            </div>
            <div style="flex: 1;">
                <div class="chart-container-diet">
                    <h5 style="margin-bottom: 15px; color: var(--primary);">🍕 Types de Repas</h5>
                    <canvas id="chart-meals"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- PLANS SECTION -->
    <div id="diet-plans" class="page-section">
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <button class="btn-diet btn-primary-diet" onclick="openPlanModal()">➕ Nouveau Programme</button>
            <input type="text" id="search-plans" class="form-control-diet" style="width: 250px;" placeholder="Rechercher..." onkeyup="debounceDietSearch('plans')">
        </div>
        <div class="card-diet">
            <div class="card-body-diet"><div id="plans-list"></div></div>
        </div>
    </div>

    <!-- RECIPES SECTION -->
    <div id="diet-recipes" class="page-section">
        <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
            <button class="btn-diet btn-primary-diet" onclick="openRecipeModal()">➕ Nouvelle Recette</button>
            <input type="text" id="search-recipes" class="form-control-diet" style="width: 250px;" placeholder="Rechercher..." onkeyup="debounceDietSearch('recipes')">
        </div>
        <div class="card-diet">
            <div class="card-body-diet"><div id="recipes-list"></div></div>
        </div>
    </div>

    <!-- CALENDAR SECTION -->
    <div id="diet-calendar" class="page-section">
        <div style="margin-bottom: 20px; display: flex; gap: 15px; align-items: center;">
            <select id="calendar-plan-select" class="form-control-diet" style="width: 250px;" onchange="loadDietCalendar(this.value)">
                <option value="">Sélectionner un programme...</option>
            </select>
            <div id="calendar-info" style="font-weight: 600; color: var(--primary);"></div>
        </div>
        <div id="calendar-widget"></div>
    </div>
</div>

<!-- MODALS -->
<div id="plan-modal" class="modal-diet">
    <div class="modal-content-diet">
        <div class="modal-header-diet">
            <h5 id="plan-modal-title">Nouveau Programme</h5>
            <button style="background:none; border:none; color:white; font-size:24px; cursor:pointer;" onclick="closeDietModal('plan-modal')">&times;</button>
        </div>
        <form id="plan-form">
            <div class="modal-body-diet">
                <input type="hidden" id="plan-id">
                <div class="form-group-diet">
                    <label>Titre *</label>
                    <input type="text" id="plan-title" class="form-control-diet">
                </div>
                <div class="form-group-diet">
                    <label>Objectif *</label>
                    <input type="text" id="plan-goal" class="form-control-diet">
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group-diet" style="flex:1">
                        <label>Durée (jours)</label>
                        <input type="number" id="plan-duration" class="form-control-diet" value="30">
                    </div>
                    <div class="form-group-diet" style="flex:1">
                        <label>Calories/jour</label>
                        <input type="number" id="plan-calories" class="form-control-diet">
                    </div>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group-diet" style="flex:1">
                        <label>Niveau</label>
                        <select id="plan-level" class="form-control-diet">
                            <option value="BEGINNER">Débutant</option>
                            <option value="INTERMEDIATE">Intermédiaire</option>
                            <option value="ADVANCED">Avancé</option>
                        </select>
                    </div>
                    <div class="form-group-diet" style="flex:1">
                        <label>Statut</label>
                        <select id="plan-status" class="form-control-diet">
                            <option value="ACTIVE">Actif</option>
                            <option value="INACTIVE">Inactif</option>
                        </select>
                    </div>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #eee; text-align: right;">
                <button type="button" class="btn-diet" onclick="closeDietModal('plan-modal')">Annuler</button>
                <button type="submit" class="btn-diet btn-primary-diet">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<div id="recipe-modal" class="modal-diet">
    <div class="modal-content-diet">
        <div class="modal-header-diet">
            <h5 id="recipe-modal-title">Nouvelle Recette</h5>
            <button style="background:none; border:none; color:white; font-size:24px; cursor:pointer;" onclick="closeDietModal('recipe-modal')">&times;</button>
        </div>
        <form id="recipe-form">
            <div class="modal-body-diet">
                <input type="hidden" id="recipe-id">
                <div class="form-group-diet">
                    <label>Nom de la Recette *</label>
                    <input type="text" id="recipe-name" class="form-control-diet">
                </div>
                <div class="form-group-diet">
                    <label>Programme Associé *</label>
                    <select id="recipe-plan" class="form-control-diet" onchange="getDietAiSuggestion(this.value)"></select>
                </div>
                <div style="display: flex; gap: 15px;">
                    <div class="form-group-diet" style="flex:1">
                        <label>Type de Repas</label>
                        <select id="recipe-meal" class="form-control-diet">
                            <option value="BREAKFAST">Petit Déjeuner</option>
                            <option value="LUNCH">Déjeuner</option>
                            <option value="DINNER">Dîner</option>
                            <option value="SNACK">Snack</option>
                        </select>
                    </div>
                    <div class="form-group-diet" style="flex:1">
                        <label>Jour n°</label>
                        <input type="number" id="recipe-day" class="form-control-diet" value="1">
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px;">
                    <div class="form-group-diet"><label>Calories</label><input type="number" id="recipe-cal" class="form-control-diet"></div>
                    <div class="form-group-diet"><label>Protéines (g)</label><input type="number" id="recipe-prot" class="form-control-diet"></div>
                    <div class="form-group-diet"><label>Glucides (g)</label><input type="number" id="recipe-carb" class="form-control-diet"></div>
                    <div class="form-group-diet"><label>Lipides (g)</label><input type="number" id="recipe-fat" class="form-control-diet"></div>
                </div>
            </div>
            <div style="padding: 20px; border-top: 1px solid #eee; text-align: right;">
                <button type="button" class="btn-diet" onclick="closeDietModal('recipe-modal')">Annuler</button>
                <button type="submit" class="btn-diet btn-primary-diet">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<script>
    const DIET_API = 'index.php';
    let dietState = { 
        section: 'dashboard',
        plans: { search: '', sort: 'id', order: 'DESC' },
        recipes: { search: '', sort: 'r.id', order: 'DESC' }
    };

    function showDietSection(section, el) {
        document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
        document.querySelectorAll('.diet-tab').forEach(a => a.classList.remove('active'));
        document.getElementById('diet-' + section).classList.add('active');
        el.classList.add('active');
        
        if (section === 'dashboard') loadDietDashboard();
        if (section === 'plans') loadDietPlans();
        if (section === 'recipes') loadDietRecipes();
        if (section === 'calendar') prepareDietCalendar();
    }

    let dietCharts = {};
    async function loadDietDashboard() {
        try {
            const [pRes, rRes, pStatsRes, rStatsRes] = await Promise.all([
                fetch(`${DIET_API}?controller=DietPlan&action=obtenirTous`),
                fetch(`${DIET_API}?controller=Recipe&action=obtenirTous`),
                fetch(`${DIET_API}?controller=DietPlan&action=obtenirStats`),
                fetch(`${DIET_API}?controller=Recipe&action=obtenirStats`)
            ]);
            
            const pData = await pRes.json();
            const rData = await rRes.json();
            const pStats = await pStatsRes.json();
            const rStats = await rStatsRes.json();

            if (pData.success) document.getElementById('stat-active-plans').textContent = pData.plans.filter(p => p.status === 'ACTIVE').length;
            if (rData.success) document.getElementById('stat-total-recipes').textContent = rData.recipes.length;

            renderDietCharts(pStats, rStats);
        } catch (e) { console.error(e); }
    }

    function renderDietCharts(pStats, rStats) {
        if (dietCharts.levels) dietCharts.levels.destroy();
        if (dietCharts.meals) dietCharts.meals.destroy();

        const levelCtx = document.getElementById('chart-levels').getContext('2d');
        dietCharts.levels = new Chart(levelCtx, {
            type: 'pie',
            data: {
                labels: pStats.levels.map(l => l.label),
                datasets: [{
                    data: pStats.levels.map(l => l.value),
                    backgroundColor: ['#2e7d32', '#66bb6a', '#a5d6a7', '#c8e6c9']
                }]
            },
            options: { maintainAspectRatio: false }
        });

        const mealCtx = document.getElementById('chart-meals').getContext('2d');
        dietCharts.meals = new Chart(mealCtx, {
            type: 'doughnut',
            data: {
                labels: rStats.meals.map(m => m.label),
                datasets: [{
                    data: rStats.meals.map(m => m.value),
                    backgroundColor: ['#ff9800', '#2196f3', '#4caf50', '#9c27b0']
                }]
            },
            options: { maintainAspectRatio: false }
        });
    }

    async function loadDietPlans() {
        const { search, sort, order } = dietState.plans;
        try {
            const resp = await fetch(`${DIET_API}?controller=DietPlan&action=obtenirTous&search=${search}&sort=${sort}&order=${order}`);
            const data = await resp.json();
            if (data.success) {
                let html = `<table class="table-diet">
                    <thead><tr><th>Titre</th><th>Objectif</th><th>Durée</th><th>Cal/j</th><th>Niveau</th><th>Actions</th></tr></thead>
                    <tbody>`;
                data.plans.forEach(p => {
                    html += `<tr>
                        <td><strong>${p.title}</strong></td>
                        <td>${p.goal}</td>
                        <td>${p.duration_days}j</td>
                        <td>${p.target_calories_per_day}</td>
                        <td>${p.level}</td>
                        <td>
                            <button class="btn-diet btn-info-diet" onclick="editDietPlan(${p.id})">✎</button>
                            <button class="btn-diet btn-danger-diet" onclick="deleteDietPlan(${p.id})">✗</button>
                        </td>
                    </tr>`;
                });
                document.getElementById('plans-list').innerHTML = html + '</tbody></table>';
            }
        } catch (e) { console.error(e); }
    }

    async function loadDietRecipes() {
        const { search, sort, order } = dietState.recipes;
        try {
            const resp = await fetch(`${DIET_API}?controller=Recipe&action=obtenirTous&search=${search}&sort=${sort}&order=${order}`);
            const data = await resp.json();
            if (data.success) {
                let html = `<table class="table-diet">
                    <thead><tr><th>Nom</th><th>Programme</th><th>Type</th><th>Jour</th><th>Cal</th><th>Actions</th></tr></thead>
                    <tbody>`;
                data.recipes.forEach(r => {
                    html += `<tr>
                        <td><strong>${r.name}</strong></td>
                        <td>${r.diet_plan_title}</td>
                        <td>${r.meal_type}</td>
                        <td>J-${r.day_number}</td>
                        <td>${r.calories}</td>
                        <td>
                            <button class="btn-diet btn-info-diet" onclick="editDietRecipe(${r.id})">✎</button>
                            <button class="btn-diet btn-danger-diet" onclick="deleteDietRecipe(${r.id})">✗</button>
                        </td>
                    </tr>`;
                });
                document.getElementById('recipes-list').innerHTML = html + '</tbody></table>';
            }
        } catch (e) { console.error(e); }
    }

    let dietFullCalendar;
    async function prepareDietCalendar() {
        try {
            const resp = await fetch(`${DIET_API}?controller=DietPlan&action=obtenirTous`);
            const data = await resp.json();
            if (data.success) {
                const select = document.getElementById('calendar-plan-select');
                select.innerHTML = '<option value="">Sélectionner un programme...</option>' + 
                    data.plans.map(p => `<option value="${p.id}">${p.title}</option>`).join('');
            }
        } catch (e) { console.error(e); }
    }

    async function loadDietCalendar(planId) {
        if (!planId) return;
        try {
            const resp = await fetch(`${DIET_API}?controller=DietPlan&action=obtenirCalendrier&id=${planId}`);
            const data = await resp.json();
            if (data.success) {
                const events = [];
                const startDate = new Date();
                data.calendar.forEach(day => {
                    const dateStr = new Date(startDate.getTime() + (day.day - 1) * 86400000).toISOString().split('T')[0];
                    day.recipes.forEach(r => {
                        events.push({ title: r.name, start: dateStr });
                    });
                });
                const calendarEl = document.getElementById('calendar-widget');
                if (dietFullCalendar) dietFullCalendar.destroy();
                dietFullCalendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    events: events
                });
                dietFullCalendar.render();
            }
        } catch (e) { console.error(e); }
    }

    function openPlanModal(plan = null) { 
        if (plan) {
            document.getElementById('plan-modal-title').textContent = 'Modifier le Programme';
            document.getElementById('plan-id').value = plan.id;
            document.getElementById('plan-title').value = plan.title;
            document.getElementById('plan-goal').value = plan.goal;
            document.getElementById('plan-duration').value = plan.duration_days;
            document.getElementById('plan-calories').value = plan.target_calories_per_day;
            document.getElementById('plan-level').value = plan.level;
            document.getElementById('plan-status').value = plan.status;
        } else {
            document.getElementById('plan-modal-title').textContent = 'Nouveau Programme';
            document.getElementById('plan-form').reset();
            document.getElementById('plan-id').value = '';
        }
        document.getElementById('plan-modal').classList.add('show'); 
    }

    function openRecipeModal(recipe = null) { 
        fetch(`${DIET_API}?controller=DietPlan&action=obtenirTous`).then(r => r.json()).then(data => {
            const select = document.getElementById('recipe-plan');
            select.innerHTML = data.plans.map(p => `<option value="${p.id}">${p.title}</option>`).join('');
            
            if (recipe) {
                document.getElementById('recipe-modal-title').textContent = 'Modifier la Recette';
                document.getElementById('recipe-id').value = recipe.id;
                document.getElementById('recipe-name').value = recipe.name;
                document.getElementById('recipe-plan').value = recipe.diet_plan_id;
                document.getElementById('recipe-meal').value = recipe.meal_type;
                document.getElementById('recipe-day').value = recipe.day_number;
                document.getElementById('recipe-cal').value = recipe.calories;
                document.getElementById('recipe-prot').value = recipe.proteins;
                document.getElementById('recipe-carb').value = recipe.carbs;
                document.getElementById('recipe-fat').value = recipe.fats;
            } else {
                document.getElementById('recipe-modal-title').textContent = 'Nouvelle Recette';
                document.getElementById('recipe-form').reset();
                document.getElementById('recipe-id').value = '';
            }
        });
        document.getElementById('recipe-modal').classList.add('show'); 
    }

    async function editDietPlan(id) {
        const resp = await fetch(`${DIET_API}?controller=DietPlan&action=obtenirTous`);
        const data = await resp.json();
        const plan = data.plans.find(p => p.id == id);
        if (plan) openPlanModal(plan);
    }

    async function deleteDietPlan(id) {
        if (!confirm('Supprimer ce programme ?')) return;
        await fetch(`${DIET_API}?controller=DietPlan&action=supprimer&id=${id}`);
        loadDietPlans();
        loadDietDashboard();
    }

    async function editDietRecipe(id) {
        const resp = await fetch(`${DIET_API}?controller=Recipe&action=obtenirTous`);
        const data = await resp.json();
        const recipe = data.recipes.find(r => r.id == id);
        if (recipe) openRecipeModal(recipe);
    }

    async function deleteDietRecipe(id) {
        if (!confirm('Supprimer cette recette ?')) return;
        await fetch(`${DIET_API}?controller=Recipe&action=supprimer&id=${id}`);
        loadDietRecipes();
        loadDietDashboard();
    }

    let dietSearchTimeout;
    function debounceDietSearch(type) {
        clearTimeout(dietSearchTimeout);
        dietSearchTimeout = setTimeout(() => {
            dietState[type].search = document.getElementById(`search-${type}`).value;
            if (type === 'plans') loadDietPlans();
            else loadDietRecipes();
        }, 300);
    }

    function closeDietModal(id) { document.getElementById(id).classList.remove('show'); }

    async function getDietAiSuggestion(planId) {
        if (!planId) return;
        const btn = document.querySelector('#recipe-form button[type="submit"]');
        const originalText = btn.textContent;
        btn.disabled = true;
        btn.textContent = 'IA en cours...';

        try {
            const resp = await fetch(`${DIET_API}?controller=OpenAI&action=proposerRecette&plan_id=${planId}`);
            const data = await resp.json();
            if (data.success && data.recipe) {
                const r = data.recipe;
                document.getElementById('recipe-name').value = r.name || '';
                document.getElementById('recipe-meal').value = r.meal_type || 'LUNCH';
                document.getElementById('recipe-cal').value = r.calories || 0;
                document.getElementById('recipe-prot').value = r.proteins || 0;
                document.getElementById('recipe-carb').value = r.carbs || 0;
                document.getElementById('recipe-fat').value = r.fats || 0;
            } else {
                alert("L'IA n'a pas pu générer de suggestion : " + (data.message || "Erreur inconnue"));
            }
        } catch (e) { 
            console.error(e);
            alert("Erreur lors de la communication avec l'IA.");
        } finally {
            btn.disabled = false;
            btn.textContent = originalText;
        }
    }

    document.getElementById('plan-form').onsubmit = async (e) => {
        e.preventDefault();
        const id = document.getElementById('plan-id').value;
        const data = {
            title: document.getElementById('plan-title').value,
            goal: document.getElementById('plan-goal').value,
            duration_days: document.getElementById('plan-duration').value,
            target_calories_per_day: document.getElementById('plan-calories').value,
            level: document.getElementById('plan-level').value,
            status: document.getElementById('plan-status').value
        };
        const action = id ? 'mettre_a_jour' : 'creer';
        const url = `${DIET_API}?controller=DietPlan&action=${action}${id ? '&id='+id : ''}`;
        await fetch(url, { method: 'POST', body: JSON.stringify(data) });
        closeDietModal('plan-modal'); loadDietPlans(); loadDietDashboard();
    };

    document.getElementById('recipe-form').onsubmit = async (e) => {
        e.preventDefault();
        const id = document.getElementById('recipe-id').value;
        const data = {
            name: document.getElementById('recipe-name').value,
            diet_plan_id: document.getElementById('recipe-plan').value,
            meal_type: document.getElementById('recipe-meal').value,
            day_number: document.getElementById('recipe-day').value,
            calories: document.getElementById('recipe-cal').value,
            proteins: document.getElementById('recipe-prot').value,
            carbs: document.getElementById('recipe-carb').value,
            fats: document.getElementById('recipe-fat').value
        };
        const action = id ? 'mettre_a_jour' : 'creer';
        const url = `${DIET_API}?controller=Recipe&action=${action}${id ? '&id='+id : ''}`;
        await fetch(url, { method: 'POST', body: JSON.stringify(data) });
        closeDietModal('recipe-modal'); loadDietRecipes(); loadDietDashboard();
    };

    loadDietDashboard();
</script>
