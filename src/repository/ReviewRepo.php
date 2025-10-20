<?php

namespace App\repository;

use App\model\ReviewModel;

class ReviewRepo extends BaseRepoMongo
{
    protected string $collectionName = 'review';
    protected ?string $className = ReviewModel::class;


    // Recherche un avis par son destinataire
    public function findByTarget(int $idTarget): array
    {
        $cursor = $this->collection->find(['id_target' => $idTarget]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }

    // Recherche un avis par son rédacteur
    public function findByRedactor(int $idRedactor): array
    {
        $cursor = $this->collection->find(['id_redactor' => $idRedactor]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;
    }

    // Recherche un avis par son statut
    public function findByStatus(string $status): array
    {
        $cursor = $this->collection->find(['status_review' => $status]);
        $out = []; 
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        return $out;        
    }
}