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
    *   `robmorgan/phinx` pour les migrations de base de données
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

Créez un fichier `.env` à la racine du projet en copiant le contenu suivant. Ce fichier contient les informations de connexion à la base de données.

```env
DB_HOST=db
DB_NAME=test_db
DB_USER=root
DB_PASS=root
DB_PORT=3306
```

**3. Construire et lancer les conteneurs Docker**

Cette commande va construire l'image du serveur web et démarrer tous les services en arrière-plan.

```bash
docker-compose up -d --build
```
> **Note :** Le conteneur de la base de données (`db`) peut prendre 10 à 20 secondes pour être pleinement opérationnel. Attendez un peu avant de passer à l'étape suivante.

**4. Installer les dépendances PHP**

Exécutez Composer à l'intérieur du conteneur `www` pour télécharger les dépendances du projet.

```bash
docker-compose exec www composer install
```

**5. Exécuter les migrations de la base de données (Étape cruciale)**

Cette commande exécute les scripts de migration pour créer toutes les tables nécessaires (`parents`, `ados`, `comptes`) dans votre base de données.

```bash
docker-compose exec www ./vendor/bin/phinx migrate
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

Le projet dispose d'une suite de tests complète qui peut être lancée avec PHPUnit.

Pour exécuter tous les tests, lancez la commande suivante :

```bash
docker-compose exec www ./vendor/bin/phpunit
```

---

## Structure du Projet

```
.
├── db/                 # Fichiers de migration de la base de données
│   └── migrations/
├── docker/             # Fichiers de configuration pour Docker
├── public/             # Racine web, point d'entrée unique de l'application
├── src/                # Cœur de l'application (PHP)
│   ├── Controller/
│   ├── Entity/
│   └── Repository/
├── templates/          # Fichiers de vue (HTML + PHP)
├── tests/              # Suite de tests automatisés
├── vendor/             # Dépendances Composer
├── Docker-compose.yml  # Fichier d'orchestration des services Docker
├── phinx.php           # Fichier de configuration pour Phinx
└── phpunit.xml         # Configuration de la suite de tests PHPUnit
```
