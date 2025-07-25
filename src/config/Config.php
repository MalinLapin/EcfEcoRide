<?php

// Fichier contenant notre classe de configuration. C'est lui qui va charger nos variable d'environement et charger notre .env
namespace App\config;
use Dotenv\Dotenv;

class Config
{
    /**
     * @param string $path le chemin vers le dossier contenant le fichier .env
     */

    public static function load($path = __DIR__ . '../'):void
    {
        //on vérifie si le fichier .env avant de tenter de le charger.
        if(file_exists($path . '.env'))
        {
            $dotenv = Dotenv::createImmutable($path);
            $dotenv->load();
        }
    }

    
}