# 🏥 Système de Gestion des Allergies et Traitements

> Un système web complet de gestion des allergies et traitements médicaux avec CRUD fonctionnel, architecture MVC, et validations côté serveur.

![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C87?style=for-the-badge&logo=mysql&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green.svg?style=for-the-badge)

---

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Architecture](#-architecture)
- [Installation](#-installation)
- [Utilisation](#-utilisation)
- [Validations](#-validations)
- [Structure du Projet](#-structure-du-projet)
- [Technologies](#-technologies)
- [Contribueurs](#-contribueurs)
- [Licence](#-licence)

---

## ✨ Fonctionnalités

### BackOffice (Admin)

- ✅ **CRUD complet** pour les allergies
  - **C**reate: Ajouter de nouvelles allergies
  - **R**ead: Afficher la liste paginée
  - **U**pdate: Éditer les allergies existantes
  - **D**elete: Supprimer les allergies
- 📊 Dashboard avec statistiques
- 🔍 Recherche et filtrage
- 📄 Pagination (10 par page)
- 🎨 Interface responsive et moderne

### FrontOffice (Client)

- 🏠 Accueil avec statistiques
- 📋 Affichage de toutes les allergies
- 🔍 Recherche en temps réel
- 📖 Détails complets d'une allergie
- 📱 Design responsive mobile-first

---

## 🏗️ Architecture

### Pattern MVC

```
app/
├── models/
│   └── Allergie.php         # Logique métier, validations, requêtes BD
├── controllers/
│   └── AllergiController.php # Contrôle des actions, logique applicative
└── views/                    # Templates HTML (index.php et admin.php)

config/
└── Database.php              # Connexion PDO et gestion BD
```

### Principes

- **MVC**: Séparation des responsabilités
- **POO**: Classes, héritage, encapsulation
- **PDO**: Prépention des injections SQL
- **Validation serveur**: Pas de HTML5, validation PHP pure

---

## 🔧 Installation

### Prérequis

- PHP 7.4+
- MySQL 5.7+ ou MariaDB 10.3+
- XAMPP ou un serveur web local
- Git

### Étapes

#### 1. Cloner le repository

```bash
git clone https://github.com/votre-username/gestion-allergies.git
cd gestion-allergies
```

#### 2. Configuration XAMPP

```
Copier le dossier dans: C:\xampp\htdocs\gestion-allergies
```

#### 3. Démarrer les services

- Lancer XAMPP Control Panel
- Activer **Apache** et **MySQL**

#### 4. Initialiser la base de données

La base de données `gestion_allergies` sera créée automatiquement au premier accès (voir `config/Database.php`)

#### 5. Accéder à l'application

- **FrontOffice**: http://localhost/gestion-allergies/index.php
- **BackOffice**: http://localhost/gestion-allergies/admin.php

---

## 📖 Utilisation

### FrontOffice (Publique)

```
http://localhost/gestion-allergies/index.php
```

- Accueil avec statistiques
- Parcourir toutes les allergies
- Chercher par nom/symptômes/type
- Voir les détails de chaque allergie
- Accès restreint à la lecture

### BackOffice (Admin)

```
http://localhost/gestion-allergies/admin.php
```

#### Ajouter une allergie

1. Cliquer sur "➕ Ajouter une allergie"
2. Remplir le formulaire complet
3. Les validations se feront côté serveur
4. Confirmation du succès

#### Éditer une allergie

1. Cliquer sur ✏️ dans la liste
2. Modifier les champs
3. Enregistrer les modifications
4. Retour à la liste

#### Supprimer une allergie

1. Cliquer sur 🗑️ dans la liste
2. Confirmer la suppression
3. L'allergie est supprimée avec ses associations

#### Rechercher

- Utiliser la barre de recherche du FrontOffice
- Permet de chercher par: nom, type, symptômes

---

## ✔️ Validations

### Côté Serveur (PHP - Obligatoire)

Toutes les validations se font en PHP, **aucune validation HTML5**:

#### Champ "Nom"

- Non vide
- Min: 3 caractères
- Max: 100 caractères
- Format: Lettres et espaces uniquement
- Unicité dans la BD

#### Champ "Description"

- Non vide
- Min: 5 caractères

#### Champ "Niveau de danger"

- Required: [faible, moyen, élevé, critique]

#### Champ "Symptômes"

- Non vide
- Min: 5 caractères

#### Champ "Type"

- Required: [alimentaire, médicament, environnemental, contact, autre]

### Messages d'erreur

```php
// Exemple de réponse avec erreurs
{
    "succes": false,
    "erreurs": {
        "nom": "Le nom doit contenir au moins 3 caractères",
        "niveau_danger": "Le niveau de danger est invalide"
    }
}
```

---

## 📁 Structure du Projet

```
gestion-allergies/
├── app/
│   ├── models/
│   │   ├── Allergie.php           # Modèle Allergie (297 lignes)
│   │   └── Traitement.php         # À implémenter
│   ├── controllers/
│   │   ├── AllergiController.php   # Contrôleur Allergie
│   │   └── TraitementController.php # À implémenter
│   └── views/                     # Templates
├── config/
│   ├── Database.php               # Connexion PDO, gestion tables
│   └── config.php                 # Configuration app
├── assets/
│   ├── css/
│   │   ├── admin.css              # Styles dashboard
│   │   └── client.css              # Styles frontend
│   └── js/                        # JavaScript (futur)
├── admin.php                      # Point d'entrée BackOffice
├── index.php                      # Point d'entrée FrontOffice
├── README.md                      # Documentation
├── .gitignore                     # Git ignore
└── .github/
    └── workflows/                 # CI/CD (futur)
```

---

## 🛠️ Technologies

| Layer            | Technology                                  |
| ---------------- | ------------------------------------------- |
| **Frontend**     | HTML5, CSS3, JavaScript (Vanilla)           |
| **Backend**      | PHP 7.4+ (POO)                              |
| **Database**     | MySQL 5.7+ avec PDO                         |
| **Server**       | Apache (XAMPP)                              |
| **Architecture** | MVC Pattern                                 |
| **Security**     | PDO Prepared Statements, Input Sanitization |

---

## 🚀 Améliorations Futures

- [ ] Authentification utilisateur
- [ ] Gestion des traitements (CRUD)
- [ ] Association allergie-traitement
- [ ] Export PDF/Excel
- [ ] API REST
- [ ] Tests unitaires PHPUnit
- [ ] Docker setup
- [ ] Notification par email
- [ ] Dashboard analytics avancé
- [ ] Gestion des utilisateurs

---

## 🤝 Contribueurs

- **Youssef** - Développeur Lead
- Equipe Groupe - Contributeurs

---

## 📜 Licence

Ce projet est sous licence MIT. Voir [LICENSE.md](LICENSE.md) pour plus de détails.

---

## 📞 Support

Pour toute question ou problème:

- Ouvrir une issue sur GitHub
- Consulter la documentation
- Vérifier XAMPP est bien démarré
- S'assurer que MySQL est actif

---

## 📝 Notes de Développement

### Base de Données

```sql
-- Tables créées automatiquement:
CREATE TABLE allergie (
    id_allergie INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    niveau_danger VARCHAR(50),
    symptomes TEXT,
    type VARCHAR(50)
);

CREATE TABLE traitement (
    id_traitement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    type_traitement VARCHAR(100),
    dosage VARCHAR(100),
    duree VARCHAR(100),
    effets_secondaires TEXT
);
```

### Bonnes Pratiques Implémentées

✅ Validation serveur stricte (pas HTML5)
✅ Prepared statements PDO
✅ Gestion des erreurs
✅ Architecture MVC
✅ Code orienté objet
✅ Sanitization des entrées
✅ Messages d'erreur clairs
✅ Interface responsive
✅ Code commenté

---

**Créé avec ❤️ pour la gestion des allergies**
