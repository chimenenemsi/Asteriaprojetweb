# 🧪 Guide De Test - Gestion des Allergies

## ✅ Avant de commencer

1. **XAMPP en marche**: Apache + MySQL démarrés
2. **Dossier correct**: `C:\xampp\htdocs\gestion-allergies\`
3. **URL**: http://localhost/gestion-allergies/

---

## 🧪 Scénarios de Test

### 1️⃣ Premier Accès (Création BD)

✅ **Attendu**: La base de données se crée automatiquement

**Étapes**:

1. Accéder à http://localhost/gestion-allergies/admin.php
2. Vérifier qu'aucune erreur n'apparaît
3. La page doit s'afficher avec "Liste des allergies"

**Résultat**:

- Tables créées dans `gestion_allergies`
- Interface affichée correctement

---

### 2️⃣ Test CRUD - Créer une Allergie

#### Test 2a: Création avec données valides

**URL**: admin.php?action=ajouter

```
Remplir le formulaire:
- Nom: "Cacahuète"
- Type: "alimentaire"
- Niveau: "critique"
- Description: "Réaction grave aux cacahuètes et dérivés"
- Symptômes: "Gonflements, urticaire, anaphylaxie"
```

**Attendu**:

- Message de succès ✓
- Redirection à la liste
- Nouveaux enregistrement visible

#### Test 2b: Création avec validation - Nom trop court

**Données**:

```
Nom: "Ca"  (seulement 2 caractères)
```

**Attendu**:

- Erreur: "Le nom doit contenir au moins 3 caractères"
- Formulaire conservé

#### Test 2c: Création avec validation - Nom invalide (chiffres)

**Données**:

```
Nom: "Allergie123"  (contient des chiffres)
```

**Attendu**:

- Erreur: "Le nom ne doit contenir que des lettres"

#### Test 2d: Création - Nom déjà existant

**Données**:

```
Nom: "Cacahuète"  (créé précédemment)
```

**Attendu**:

- Erreur: "Une allergie avec ce nom existe déjà"

#### Test 2e: Création - Description vide

**Attribuer vide la description**

**Attendu**:

- Erreur: "La description est requise"

#### Test 2f: Création - Niveau invalide

**Données**:

```
Niveau: "fortement dangereux"  (pas dans enum)
```

**Attendu**:

- Erreur: "Le niveau de danger est invalide"

---

### 3️⃣ Test CRUD - Lire les Allergies

#### Test 3a: Liste complète

**URL**: admin.php (défaut)

**Attendu**:

- Tableau avec toutes les allergies
- Colonnes: ID, Nom, Type, Niveau, Symptômes
- Actions: ✏️ Éditer, 🗑️ Supprimer

#### Test 3b: Pagination

**Attendu** (si > 10 allergies):

- Lien "Première", "Précédente", numéros, "Suivante", "Dernière"
- Affichage 10 items par page

#### Test 3c: Recherche FrontOffice

**URL**: index.php
**Recherche**: "Cacahuète"

**Attendu**:

- Résultats affichés
- Redirection? index.php?action=rechercher&q=Cacahuète

---

### 4️⃣ Test CRUD - Éditer une Allergie

#### Test 4a: Édition avec changement valide

**URL**: admin.php?action=editer&id=1

**Faire**:

1. Changer le nom: "Cacahuète" → "Arachide"
2. Changer la description
3. Soumettre

**Attendu**:

- Message de succès
- Redirection à la liste
- Changements appliqués

#### Test 4b: Édition - Validation nom

**Données**:

```
Nom: "Ar"  (trop court)
```

**Attendu**:

- Erreur de validation
- Formulaire conservé

#### Test 4c: Édition - Nom utilisé ailleurs

**Données**:

```
Allergie A: "Cacahuète"
Allergie B: "Arachide"

