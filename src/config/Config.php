<?php

// Fichier contenant notre classe de configuration. C'est lui qui va charger nos variable d'environement et charger notre .env
namespace App\config;
use Dotenv\Dotenv;

class Config
{
    /**
     * Charge le fichier .env (par défaut) ou un autre (ex: .env.test)
     * @param string $path Dossier contenant le .env
     * @param string $envFile Nom du fichier d'environnement à charger
     */
    public static function load($path = __DIR__ . '/../../', $envFile ='.env'):void
    {
        // On vérifie que le fichier .env existe avant de le charger
        if(file_exists($path . $envFile))
        {
            // On utilise la librairie vlucas/phpdotenv pour charger les variables d'environnement
            $dotenv = Dotenv::createImmutable($path, $envFile);
            // On charge les variables d'environnement dans $_ENV
            $dotenv->load();
        }

        // On analyse JAWSDB_URL si présent, (Bdd en ligne via heroku)
        $jawsdbUrl = getenv('JAWSDB_URL');
        if ($jawsdbUrl !== false && !empty($jawsdbUrl)) {
            $dbparts = parse_url($jawsdbUrl);
            
            // Alimente $_ENV avec les variables parsées
            $_ENV['DB_HOST'] = $dbparts['host'];
            $_ENV['DB_PORT'] = $dbparts['port'] ?? '3306';
            $_ENV['DB_NAME'] = ltrim($dbparts['path'], '/');
            $_ENV['DB_USER'] = $dbparts['user'];
            $_ENV['DB_PASSWORD'] = $dbparts['pass'];
        }

        // On charge toutes les variables système dans $_ENV pour SMTP par Ex.
        foreach ($_SERVER as $key => $value) {
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }
    }

    /**
     * Récupère une variable d'environnement par sa clé
     * @param string $key La clé de la variable d'environnement
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed La valeur de la variable d'environnement ou la valeur par défaut
     */
    public static function get(string $key, $default = null)
    {
        return $_ENV[$key] ?? $default;
    }
}
