<?php
/**
 * Dashboard Admin - Gestion de Nutrition (Régimes & Recettes)
 * Style ECOSAVE (Vert écologique)
 * Point d'accès: http://localhost/gestion-allergies/app/views/admin.php
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - EcoSave Diet Manager</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;800&display=swap" rel="stylesheet">
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
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Asteria</h2>
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

            <div class="row" style="display: flex; gap: 30px;">
                <div class="card" style="flex: 2;">
                    <div class="card-header"><h5>🔥 Nouveaux Plans</h5></div>
                    <div class="card-body" id="latest-plans"></div>
                </div>
                <div class="card" style="flex: 1;">
                    <div class="card-header"><h5>📂 Types de Repas</h5></div>
                    <div class="card-body" style="padding: 20px;">
                        <ul style="list-style: none;">
                            <li style="margin: 10px 0; display: flex; justify-content: space-between;">Petit Déj <span class="badge bg-success">40%</span></li>
                            <li style="margin: 10px 0; display: flex; justify-content: space-between;">Déjeuner <span class="badge bg-success">35%</span></li>
                            <li style="margin: 10px 0; display: flex; justify-content: space-between;">Dîner <span class="badge bg-success">25%</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- PLANS SECTION -->
        <div id="plans" class="page-section">
            <div style="margin-bottom: 30px; display: flex; justify-content: space-between;">
                <button class="btn btn-primary" onclick="openPlanModal()">➕ Nouveau Programme</button>
            </div>
            <div class="card">
                <div class="card-body"><div id="plans-list"></div></div>
            </div>
        </div>

        <!-- RECIPES SECTION -->
        <div id="recipes" class="page-section">
            <div style="margin-bottom: 30px;">
                <button class="btn btn-primary" onclick="openRecipeModal()">➕ Nouvelle Recette</button>
            </div>
            <div class="card">
                <div class="card-body"><div id="recipes-list"></div></div>
            </div>
        </div>

        <!-- PAGES SECTION -->
        <div id="pages" class="page-section">
            <div style="margin-bottom: 30px;">
                <button class="btn btn-primary" onclick="openPageModal()">➕ Nouvelle Page</button>
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
            <form id="plan-form">
                <div class="modal-body">
                    <input type="hidden" id="plan-id">
                    <div class="form-group">
                        <label>Titre *</label>
                        <input type="text" id="plan-title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Objectif *</label>
                        <input type="text" id="plan-goal" class="form-control" placeholder="Ex: Perte de poids" required>
                    </div>
                    <div style="display: flex; gap: 20px;">
                        <div class="form-group" style="flex:1">
                            <label>Durée (jours) *</label>
                            <input type="number" id="plan-duration" class="form-control" value="30" required>
                        </div>
                        <div class="form-group" style="flex:1">
                            <label>Calories/jour *</label>
                            <input type="number" id="plan-calories" class="form-control" required>
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
            <form id="recipe-form">
                <div class="modal-body">
                    <input type="hidden" id="recipe-id">
                    <div class="form-group">
                        <label>Nom de la Recette *</label>
                        <input type="text" id="recipe-name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>Programme Associé *</label>
                        <select id="recipe-plan" class="form-control" required></select>
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

    <script src="assets/js/validation.js"></script>
    <script>
        const API_BASE = '../../index.php';
        let state = { section: 'dashboard' };

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

        async function loadDashboard() {
            try {
                const [pRes, rRes, pgRes] = await Promise.all([
                    fetch(`${API_BASE}?controller=DietPlan&action=obtenirTous`),
                    fetch(`${API_BASE}?controller=Recipe&action=obtenirTous`),
                    fetch(`${API_BASE}?controller=StaticPage&action=obtenirTous`)
                ]);
                const pData = await pRes.json();
                const rData = await rRes.json();
                const pgData = await pgRes.json();

                if (pData.success) {
                    document.getElementById('stat-active-plans').textContent = pData.plans.filter(p => p.status === 'ACTIVE').length;
                    const latest = pData.plans.slice(0, 5);
                    document.getElementById('latest-plans').innerHTML = `<table class="table">` + latest.map(p => `<tr><td><strong>${p.title}</strong></td><td><span class="badge bg-success">${p.level}</span></td></tr>`).join('') + `</table>`;
                }
                if (rData.success) document.getElementById('stat-total-recipes').textContent = rData.recipes.length;
                if (pgData.success) document.getElementById('stat-total-pages').textContent = pgData.pages.length;
            } catch (e) { console.error(e); }
        }

        // PLANS
        let allPlans = [];
        async function loadPlans() {
            const resp = await fetch(`${API_BASE}?controller=DietPlan&action=obtenirTous`);
            const data = await resp.json();
            if (data.success) {
                allPlans = data.plans;
                let html = '<table class="table"><thead><tr><th>Titre</th><th>Objectif</th><th>Durée</th><th>Calories</th><th>Niveau</th><th>Statut</th><th>Actions</th></tr></thead><tbody>';
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
                        </td>
                    </tr>`;
                });
                document.getElementById('plans-list').innerHTML = html + '</tbody></table>';
            }
        }

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
                await fetch(`${API_BASE}?controller=DietPlan&action=supprimer&id=${id}`, { method: 'DELETE' });
                loadPlans(); loadDashboard();
            }
        }

        async function openPlanModal() {
            document.getElementById('plan-id').value = '';
            document.getElementById('plan-form').reset();
            document.getElementById('plan-modal').classList.add('show');
        }

        document.getElementById('plan-form').onsubmit = async (e) => {
            e.preventDefault();
            if (!validerPlan()) return;
            const id = document.getElementById('plan-id').value;
            const data = {
                title: document.getElementById('plan-title').value,
                goal: document.getElementById('plan-goal').value,
                duration_days: document.getElementById('plan-duration').value,
                target_calories_per_day: document.getElementById('plan-calories').value,
                level: document.getElementById('plan-level').value,
                status: document.getElementById('plan-status').value
            };
            const url = id ? `${API_BASE}?controller=DietPlan&action=mettre_a_jour&id=${id}` : `${API_BASE}?controller=DietPlan&action=creer`;
            await fetch(url, { method: id ? 'PUT' : 'POST', body: JSON.stringify(data) });
            closeModal('plan-modal'); loadPlans();
        };

        // RECIPES
        let allRecipesList = [];
        async function loadRecipes() {
            const resp = await fetch(`${API_BASE}?controller=Recipe&action=obtenirTous`);
            const data = await resp.json();
            if (data.success) {
                allRecipesList = data.recipes;
                let html = '<table class="table"><thead><tr><th>Nom</th><th>Programme</th><th>Type</th><th>Jour</th><th>Calories</th><th>Macros (P/G/L)</th><th>Actions</th></tr></thead><tbody>';
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
                        </td>
                    </tr>`;
                });
                document.getElementById('recipes-list').innerHTML = html + '</tbody></table>';
            }
        }

        async function editRecipe(id) {
            const r = allRecipesList.find(x => x.id == id);
            if (!r) return;
            await openRecipeModal(); // Load plans
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
                await fetch(`${API_BASE}?controller=Recipe&action=supprimer&id=${id}`, { method: 'DELETE' });
                loadRecipes(); loadDashboard();
            }
        }

        async function openRecipeModal() {
            const pRes = await fetch(`${API_BASE}?controller=DietPlan&action=obtenirTous`);
            const pData = await pRes.json();
            if (pData.success) {
                document.getElementById('recipe-plan').innerHTML = pData.plans.map(p => `<option value="${p.id}">${p.title}</option>`).join('');
            }
            document.getElementById('recipe-form').reset();
            document.getElementById('recipe-modal').classList.add('show');
        }

        document.getElementById('recipe-form').onsubmit = async (e) => {
            e.preventDefault();
            if (!validerRecette()) return;
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
            const url = id ? `${API_BASE}?controller=Recipe&action=mettre_a_jour&id=${id}` : `${API_BASE}?controller=Recipe&action=creer`;
            await fetch(url, { method: id ? 'PUT' : 'POST', body: JSON.stringify(data) });
            closeModal('recipe-modal'); loadRecipes();
        }

        // PAGES
        async function loadPages() {
            const resp = await fetch(`${API_BASE}?controller=StaticPage&action=obtenirTous`);
            const data = await resp.json();
            if (data.success) {
                let html = '<table class="table"><thead><tr><th>Slug</th><th>Titre</th><th>Type</th><th>Actions</th></tr></thead><tbody>';
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
        }

        async function deletePage(id) {
            if (confirm('Supprimer cette page ?')) {
                await fetch(`${API_BASE}?controller=StaticPage&action=supprimer&id=${id}`, { method: 'DELETE' });
                loadPages(); loadDashboard();
            }
        }

        function closeModal(id) { document.getElementById(id).classList.remove('show'); }
        document.getElementById('current-date').textContent = new Date().toLocaleDateString('fr-FR', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        loadDashboard();
    </script>
</body>
</html>
