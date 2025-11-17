<?php

namespace App\config;
use PDO;
use PDOException;
use MongoDB\Client;
use MongoDB\Database as MongoDatabase;
use Exception;

// Fichier contenant notre classe de configuration de la base de données. C'est lui qui va gérer la connexion à la base de données.
class Database
{    
    // Propriété static privée pour stocker l'instance unique de PDO.
    private static ?PDO $instancePDO = null;

    // Propriété static privée pour stocker l'instance unique de MongoDatabase.
    private static ?MongoDatabase $db = null;

    //Le constructeur est private pour empècher la création d'objet via new database.
    private function __construct(){}

    //La méthode de clonnage est privé pour évite de clonner l'instance.
    private function __clone(){}

    //Permet le point d'accès unique à la bdd relationnel (Patern Singleton)
    public static function getInstancePDO():PDO
    {
        // Si l'instance n'a pas été créée
        if(self::$instancePDO === null)
        {
            // On construit le DSN (Data Source Name) avec les infos du fichier .env
            $dsn = sprintf("mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4", Config::get('DB_HOST'), Config::get('DB_PORT', '3306'), Config::get('DB_NAME'));

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Lance des exeptions en cas d'erreur SQL
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC// Récupere les résultats sous forme de tableau associatif
            ];
            try{
                // On crée l'instance de PDO et on la stock
                self::$instancePDO = new PDO($dsn, Config::get('DB_USER'), Config::get('DB_PASSWORD'), $options);
            }catch(PDOException $e){
                die("Erreur de connexion à la base de données Sql : " . $e->getMessage());
            }
        }
        return self::$instancePDO;
    }

    //Permet le point d'accès unique à la bdd non relationnel
    public static function getInstanceMongo():MongoDatabase
    {
        if (self::$db === null) {
            $uri = Config::get('MONGODB_URI');
            $dbName = Config::get('MONGO_DB');

             // 🔍 LOGS DE DEBUG
        error_log("=== MONGODB CONNECTION DEBUG ===");
        error_log("URI présent: " . (!empty($uri) ? "YES" : "NO"));
        error_log("URI starts with mongodb+srv: " . (strpos($uri, 'mongodb+srv://') === 0 ? "YES" : "NO"));
        error_log("DB Name: " . $dbName);

            try{
                $client = new Client($uri, [
                'typeMap' => [
                    'root' => 'array',
                    'document' => 'array',
                    'array' => 'array',
                ],
                'tls' => true,
                'serverSelectionTimeoutMS' => 5000,
                'connectTimeoutMS' => 10000,
            ]);

            error_log("Client MongoDB créé");

            self::$db = $client->selectDatabase($dbName);
            error_log("Base de données sélectionnée");

            }catch(Exception $e){
                error_log("❌ MongoDB Exception: " . $e->getMessage());
            error_log("❌ Code: " . $e->getCode());
            die("Erreur de connexion à la base de donnée NoSql : ". $e->getMessage());
            }            
        }
        return self::$db;    
    }

}