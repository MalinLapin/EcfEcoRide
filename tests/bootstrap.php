<?php


// Charger l'autoload de Composer
require_once dirname(__DIR__) . '/vendor/autoload.php';

use App\config\Config;

//Charger un fichier de configuration spécifique pour les tests afin d'utiliser une base de données séparer
//Cela évite de polluer la base de données de développement.
Config::load(dirname(__DIR__), '/.env.test');
