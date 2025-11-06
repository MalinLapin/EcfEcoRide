<?php

namespace App\repository;

use App\model\ReviewModel;
use App\model\StatusReview;

class ReviewRepo extends BaseRepoMongo
{
    protected string $collectionName = 'review';
    protected ?string $className = ReviewModel::class;


    // Recherche un avis par son statut
    public function findByStatus(StatusReview $status): array
    {
        $cursor = $this->collection->find(['statusReview' => $status]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }
        var_dump($out);
        return $out;
    }

    // Calcule la note moyenne d'un conducteur via les avis qui lui sont addresser
    public function getAverageRatingByIdUser(int $id): float
    {
        // On selectionne ques les avis approved par les employés
        $filters = ['idTarget' => $id,
                    'statuReview'=> 'approved'];
        
        // On recherche dans notre collection les documents qui correspondent.
        $cursor = $this->collection->find([$filters]);

        $out = [];

        // Pour faire la moyenne nous aurons besoin de la somme des notes précédentes.
        $totalRating= 0;

        foreach ($cursor as $doc) {
            $review = $this->toModel($doc);
            $totalRating += $review->getRating();
            $out[]=$review;
        }

        // On divise ensuite par le nombre d'avis.
        return $totalRating/count($out[]);
    }

    public function getDetailByIdReview(string $idRevew):array
    {
        $cursor = $this->collection->find(['_id' => $idRevew]);
        $out = [];
        foreach ($cursor as $doc) {
            $out[] = $this->toModel($doc);
        }

        

    }

}