<?php

// Fichier contenant notre classe de configuration. C'est lui qui va charger nos variable d'environement et charger notre .env
namespace App\config;
use Dotenv\Dotenv;

class Config
{
    /**
     * @param string $path le chemin vers le dossier contenant le fichier .env
     */

    public static function load($path = __DIR__ . '../', $envFile ='.env'):void
    {
        //on vérifie si le fichier .env avant de tenter de le charger.
        if(file_exists($path . $envFile))
        {
            $dotenv = Dotenv::createImmutable($path, $envFile);
            $dotenv->load();
        }
    }

    /**
     * @param string $key le nom de la variable
     * @param mixed $default une valeur par défaut à retrouner si la variable n'existe pas
     * @return mixed la valeur de la variable ou la valeur par défaut
     */
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}

