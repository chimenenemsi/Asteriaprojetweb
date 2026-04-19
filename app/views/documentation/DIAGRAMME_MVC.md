# 🏗️ Architecture MVC - Diagramme Détaillé

## 📐 Diagramme de l'Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        POINT D'ENTRÉE                           │
│                    index.php / admin.php                        │
│                     (Routeur Principal)                         │
└────────────────────────────────┬────────────────────────────────┘
                                 │
                    ┌────────────┴────────────┐
                    ↓                         ↓
        ┌─────────────────────┐   ┌──────────────────────┐
        │    AllergiController    │   │ TraitementController │
        │   (Méthodes statiques)  │   │ (Méthodes statiques) │
        │                         │   │                      │
        │ ✅ creer()              │   │ ✅ creer()           │
        │ ✅ obtenirParId()       │   │ ✅ obtenirParId()    │
        │ ✅ obtenirTous()        │   │ ✅ obtenirTous()     │
        │ ✅ mettre_a_jour()      │   │ ✅ mettre_a_jour()   │
        │ ✅ supprimer()          │   │ ✅ supprimer()       │
        │ ✅ rechercher()         │   │ ✅ rechercher()      │
        │ ✅ valider()            │   │ ✅ valider()         │
        │                         │   │                      │
        │ [Validation + Format]   │   │ [Validation + Format]│
        └────────────┬────────────┘   └──────────┬───────────┘
                     │                           │
                     └───────────┬───────────────┘
                                 ↓
                    ┌────────────────────────────┐
                    │   MODELS (Modèles)         │
                    ├────────────────────────────┤
                    │  ┌──────────────────────┐  │
                    │  │   Allergie.php       │  │
                    │  │ ────────────────────  │  │
                    │  │ Attributes:          │  │
                    │  │ • id_allergie        │  │
                    │  │ • nom                │  │
                    │  │ • description        │  │
                    │  │ • niveau_danger      │  │
                    │  │ • symptomes          │  │
                    │  │ • type               │  │
                    │  │                      │  │
                    │  │ Getters: ✅ (6)      │  │
                    │  │ Setters: ✅ (6)      │  │
                    │  │                      │  │
                    │  │ CRUD Methods:        │  │
                    │  │ • creer()            │  │
                    │  │ • obtenirParId()     │  │
                    │  │ • obtenirTous()      │  │
                    │  │ • mettre_a_jour()    │  │
                    │  │ • supprimer()        │  │
                    │  │ • rechercher()       │  │
                    │  │                      │  │
                    │  │ PDO Queries:         │  │
                    │  │ • INSERT ✅          │  │
                    │  │ • SELECT ✅          │  │
                    │  │ • UPDATE ✅          │  │
                    │  │ • DELETE ✅          │  │
                    │  │ • SEARCH ✅          │  │
                    │  └──────────────────────┘  │
                    │  ┌──────────────────────┐  │
                    │  │  Traitement.php      │  │
                    │  │  (Même structure)    │  │
                    │  └──────────────────────┘  │
                    └────────────┬───────────────┘
                                 ↓
                    ┌────────────────────────────┐
                    │   CONFIG (Configuration)   │
                    ├────────────────────────────┤
                    │  config/Database.php       │
                    │  ────────────────────      │
                    │                            │
                    │  class Config {            │
                    │   private static $pdo;     │
                    │                            │
                    │   public static function   │
                    │   getConnexion() {...}     │ → Singleton Pattern
                    │                            │
                    │   Une seule connexion!     │
                    │  }                         │
                    └────────────┬───────────────┘
                                 ↓
                    ┌────────────────────────────┐
                    │   BASE DE DONNÉES          │
                    │                            │
                    │  projet2a33                │
                    │  ├── allergie table        │
                    │  └── traitement table      │
                    └────────────────────────────┘
```

---

## 📊 Flux d'une Requête GET (Afficher liste)

```
User clique "Allergies"
        ↓
GET /index.php?action=allergies&page=1
        ↓
index.php route switch→action="allergies"
        ↓
AllergiController::obtenirTous(1, 12)
    {
        offset = (1-1)*12 = 0
        allergies = Allergie::obtenirTous(12, 0)
                    ↓
                    SELECT * FROM allergie LIMIT 0, 12
                    ↓
                    [$allergie1, $allergie2, ...]

        total = Allergie::obtenirNombreTotal()
                 ↓
                 SELECT COUNT(*) FROM allergie
                 ↓
                 127

        return [
            'allergies' => $allergies,
            'total' => 127,
            'page' => 1,
            'pages' => 11
        ]
    }
        ↓
index.php set $data = résultat
        ↓
include 'app/views/frontend/allergies_liste.php'
        ↓
Vue itère:
    foreach ($data['allergies'] as $allergie) {
        echo $allergie->getNom()    ← Utilise GETTER!
    }
        ↓
HTML généré avec Bootstrap
        ↓
Browser affiche tableau
```

---

## 📊 Flux d'une Requête POST (Créer)

```
Admin remplit formulaire et clique "Créer"
        ↓
POST /admin.php?action=ajouter_allergie
POST data: {nom: "Pollen", description: "...", ...}
        ↓
admin.php $_SERVER['REQUEST_METHOD'] === 'POST'
        ↓
AllergiController::creer($_POST)
    {
        // Validation optionnelle
        $validation = AllergiController::valider($_POST)
        if (!$validation['valide']) {
            return error
        }

        // Créer objet modèle
        $allergie = new Allergie(
            $_POST['nom'],
            $_POST['description'],
            ...
        )

        // Appeler méthode du modèle
        if ($allergie->creer()) {
            return ['succes' => true, 'message' => '...']
        }
    }
        ↓
