<?php

namespace App\repository;

use App\config\Database;

/**
 * Classe de base pour les repositories.
 * Fournit une connexion à la base de données via PDO.
 */
class Repository
{
    protected \PDO $pdo;

    public function __construct()
    {
        // On récupère l'instance de la base de données
        $this->pdo = Database::getInstance();
    }
}