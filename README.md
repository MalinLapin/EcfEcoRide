# EcoRide

## Description

EcoRide est une startup française, ayant pour objectif de réduire l'impact environnemental des déplacements en encourageant le covoiturage. Elle prône une approche écologique et aspire à devenir la principale plateforme de covoiturage pour les voyageurs soucieux de l'environnement et ceux recherchant une solution économique pour leurs déplacements.

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
- **MongoDB Community Server** : https://www.mongodb.com/try/download/community bien installé avec l'option "install as a Service".
- **Git** : https://git-scm.com/

---

## Installation

### 1.Cloner le projet

En invite de commande se rendre dans le dossier de destination de notre projet local. Ici, nous le nommerons ecfBackup, avec ce chemin :

```bash
cd C:/Env/workSpace/ecfBackup
```

Une fois dans le dossier, il faut cloner le repo github grâce à :

```bash
git clone https://github.com/MalinLapin/EcfEcoRide.git
```

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
3. Executer le fichier `DataBase.sql`situé dans `src/bdd/sql`.

### 4.Configuration MongoDB

Si vous avez installer MongoDB comme un service en cochant l'option "Install as a Service" ce dernier doit démarrer automatiquement.
<<<<<<< HEAD
Pour vérifier, dans un terminal ce rendre a la racine de notre projet puis:
=======
Pour vérifier, dans un terminal se rendre à la racine de notre projet puis:
>>>>>>> 3f5cefd573edcd0bd7aebb57cec53149a87f9a1c

```bash
mongosh
```

Ensuite dans mongosh il faut renseigner le nom de notre bdd:

```bash
use ecoride_nosql
```

Puis charger le init.mongo qui contient les collections ainsi que leurs règles:

```bash
load("src/bdd/mongo/init.mongodb")
```

Pour vérifier visionnons nos collections nouvellement créées:

```bash
show collections
```

### 5.Configuration des variables d'environnement

Pour ce faire, il faut faire une copie de `.env.example` et la renommer `.env`.
Dans un terminal:

```bash
copy .env.example .env
```

<<<<<<< HEAD
Il va ensuite falloir renseigné vos donnée personnel de connexion.
=======
Il va ensuite falloir renseigner vos données personnelles de connexion.
>>>>>>> 3f5cefd573edcd0bd7aebb57cec53149a87f9a1c

## Lancer l'application

### En Local après avoir suivi la procédure

1. Avec XAMPP : placez votre projet dans `C:\xampp\htdocs\ecoride`.
2. Puis accédez via l'url : http://localhost/ecoride/public

### La version déployée en ligne

Scannez le QR Code pour accéder à l'application en ligne :

<p align="center">
  <img src="public/assets/images/QrCode.png" alt="QR Code EcoRide" width="200"/>
</p>

**OU accédez directement via :** [https://EcoRide.com/](https://stark-mountain-00422-f4d7d334b310.herokuapp.com)

---

## Contact

**Auteur** : UNY Marc
**Formation** : Développeur Web et Web Mobile
**Email** : marc.uny@orange.fr
