# MyWeeklyAllowance

MyWeeklyAllowance est une application web simple développée en PHP, conçue pour permettre à un parent de gérer l'argent de poche de ses adolescents. L'application est entièrement conteneurisée avec Docker pour une installation et un déploiement faciles.

## Technologies Utilisées

*   **Backend**: PHP 8.3
*   **Serveur Web**: Apache
*   **Base de données**: MySQL 8.0
*   **Environnement**: Docker & Docker Compose
*   **Dépendances**:
    *   `nikic/fast-route` pour le routage des URL
    *   `vlucas/phpdotenv` pour la gestion des variables d'environnement
*   **Tests**: PHPUnit pour les tests unitaires et fonctionnels

---

## Prérequis

*   [Docker Desktop](https://www.docker.com/products/docker-desktop/) doit être installé et en cours d'exécution sur votre machine.

---

## Installation et Démarrage

Suivez ces étapes pour lancer l'application en environnement de développement.

**1. Cloner le projet**
```bash
git clone <URL_DU_REPOSITORY_GIT>
cd MyWeeklyAllowance
```

**2. Créer le fichier d'environnement**

Créez un fichier `.env` à la racine du projet en copiant le contenu suivant. Ce fichier contient les informations de connexion à la base de données utilisées par l'application et Docker Compose.

```env
DB_HOST=db
DB_NAME=test_db
DB_USER=root
DB_PASS=root
DB_PORT=3306
```

**3. Construire et lancer les conteneurs Docker**

Cette commande va construire l'image du serveur web et démarrer tous les services (PHP/Apache, MySQL, PhpMyAdmin) en arrière-plan.

```bash
docker-compose up -d --build
```

**4. Installer les dépendances PHP**

Une fois les conteneurs lancés, exécutez Composer à l'intérieur du conteneur `www` pour télécharger les dépendances du projet.

```bash
docker-compose exec www composer install
```

Le projet est maintenant installé et fonctionnel !

---

## Utilisation

*   **Application Web** : L'application est accessible dans votre navigateur à l'adresse suivante :
    *   **<http://localhost:8000>**

*   **Gestion de la base de données (PhpMyAdmin)** : Vous pouvez inspecter la base de données via l'interface de PhpMyAdmin.
    *   **URL**: **<http://localhost:8080>**
    *   **Serveur**: `db`
    *   **Utilisateur**: `root`
    *   **Mot de passe**: `root`

---

## Lancer les Tests

Le projet dispose d'une suite de tests complète (unitaires et fonctionnels) qui peut être lancée avec PHPUnit.

Pour exécuter tous les tests, lancez la commande suivante :

```bash
docker-compose exec www ./vendor/bin/phpunit
```

Pour exécuter les tests et voir le rapport de couverture de code dans le terminal :

```bash
docker-compose exec www ./vendor/bin/phpunit --coverage-text
```

---

## Structure du Projet

```
.
├── docker/             # Fichiers de configuration pour Docker (Dockerfile, vhost)
├── public/             # Racine web, point d'entrée unique de l'application (index.php)
├── src/                # Cœur de l'application (PHP)
│   ├── Controller/     # Logique de gestion des requêtes
│   ├── Entity/         # Classes représentant les objets métier (Ado, Compte...)
│   ├── Repository/     # Classes pour l'accès aux données (logique SQL)
│   └── ...
├── templates/          # Fichiers de vue (HTML + PHP)
├── tests/              # Suite de tests automatisés
│   ├── Functional/     # Tests simulant la navigation utilisateur
│   └── Unit/           # Tests isolés pour chaque classe
├── vendor/             # Dépendances Composer
├── Docker-compose.yml  # Fichier d'orchestration des services Docker
└── phpunit.xml         # Configuration de la suite de tests PHPUnit
```
