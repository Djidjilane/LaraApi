# Spécifications techniques — SchoolSoft

## 1. Objet du document

Ce document présente les informations techniques nécessaires à la compréhension, au développement, à la maintenance et à l'évolution du projet **SchoolSoft**.

Il complète le cahier des charges en précisant notamment l'environnement de développement, l'organisation du code source, les conventions techniques, les procédures de test et les règles de contribution.

Les besoins fonctionnels et les règles métier détaillés sont définis dans le cahier des charges du projet.

---

## 2. Architecture technique

SchoolSoft repose sur une architecture séparant le frontend, le backend et la base de données.


┌──────────────────────────────┐
│          UTILISATEUR         │
└──────────────┬───────────────┘
               │
               ▼
┌──────────────────────────────┐
│       FRONTEND WEB           │
│      React.js + Vite         │
└──────────────┬───────────────┘
               │
          HTTP / JSON
               │
               ▼
┌──────────────────────────────┐
│          BACKEND             │
│         Laravel 12           │
│                              │
│ Controllers / Requests       │
│ Models / Services / API      │
└──────────────┬───────────────┘
               │
          Eloquent ORM
               │
               ▼
┌──────────────────────────────┐
│       BASE DE DONNÉES        │
│        MySQL/MariaDB         │
└──────────────────────────────┘
```

Cette séparation permet de développer et de maintenir indépendamment l'interface utilisateur et la logique métier.

---

## 3. Environnement technique

### Backend

* PHP 8.2 ou supérieur
* Laravel 12
* Composer
* MySQL/MariaDB

### Frontend

* Node.js
* npm
* React.js
* Vite

### Outils de développement

* Visual Studio Code
* Git
* GitHub
* Postman pour les tests de l'API

---

## 4. Installation du projet

### 4.1 Récupération du projet

Cloner le dépôt Git :


git clone <URL_DU_DEPOT>
cd SchoolSoft


### 4.2 Installation du backend

Installer les dépendances PHP :

```bash
composer install
```

Créer le fichier d'environnement :


cp .env.example .env
```

Sous Windows PowerShell :


copy .env.example .env
```

Générer la clé de l'application :


php artisan key:generate
```

Configurer les informations de connexion à la base de données dans `.env`.



```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_api
DB_USERNAME=root
DB_PASSWORD=
```

Exécuter les migrations :

`
php artisan migrate
```

### 4.3 Installation du frontend

Installer les dépendances Node.js :

```bash
npm install
```

Lancer le serveur de développement :

```bash
npm run dev
```

### 4.4 Lancement du backend

Dans un autre terminal :

```bash
php artisan serve
```

Le frontend et le backend peuvent ainsi fonctionner simultanément pendant le développement.

---

## 5. Organisation du projet

L'organisation générale du projet est la suivante :

```text
SchoolSoft/
│
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   ├── Requests/
│   │   └── Resources/
│   ├── Models/
│   └── Services/
│
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
│
├── routes/
│   ├── api.php
│   └── web.php
│
├── tests/
│   ├── Feature/
│   └── Unit/
│
├── resources/
│
├── src/
│   ├── components/
│   ├── pages/
│   ├── services/
│   ├── hooks/
│   ├── layouts/
│   └── utils/
│
├── docs/
│   ├── cahier-des-charges.pdf
│   ├── specifications-techniques.md
│   └── diagrammes/
│
└── README.md
```

La structure exacte peut évoluer au cours du développement, mais les responsabilités des différentes parties doivent rester clairement séparées.

---

## 6. Organisation du backend Laravel

### Controllers

Les contrôleurs sont responsables de la réception des requêtes HTTP et de l'appel aux traitements nécessaires.

Exemples :

```text
AuthController
FormationController
UEController
ECUEController
UEECUEController
EtudiantController
InscriptionController
NoteController
```

Les contrôleurs doivent rester simples et éviter de contenir des traitements métier complexes.

### Form Requests

Les `FormRequest` permettent de centraliser les règles de validation des données reçues par l'API.

Exemples :

```text
RegisterRequest
LoginRequest
FormationRequest
NoteRequest
InscriptionRequest
```

### Models

Les modèles Eloquent représentent les principales entités de la base de données et définissent leurs relations.

### Services

Les traitements métier complexes doivent être regroupés dans des services.

Exemples :

```text
CalculNoteService
ResultService
```

Cette organisation facilite les tests et évite de surcharger les contrôleurs.

---

## 7. Organisation du frontend

L'interface est développée avec React.js.

Les composants réutilisables sont placés dans :


src/components/
```

Les pages principales sont placées dans :


src/pages/
```

Les communications avec l'API sont regroupées dans :

`
src/services/
```


authService.js
formationService.js
ueService.js
ecueService.js
etudiantService.js
inscriptionService.js
noteService.js
```

Cette organisation permet de séparer :

* l'affichage ;
* la logique des composants ;
* la navigation ;
* les appels à l'API.

---

## 8. Communication Frontend / Backend

Le frontend communique avec l'API Laravel au moyen de requêtes HTTP.

Les données sont échangées au format JSON.

Exemple de communication :

```text
React
  │
  │ POST /api/auth/login
  │
  ▼
Laravel
  │
  │ Validation
  │ Authentification
  │
  ▼
Réponse JSON
  │
  ▼
