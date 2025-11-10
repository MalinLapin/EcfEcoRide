<?php

namespace App\repository;

use App\model\ReviewModel;
use App\model\StatusReview;
use DateTimeImmutable;
use DateTimeZone;
use MongoDB\Operation\CountDocuments;
use MongoDB\BSON\UTCDateTime; 

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
        return $out;
    }

    // Calcule la note moyenne d'un conducteur via les avis qui lui sont addresser
    public function getAverageRatingByIdUser(int $id): float
    {
        // On selectionne ques les avis approved par les employés
        $filters = ['idTarget' => (int)$id,
                    'statuReview'=> 'approved'];
        
        // On recherche dans notre collection les documents qui correspondent.
        $cursor = $this->collection->find($filters);

        $out = [];

        // Pour faire la moyenne nous aurons besoin de la somme des notes précédentes.
        $totalRating= 0;

        foreach ($cursor as $doc) {
            $review = $this->toModel($doc);
            $totalRating += $review->getRating();
            $out[]=$review;
        }

        // on retourne le total / le nombre d'avis arrondi à un chiffre apres la virgule.
        return round($totalRating/count($out[]) , 1);
    }

    //
    public function countReviewApprovedByDay (DateTimeImmutable $date):int
    {
        // on convertie déjà le propriété pour récupere le début de journée.
        $startdate = $date->setTimezone(new DateTimeZone("UTC"))->setTime(0, 0, 0);
        // on recherche la date du lendemain
        $endDate = $$startdate->modify('+1 day');

        // on génere le filtre de recherche.
        $filter = ['statusReview'=>'approved', // uniquement les avis approuvés
                    // on défini la date d'approbation en donnant une fourchette de début et de fin.
                    'reviewedAt'=>[
                        '$gte'=>new UTCDateTime($startdate->getTimestamp() * 1000), 
                        '$lt'=>new UTCDateTime($endDate->getTimestamp() * 1000)
                    ]];

         // On compte ensuite les documents correspondant au filtre dans notre collection.
        $count = $this->collection->countDocuments($filter); 

        return $count;
    }
}