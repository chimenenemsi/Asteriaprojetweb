# 🏗️ Architecture MVC Stricte - Gestion des Allergies et Traitements

## 📋 Structure du Projet

```
gestion-allergies/
│
├── 📍 Points d'Entrée (Racine - SEULEMENT 2 fichiers!)
│   ├── index.php              ← FrontOffice (site public)
│   └── admin.php              ← BackOffice (administration)
│
├── 📁 config/
│   └── Database.php           ← Configuration BD (Singleton Pattern)
│
├── 📁 app/
│   ├── models/
│   │   ├── Allergie.php       ← Model Allergie + CRUD
│   │   └── Traitement.php     ← Model Traitement + CRUD
│   │
│   ├── controllers/
│   │   ├── AllergiController.php       ← Contrôleur Allergie
│   │   └── TraitementController.php    ← Contrôleur Traitement
│   │
│   └── views/
│       ├── layouts/
│       │   ├── header.php     ← Navigation Bootstrap 5.3
│       │   └── footer.php     ← Pied de page + scripts
│       │
│       ├── frontend/
│       │   ├── accueil.php           ← Page d'accueil
│       │   ├── allergies_liste.php   ← List paginée
│       │   ├── allergies_detail.php  ← Détail allergie
│       │   ├── traitements_liste.php ← List paginée traitements
│       │   ├── traitements_detail.php← Détail traitement
│       │   └── search.php            ← Résultats recherche
│       │
│       └── admin/
│           ├── dashboard.php         ← Tableau de bord
│           ├── allergies_gestion.php ← CRUD allergies list
│           ├── allergie_formulaire.php← Form ajouter/éditer
│           ├── traitements_gestion.php← CRUD traitements list
│           └── traitement_formulaire.php← Form ajouter/éditer
│
├── 📁 assets/
│   ├── css/
│   │   └── style.css          ← Bootstrap 5.3 + Custom
│   │
│   └── js/
│       └── validation.js      ← Client-side validation ES6
│
├── 📁 data/
│   └── test_data.sql          ← Données test
│
├── 📄 Racine (Documentation + Config)
│   ├── README.md
│   ├── RAPPORT_PROJET.md
│   ├── LIVRABLES.md
│   ├── GUIDE_TEST.md
│   ├── create_database.sql
│   ├── .gitignore
│   └── ARCHITECTURE_MVC.md    ← CET FICHIER
```

### ✅ Nouvelle Structure = ZÉRO fichiers inutiles dans la racine!

## 🔧 Couche Configuration

### Database.php (Singleton Pattern)

```php
<?php
class Config {
    private static $pdo = null;

    public static function getConnexion() {
        if (!self::$pdo) {
            self::$pdo = new PDO(
                'mysql:host=localhost;dbname=projet2a33',
                'root',
                '',
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        }
        return self::$pdo;
    }
}
?>
```

**Utilisation partout:**

```php
$pdo = Config::getConnexion(); // Une seule instance utilisée
```

## 📦 Couche Models

### Allergie.php - Modèle Complet

**Propriétés + Constantes:**

```php
class Allergie {
    private $id_allergie;
    private $nom;
    private $description;
    private $niveau_danger;
    private $symptomes;
    private $type;

    const NIVEAUX_DANGER = ['faible', 'moyen', 'élevé', 'critique'];
    const TYPES_ALLERGIE = ['alimentaire', 'médicament', 'environnemental', 'contact', 'autre'];
}
```

**Constructeur Paramétré:**

```php
public function __construct($nom = null, $description = null, $niveau_danger = null,
                           $symptomes = null, $type = null, $id_allergie = null)
```

**Tous les Getters:**

```php
getId()            // → int
getNom()           // → string
getDescription()   // → string
getNiveauDanger()  // → string (faible|moyen|élevé|critique)
getSymptomes()     // → string
getType()          // → string (alimentaire|médicament|...)
```

**Setters avec Fluent Interface:**

```php
setNom($nom)              // → $this (pour enchaîner)
setDescription($desc)     // → $this
setNiveauDanger($niveau)  // → $this
setSymptomes($symp)       // → $this
setType($type)            // → $this
```

