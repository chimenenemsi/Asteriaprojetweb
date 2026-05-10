/**
 * Validation JavaScript - Diet & Nutrition Management System
 * app/views/assets/js/validation.js
 */

// ===== VALIDATION PROGRAMME (DietPlan) =====
function validerPlan() {
  const erreurs = {};
  const titre = document.getElementById('plan-title')?.value.trim();
  const calories = document.getElementById('plan-calories')?.value;
  const duree = document.getElementById('plan-duration')?.value;

  if (!titre) erreurs.title = "❌ Le titre est requis";
  else if (titre.length < 3) erreurs.title = "❌ Minimum 3 caractères";

  if (!calories || isNaN(calories) || calories < 500 || calories > 10000) 
    erreurs.target_calories_per_day = "❌ Calories invalides (500-10000)";

  if (!duree || isNaN(duree) || duree < 1 || duree > 365)
    erreurs.duration_days = "❌ Durée invalide (1-365 jours)";

  afficherErreurs(erreurs, 'plan-form');
  return Object.keys(erreurs).length === 0;
}

// ===== VALIDATION RECETTE (Recipe) =====
function validerRecette() {
  const erreurs = {};
  const nom = document.getElementById('recipe-name')?.value.trim();
  const calories = document.getElementById('recipe-cal')?.value;
  const proteins = document.getElementById('recipe-prot')?.value;
  const carbs = document.getElementById('recipe-carb')?.value;
  const fats = document.getElementById('recipe-fat')?.value;

  if (!nom) erreurs.name = "❌ Le nom est requis";
  
  if (!calories || isNaN(calories) || calories < 0 || calories > 5000)
    erreurs.calories = "❌ Calories invalides (0-5000)";

  if (proteins && (isNaN(proteins) || proteins < 0 || proteins > 1000))
    erreurs.proteins = "❌ Protéines invalides (0-1000g)";

  if (carbs && (isNaN(carbs) || carbs < 0 || carbs > 1000))
    erreurs.carbs = "❌ Glucides invalides (0-1000g)";

  if (fats && (isNaN(fats) || fats < 0 || fats > 1000))
    erreurs.fats = "❌ Lipides invalides (0-1000g)";

  afficherErreurs(erreurs, 'recipe-form');
  return Object.keys(erreurs).length === 0;
}

// ===== AFFICHER ERREURS =====
function afficherErreurs(erreurs, formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  // Nettoyage
  form.querySelectorAll(".error-message").forEach((el) => el.remove());
  form.querySelectorAll(".form-control.is-invalid").forEach((el) => el.classList.remove("is-invalid"));

  // Affichage
  Object.keys(erreurs).forEach((champID) => {
    let element = form.querySelector(`#plan-${champID}, #recipe-${champID}`);
    if (!element) element = document.getElementById(`${formId.split('-')[0]}-${champID}`);

    if (element) {
      element.classList.add("is-invalid");
      const messageDiv = document.createElement("div");
      messageDiv.className = "error-message text-danger small mt-1";
      messageDiv.textContent = erreurs[champID];
      element.parentNode.appendChild(messageDiv);
    }
  });

  const premiereErreur = form.querySelector(".is-invalid");
  if (premiereErreur) premiereErreur.focus();
}

// Nettoyage en temps réel
document.addEventListener("DOMContentLoaded", function () {
  document.body.addEventListener("input", function (e) {
    if (e.target.classList.contains("form-control")) {
      e.target.classList.remove("is-invalid");
      const erreur = e.target.parentNode.querySelector(".error-message");
      if (erreur) erreur.remove();
    }
  });
});