Allergie::creer()
    {
        pdo = Config::getConnexion()  ← Singleton!

        sql = "INSERT INTO allergie (nom, description, ...)
               VALUES (:nom, :description, ...)"

        stmt = pdo->prepare(sql)      ← Requête préparée
        stmt->execute([
            ':nom' => $this->nom,
            ...
        ])

        this->id_allergie = pdo->lastInsertId()
        return true
    }
        ↓
result = succes → true
        ↓
admin.php recoit ['succes' => true, ...]
        ↓
header('Location: /admin.php?action=allergies')
        ↓
Browser redirige
        ↓
Admin voit la liste mise à jour
```

---

## 🔄 Communication entre les Couches

### Contrôleur → Modèle

```php
// Controller APPELLE le modèle

// Créer objet
$allergie = new Allergie($nom, $desc, ...);

// Appeler méthode
$allergie->creer();                           // Instance method
$allergie_recuperee = Allergie::obtenirParId($id);  // Static method

// Appeler setter
$allergie->setNom($nouveau_nom)
         ->setDescription($nouvelle_desc);   // Fluent Interface
```

### Modèle → Contrôleur

```php
// Model RETOURNE au contrôleur

// Pour single item:
return new Allergie(...);  // Objet avec accès aux getters

// Pour liste:
return [Allergie, Allergie, ...];  // Array d'objets

// Pour opérations:
return true/false;  // Boolean
```

### Contrôleur → Vue

```php
// Controller PREPARE les DONNÉES

$data = [
    'allergies' => $allergies,  // Array d'objets Allergie
    'total' => 127,
    'page' => 1,
    'pages' => 11
];

include 'app/views/frontend/allergies_liste.php';
```

### Vue → Modèle (Lecture)

```php
// View UTILISE les getters

foreach ($data['allergies'] as $allergie) {
    echo $allergie->getNom();              // Getter!
    echo $allergie->getDescription();      // Getter!
    echo $allergie->getNiveauDanger();     // Getter!
}

// Vue n'accède JAMAIS direct aux attributs privés
// Vue n'accède JAMAIS à la base de données
```

---

## 🔐 Points de Sécurité

```
INPUT (User POST data)
        ↓
Controller validation ✅
- check empty
- check length
- check type
        ↓
Model setter processing ✅
- htmlspecialchars()        ← XSS prevention
- trim()                    ← Clean input
        ↓
Database query ✅
- PDO prepared statement    ← SQL injection prevention
- :placeholders             ← Type safe
        ↓
Model getter returning ✅
- Data is escaped already
        ↓
View output ✅
- htmlspecialchars() again  ← Double protection
        ↓
OUTPUT (Safe HTML)
```

---

## 📋 Checklist d'Utilisation

### ✅ CÉ QU'ON FAIT CORRECTEMENT

```php
// ✅ Créer objet avec constructeur
$allergie = new Allergie("Pollen", "Description...");

// ✅ Utiliser setters
$allergie->setNiveauDanger("critique");

// ✅ Appeler méthode du modèle via contrôleur
$resultat = AllergiController::creer(['nom' => '...']);

// ✅ Utiliser getters dans les vues
echo $allergie->getNom();

// ✅ Requêtes PDO préparées
$stmt = $pdo->prepare("SELECT * FROM allergie WHERE id = :id");
$stmt->execute([':id' => $id]);

// ✅ Validation dans le contrôleur
AllergiController::valider($data);

// ✅ Réponse standardisée du contrôleur
return ['succes' => true, 'message' => '...', 'data' => [...], 'erreurs' => [...]];
```

### ❌ CE QU'ON NE FAIT PAS

```php
// ❌ Requête SQL direkte dans la vue
SELECT * FROM allergie  // NE PAS faire!

// ❌ Accès direct aux attributs privés
echo $allergie->nom;    // ERREUR! Utiliser getNom()

// ❌ Construction de requête avec concaténation
$sql = "SELECT * FROM allergie WHERE nom = '" . $nom . "'";  // SQL INJECTION!

// ❌ Requête SQL directe dans le contrôleur
$pdo->query("INSERT INTO allergie ...");  // Le modèle le fait!

// ❌ HTML dans le contrôleur
echo "<h1>...</h1>";    // C'est la vue qui l'affiche!

// ❌ Logique complexe dans la vue
if ($allergie->id > 10 && $allergie->type == 'algo') { ... }  // Au contrôleur!
```

---

## 🎯 Résumé par Couche

| Couche         | Responsabilité          | Contient                                         | N'a pas                 |
| -------------- | ----------------------- | ------------------------------------------------ | ----------------------- |
| **Model**      | Données + Persistance   | Attributs, Getters, Setters, CRUD, PDO, Requêtes | Validation métier, HTML |
| **Controller** | Logique + Orchestration | Validation, Appel modèle, Format réponse         | Requêtes SQL, HTML      |
| **View**       | Affichage               | HTML, Bootstrap, Getters                         | Requêtes SQL, Logique   |
| **Config**     | Configuration           | Singleton PDO                                    | Business logic          |

---

**Architecture:** MVC Stricte ✅  
**Sécurité:** Excellente (PDO + htmlspecialchars) ✅  
**Maintenabilité:** Excellente (Séparation claire) ✅  
**Réutilisabilité:** Excellente (Modèles testables) ✅
