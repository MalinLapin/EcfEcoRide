<?php

// Fichier contenant notre classe de configuration. C'est lui qui va charger nos variable d'environement et charger notre .env
namespace App\config;
use Dotenv\Dotenv;

class Config
{
    /**
     * Indique si le .env a déjà été chargé (pour éviter de le recharger)
     */
    private static bool $loaded = false;

    /**
     * Charge le fichier .env (par défaut) ou un autre (ex: .env.test)
     * @param string $path Dossier contenant le .env
     * @param string $envFile Nom du fichier d'environnement à charger
     */
    public static function load(string $path = __DIR__ . '/../../', string $envFile = '.env'): void
    {
        // Éviter de recharger plusieurs fois
        if (self::$loaded) {
            return;
        }

        // On charge toutes les variables système dans $_ENV (pour SMTP, etc.)
        foreach ($_SERVER as $key => $value) {
            if (!isset($_ENV[$key])) {
                $_ENV[$key] = $value;
            }
        }

        // On vérifie que le fichier .env existe avant de le charger (LOCAL)
        if (file_exists($path . $envFile)) {
            // On utilise la librairie vlucas/phpdotenv pour charger les variables d'environnement
            $dotenv = Dotenv::createImmutable($path, $envFile);
            // On charge les variables d'environnement dans $_ENV
            $dotenv->safeLoad();
        }

        // On analyse JAWSDB_URL si présent (HEROKU)
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


        self::$loaded = true;
    }

    /**
     * Récupère une variable d'environnement par sa clé
     * @param string $key La clé de la variable d'environnement
     * @param mixed $default Valeur par défaut si la clé n'existe pas
     * @return mixed La valeur de la variable d'environnement ou la valeur par défaut
     */
    public static function get(string $key, $default = null)
    {
        // S'assurer que le .env est chargé (si on est en local)
        if (!self::$loaded) {
            self::load();
        }

        // 1. Priorité : variables d'environnement système (Heroku, Docker, etc.)
        $value = getenv($key);
        if ($value !== false && $value !== '') {
            return $value;
        }

        // 2. Fallback : fichier .env via $_ENV (développement local)
        if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
            return $_ENV[$key];
        }

        // 3. Valeur par défaut
        return $default;
    }

}
