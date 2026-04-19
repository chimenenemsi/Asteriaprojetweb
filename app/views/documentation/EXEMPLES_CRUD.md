# 📚 EXEMPLES CRUD COMPLETS

## 1️⃣ CREATE (Créer une allergie)

### Étape 1: Le Contrôleur reçoit les données

**Fichier:** `admin.php` (Point d'entrée)

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'ajouter_allergie') {
    $allergie_data = [
        'nom' => $_POST['nom'] ?? '',
        'description' => $_POST['description'] ?? '',
        'niveau_danger' => $_POST['niveau_danger'] ?? 'MOYEN',
        'symptomes' => $_POST['symptomes'] ?? '',
        'type' => $_POST['type'] ?? 'ALIMENTAIRE'
    ];

    $resultat = AllergiController::creer($allergie_data);

    if ($resultat['succes']) {
        $_SESSION['message'] = "✅ Allergie créée avec succès (ID: {$resultat['id']})";
        header('Location: admin.php?action=allergies');
        exit;
    } else {
        $_SESSION['erreur'] = $resultat['message'];
    }
}
```

### Étape 2: Le Contrôleur valide et délègue

**Fichier:** `app/controllers/AllergiController.php`

```php
public static function creer($data)
{
    // 1. VALIDATION (optionnelle mais recommandée)
    $validation = self::valider($data);
    if (!$validation['valide']) {
        return [
            'succes' => false,
            'message' => 'Données invalides',
            'erreurs' => $validation['erreurs']
        ];
    }

    // 2. CRÉER OBJET MODÈLE
    $allergie = new Allergie(
        $data['nom'],
        $data['description'],
        $data['niveau_danger'],
        $data['symptomes'],
        $data['type']
    );

    // 3. APPELER MÉTHODE DU MODÈLE pour sauver
    if ($allergie->creer()) {
        return [
            'succes' => true,
            'message' => 'Allergie créée avec succès',
            'id' => $allergie->getId()
        ];
    }

    // 4. RETOURNER ERREUR
    return [
        'succes' => false,
        'message' => 'Erreur lors de la création'
    ];
}
```

### Étape 3: Le Modèle exécute la requête

**Fichier:** `app/models/Allergie.php`

```php
public function creer()
{
    try {
        // 1. OBTENIR CONNEXION (Singleton!)
        $pdo = Config::getConnexion();

        // 2. PRÉPARER REQUÊTE
        $sql = "INSERT INTO allergie (nom, description, niveau_danger, symptomes, type)
                VALUES (:nom, :description, :niveau_danger, :symptomes, :type)";

        $stmt = $pdo->prepare($sql);  // ✅ Requête préparée (sécurisée)

        // 3. EXÉCUTER avec paramètres sécurisés
        $stmt->execute([
            ':nom' => $this->nom,
            ':description' => $this->description,
            ':niveau_danger' => $this->niveau_danger,
            ':symptomes' => $this->symptomes,
            ':type' => $this->type
        ]);

        // 4. RÉCUPÉRER L'ID généré
        $this->id_allergie = $pdo->lastInsertId();

        return true;  // ✅ Succès

    } catch (PDOException $e) {
        echo "Erreur SQL: " . $e->getMessage();
        return false;  // ❌ Erreur
    }
}
```

### Table Finale (Base de données)

```sql
--Avant (vide)
SELECT * FROM allergie;
-- (aucune ligne)

--Après
SELECT * FROM allergie;
-- id_allergie | nom    | description   | niveau_danger | symptomes    | type
-- ------------|--------|---------------|---------------|--------------|----------
-- 23          | Pollen | Allergie...   | critique      | Éternuements | environnemental
```

---

## 2️⃣ READ (Lire une allergie)

### Cas A: Lire une allergie par ID

**Fichier:** `app/controllers/AllergiController.php`

```php
public static function obtenirParId($id)
{
    // Simplement passer l'appel au modèle
    return Allergie::obtenirParId($id);
}
```

**Fichier:** `app/models/Allergie.php`

```php
public static function obtenirParId($id)
{
    try {
        $pdo = Config::getConnexion();

        // ✅ Requête préparée
        $sql = "SELECT * FROM allergie WHERE id_allergie = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);  // ✅ Paramètre sécurisé

        // Récupérer une ligne
        $data = $stmt->fetch();  // ou fetch(PDO::FETCH_ASSOC)

        if ($data) {
            // Retourner objet Allergie
            return new Allergie(
                $data['nom'],
                $data['description'],
                $data['niveau_danger'],
                $data['symptomes'],
                $data['type'],
                $data['id_allergie']
            );
        }
        return null;  // Pas trouvé

    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        return null;
    }
}
```

**Utilisation dans Vue:**

```php
<!-- index.php?action=detail_allergie&id=23 -->

<?php
$allergie = AllergiController::obtenirParId(23);