**Méthodes CRUD (statiques/publiques):**

```php
// CREATE
$allergie = new Allergie($nom, $description, $danger, $symp, $type);
$allergie->creer(); // Enregistre en BD

// READ
$allergie = Allergie::obtenirParId(1);                  // Une allergie
$allergies = Allergie::obtenirTous($limite, $offset);  // Liste paginée
$total = Allergie::obtenirNombreTotal();               // Count

// UPDATE
$allergie->setNom('Nouveau nom')->mettre_a_jour();   // Mise à jour

// DELETE
$allergie->supprimer();  // Suppression

// SEARCH
$resultats = Allergie::rechercher('pollen');  // Recherche texte
```

### Traitement.php - Structure Identique

Même pattern que Allergie, avec propriétés:

```php
private $id_traitement;
private $nom;
private $type_traitement;  // médecin|naturel|pharmacie|autre
private $dosage;
private $duree;
private $effets_secondaires;

const TYPES_TRAITEMENT = ['médecin', 'naturel', 'pharmacie', 'autre'];
```

Mêmes méthodes CRUD que Allergie: `creer()`, `obtenirParId()`, `obtenirTous()`, etc.

## 🎮 Couche Controllers

### AllergiController - Méthodes Statiques

**Signature complète:**

```php
class AllergiController {

    // CREATE
    public static function creer($data)
        // $data = ['nom'=>'', 'description'=>'', 'niveau_danger'=>'', 'symptomes'=>'', 'type'=>'']
        // Retour: ['succes'=>true/false, 'message'=>'...', 'data'=>['id'=>N], 'erreurs'=>[...]]

    // READ
    public static function obtenirParId($id)
        // Retour: Allergie object ou null

    public static function obtenirTous($page, $limite)
        // Retour: ['succes'=>true, 'allergies'=>[Allergie,...], 'total'=>N, 'page'=>N, 'pages'=>N]

    // UPDATE
    public static function mettre_a_jour($id, $data)
        // Retour: ['succes'=>true/false, 'message'=>'...']

    // DELETE
    public static function supprimer($id)
        // Retour: ['succes'=>true/false, 'message'=>'...']

    // SEARCH
    public static function rechercher($terme)
        // Retour: [Allergie object, Allergie object, ...]

    // VALIDATION
    public static function valider($data)
        // Retour: ['valide'=>true/false, 'erreurs'=>[...]]

    // CONSTANTS
    public static function obtenirConstantes()
        // Retour: ['niveaux_danger'=>[...], 'types'=>[...]]
}
```

### TraitementController - Identique

Même structure de méthodes, appliquée aux traitements.

## 🎨 Couche Views

### Organization

- **Layouts**: Code partagé (header, footer)
- **Frontend**: Pages publiques (accueil, listes, détails, recherche)
- **Admin**: Pages administration (dashboard, CRUD)

### Frontend Views

| Fichier                  | URL                                        | Description                                 |
| ------------------------ | ------------------------------------------ | ------------------------------------------- |
| `accueil.php`            | `/index.php`                               | Homep, stats, recherche, allergies récentes |
| `allergies_liste.php`    | `/index.php?action=allergies&page=1`       | Liste paginée (12/page)                     |
| `allergies_detail.php`   | `/index.php?action=detail_allergie&id=1`   | Détail complet avec sidebar                 |
| `traitements_liste.php`  | `/index.php?action=traitements&page=1`     | Liste paginée traitements                   |
| `traitements_detail.php` | `/index.php?action=detail_traitement&id=1` | Détail traitement                           |
| `search.php`             | `/index.php?action=search&terme=...`       | Résultats combinés                          |

### Admin Views

| Fichier                     | URL                                    | Description             |
| --------------------------- | -------------------------------------- | ----------------------- |
| `dashboard.php`             | `/admin.php`                           | Tableau de bord + stats |
| `allergies_gestion.php`     | `/admin.php?action=allergies`          | CRUD allergies list     |
| `allergie_formulaire.php`   | `/admin.php?action=ajouter_allergie`   | Form ajouter/éditer     |
| `traitements_gestion.php`   | `/admin.php?action=traitements`        | CRUD traitements list   |
| `traitement_formulaire.php` | `/admin.php?action=ajouter_traitement` | Form ajouter/éditer     |

