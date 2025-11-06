<?php



namespace App\repository;

use Exception;
use MongoDB\Client;
use MongoDB\Collection;
use App\config\Database;
use App\model\BaseModel;
use App\model\StatusReview;
use MongoDB\BSON\ObjectId;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Model\BSONDocument;
use MongoDB\Database as MongoDatabase;
use MongoDB\Driver\Exception\BulkWriteException;

abstract class BaseRepoMongo
{
    // Nom de collection défini dans les sous-classes
    protected string $collectionName;

    // Nom de la classe du modèle, utilisé dans les sous-classes
    protected ?string $className = null;

    // Collection MongoDB où les opérations seront effectuées
    protected Collection $collection;

    public function __construct(?MongoDatabase $db = null, ?Collection $collection = null)
    {
        // Si la collection n'est pas définie, on utilise la base de données
        // sinon on utilise la collection passée en paramètre.
        if ($collection === null && $db === null) {
            $db = Database::getInstanceMongo();
        }

        // Vérifie que le nom de la collection est défini
        // et que la collection est soit passée en paramètre, soit créée à partir de la base de données.
        if (!isset($this->collectionName) || $this->collectionName === '') {
            throw new \LogicException('collectionName non défini dans ' . static::class);
        }
        
        // Si la collection est déjà passée en paramètre, on l'utilise directement.
        // Sinon, on la crée à partir de la base de données.
        if (!isset($this->collectionName) || $this->collectionName === '') {
            throw new \LogicException('collectionName non défini dans ' . static::class);
        }

        // Si la collection est passée en paramètre, on l'utilise.
        // Sinon, on la crée à partir de la base de données.
        if ($collection === null) {
            $collection = $db->selectCollection($this->collectionName);
        }
        // Vérifie que la collection est bien une instance de MongoDB\Collection
        // et l'assigne à la propriété $collection.
        if (!$collection instanceof Collection) {
            throw new \RuntimeException('La collection doit être une instance de MongoDB\Collection');
        }
        // Si la collection est une instance de Collection, on l'assigne à la propriété $collection.
        // Sinon, on lève une exception.
        // Cela permet de s'assurer que la collection est bien initialisée avant de l'utiliser.
        if ($collection instanceof Collection) {
            $this->collection = $collection;
            return;
        }

        $db = $db ?? Database::getInstanceMongo(); // doit retourner MongoDB\Database
        if (!$db instanceof MongoDatabase) {
            throw new \RuntimeException('Database::getInstanceMongo() doit retourner MongoDB\Database');
        }

        $this->collection = $db->selectCollection($this->collectionName);
    }
    
    /**
     * Crée une nouvelle entrée dans la collection à partir d'un modèle.
     * @param object|array $model Le modèle à insérer dans la collection.
     * @return bool Retourne true si le document à été inséré.
     * 
     */
    public function create(object|array $model): bool
    {
        $doc = $this->toDocument($model);
        
        $result = $this->collection->insertOne($doc);

        if ($result){
            $result->getInsertedId();
            return true;
        }
        return false;
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

        // Ajouter created_at si absent
        if (!isset($data['created_at'])) {
            $data['created_at'] = new \DateTimeImmutable();
        }

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

        // On retire l'ID du document + le created_at
        unset($doc['id']);
        unset($doc['created_at']);

        $result = $this->collection->updateOne($filter, ['$set' => $doc]);
        return $result->getModifiedCount() > 0;
    }

    /**
     * Construit le filtre de recherche pour un id donné.
     * - Si $id est une string au format ObjectId, on convertit en ObjectId.
     * @param string $id
     * @return array Filtre MongoDB, typiquement ['id_' => new ObjectId(...)]
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
    protected function toModel(array|BSONDocument $doc): object
    {        
       // Normalisation BSONDocument -> array
        if ($doc instanceof BSONDocument) {
            $doc = $doc->getArrayCopy();
        }

        foreach ($doc as $k => $v) {
            if ($v instanceof UTCDateTime) {
                $doc[$k] = \DateTimeImmutable::createFromInterface($v->toDateTime());
            } elseif ($v instanceof ObjectId) {
                $doc[$k] = (string) $v;
            } elseif ($k === 'statusReview'){
                $doc[$k] = StatusReview::from($v);
            }
        }

        if (isset($doc['_id']))
        {
            $doc['id'] = $doc['_id'];
            unset($doc['_id']);
        }

        if ($this->className)
        {
            return new $this->className($doc);
        }
        return (object) $doc;
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