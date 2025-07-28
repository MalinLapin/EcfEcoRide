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
        // On vérifie que le modèle est une instance de BaseModel
        $data = $this->extractData($model);

        // On s'assure que le champ ID n'est pas inclus dans les données à insérer
        $idField = $this->getIdField();        
        unset($data[$idField]);

        // On construit la requête d'insertion
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));

        // On prépare et exécute la requête
        $sql = "INSERT INTO {$this->tableName} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        // On lie les valeurs aux paramètres de la requête
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }
        return $stmt->execute();
    }

    /**
    * Extrait les données d'un modèle en utilisant la réflexion.
    * @param BaseModel $model Le modèle dont on veut extraire les données.
    * @return array Un tableau associatif contenant les noms des propriétés et leurs valeurs.
    */
    protected function extractData(BaseModel $model): array
    {
        // On initialise un tableau vide pour stocker les données  
        $data = [];
        // On utilise la réflexion pour accéder aux propriétés du modèle
        $reflection = new \ReflectionClass($model);
        // On parcourt les propriétés du modèle et on les ajoute au tableau de données
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
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

    public function update(BaseModel $model): bool
    {
        // On extrait les données du modèle
        $data = $this->extractData($model);
        
        // On s'assure que le champ ID est présent dans les données
        $idField = $this->getIdField();
        if (!isset($data[$idField])) {
            throw new \Exception("ID field is required for update.");
        }

        // On prépare la requête de mise à jour
        $setClause = '';
        foreach ($data as $key => $value) {
            if ($key !== $idField) {
                $setClause .= "{$key} = :{$key}, ";
            }
        }
        $setClause = rtrim($setClause, ', ');

        // On construit la requête SQL
        $sql = "UPDATE {$this->tableName} SET {$setClause} WHERE {$idField} = :{$idField}";
        $stmt = $this->pdo->prepare($sql);

        // On lie les valeurs aux paramètres de la requête
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $value);
        }

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        // On prépare la requête de suppression
        $idField = $this->getIdField();
        $sql = "DELETE FROM {$this->tableName} WHERE {$idField} = :{$idField}";
        $stmt = $this->pdo->prepare($sql);
        
        // On lie l'ID à la requête
        $stmt->bindValue(":{$idField}", $id, \PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function findById(int $id): ?BaseModel
    {
        // On prépare la requête de sélection
        $idField = $this->getIdField();
        $sql = "SELECT * FROM {$this->tableName} WHERE {$idField} = :{$idField}";
        $stmt = $this->pdo->prepare($sql);
        
        // On lie l'ID à la requête
        $stmt->bindValue(":{$idField}", $id, \PDO::PARAM_INT);
        $stmt->execute();
        
        // On récupère le résultat
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($result) {
            // On crée une instance du modèle associé avec les données récupérées
            return new $this->className($result);
        }
        
        return null; // Retourne null si aucun résultat n'est trouvé
    }

    public function findAll(): array
    {
        // On prépare la requête de sélection
        $sql = "SELECT * FROM {$this->tableName}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        // On récupère tous les résultats
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // On crée une liste de modèles à partir des résultats
        $models = [];
        foreach ($results as $result) {
            $models[] = new $this->className($result);
        }
        
        return $models; // Retourne un tableau de modèles
    }
}