### Layouts

**header.php:**

- Navigation Bootstrap 5.3 responsive
- Logo et branding
- Barre de recherche
- Menu de navigation

**footer.php:**

- Inclusion Bootstrap JS
- Inclusion validation.js
- Fermeture HTML

### Transmission de Données

Views utilisent un tableau `$data[]` pour accéder aux infos du controller:

```php
// Depuis index.php:
$data['allergies']          // Array d'objets Allergie
$data['total_allergies']    // Count total
$data['page']               // Numéro page actuelle
$data['pages']              // Total pages

// Dans la vue:
foreach ($data['allergies'] as $allergie) {
    echo $allergie->getNom();
}
```

## 📋 Conventions de Codage

### Nommage des Méthodes

- Read (GET): `obtenirParId()`, `obtenirTous()`, `recherer()`
- Create (POST): `creer()`
- Update (PUT): `mettre_a_jour()`, `modifier()`
- Delete (DELETE): `supprimer()`

### Nommage des Fichiers

- Controllers: `NomController.php` (PascalCase)
- Models: `Nom.php` (PascalCase)
- Views: `nom.php` (snake_case)
- Assets: `nom.js`, `nom.css` (snake_case)

### Structure des Réponses

```php
[
    'succes' => true/false,
    'message' => 'Message de retour',
    'data' => [...],
    'erreurs' => [...]
]
```

## 🔐 Sécurité

- **Échappement HTML**: `htmlspecialchars()` sur tous les outputs
- **Trim des espaces**: `trim()` sur toutes les entrées
- **Requêtes préparées**: PDO avec placeholders (? ou :named)
- **Validation côté serveur**: Dans les Controllers
- **Validation côté client**: JavaScript ES6 pour UX

## 🔀 Points d'Entrée (Routeurs)

### index.php - FrontOffice

```php
$action = $_GET['action'] ?? 'accueil';

switch ($action) {
    case 'allergies':          // GET listeé
    case 'detail_allergie':    // GET détail allergie
    case 'traitements':        // GET liste
    case 'detail_traitement':  // GET détail traitement
    case 'search':             // GET résultats recherche
    default: 'accueil'         // GET home
}
```

**Exemple flux:**

```
GET /index.php?action=allergies&page=1
  1. AllergiController::obtenirTous(1, 12)
  2. Allergie::obtenirTous(12, 0) → SQL: SELECT * FROM allergie LIMIT 0, 12
  3. $data['allergies'] = [Allergie, Allergie, ...]
  4. $data['total'] = 127
  5. include 'app/views/frontend/allergies_liste.php'
  6. View itère: foreach ($data['allergies'] as $allergie)
  7. Bootstrap HTML généré
```

### admin.php - BackOffice

**GET Actions (affichage formulaires & listes):**

```
GET /admin.php
  → include 'app/views/admin/dashboard.php'

GET /admin.php?action=allergies
  → $data = AllergiController::obtenirTous($page, 10)
  → include 'app/views/admin/allergies_gestion.php'

GET /admin.php?action=ajouter_allergie
  → $data['constantes'] = AllergiController::obtenirConstantes()
  → include 'app/views/admin/allergie_formulaire.php'

GET /admin.php?action=editer_allergie&id=1
  → $allergie = AllergiController::obtenirParId(1)
  → $data['allergie'] = $allergie
  → include 'app/views/admin/allergie_formulaire.php'

GET /admin.php?action=supprimer_allergie&id=1
  → AllergiController::supprimer(1)
  → header('Location: admin.php?action=allergies')
```

**POST Actions (créer/modifier):**

```
POST /admin.php?action=ajouter_allergie
  → $resultat = AllergiController::creer($_POST)
  → if ($resultat['succes']): header('Location: admin.php?action=allergies')

POST /admin.php?action=editer_allergie
  → $resultat = AllergiController::mettre_a_jour($id, $_POST)
  → if ($resultat['succes']): header('Location: admin.php?action=allergies')
```

## 📊 Diagramme Flux Requête

