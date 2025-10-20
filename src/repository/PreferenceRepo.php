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
        $cursor = $this->collection->find(['id_car' => $idRidesharing]);
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