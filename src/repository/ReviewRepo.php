<?php

namespace App\repository;

use App\Model\ReviewModel;

class ReviewRepository extends BaseRepoMongo
{
    protected string $collectionName = 'reviews';
    protected string $className = ReviewModel::class;
    
    // Recherche un avis par son destinataire
    public function findByTarget(int $idTarget): array
    {
        $cursor = $this->collection->find(['idTarget' => $idTarget]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }

    // Recherche un avis par son rédacteur
    public function findByRedactor(int $idRedactor): array
    {
        $cursor = $this->collection->find(['idRedactor' => $idRedactor]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }

    // Recherche un avis par son statut
    public function findByStatus(string $status): array
    {
        $cursor = $this->collection->find(['status' => $status]);
        $out = []; 
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;        
    }
}