```
User Click
    ↓
Browser GET/POST
    ↓
index.php OU admin.php (Routeur principal)
    ↓
AllergiController::action() ou TraitementController::action()
    ↓
Allergie::method() [MODEL - SQL Queries]
    ↓
$data[] = résultats
    ↓
include 'app/views/.../view.php'
    ↓
View itère $data[] avec getters du model
    ↓
HTML Bootstrap généré
    ↓
Browser affiche
```

## ✅ Checklist Implémentation MVC

- [✅] Config Singleton pour BD
- [✅] Models Allergie et Traitement avec constructeurs paramétrés
- [✅] Getters et Setters (Fluent Interface)
- [✅] Méthodes CRUD complètes dans Models
- [✅] Controllers statiques pour logique métier
- [✅] Validation données côté serveur
- [✅] 6 Views Frontend (accueil, allergies, traitements, détails, search)
- [✅] 5 Views Admin (dashboard, CRUD allergies/traitements)
- [✅] Layouts partagés (header, footer)
- [✅] Bootstrap 5.3 intégré partout
- [✅] Validation JavaScript client
- [✅] **ZÉRO fichiers dans la racine** (sauf 2 points d'entrée!)
- [✅] Documentation complète
- [✅] Array response pattern standardisé

## 🚀 URLs de Test

### Frontend (Public)

```
http://localhost/gestion-allergies/
http://localhost/gestion-allergies/index.php
http://localhost/gestion-allergies/index.php?action=allergies
http://localhost/gestion-allergies/index.php?action=allergies&page=2
http://localhost/gestion-allergies/index.php?action=detail_allergie&id=1
http://localhost/gestion-allergies/index.php?action=traitements
http://localhost/gestion-allergies/index.php?action=detail_traitement&id=1
http://localhost/gestion-allergies/index.php?action=search&terme=pollen
```

### Admin (Gestion)

```
http://localhost/gestion-allergies/admin.php
http://localhost/gestion-allergies/admin.php?action=dashboard
http://localhost/gestion-allergies/admin.php?action=allergies
http://localhost/gestion-allergies/admin.php?action=allergies&page=2
http://localhost/gestion-allergies/admin.php?action=ajouter_allergie
http://localhost/gestion-allergies/admin.php?action=editer_allergie&id=1
http://localhost/gestion-allergies/admin.php?action=traitements
http://localhost/gestion-allergies/admin.php?action=ajouter_traitement
http://localhost/gestion-allergies/admin.php?action=editer_traitement&id=1
```

## 🎯 Cas d'Utilisation

### 1. Afficher la liste des allergies

```
Utilisateur clique "Allergies" dans le menu
  ↓
GET /index.php?action=allergies&page=1
  ↓
AllergiController::obtenirTous(1, 12)
  ↓
SELECT * FROM allergie LIMIT 0, 12
  ↓
Affiche tableau paginé avec Bootstrap
```

### 2. Créer une allergie (Admin)

```
Admin clique "Ajouter allergie"
  ↓
GET /admin.php?action=ajouter_allergie
  ↓
Affiche allergie_formulaire.php vide
  ↓
Admin remplit et soumet
  ↓
POST /admin.php?action=ajouter_allergie
  ↓
AllergiController::creer($_POST)
  ↓
INSERT INTO allergie (...)
  ↓
header('Location: /admin.php?action=allergies')
  ↓
Affiche liste mise à jour
```

### 3. Editer une allergie (Admin)

```
Admin clique le bouton "Editer"
  ↓
GET /admin.php?action=editer_allergie&id=5
  ↓
$allergie = AllergiController::obtenirParId(5)
  ↓
Affiche allergie_formulaire.php REMPLI
  ↓
Admin modifie et soumet
  ↓
POST /admin.php?action=editer_allergie
  ↓
AllergiController::mettre_a_jour(5, $_POST)
  ↓
UPDATE allergie SET ... WHERE id_allergie = 5
  ↓
header('Location: /admin.php?action=allergies')
```

---

**Architecture**: MVC Stricte avec Singleton Pattern  
**Version**: 2.0 (Réorganisation complète)  
**Dernier mise à jour**: Janvier 2025  
**Responsable**: Équipe Développement
