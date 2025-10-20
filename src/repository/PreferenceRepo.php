<?php

namespace App\repository;
use App\model\PreferenceModel;

class PreferenceRepo extends BaseRepoMongo
{
    protected string $collectionName = 'preference';
    protected ?string $className = PreferenceModel::class;

    // Recherche une préférences par son identifiant de voiture
    public function findByCar(int $idCar): array
    {
        $cursor = $this->collection->find(['id_car' => $idCar]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }

    // Recherche une préférence par son statut d'acceptation
    public function findByAccept(bool $isAccepted): array
    {
        $cursor = $this->collection->find(['is_accepted' => $isAccepted]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }    
}