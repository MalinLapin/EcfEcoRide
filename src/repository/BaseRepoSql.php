<?php

namespace App\repository;

use App\config\Database;
use App\model\BaseModel;
use App\Attribute\NotMapped;
use BackedEnum;

/**
 * Classe de base pour les repositories des models relationnel.
 * Fournit une connexion à la base de données via PDO.
 */
abstract class BaseRepoSql
{
    protected \PDO $pdo;
    // Le nom de la table associée au repository
    protected string $tableName;
    // Le nom de la classe du modèle associé au repository
    protected string $className;

    public function __construct()
    {
        // On récupère l'instance de la base de données
        $this->pdo = Database::getInstancePDO();
    }

    /**
     * Crée une nouvelle entrée dans la base de données à partir d'un modèle.
     * @param BaseModel $model Le modèle à insérer dans la base de données.
     * @return bool Retourne true si l'insertion a réussi, false sinon.
     */
    public function create(BaseModel $model):int
    {
        
        // On vérifie que le modèle est une instance de BaseModel
        $data = $this->extractData($model);

        // On s'assure que le champ ID n'est pas inclus dans les données à insérer car il est auto incrémenter.
        $idField = $this->getPrimaryKeyField();        
        unset($data[$idField]);

        // On construit la requête d'insertion
        $columns = implode(',', array_keys($data));
        $placeholders = ':' . implode(',:', array_keys($data));

        // On prépare et exécute la requête
        $sql = "INSERT INTO {$this->tableName} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->pdo->prepare($sql);
        // On lie les valeurs aux paramètres de la requête
        foreach ($data as $key => $value) {
            $stmt->bindValue(":{$key}", $this->prepareParamForDatabase($value));

        }
        $stmt->execute();
        
        return $this->pdo->lastInsertId();
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
        foreach ($reflection->getProperties() as $property) 
        {
            // On vérifie si la propriété a l'attribut #[NotMapped]
            $attributes = $property->getAttributes(NotMapped::class);

            if (!empty($attributes))
            {
                // Cette propriété est marquée #[NotMapped], on la saute
                continue;
            }


            // On rend la propriété accessible même si elle est privée ou protégée
            $property->setAccessible(true);
            // On vérifie si la propriété est initialisée avant de tenter de récupérer sa valeur
            if ($property->isInitialized($model)) {
                // 
                $data[self::camelToSnake($property->getName())] = $property->getValue($model);
            }else { // Si la propriété n'est pas initialisée, on peut choisir de l'ignorer ou de lui attribuer une valeur par défaut (comme null)
                $data[self::camelToSnake($property->getName())] = null;
            }
        }
        return $data;
    }

    /**
     * Convertie le camelCase en snake_case pour coller aux noms des colonnes de ma Bdd.
     * @param string $input string a convertir
     * @return string Nouvelle string en snake_case.
     */
    public static function camelToSnake(string $input): string 
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $input));
    }

    /**
     * Récupère le nom du champ ID pour la table associée.
     * @return string Le nom du champ ID.
     */
    protected function getPrimaryKeyField(): string
    {
        // La colonne ID est nommée 'id_' suivie du nom de la table.
        return 'id_' . $this->tableName;
    }

    /**
     * Met à jour une entrée dans la base de données à partir d'un modèle.
     * @param BaseModel $model Le modèle à mettre à jour dans la base de données.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function update(BaseModel $model): bool
    {
        // On extrait les données du modèle
        $data = $this->extractData($model);
        
        // On s'assure que le champ ID est présent dans les données
        $idField = $this->getPrimaryKeyField();
        if (!isset($data[$idField])) {
            throw new \Exception("Le champ ID est nécéssaire à la modification.");
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
            $preparedValue = self::prepareParamForDatabase($value);
            $stmt->bindValue(":{$key}", $preparedValue);
        }
        
        $stmt->execute();
        
        // On test si le nombre de ligne à bien été modifier
        return $stmt->rowCount() > 0;
    }

    /**
     * Supprime une entrée de la base de données par son ID.
     * @param int $id L'ID de l'entrée à supprimer.
     * @return bool Retourne true si la suppression a réussi, false sinon.
     */
    public function delete(int $id): bool
    {
        // On prépare la requête de suppression
        $idField = $this->getPrimaryKeyField();
        $sql = "DELETE FROM {$this->tableName} WHERE {$idField} = :{$idField}";
        $stmt = $this->pdo->prepare($sql);
        
        // On lie l'ID à la requête
        $stmt->bindValue(":{$idField}", $id, \PDO::PARAM_INT);
        
        $stmt->execute();
        return $stmt->rowCount() > 0;
    }

    /**
     * Trouve une entrée par son ID.
     * @param int $id L'ID de l'entrée à trouver.
     * @return BaseModel|null Retourne une instance du modèle si trouvé, null sinon.
     */
    public function findById(int $id): ?BaseModel
    {
        // On prépare la requête de sélection
        $idField = $this->getPrimaryKeyField();
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

    /**
     * Trouve toutes les entrées de la table associée.
     * @return BaseModel[] Un tableau d'instances du modèle.
     */
    public function findAll(): ?array
    {
        // On prépare la requête de sélection
        $sql = "SELECT * FROM {$this->tableName}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        
        // On récupère tous les résultats
        $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Si résultat :
        if($results)
        {
            // On crée une liste de modèles à partir des résultats
            $models = [];
            foreach ($results as $result) {
                $models[] = new $this->className($result);
            }
            
            return $models; // Retourne un tableau de modèles
        }

        // Si aucun résultat on retourne null
        return null;
    }

    /**
     * Prépare une valeur à être insérée en BDD.
     * Si c'est une date, la transforme au bon format MySQL. Sinon, retourne la valeur telle quelle.
     * Si c'est une enum, la transforme sa valeur en string.
     * @param mixed $value La valeur en transforme pour une entré en Bdd.
     * @return mixed
     */
    protected function prepareParamForDatabase($value):mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d H:i:s');
        }

        if ($value instanceof \BackedEnum)
        {
            return $value->value;
        }
        return $value;
    }
}