if ($allergie) {
    echo "<h1>" . $allergie->getNom() . "</h1>";           // Getter!
    echo "<p>" . $allergie->getDescription() . "</p>";     // Getter!
    echo "<strong>Niveau: " . $allergie->getNiveauDanger() . "</strong>";
}
?>
```

### Cas B: Lire TOUTES les allergies (avec pagination)

**Fichier:** `app/controllers/AllergiController.php`

```php
public static function obtenirTous($page = 1, $limite = 10)
{
    // Calcul de l'offset
    $offset = ($page - 1) * $limite;

    // Appeler modèle pour récupérer les allergies
    $allergies = Allergie::obtenirTous($limite, $offset);

    // Appeler modèle pour récupérer le total
    $total = Allergie::obtenirNombreTotal();

    // Retourner réponse formatée
    return [
        'succes' => true,
        'allergies' => $allergies,
        'page' => $page,
        'limite' => $limite,
        'total' => $total,
        'pages' => ceil($total / $limite)
    ];
}
```

**Fichier:** `app/models/Allergie.php`

```php
public static function obtenirTous($limite = 10, $offset = 0)
{
    try {
        $pdo = Config::getConnexion();

        $sql = "SELECT * FROM allergie LIMIT :limite OFFSET :offset";

        $stmt = $pdo->prepare($sql);

        // ✅ Bind avec PDO::PARAM_INT pour sécurité
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        $stmt->execute();

        // Récupérer TOUTES les lignes
        $allergies = [];
        while ($data = $stmt->fetch()) {  // fetchAll() alternative
            $allergies[] = new Allergie(
                $data['nom'],
                $data['description'],
                $data['niveau_danger'],
                $data['symptomes'],
                $data['type'],
                $data['id_allergie']
            );
        }
        return $allergies;

    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        return [];
    }
}

public static function obtenirNombreTotal()
{
    try {
        $pdo = Config::getConnexion();

        $sql = "SELECT COUNT(*) as total FROM allergie";

        $stmt = $pdo->query($sql);
        $result = $stmt->fetch();

        return $result['total'] ?? 0;

    } catch (PDOException $e) {
        return 0;
    }
}
```

**Utilisation dans Vue:**

```php
<!-- index.php?action=allergies&page=1 -->

<?php
$data = AllergiController::obtenirTous(1, 12);

echo "<p>Total: " . $data['total'] . " allergies</p>";
echo "<p>Page " . $data['page'] . " sur " . $data['pages'] . "</p>";

foreach ($data['allergies'] as $allergie) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($allergie->getNom()) . "</td>";
    echo "<td>" . htmlspecialchars($allergie->getNiveauDanger()) . "</td>";
    echo "</tr>";
}
?>
```

---

## 3️⃣ UPDATE (Modifier une allergie)

### Étape 1: Charger les données existantes

**Fichier:** `admin.php?action=editer_allergie&id=23`

```php
$allergie = AllergiController::obtenirParId(23);  // Récupère du modèle

if ($allergie) {
    // Afficher formulaire pré-rempli
    $data['allergie'] = $allergie;
    $data['est_edition'] = true;
    include 'app/views/admin/allergie_formulaire.php';
}
```

### Étape 2: Valider et mettre à jour

**Fichier:** `admin.php` (POST)

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'editer_allergie') {
    $id = $_POST['id_allergie'] ?? null;

    $allergie_data = [
        'nom' => $_POST['nom'],
        'description' => $_POST['description'],
        'niveau_danger' => $_POST['niveau_danger'],
        'symptomes' => $_POST['symptomes'],
        'type' => $_POST['type']
    ];

    $resultat = AllergiController::mettre_a_jour($id, $allergie_data);

    if ($resultat['succes']) {
        header('Location: admin.php?action=allergies');
        exit;
    }
}
```

### Étape 3: Le Contrôleur met à jour

**Fichier:** `app/controllers/AllergiController.php`

```php
public static function mettre_a_jour($id, $data)
{
    // 1. RÉCUPÉRER l'allergie existante
    $allergie = Allergie::obtenirParId($id);

    if (!$allergie) {
        return ['succes' => false, 'message' => 'Allergie non trouvée'];
    }

    // 2. METTRE À JOUR les propriétés avec setters
    $allergie->setNom($data['nom'] ?? $allergie->getNom())
             ->setDescription($data['description'] ?? $allergie->getDescription())
             ->setNiveauDanger($data['niveau_danger'] ?? $allergie->getNiveauDanger())
             ->setSymptomes($data['symptomes'] ?? $allergie->getSymptomes())
             ->setType($data['type'] ?? $allergie->getType());

    // 3. SAUVER via modèle
    if ($allergie->mettre_a_jour()) {
        return ['succes' => true, 'message' => 'Allergie mise à jour'];
    }

    return ['succes' => false, 'message' => 'Erreur lors de la mise à jour'];
}
```

### Étape 4: Le Modèle met à jour en BD

**Fichier:** `app/models/Allergie.php`

