## API REST - Exemples d'utilisation

Point d'entrée unique: `http://localhost/gestion-allergies/index.php`

### Format URL:

```
?controller=Allergie&action=ACTION
?controller=Traitement&action=ACTION
```

---

## 🔵 ALLERGIE - API Endpoints

### 1. OBTENIR TOUTES LES ALLERGIES

```
GET http://localhost/gestion-allergies/index.php?controller=Allergie&action=obtenirTous&page=1&limite=10

Response:
{
  "success": true,
  "allergies": [
    {
      "id_allergie": 1,
      "nom": "Arachide",
      "description": "Allergie aux arachides",
      "niveau_danger": "critique",
      "symptomes": "Gonflement, démangeaisons",
      "type": "alimentaire"
    }
  ],
  "pagination": {
    "page": 1,
    "limite": 10,
    "total": 5,
    "total_pages": 1
  }
}
```

### 2. OBTENIR UNE ALLERGIE PAR ID

```
GET http://localhost/gestion-allergies/index.php?controller=Allergie&action=obtenirParId&id=1

Response:
{
  "success": true,
  "allergie": {
    "id_allergie": 1,
    "nom": "Arachide",
    ...
  }
}
```

### 3. CRÉER UNE ALLERGIE

```
POST http://localhost/gestion-allergies/index.php?controller=Allergie&action=creer

Body (JSON):
{
  "nom": "Latex",
  "description": "Allergie au latex",
  "niveau_danger": "élevé",
  "symptomes": "Urticaire, difficultés respiratoires",
  "type": "contact"
}

Response (201):
{
  "success": true,
  "message": "Allergie créée avec succès",
  "id": 6
}
```

### 4. METTRE À JOUR UNE ALLERGIE

```
PUT http://localhost/gestion-allergies/index.php?controller=Allergie&action=mettre_a_jour&id=1

Body (JSON):
{
  "nom": "Arachide - Sévère",
  "description": "Allergie grave aux arachides"
}

Response:
{
  "success": true,
  "message": "Allergie mise à jour avec succès"
}
```

### 5. SUPPRIMER UNE ALLERGIE

```
DELETE http://localhost/gestion-allergies/index.php?controller=Allergie&action=supprimer&id=1

Response:
{
  "success": true,
  "message": "Allergie supprimée avec succès"
}
```

### 6. RECHERCHER DES ALLERGIES

```
GET http://localhost/gestion-allergies/index.php?controller=Allergie&action=rechercher&terme=arachide

Response:
{
  "success": true,
  "allergies": [...],
  "count": 1
}
```

### 7. OBTENIR CONSTANTES

```
GET http://localhost/gestion-allergies/index.php?controller=Allergie&action=obtenirConstantes

Response:
{
  "success": true,
  "constantes": {
    "niveaux_danger": ["faible", "moyen", "élevé", "critique"],
    "types": ["alimentaire", "médicament", "environnemental", "contact", "autre"]
  }
}
```

---

## 🟢 TRAITEMENT - API Endpoints

### 1. OBTENIR TOUS LES TRAITEMENTS

```
GET http://localhost/gestion-allergies/index.php?controller=Traitement&action=obtenirTous&page=1&limite=10
```

### 2. OBTENIR UN TRAITEMENT PAR ID

```
GET http://localhost/gestion-allergies/index.php?controller=Traitement&action=obtenirParId&id=1
```

### 3. CRÉER UN TRAITEMENT

```
POST http://localhost/gestion-allergies/index.php?controller=Traitement&action=creer

Body (JSON):
{
  "nom": "Antihistaminique",
  "type_traitement": "pharmacie",
  "dosage": "10mg",
  "duree": "1-3 mois",
  "effets_secondaires": "Somnolence, vertiges"
}
```

### 4. METTRE À JOUR UN TRAITEMENT

```
PUT http://localhost/gestion-allergies/index.php?controller=Traitement&action=mettre_a_jour&id=1

Body (JSON):
{
  "nom": "Antihistaminique modifié",
  "dosage": "15mg"
}
```

### 5. SUPPRIMER UN TRAITEMENT

```
DELETE http://localhost/gestion-allergies/index.php?controller=Traitement&action=supprimer&id=1
```

### 6. RECHERCHER DES TRAITEMENTS

```
GET http://localhost/gestion-allergies/index.php?controller=Traitement&action=rechercher&terme=histaminique
```

### 7. OBTENIR CONSTANTES

```
GET http://localhost/gestion-allergies/index.php?controller=Traitement&action=obtenirConstantes
```

---

## 📝 NOTES IMPORTANTES

- **Pas de sessions** - Tout est accessible directement
- **Retour toujours en JSON** - Content-Type: application/json
- **Validation côté client (JS)** avant l'envoi
- **Sécurité appliquée au Controller** - htmlspecialchars + trim
- **HTTP Status Codes**:
  - 200: Succès
  - 201: Créé
  - 400: Erreur requête
  - 404: Non trouvé
  - 500: Erreur serveur

---

## 🧪 Test avec cURL

```bash
# GET
curl "http://localhost/gestion-allergies/index.php?controller=Allergie&action=obtenirTous"

# POST
curl -X POST "http://localhost/gestion-allergies/index.php?controller=Allergie&action=creer" \
  -H "Content-Type: application/json" \
  -d '{"nom":"Test","description":"Allergie test","niveau_danger":"faible","symptomes":"Aucun","type":"autre"}'

# PUT
curl -X PUT "http://localhost/gestion-allergies/index.php?controller=Allergie&action=mettre_a_jour&id=1" \
  -H "Content-Type: application/json" \
  -d '{"nom":"Allergie modifiée"}'

# DELETE
curl -X DELETE "http://localhost/gestion-allergies/index.php?controller=Allergie&action=supprimer&id=1"
```

---

## 🧠 Frontend Exemple (JavaScript Fetch)

```javascript
// GET
fetch(
  "http://localhost/gestion-allergies/index.php?controller=Allergie&action=obtenirTous?page=1&limite=10"
)
  .then((r) => r.json())
  .then((data) => console.log(data));

// POST
fetch(
  "http://localhost/gestion-allergies/index.php?controller=Allergie&action=creer",
  {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({
      nom: "Allergie Test",
      description: "Description test",
      niveau_danger: "moyen",
      symptomes: "Symptômes test",
      type: "alimentaire",
    }),
  }
)
  .then((r) => r.json())
  .then((data) => console.log(data));

// PUT
fetch(
  "http://localhost/gestion-allergies/index.php?controller=Allergie&action=mettre_a_jour&id=1",
  {
    method: "PUT",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ nom: "Nouveau nom" }),
  }
)
  .then((r) => r.json())
  .then((data) => console.log(data));

// DELETE
fetch(
  "http://localhost/gestion-allergies/index.php?controller=Allergie&action=supprimer&id=1",
  {
    method: "DELETE",
  }
)
  .then((r) => r.json())
  .then((data) => console.log(data));
```