Éditer B et mettre: "Cacahuète"
```

**Attendu**:

- Erreur: "Une allergie avec ce nom existe déjà"

---

### 5️⃣ Test CRUD - Supprimer une Allergie

#### Test 5a: Suppression simple

**Faire**:

1. Aller à la liste (admin.php)
2. Cliquer sur 🗑️ d'une allergie
3. Confirmer dans la popup

**Attendu**:

- Message de succès
- Allergie disparait de la liste
- Suppression cascade (allergie_traitement)

#### Test 5b: Suppression allergie non-existante

**URL**: admin.php?action=supprimer&id=99999

**Attendu**:

- Erreur: "Allergie non trouvée"

---

### 6️⃣ Test FrontOffice

#### Test 6a: Accueil

**URL**: index.php

**Attendu**:

- Hero section
- Statistiques (total, critiques, alimentaires)
- Grille allergies critiques
- Design agréable

#### Test 6b: Toutes les allergies (Pagination)

**URL**: index.php?action=allergies

**Attendu**:

- Grille 3 colonnes
- 12 items par page
- Pagination fonctionnelle
- Badges de niveau/type

#### Test 6c: Détail allergie

**URL**: index.php?action=detail&id=1

**Attendu**:

- Affichage complet
- Formatage lisible
- Niveau de danger coloré
- Bouton "Retour"

#### Test 6d: Recherche

**URL**: index.php
**Saisir**: "nut" dans la barre

**Attendu**:

- Redirection vers résultats
- Affichage allergies correspondantes

#### Test 6e: Lien Admin depuis FrontOffice

**Cliquer sur**: 🔐 Admin

**Attendu**:

- Redirection vers admin.php
- Session maintenue si nécessaire

---

### 7️⃣ Test Sécurité

#### Test 7a: Injection SQL

**Saisir dans Nom**: `"; DROP TABLE allergie; --`

**Attendu**:

- Erreur validation: "Format invalide"
- Table NOT dropped
- Préparation PDO protège

#### Test 7b: XSS (Cross-Site Scripting)

**Saisir dans Description**: `<script>alert('XSS')</script>`

**Attendu**:

- Texte affiché comme texte brut
- Aucun script exécuté
- htmlspecialchars() fonctionne

#### Test 7c: Accès direct allergie supprimée

**URL**: index.php?action=detail&id=99999

**Attendu**:

- Redirection à l'accueil
- Message d'erreur douce
- Pas de crash

---

### 8️⃣ Test Responsive

#### Desktop (1920x1080)

- ✅ Sidebar visible
- ✅ Layout 2+ colonnes
- ✅ Tous éléments affichés

#### Tablette (768x1024)

- ✅ Layout adapté
- ✅ Éléments lisibles
- ✅ Pas de débordement

#### Mobile (375x667)

- ✅ Menu responsive
- ✅ Layout 1 colonne
- ✅ Touches grandeur tactile

---

### 9️⃣ Test Performance

#### Test 9a: Temps de réponse

**Faire**: Créer → Lister → Éditer → Supprimer

**Attendu**:

- < 1s par action
- Pas de lag

#### Test 9b: Pagination 100+ items

**Faire**: Insérer beaucoup de données

**Attendu**:

- Pagination rapide
- Pas de ralentissement

---

### 🔟 Test de Régression

**Avant chaque déploiement, vérifier**:

```
☐ Create: ✅
☐ Read: ✅
☐ Update: ✅
☐ Delete: ✅
☐ Validation Nom: ✅
☐ Validation Type: ✅
☐ Validation Niveau: ✅
☐ XSS Protection: ✅
☐ SQL Injection Protection: ✅
☐ Pagination: ✅
☐ Recherche: ✅
☐ Responsive: ✅
☐ Messages d'erreur: ✅
☐ Messages de succès: ✅
```

---

## 🐛 Debugging

### Erreur: "Connection refused"

**Solution**:

1. Vérifier Apache + MySQL démarrés (XAMPP)
2. Vérifier localhost/phpmyadmin accessible
3. Vérifier dossier dans htdocs

### Erreur: "Table already exists"

**Solution**:

1. Souhaitable au premier accès (création auto)
2. Supprimer la base et recharger
3. Via PHPMyAdmin: DROP DATABASE gestion_allergies;

### Erreur: "No database selected"

**Solution**:

1. Vérifier que PDO crée la base
2. Vérifier credentials (localhost, root, sans pwd)
3. Vérifier version MySQL

### Allergie ne s'affiche pas

**Vérifier**:

1. Créée en BD? (PhpMyAdmin)
2. Pagination? (Naviguer page 1)
3. Recherche? (Vérifier terme)

---

## ✅ Checklist Finale

```
CRUD Fonctionnel:
☐ Créer allergie
☐ Lire allergies
☐ Éditer allergie
☐ Supprimer allergie

Validations:
☐ Validation Nom (min/max/format)
☐ Validation Description
☐ Validation Niveau
☐ Validation Type
☐ Validation Symptômes
☐ Unicité nom BD

Sécurité:
☐ PDO Prepared Statements
☐ htmlspecialchars()
☐ Validation serveur (0 HTML5)
☐ Messages erreur appropriés

Architecture:
☐ Models séparé
☐ Controllers séparé
☐ Views séparé
☐ POO implémentée

UI:
☐ BackOffice responsive
☐ FrontOffice responsive
☐ Design cohérent
☐ Navigation claire

Données:
☐ BD créée auto
☐ Tables créées auto
☐ Données test opérationnelles
```

---

**Status**: 🟢 **PRÊT POUR TEST**

Lancez les tests et signalez tout problème!
