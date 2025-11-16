# EcoRide

## Description

    EcoRide est une startup française, ayant pour objectif de réduire l'impact

environnemental des déplacements en encourageant le covoiturage. Elle prône une approche
écologique et aspire à devenir la principale plateforme de covoiturage pour les voyageurs
soucieux de l'environnement et ceux recherchant une solution économique pour leurs
déplacements.

## Technologies utilisées

### Backend

- PHP 8.2
- Architecture MVC
- Composer

### Frontend

- HTML5
- CSS3
- JavaScript

### Base de données

- MySQL (données relationnelles)
- MongoDB (données NoSQL)

### Dépendance PHP (via Composer)

- `phpmailer/phpmailer` : envoi d'emails
- `nikic/fast-route` : routage
- `vlucas/phpdoenv` : gestion des variables d'environnement

## Prérequis

Avant d'installer le projet, assurez-vous d'avoir :

- **XAMPP** (version 8.2+) : https://www.apachefriends.org/
- **Composer** : https://getcomposer.org/
- **MongoDB Community Server** : https://www.mongodb.com/try/download/community
- **Git** : https://git-scm.com/

---

## Installation

### 1.Cloner le projet

En invite de commande se rendre dans le dossier de destination de notre projet local. Ici, nous le nommerons ecfBackup, avec ce chemin :

```bash
cd C:/Env/workSpace/ecfBackup
```

Une fois dans le dossier il faut cloner le repo github grâce à :

```bash
git clone https://github.com/MalinLapin/EcfEcoRide.git
```

Lisez la liste des [contributeurs](https://github.com/your/project/contributors) pour voir qui à aidé au projet !

Toujours dans notre dossier en ligne commande il va falloir installer Composer, afin d'avoir accès à ses dépendances.

```bash
composer install
```

### 3.Configurer la base de données MySQL

#### 3.1.Créer la base de données

1. Démarrez XAMPP et lancez Apache et MySQL
2. Accédez à phpMyAdmin via l'URL : http://localhost/phpmyadmin
3. Créez une nouvelle base de données nommée **ecoridebBackup**

#### 3.2.Importer le schéma SQL

1. Séléctionnez la base **ecorideBackup**
2. Allez dans l'onglet Importer

## License

Ce projet est sous licence `exemple: WTFTPL` - voir le fichier [LICENSE.md](LICENSE.md) pour plus d'informations
