<?php
// src/repository/MongoBaseRepo.php
namespace App\repository;

use App\config\Database;
use App\Model\BaseModel;
use MongoDB\Collection; // En base de données MongoDb, on utilise la classe Collection (fournie par le driver MongoDb de PHP) pour interagir avec les collections de documents.
use MongoDB\BSON\ObjectId; // Nous avons besoin de la classe ObjectId pour manipuler les identifiants des documents MongoDb.
use MongoDB\BSON\UTCDateTime;// Nous avons besoin de la classe UTCDateTime pour manipuler les dates au format MongoDb.

/**
 * Classe de base pour les repositories des models non-relationnel.
 */
abstract class BaseRepoMongo
{
    //Comme pour BaseRepoSql, on utilise une propriété pour lma table (ici en MongoDb, on parle de collection).
    //Il nous faut aussi une propriété pour la classe du modèle associé.
    protected Collection $collection;
    protected string $className ;

    public function __construct(string $collectionName)
    {
        $this->collection = Database::getInstanceMongo()->selectCollection($collectionName);
    }

    /**
     * Crée une nouvelle entrée dans la collection à partir d'un modèle.
     * @param object|array $model Le modèle à insérer dans la collection.
     * @return string Retourne l'ID du document inséré.
     * 
     */
    public function create(object|array $model): string
    {
        // On convertie l'objet ou le tableau en document MongoDb.
        $doc = $this->toDocument($model);

        $result = $this->collection->insertOne($doc);
        return $result->getInsertedId();
    }

    /**
     * Convertit un modèle ou un tableau en document MongoDB.
     * @param object|array $model Le modèle ou le tableau à convertir.
     * @return array Le document MongoDB.
     * 
     * Cette méthode normalise les données pour MongoDB, en convertissant les dates et les énumérations.
     * Elle est utilisée pour préparer les données avant l'insertion ou la mise à jour dans la collection.
     */
    protected function toDocument(object|array $model): array
    {
        // Si on reçoit un objet, on extrait ses propriétés en array
        $data = is_array($model) ? $model : $this->extractData($model);

        // On convertit les propriétés DateTime en UTCDateTime ainsi que les enums pour MongoDB.
        foreach ($data as $k => $v) {
            if ($v instanceof \DateTimeInterface) {
                $data[$k] = new UTCDateTime($v);
            } elseif ($v instanceof \BackedEnum) {
                $data[$k] = $v->value;
            }
        }
        return $data;
    }

    /**
     * UPDATE: met à jour un document existant par son identifiant.
     * @param string $id Identifiant du document à mettre à jour.
     * @param object|array $model Nouvelles données (objet/array) à appliquer
     * @return bool true si au moins 1 doc a été modifié
     */
    public function update(string $id, object|array $model): bool
    {
        $filter = $this->idFilter($id);
        $doc = $this->toDocument($model);

        // On retire l'ID du document
        unset($doc['_id']);

        $result = $this->collection->updateOne($filter, ['$set' => $doc]);
        return $result->getModifiedCount() > 0;
    }

    /**
     * Construit le filtre de recherche pour un id donné.
     * - Si $id est une string au format ObjectId, on convertit en ObjectId.
     * @param string $id
     * @return array Filtre MongoDB, typiquement ['_id' => new ObjectId(...)]
     */
    protected function idFilter(string $id): array
    {
        // Si l'ID est un ObjectId, on le retourne directement
        if (preg_match('/^[a-f0-9]{24}$/i', $id)) {
            return ['_id' => new ObjectId($id)];
        }
        // Sinon, on le traite comme un entier ou une chaîne
        return ['_id' => $id];
    }

    /**
     * DELETE: supprime un document par son identifiant.
     * @param mixed $id
     * @return bool true si un document a été supprimé
     */
    public function delete(mixed $id): bool
    {
        $result = $this->collection->deleteOne($this->idFilter($id));
        return $result->getDeletedCount() > 0;
    }

    /**
     * Trouve une entrée par son ID.
     * @param mixed $id peut etre un ObjectId (donc en string) ou un int.
     * @return BaseModel|null Modèle instancié ou null si non trouvé
     */
    public function findById(mixed $id): ?BaseModel
    {
        $doc = $this->collection->findOne($this->idFilter($id));
        return $doc ? $this->toModel($doc) : null;
    }

    /**
     * READ (by id): récupère tout les documents de la collection.
     * @return array Tableau des resultats sous forme de modèles.
     * Coter production, on utilisera un filtre pour limiter les résultats.
     */
    public function findAll(): array
    {
        $cursor = $this->collection->find([]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }

    /**
     * Transforme un document Mongo (array) en objet modèle.
     * - Convertit UTCDateTime -> DateTime
     * - Convertit ObjectId -> string
     * - Si $className est défini, instancie cette classe avec le tableau.
     * @param array $doc
     * @return object
     */
    protected function toModel(array $doc): object
    {
        
        foreach ($doc as $k => $v) {
            if ($v instanceof UTCDateTime) {
                // Conversion en dateTime
                $doc[$k] = $v->toDateTime();
            }
            if ($v instanceof ObjectId) {
                // On renvoie l’id en string pour simplicité côté applis
                $doc[$k] = (string)$v;
            }
        }
        if ($this->className) {
            return new $this->className($doc);
        }
        // Si aucun modèle déclaré, renvoie un objet générique
        return (object)$doc;
    }

    // Idem que pour BaseRepoSql, on utilise la réflexion pour extraire les données d'un modèle.
    // Cette méthode est utilisée pour convertir un modèle en tableau avant de l'insérer ou de le mettre à jour dans la collection.
    protected function extractData(object $model): array
    {
        $data = [];
        $ref = new \ReflectionClass($model);
        foreach ($ref->getProperties() as $prop) {
            $prop->setAccessible(true);
            $data[$prop->getName()] = $prop->getValue($model);
        }
        return $data;
    }
}