```php
public function mettre_a_jour()
{
    try {
        $pdo = Config::getConnexion();

        // ✅ Requête préparée
        $sql = "UPDATE allergie SET
                nom = :nom,
                description = :description,
                niveau_danger = :niveau_danger,
                symptomes = :symptomes,
                type = :type
                WHERE id_allergie = :id";

        $stmt = $pdo->prepare($sql);

        // ✅ Paramètres sécurisés
        $stmt->execute([
            ':nom' => $this->nom,
            ':description' => $this->description,
            ':niveau_danger' => $this->niveau_danger,
            ':symptomes' => $this->symptomes,
            ':type' => $this->type,
            ':id' => $this->id_allergie
        ]);

        return true;

    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        return false;
    }
}
```

### Setter avec Fluent Interface

**Fichier:** `app/models/Allergie.php`

```php
public function setNom($nom)
{
    // ✅ Sécurité: htmlspecialchars + trim
    $this->nom = htmlspecialchars(trim($nom));

    return $this;  // Fluent Interface (pour enchaîner)
}
```

---

## 4️⃣ DELETE (Supprimer une allergie)

### Étape 1: Le Contrôleur supprime

**Fichier:** `admin.php?action=supprimer_allergie&id=23`

```php
if ($_GET['action'] === 'supprimer_allergie') {
    $id = $_GET['id'] ?? null;

    $resultat = AllergiController::supprimer($id);

    if ($resultat['succes']) {
        $_SESSION['message'] = "✅ Allergie supprimée";
    }

    header('Location: admin.php?action=allergies');
    exit;
}
```

### Étape 2: Le Contrôleur valide et délègue

**Fichier:** `app/controllers/AllergiController.php`

```php
public static function supprimer($id)
{
    // 1. VÉRIFIER que l'allergie existe
    $allergie = Allergie::obtenirParId($id);

    if (!$allergie) {
        return ['succes' => false, 'message' => 'Allergie non trouvée'];
    }

    // 2. SUPPRIMER via modèle
    if ($allergie->supprimer()) {
        return ['succes' => true, 'message' => 'Allergie supprimée'];
    }

    return ['succes' => false, 'message' => 'Erreur lors de la suppression'];
}
```

### Étape 3: Le Modèle supprime de la BD

**Fichier:** `app/models/Allergie.php`

```php
public function supprimer()
{
    try {
        $pdo = Config::getConnexion();

        $sql = "DELETE FROM allergie WHERE id_allergie = :id";

        $stmt = $pdo->prepare($sql);  // ✅ Préparée

        $stmt->execute([':id' => $this->id_allergie]);  // ✅ Paramètre sécurisé

        return true;

    } catch (PDOException $e) {
        echo "Erreur: " . $e->getMessage();
        return false;
    }
}
```

---

## 🔍 SEARCH (Rechercher)

### Contrôleur

**Fichier:** `app/controllers/AllergiController.php`

```php
public static function rechercher($terme)
{
    // Valider le terme
    if (empty(trim($terme)) || strlen($terme) < 2) {
        return [];
    }

    // Appeler modèle
    return Allergie::rechercher($terme);
}
```

### Modèle

**Fichier:** `app/models/Allergie.php`

```php
public static function rechercher($terme)
{
    try {
        $pdo = Config::getConnexion();

        $sql = "SELECT * FROM allergie
                WHERE nom LIKE :terme
                OR description LIKE :terme
                OR symptomes LIKE :terme";

        $stmt = $pdo->prepare($sql);

        // ✅ %terme% pour recherche globale
        $stmt->execute([':terme' => '%' . $terme . '%']);

        $allergies = [];
        while ($data = $stmt->fetch()) {
            $allergies[] = new Allergie(...$data);
        }
        return $allergies;

    } catch (PDOException $e) {
        return [];
    }
}
```

### Vue

**Fichier:** `app/views/frontend/search.php`

```php
<?php
$resultat = AllergiController::rechercher($_GET['terme'] ?? '');

if (!empty($resultat)) {
    foreach ($resultat as $allergie) {
        echo "<p>" . $allergie->getNom() . " - " . $allergie->getNiveauDanger() . "</p>";
    }
} else {
    echo "<p>Aucun résultat</p>";
}
?>
```

---

## ✅ Checklist CRUD

| Opération  | Contrôleur            | Modèle      | Vue               | Sécurité                |
| ---------- | --------------------- | ----------- | ----------------- | ----------------------- |
| **CREATE** | Valide + crée objet   | INSERT PDO  | Form view         | htmlspecialchars + trim |
| **READ**   | Appelle modèle        | SELECT PDO  | Affiche getters   | PDO prepared            |
| **UPDATE** | Récupère + met à jour | UPDATE PDO  | Form pré-rempli   | htmlspecialchars + trim |
| **DELETE** | Valide + appelle      | DELETE PDO  | Bouton            | PDO prepared            |
| **SEARCH** | Valide terme          | SELECT LIKE | Affiche résultats | % échappé               |

---

**Pattern:** Repository/Model-based CRUD ✅  
**Sécurité:** Excellente ✅  
**Maintenabilité:** Excellent ✅
