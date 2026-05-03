<?php
/**
 * Dashboard Admin - Gestion de Nutrition (Régimes & Recettes)
 * Style asteria (Vert écologique)
 * Point d'accès: http://localhost/gestion-allergies/app/views/admin.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - asteria Diet Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.23/jspdf.plugin.autotable.min.js"></script>
    <style>
        :root { --primary: #2e7d32; --primary-light: #66bb6a; --bg: #f4f9f4; --card-bg: #ffffff; --text: #1a3a1a; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Outfit', sans-serif; background-color: var(--bg); color: var(--text); display: flex; min-height: 100vh; }
        
        .sidebar { width: 280px; background: var(--primary); color: white; padding: 30px; transition: all 0.3s; position: sticky; top: 0; height: 100vh; }
        .sidebar h2 { margin-bottom: 40px; font-size: 24px; text-align: center; font-weight: 800; letter-spacing: -1px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 20px; }
        .sidebar a { display: flex; align-items: center; color: white; text-decoration: none; margin: 12px 0; padding: 14px 18px; border-radius: 12px; transition: all 0.3s; font-size: 15px; font-weight: 600; }
        .sidebar a:hover, .sidebar a.active { background: rgba(255,255,255,0.15); transform: translateX(8px); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        
        .main { flex: 1; padding: 40px; overflow-y: auto; }
        .navbar { background: white; padding: 20px 35px; box-shadow: 0 10px 30px rgba(0,0,0,0.03); margin-bottom: 40px; display: flex; justify-content: space-between; align-items: center; border-radius: 20px; }
        .navbar-brand { color: var(--primary); font-weight: 800; font-size: 26px; }
        
        .page-section { display: none; animation: slideUp 0.5s ease-out; }
        .page-section.active { display: block; }
        @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; margin-bottom: 40px; }
        .stat-card { background: white; border-radius: 24px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); text-align: center; border-bottom: 6px solid var(--primary); transition: 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
        .stat-card h3 { color: var(--primary); font-size: 3rem; margin: 10px 0; font-weight: 800; }
        .stat-card p { color: #666; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 1px; }

        .card { background: white; border-radius: 24px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); margin-bottom: 40px; overflow: hidden; border: 1px solid rgba(0,0,0,0.02); }
        .card-header { background: var(--primary-light); color: white; padding: 22px 30px; display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 0; }
        
        .table { width: 100%; border-collapse: collapse; }
        .table th { background: #fcfdfc; padding: 20px; text-align: left; color: var(--primary); font-weight: 700; border-bottom: 2px solid #edf5ed; font-size: 14px; }
        .table td { padding: 20px; border-bottom: 1px solid #f8f8f8; vertical-align: middle; font-size: 14px; color: #444; }
        .table tr:hover { background: #fdfefd; }

        .btn { padding: 12px 24px; border: none; border-radius: 14px; cursor: pointer; font-weight: 600; transition: all 0.3s; font-family: inherit; font-size: 14px; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: #1b5e20; transform: translateY(-2px); box-shadow: 0 8px 20px rgba(46, 125, 50, 0.2); }
        .btn-info { background: #e3f2fd; color: #1976d2; margin-right: 5px; }
        .btn-danger { background: #ffebee; color: #c62828; }
        
        .badge { padding: 6px 14px; border-radius: 25px; font-size: 0.7rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.5px; }
        .bg-success { background: #e8f5e9; color: #2e7d32; }
        .bg-warning { background: #fff3e0; color: #e65100; }
        .bg-danger { background: #ffebee; color: #c62828; }

        /* Modal */
        .modal { display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(26,58,26,0.6); backdrop-filter: blur(8px); }
        .modal.show { display: flex; align-items: center; justify-content: center; }
        .modal-content { background: white; width: 90%; max-width: 650px; border-radius: 30px; box-shadow: 0 40px 100px rgba(0,0,0,0.3); animation: pop 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        @keyframes pop { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .modal-header { padding: 25px 35px; background: var(--primary); color: white; border-radius: 30px 30px 0 0; display: flex; justify-content: space-between; align-items: center; }
        .modal-body { padding: 35px; max-height: 75vh; overflow-y: auto; }
        .modal-footer { padding: 20px 35px; border-top: 1px solid #eee; text-align: right; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 10px; font-weight: 600; color: #333; font-size: 14px; }
        .form-control { width: 100%; padding: 14px 20px; border: 2px solid #edf2ed; border-radius: 14px; font-size: 15px; background: #fafdfa; color: var(--text); transition: 0.3s; }
        .form-control:focus { border-color: var(--primary); outline: none; background: white; box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.05); }

        /* Search Bar */
        .search-container { position: relative; width: 300px; }
        .search-container input { padding-left: 45px; }
        .search-container::before { content: '🔍'; position: absolute; left: 15px; top: 50%; transform: translateY(-50%); opacity: 0.5; }

        /* Sorting Headers */
        .sortable { cursor: pointer; position: relative; user-select: none; }
        .sortable:hover { background: #f0f7f0 !important; }
        .sortable::after { content: '↕'; position: absolute; right: 10px; opacity: 0.3; }
        .sortable.asc::after { content: '↑'; opacity: 1; color: var(--primary); }
        .sortable.desc::after { content: '↓'; opacity: 1; color: var(--primary); }

        /* Toast Notifications */
        #toast-container { position: fixed; bottom: 30px; right: 30px; z-index: 9999; }
        .toast { background: #2e7d32; color: white; padding: 16px 28px; border-radius: 16px; margin-top: 10px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-in 3s forwards; display: flex; align-items: center; gap: 12px; font-weight: 600; }
        @keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes fadeOut { to { opacity: 0; transform: translateY(20px); } }

        /* Loading Spinner */
        #spinner { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.7); z-index: 10000; align-items: center; justify-content: center; backdrop-filter: blur(2px); }
        .spinner-icon { width: 50px; height: 50px; border: 5px solid #e0e0e0; border-top: 5px solid var(--primary); border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }

        /* Stats Chart Container */
        .chart-container { background: white; border-radius: 24px; padding: 25px; box-shadow: 0 10px 40px rgba(0,0,0,0.04); margin-bottom: 30px; height: 350px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>🌱 asteria Admin</h2>
        <a href="#" class="active" onclick="showSection('dashboard', this)"><span>📊</span> Dashboard</a>
        <a href="#" onclick="showSection('plans', this)"><span>📅</span> Programmes</a>
        <a href="#" onclick="showSection('recipes', this)"><span>🥗</span> Recettes</a>
        
    </div>

    <div class="main">
        <div class="navbar">
            <div class="navbar-brand" id="section-title">Tableau de Bord</div>
            <div style="font-weight: 600">📅 <span id="current-date"></span></div>
        </div>

        <!-- DASHBOARD -->
        <div id="dashboard" class="page-section active">
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
                    <p>Pages Gérées</p>
                    <h3 id="stat-total-pages">0</h3>
                </div>
                <div class="stat-card">
                    <p>Niveau Moyen</p>
                    <h3>INT.</h3>
                </div>
            </div>

            <div class="row" style="display: flex; gap: 30px; margin-bottom: 40px;">
                <div style="flex: 1;">
                    <div class="chart-container">
                        <h5 style="margin-bottom: 20px; color: var(--primary);">📊 Répartition par Niveau</h5>
                        <canvas id="chart-levels"></canvas>
                    </div>
                </div>
                <div style="flex: 1;">
                    <div class="chart-container">
                        <h5 style="margin-bottom: 20px; color: var(--primary);">🍕 Types de Repas</h5>
                        <canvas id="chart-meals"></canvas>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h5>🔥 Nouveaux Plans</h5></div>
                <div class="card-body" id="latest-plans"></div>
            </div>
        </div>

        <!-- PLANS SECTION -->
        <div id="plans" class="page-section">
            <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <button class="btn btn-primary" onclick="openPlanModal()">➕ Nouveau Programme</button>
                <div class="search-container">
                    <input type="text" id="search-plans" class="form-control" placeholder="Rechercher un programme..." onkeyup="debounceSearch('plans')">
                </div>
            </div>
            <div class="card">
                <div class="card-body"><div id="plans-list"></div></div>
            </div>
        </div>

        <!-- RECIPES SECTION -->
        <div id="recipes" class="page-section">
            <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <button class="btn btn-primary" onclick="openRecipeModal()">➕ Nouvelle Recette</button>
                <div class="search-container">
                    <input type="text" id="search-recipes" class="form-control" placeholder="Rechercher une recette..." onkeyup="debounceSearch('recipes')">
                </div>
            </div>
            <div class="card">
                <div class="card-body"><div id="recipes-list"></div></div>
            </div>
        </div>

        <!-- PAGES SECTION -->
        <div id="pages" class="page-section">
            <div style="margin-bottom: 30px; display: flex; justify-content: space-between; align-items: center;">
                <button class="btn btn-primary" onclick="openPageModal()">➕ Nouvelle Page</button>
                <div class="search-container">
                    <input type="text" id="search-pages" class="form-control" placeholder="Rechercher une page..." onkeyup="debounceSearch('pages')">
                </div>
            </div>
            <div class="card">
                <div class="card-body"><div id="pages-list"></div></div>
            </div>
        </div>
    </div>

    <!-- MODAL PLAN -->
    <div id="plan-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="plan-modal-title">Nouveau Programme</h5>
                <button style="background:none; border:none; color:white; font-size:28px; cursor:pointer;" onclick="closeModal('plan-modal')">&times;</button>
            </div>
            <form id="plan-form" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="plan-id">
                    <div class="form-group">
                        <label>Titre *</label>
                        <input type="text" id="plan-title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Objectif *</label>
                        <input type="text" id="plan-goal" class="form-control" placeholder="Ex: Perte de poids">
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex:1">
                            <label>Durée (jours) *</label>
                            <input type="number" id="plan-duration" class="form-control" value="30">
                        </div>
                        <div class="form-group" style="flex:1">
                            <label>Calories/jour *</label>
                            <input type="number" id="plan-calories" class="form-control">
                        </div>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex:1">
                            <label>Niveau</label>
                            <select id="plan-level" class="form-control">
                                <option value="BEGINNER">Débutant</option>
                                <option value="INTERMEDIATE">Intermédiaire</option>
                                <option value="ADVANCED">Avancé</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1">
                            <label>Statut</label>
                            <select id="plan-status" class="form-control">
                                <option value="ACTIVE">Actif</option>
                                <option value="INACTIVE">Inactif</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeModal('plan-modal')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL RECETTE -->
    <div id="recipe-modal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 id="recipe-modal-title">Nouvelle Recette</h5>
                <button style="background:none; border:none; color:white; font-size:28px; cursor:pointer;" onclick="closeModal('recipe-modal')">&times;</button>
            </div>
            <form id="recipe-form" novalidate>
                <div class="modal-body">
                    <input type="hidden" id="recipe-id">
                    <div class="form-group">
                        <label>Nom de la Recette *</label>
                        <input type="text" id="recipe-name" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>Programme Associé *</label>
                        <select id="recipe-plan" class="form-control" onchange="getAiSuggestion(this.value)"></select>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex:1">
                            <label>Type de Repas</label>
                            <select id="recipe-meal" class="form-control">
                                <option value="BREAKFAST">Petit Déjeuner</option>
                                <option value="LUNCH">Déjeuner</option>
                                <option value="DINNER">Dîner</option>
                                <option value="SNACK">Snack</option>
                            </select>
                        </div>
                        <div class="form-group" style="flex:1">
                            <label>Jour n°</label>
                            <input type="number" id="recipe-day" class="form-control" value="1">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                        <div class="form-group"><label>Calories</label><input type="number" id="recipe-cal" class="form-control"></div>
                        <div class="form-group"><label>Protéines (g)</label><input type="number" id="recipe-prot" class="form-control"></div>
                        <div class="form-group"><label>Glucides (g)</label><input type="number" id="recipe-carb" class="form-control"></div>
                        <div class="form-group"><label>Lipides (g)</label><input type="number" id="recipe-fat" class="form-control"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" onclick="closeModal('recipe-modal')">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL QR CODE -->
    <div id="qr-modal" class="modal">
        <div class="modal-content" style="max-width: 400px; text-align: center;">
            <div class="modal-header">
                <h5>QR Code de la Recette</h5>
                <button style="background:none; border:none; color:white; font-size:28px; cursor:pointer;" onclick="closeModal('qr-modal')">&times;</button>
            </div>
            <div class="modal-body">
                <div style="display: flex; justify-content: center; margin-bottom: 20px; padding: 20px; background: white; border-radius: 15px;">
                    <img id="qrcode-img" src="" alt="QR Code" style="width: 200px; height: 200px; display: none;">
                    <div id="qr-loading" class="spinner-icon" style="width: 30px; height: 30px;"></div>
                </div>
                <p id="qr-recipe-name" style="font-weight: 800; color: var(--primary); font-size: 1.2rem;"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-primary" style="width:100%" onclick="closeModal('qr-modal')">Fermer</button>
            </div>
        </div>
    </div>

    <div id="toast-container"></div>
    <div id="spinner"><div class="spinner-icon"></div></div>

    <script src="assets/js/validation.js"></script>
    <script>
        const API_BASE = '../../index.php';
        let state = { 
            section: 'dashboard',
            plans: { search: '', sort: 'id', order: 'DESC' },
            recipes: { search: '', sort: 'r.id', order: 'DESC' },
            pages: { search: '', sort: 'id', order: 'DESC' }
        };

        // --- UX UTILS ---
        function showToast(msg, type = 'success') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `<span>${type === 'success' ? '✅' : '❌'}</span> ${msg}`;
            container.appendChild(toast);
            setTimeout(() => toast.remove(), 4000);
        }

        function toggleSpinner(show) {
            document.getElementById('spinner').style.display = show ? 'flex' : 'none';
        }

        let debounceTimer;
        function debounceSearch(type) {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                state[type].search = document.getElementById(`search-${type}`).value;
                if (type === 'plans') loadPlans();
                else if (type === 'recipes') loadRecipes();
            }, 500);
        }

        function toggleSort(type, col) {
            if (state[type].sort === col) {
                state[type].order = state[type].order === 'ASC' ? 'DESC' : 'ASC';
            } else {
                state[type].sort = col;
                state[type].order = 'ASC';
            }
            if (type === 'plans') loadPlans();
            else if (type === 'recipes') loadRecipes();
        }

        function getSortClass(type, col) {
            if (state[type].sort !== col) return 'sortable';
            return `sortable ${state[type].order.toLowerCase()}`;
        }

        // --- VALIDATION (NO HTML5) ---
        function validatePlanData(data) {
            if (!data.title || data.title.trim() === '') return "Le titre est obligatoire";
            if (!data.goal || data.goal.trim() === '') return "L'objectif est obligatoire";
            if (!data.duration_days || data.duration_days <= 0) return "Durée invalide";
            if (!data.target_calories_per_day || data.target_calories_per_day <= 0) return "Calories invalides";
            return null;
        }

        function validateRecipeData(data) {
            if (!data.name || data.name.trim() === '') return "Le nom est obligatoire";
            if (!data.diet_plan_id) return "Veuillez choisir un programme";
            if (!data.calories || data.calories < 0) return "Calories invalides";
            return null;
        }

        // --- NAVIGATION ---
        function showSection(section, el) {
            document.querySelectorAll('.page-section').forEach(s => s.classList.remove('active'));
            document.querySelectorAll('.sidebar a').forEach(a => a.classList.remove('active'));
            document.getElementById(section).classList.add('active');
            el.classList.add('active');
            state.section = section;
            document.getElementById('section-title').textContent = el.textContent.replace(/[^\w\s]./g, '').trim();
            
            if (section === 'dashboard') loadDashboard();
            if (section === 'plans') loadPlans();
            if (section === 'recipes') loadRecipes();
            if (section === 'pages') loadPages();
        }

        // --- DASHBOARD & CHARTS ---
        let charts = {};
        async function loadDashboard() {
    toggleSpinner(true);
    try {
        const [pRes, rRes, pStatsRes, rStatsRes] = await Promise.all([
            fetch(`${API_BASE}?controller=DietPlan&action=obtenirTous`),
            fetch(`${API_BASE}?controller=Recipe&action=obtenirTous`),
            fetch(`${API_BASE}?controller=DietPlan&action=obtenirStats`),
            fetch(`${API_BASE}?controller=Recipe&action=obtenirStats`)
        ]);
        
        const pData = await pRes.json();
        const rData = await rRes.json();
        const pStats = await pStatsRes.json();
        const rStats = await rStatsRes.json();

        if (pData.success) {
            document.getElementById('stat-active-plans').textContent =
                pData.plans.filter(p => p.status === 'ACTIVE').length;

            const latest = pData.plans.slice(0, 5);
            document.getElementById('latest-plans').innerHTML =
                `<table class="table">` +
                latest.map(p => `
                    <tr>
                        <td><strong>${p.title}</strong></td>
                        <td><span class="badge bg-success">${p.level}</span></td>
                    </tr>
                `).join('') +
                `</table>`;
        }

        if (rData.success) {
            document.getElementById('stat-total-recipes').textContent = rData.recipes.length;
        }

        renderCharts(pStats, rStats);

    } catch (e) {
        console.error(e);
        showToast("Erreur dashboard", "error");
    } finally {
        toggleSpinner(false);
    }
}

        function renderCharts(pStats, rStats) {
            if (charts.levels) charts.levels.destroy();
            if (charts.meals) charts.meals.destroy();

            const levelCtx = document.getElementById('chart-levels').getContext('2d');
            charts.levels = new Chart(levelCtx, {
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
            charts.meals = new Chart(mealCtx, {
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

        // --- PLANS ---
        let allPlans = [];
        async function loadPlans() {
            toggleSpinner(true);
            const { search, sort, order } = state.plans;
            try {
                const resp = await fetch(`${API_BASE}?controller=DietPlan&action=obtenirTous&search=${search}&sort=${sort}&order=${order}`);
                const data = await resp.json();
                if (data.success) {
                    allPlans = data.plans;
                    let html = `<table class="table">
                        <thead>
                            <tr>
                                <th class="${getSortClass('plans', 'title')}" onclick="toggleSort('plans', 'title')">Titre</th>
                                <th class="${getSortClass('plans', 'goal')}" onclick="toggleSort('plans', 'goal')">Objectif</th>
                                <th class="${getSortClass('plans', 'duration_days')}" onclick="toggleSort('plans', 'duration_days')">Durée</th>
                                <th class="${getSortClass('plans', 'target_calories_per_day')}" onclick="toggleSort('plans', 'target_calories_per_day')">Calories</th>
                                <th class="${getSortClass('plans', 'level')}" onclick="toggleSort('plans', 'level')">Niveau</th>
                                <th class="${getSortClass('plans', 'status')}" onclick="toggleSort('plans', 'status')">Statut</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;
                    
                    data.plans.forEach(p => {
                        html += `<tr>
                            <td><strong>${p.title}</strong></td>
                            <td>${p.goal}</td>
                            <td>${p.duration_days}j</td>
                            <td>${p.target_calories_per_day}</td>
                            <td><span class="badge bg-success">${p.level}</span></td>
                            <td><span class="badge ${p.status === 'ACTIVE' ? 'bg-success' : 'bg-warning'}">${p.status}</span></td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="editPlan(${p.id})">✎</button>
                                <button class="btn btn-danger btn-sm" onclick="deletePlan(${p.id})">✗</button>
                                <button class="btn btn-primary btn-sm" style="background:#444" onclick="exportPlanPDF(${p.id})">PDF</button>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('plans-list').innerHTML = html + '</tbody></table>';
                }
            } catch (e) { showToast("Erreur lors du chargement des programmes", "error"); }
            finally { toggleSpinner(false); }
        }

        // ... Edit, Delete, Modal Plan ...
        async function editPlan(id) {
            const p = allPlans.find(x => x.id == id);
            if (!p) return;
            document.getElementById('plan-id').value = p.id;
            document.getElementById('plan-title').value = p.title;
            document.getElementById('plan-goal').value = p.goal;
            document.getElementById('plan-duration').value = p.duration_days;
            document.getElementById('plan-calories').value = p.target_calories_per_day;
            document.getElementById('plan-level').value = p.level;
            document.getElementById('plan-status').value = p.status;
            document.getElementById('plan-modal-title').textContent = 'Modifier le Programme';
            document.getElementById('plan-modal').classList.add('show');
        }

        async function deletePlan(id) {
            if (confirm('Supprimer ce programme et ses recettes ?')) {
                toggleSpinner(true);
                try {
                    await fetch(`${API_BASE}?controller=DietPlan&action=supprimer&id=${id}`, { method: 'DELETE' });
                    showToast("Programme supprimé avec succès");
                    loadPlans(); loadDashboard();
                } catch(e) { showToast("Erreur lors de la suppression", "error"); }
                finally { toggleSpinner(false); }
            }
        }

        async function openPlanModal() {
            document.getElementById('plan-id').value = '';
            document.getElementById('plan-form').reset();
            document.getElementById('plan-modal-title').textContent = 'Nouveau Programme';
            document.getElementById('plan-modal').classList.add('show');
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

            const error = validatePlanData(data);
            if (error) { showToast(error, "error"); return; }

            toggleSpinner(true);
            try {
                const url = id ? `${API_BASE}?controller=DietPlan&action=mettre_a_jour&id=${id}` : `${API_BASE}?controller=DietPlan&action=creer`;
                const resp = await fetch(url, { method: id ? 'PUT' : 'POST', body: JSON.stringify(data) });
                const res = await resp.json();
                if (res.success) {
                    showToast(id ? "Programme mis à jour" : "Programme créé");
                    closeModal('plan-modal'); 
                    loadPlans(); loadDashboard();
                } else { showToast(res.message, "error"); }
            } catch(e) { showToast("Erreur serveur", "error"); }
            finally { toggleSpinner(false); }
        };

        // --- RECIPES ---
        let allRecipesList = [];
        async function loadRecipes() {
            toggleSpinner(true);
            const { search, sort, order } = state.recipes;
            try {
                const resp = await fetch(`${API_BASE}?controller=Recipe&action=obtenirTous&search=${search}&sort=${sort}&order=${order}`);
                const data = await resp.json();
                if (data.success) {
                    allRecipesList = data.recipes;
                    let html = `<table class="table">
                        <thead>
                            <tr>
                                <th class="${getSortClass('recipes', 'r.name')}" onclick="toggleSort('recipes', 'r.name')">Nom</th>
                                <th class="${getSortClass('recipes', 'diet_plan_title')}" onclick="toggleSort('recipes', 'diet_plan_title')">Programme</th>
                                <th class="${getSortClass('recipes', 'r.meal_type')}" onclick="toggleSort('recipes', 'r.meal_type')">Type</th>
                                <th class="${getSortClass('recipes', 'r.day_number')}" onclick="toggleSort('recipes', 'r.day_number')">Jour</th>
                                <th class="${getSortClass('recipes', 'r.calories')}" onclick="toggleSort('recipes', 'r.calories')">Calories</th>
                                <th>Macros</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;
                    
                    data.recipes.forEach(r => {
                        html += `<tr>
                            <td><strong>${r.name}</strong></td>
                            <td>${r.diet_plan_title}</td>
                            <td>${r.meal_type}</td>
                            <td>J-${r.day_number}</td>
                            <td>${r.calories}</td>
                            <td>${r.proteins}/${r.carbs}/${r.fats}</td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="editRecipe(${r.id})">✎</button>
                                <button class="btn btn-danger btn-sm" onclick="deleteRecipe(${r.id})">✗</button>
                                <button class="btn btn-primary btn-sm" style="background:#444" onclick="exportRecipePDF(${r.id})">PDF</button>
                                <button class="btn btn-warning btn-sm" onclick="showQRCode(${r.id})">QR</button>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('recipes-list').innerHTML = html + '</tbody></table>';
                }
            } catch (e) { showToast("Erreur lors du chargement des recettes", "error"); }
            finally { toggleSpinner(false); }
        }

        async function editRecipe(id) {
            const r = allRecipesList.find(x => x.id == id);
            if (!r) return;
            await openRecipeModal();
            document.getElementById('recipe-id').value = r.id;
            document.getElementById('recipe-name').value = r.name;
            document.getElementById('recipe-plan').value = r.diet_plan_id;
            document.getElementById('recipe-meal').value = r.meal_type;
            document.getElementById('recipe-day').value = r.day_number;
            document.getElementById('recipe-cal').value = r.calories;
            document.getElementById('recipe-prot').value = r.proteins;
            document.getElementById('recipe-carb').value = r.carbs;
            document.getElementById('recipe-fat').value = r.fats;
            document.getElementById('recipe-modal-title').textContent = 'Modifier la Recette';
            document.getElementById('recipe-modal').classList.add('show');
        }

        async function deleteRecipe(id) {
            if (confirm('Supprimer cette recette ?')) {
                toggleSpinner(true);
                try {
                    await fetch(`${API_BASE}?controller=Recipe&action=supprimer&id=${id}`, { method: 'DELETE' });
                    showToast("Recette supprimée");
                    loadRecipes(); loadDashboard();
                } catch(e) { showToast("Erreur suppression", "error"); }
                finally { toggleSpinner(false); }
            }
        }

        async function openRecipeModal() {
            const pRes = await fetch(`${API_BASE}?controller=DietPlan&action=obtenirTous`);
            const pData = await pRes.json();
            if (pData.success) {
                document.getElementById('recipe-plan').innerHTML = '<option value="">Choisir un programme...</option>' + pData.plans.map(p => `<option value="${p.id}">${p.title}</option>`).join('');
            }
            document.getElementById('recipe-id').value = '';
            document.getElementById('recipe-form').reset();
            document.getElementById('recipe-modal-title').textContent = 'Nouvelle Recette';
            document.getElementById('recipe-modal').classList.add('show');
        }

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

            const error = validateRecipeData(data);
            if (error) { showToast(error, "error"); return; }

            toggleSpinner(true);
            try {
                const url = id ? `${API_BASE}?controller=Recipe&action=mettre_a_jour&id=${id}` : `${API_BASE}?controller=Recipe&action=creer`;
                const resp = await fetch(url, { method: id ? 'PUT' : 'POST', body: JSON.stringify(data) });
                const res = await resp.json();
                if (res.success) {
                    showToast(id ? "Recette mise à jour" : "Recette créée");
                    closeModal('recipe-modal'); loadRecipes(); loadDashboard();
                }
            } catch(e) { showToast("Erreur serveur", "error"); }
            finally { toggleSpinner(false); }
        }

        async function getAiSuggestion(planId) {
            if (!planId) return;
            
            toggleSpinner(true);
            try {
                const resp = await fetch(`${API_BASE}?controller=OpenAI&action=proposerRecette&plan_id=${planId}`);
                const data = await resp.json();
                
                if (data.success && data.recipe) {
                    const r = data.recipe;
                    document.getElementById('recipe-name').value = r.name || '';
                    document.getElementById('recipe-meal').value = r.meal_type || 'LUNCH';
                    document.getElementById('recipe-cal').value = r.calories || 0;
                    document.getElementById('recipe-prot').value = r.proteins || 0;
                    document.getElementById('recipe-carb').value = r.carbs || 0;
                    document.getElementById('recipe-fat').value = r.fats || 0;
                    showToast("✨ IA : Recette suggérée avec succès !");
                } else {
                    showToast(data.message || "L'IA n'a pas pu générer de recette", "error");
                }
            } catch (e) {
                console.error(e);
                showToast("Erreur lors de l'appel à l'IA", "error");
            } finally {
                toggleSpinner(false);
            }
        }

        // --- PDF EXPORT ---
        function exportPlanPDF(id) {
            const p = allPlans.find(x => x.id == id);
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.setFontSize(22);
            doc.setTextColor(46, 125, 50);
            doc.text("RAPPORT DE PROGRAMME DIÉTÉTIQUE", 20, 20);
            
            doc.setFontSize(12);
            doc.setTextColor(100);
            doc.text(`Date du rapport : ${new Date().toLocaleDateString()}`, 20, 30);
            
            doc.setDrawColor(46, 125, 50);
            doc.line(20, 35, 190, 35);
            
            doc.setFontSize(16);
            doc.setTextColor(0);
            doc.text(`Titre : ${p.title}`, 20, 50);
            doc.text(`Objectif : ${p.goal}`, 20, 60);
            doc.text(`Durée : ${p.duration_days} jours`, 20, 70);
            doc.text(`Cible calorique : ${p.target_calories_per_day} kcal/jour`, 20, 80);
            doc.text(`Niveau : ${p.level}`, 20, 90);
            doc.text(`Statut : ${p.status}`, 20, 100);

            doc.save(`Programme_${p.title.replace(/\s+/g, '_')}.pdf`);
            showToast("PDF généré avec succès");
        }

        function exportRecipePDF(id) {
            const r = allRecipesList.find(x => x.id == id);
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            
            doc.setFontSize(22);
            doc.setTextColor(46, 125, 50);
            doc.text("FICHE RECETTE ASTERIA", 20, 20);
            
            doc.setFontSize(16);
            doc.setTextColor(0);
            doc.text(`Nom : ${r.name}`, 20, 40);
            doc.text(`Type : ${r.meal_type}`, 20, 50);
            doc.text(`Programme : ${r.diet_plan_title}`, 20, 60);
            doc.text(`Jour : J-${r.day_number}`, 20, 70);
            
            doc.autoTable({
                startY: 80,
                head: [['Nutriment', 'Valeur']],
                body: [
                    ['Calories', `${r.calories} kcal`],
                    ['Protéines', `${r.proteins} g`],
                    ['Glucides', `${r.carbs} g`],
                    ['Lipides', `${r.fats} g`]
                ],
                theme: 'striped',
                headStyles: { fillColor: [46, 125, 50] }
            });

            doc.save(`Recette_${r.name.replace(/\s+/g, '_')}.pdf`);
            showToast("PDF généré avec succès");
        }

        function showQRCode(id) {
            const r = allRecipesList.find(x => x.id == id);
            if (!r) {
                showToast("Recette non trouvée", "error");
                return;
            }

            const img = document.getElementById('qrcode-img');
            const loader = document.getElementById('qr-loading');
            
            img.style.display = 'none';
            loader.style.display = 'block';

            const content = `RECETTE: ${r.name}\nCALORIES: ${r.calories} kcal\nPROG: ${r.diet_plan_title}`;
            const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(content)}&color=2e7d32`;
            
            img.onload = () => {
                img.style.display = 'block';
                loader.style.display = 'none';
            };
            
            img.onerror = () => {
                loader.style.display = 'none';
                showToast("Erreur lors du chargement du QR code", "error");
            };

            img.src = qrUrl;
            document.getElementById('qr-recipe-name').textContent = r.name;
            document.getElementById('qr-modal').classList.add('show');
        }

        // --- PAGES ---
        async function loadPages() {
            toggleSpinner(true);
            const { search, sort, order } = state.pages;
            try {
                const data = await resp.json();
                if (data.success) {
                    let html = `<table class="table">
                        <thead>
                            <tr>
                                <th class="${getSortClass('pages', 'slug')}" onclick="toggleSort('pages', 'slug')">Slug</th>
                                <th class="${getSortClass('pages', 'title')}" onclick="toggleSort('pages', 'title')">Titre</th>
                                <th class="${getSortClass('pages', 'type')}" onclick="toggleSort('pages', 'type')">Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>`;
                    
                    data.pages.forEach(p => {
                        html += `<tr>
                            <td><code>${p.slug}</code></td>
                            <td><strong>${p.title}</strong></td>
                            <td><span class="badge ${p.type === 'FRONT' ? 'bg-success' : 'bg-warning'}">${p.type}</span></td>
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="deletePage(${p.id})">✗</button>
                            </td>
                        </tr>`;
                    });
                    document.getElementById('pages-list').innerHTML = html + '</tbody></table>';
                }
            } catch(e) { showToast("Erreur lors du chargement des pages", "error"); }
            finally { toggleSpinner(false); }
        }

        async function deletePage(id) {
            if (confirm('Supprimer cette page ?')) {
                toggleSpinner(true);
                showToast("Page supprimée");
                loadPages(); loadDashboard();
                toggleSpinner(false);
            }
        }

        function closeModal(id) { document.getElementById(id).classList.remove('show'); }
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        loadDashboard();
    </script>
</body>
</html>
