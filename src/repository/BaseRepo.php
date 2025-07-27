<?php

namespace App\repository;

use App\config\Database;
use App\Model\BaseModel;

/**
 * Classe de base pour les repositories.
 * Fournit une connexion à la base de données via PDO.
 */
abstract class Repository
{
    protected \PDO $pdo;
    // Le nom de la table associée au repository
    protected string $tableName;
    // Le nom de la classe du modèle associé au repository
    protected string $className;

    public function __construct()
    {
        // On récupère l'instance de la base de données
        $this->pdo = Database::getInstance();
    }

    /**
     * Crée une nouvelle entrée dans la base de données à partir d'un modèle.
     * @param BaseModel $model Le modèle à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, false sinon.
     */
    public function create(BaseModel $model):bool
    {
        
        $data = $this->extractData($model);

        $idField = $this->getIdField();
        unset($data[$idField]);

        // On prépare la requête d'insertion
        $columns = implode(',', array_keys($data));
        // On prépare les placeholders pour les valeurs
        $placeholders = ':' . implode(',:', array_keys($data));

        // On construit la requête SQL
        $sql = "INSERT INTO {$this->tableName} ({$columns}) VALUES ({$placeholders})";
        // On prépare et exécute la requête
        $stmt = $this->pdo->prepare($sql);
        // On lie les valeurs aux placeholders
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        // On exécute la requête
        return $stmt->execute();
    }

    /**
    * Extrait les données d'un modèle en utilisant la réflexion.
    * @param BaseModel $model Le modèle dont on veut extraire les données.
    * @return array Un tableau associatif contenant les noms des propriétés et leurs valeurs.
    */
    protected function extractData(BaseModel $model): array
    {
        $data = [];
        // On utilise la réflexion pour accéder aux propriétés du modèle
        $reflection = new \ReflectionClass($model);
        // On parcourt les propriétés du modèle
        foreach ($reflection->getProperties() as $property) {

            // On ignore les propriétés privées ou protégées
            $property->setAccessible(true);
            // On récupère le nom de la propriété et sa valeur
            $data[$property->getName()] = $property->getValue($model);
        }
        return $data;
    }

    /**
     * Récupère le nom du champ ID pour la table associée.
     * @return string Le nom du champ ID.
     */
    protected function getIdField(): string
    {
        // La colonne ID est nommée 'id_' suivie du nom de la table.
        return 'id_' . $this->tableName;
    }

}