React
```

Les erreurs retournées par l'API doivent être traitées par le frontend afin d'afficher des messages compréhensibles à l'utilisateur.

---

## 9. Base de données

La base de données utilisée par le projet est MySQL/MariaDB.

La structure de la base est créée et modifiée à l'aide des migrations Laravel.

Les données de test et les données initiales peuvent être créées à l'aide des factories et seeders.

Les relations entre les tables doivent être définies avec des clés étrangères afin de garantir l'intégrité des données.

Les principales relations du système sont représentées dans le diagramme de classes présent dans :

```text
docs/diagrammes/class_diagram.png
```

---

## 10. Authentification et autorisation

L'authentification de l'API utilise Laravel Sanctum.

Les routes nécessitant une authentification doivent être protégées par le middleware approprié.

La gestion des rôles et permissions utilise Spatie Laravel Permission.

Les rôles actuellement définis dans le cahier des charges sont :

```text
Administrateur
Directeur des Études
Secrétaire
```

Les permissions permettent de limiter les opérations disponibles selon le rôle de l'utilisateur.

---

## 11. API

Les routes API sont regroupées dans :

```text
routes/api.php
```

Les endpoints suivent une organisation par fonctionnalité.



```text
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
GET    /api/auth/profile

GET    /api/formations
POST   /api/formations/create
GET    /api/formations/{id}
PUT    /api/formations/edit/{id}
DELETE /api/formations/delete/{id}

GET    /api/ues
POST   /api/ues/create

GET    /api/ecues
POST   /api/ecues/create

GET    /api/notes
POST   /api/notes/create
PUT    /api/notes/{id}
```

Les endpoints définitifs doivent correspondre aux routes réellement présentes dans le projet.

---

## 12. Validation des données

Les données reçues par l'API doivent être validées côté serveur.

La validation doit notamment permettre de vérifier :

* la présence des champs obligatoires ;
* le format des données ;
* l'existence des identifiants référencés ;
* l'unicité des informations lorsque nécessaire ;
* les valeurs autorisées.

Les règles de validation sont définies dans les `FormRequest`.

---

## 13. Gestion des erreurs

Les erreurs doivent être gérées de manière cohérente entre le backend et le frontend.

L'API doit retourner des codes HTTP appropriés.

Exemples :

```text
200  → requête réussie
201  → ressource créée
400  → requête incorrecte
401  → utilisateur non authentifié
403  → accès interdit
404  → ressource introuvable
422  → erreur de validation
500  → erreur serveur
```

Le frontend doit interpréter ces réponses afin de fournir un retour compréhensible à l'utilisateur.

---

## 14. Tests

Les tests automatisés sont réalisés avec PHPUnit.

### Tests unitaires

Les tests unitaires permettent de vérifier individuellement les traitements métier.

Exemple :

```text
Tests/Unit/
└── CalculNoteServiceTest.php
```

### Tests fonctionnels

Les tests fonctionnels permettent de vérifier le comportement complet des fonctionnalités et de l'API.

Exemples :

```text
Tests/Feature/
├── AuthTest.php
├── FormationTest.php
├── UETest.php
├── ECUETest.php
└── NoteTest.php
```

Les tests doivent notamment couvrir :

* l'authentification ;
* les permissions ;
* les validations ;
* les opérations CRUD ;
* le calcul des notes ;
* la gestion des inscriptions.

---

## 15. Gestion des variables d'environnement

Les informations sensibles et les paramètres dépendant de l'environnement doivent être stockés dans `.env`.

Exemples :

```env
APP_NAME=SchoolSoft
APP_ENV=local
APP_DEBUG=true

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel_api
DB_USERNAME=root
DB_PASSWORD=
```

Le fichier `.env` ne doit jamais être envoyé sur GitHub.

Le fichier `.env.example` sert de modèle pour les autres développeurs.

---

## 16. Sécurité

Les principales mesures de sécurité sont :

* hachage des mots de passe ;
* authentification des utilisateurs ;
* contrôle des rôles et permissions ;
* validation des données ;
* protection des routes sensibles ;
* utilisation de HTTPS en production ;
* protection des variables d'environnement ;
* absence de mots de passe et tokens dans les logs.

En production, le mode debug doit être désactivé :

```env
APP_ENV=production
APP_DEBUG=false
```

---



## 17. Documentation et diagrammes

Les documents techniques sont centralisés dans le dossier :

```text
docs/
├── cahier-des-charges.pdf
├── specifications-techniques.md
└── diagrammes/
    ├── class_diagram.png
    └── use_case_diagram.png
```

### Cahier des charges

Le cahier des charges présente les besoins fonctionnels, les acteurs, les fonctionnalités, les cas d'utilisation et les règles métier du système.

### Spécifications techniques

Le présent document décrit l'organisation technique du projet, son environnement de développement, son architecture, ses conventions et ses procédures techniques.

### Diagramme de classes

Le diagramme de classes présente les principales entités du système ainsi que leurs relations.

### Diagramme de cas d'utilisation

Le diagramme de cas d'utilisation présente les acteurs et leurs interactions avec le système.

---

## 18. Maintenance et évolutivité

Le code doit être organisé de manière modulaire afin de faciliter l'ajout de nouvelles fonctionnalités.

Toute nouvelle fonctionnalité doit respecter :

* l'architecture existante ;
* les conventions de développement ;
* les règles de sécurité ;
* les règles de validation ;
* les tests automatisés ;
* les conventions Git.

Toute modification importante de l'architecture ou de la structure de la base de données doit être documentée.

---
