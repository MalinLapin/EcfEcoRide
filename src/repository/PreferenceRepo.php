<?php

namespace App\repository;
use App\model\PreferenceModel;

class PreferenceRepo extends BaseRepoMongo
{
    protected string $collectionName = 'preference';
    protected ?string $className = PreferenceModel::class;

    // Recherche une préférences par l'identifiant du covoiturage
    public function findByRidesharing(int $idRidesharing): array
    {
        $cursor = $this->collection->find(['idRidesharing' => $idRidesharing]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }    
}