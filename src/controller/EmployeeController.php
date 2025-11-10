<?php

namespace App\controller;


use App\model\StatusReview;

class EmployeeController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }


    public function showEmployeeSpace():void
    {

        // On recherche les avis qui ont sont en attente de validation.
        $listReviewPending = $this->reviewRepo->findByStatus(StatusReview::pending);

        $reviewsInfo=[];

        // Pour chaque avis nous allons rechercher toutes les infos
        foreach($listReviewPending as $review){

            // Tout d'abord la participation
            $participate = $this->participateRepo->findById($review->getIdParticipation());
            // ensuite les infos du trajet
            $ride = $this ->ridesharingRepo->findById($participate->getIdRidesharing());
            // les infos du conducteur
            $driver = $this->userRepo->findById($review->getIdTarget());
            // pour finir les info du participant.
            $passanger = $this->userRepo->findById($review->getIdRedactor());

            $reviewsInfo[]=[
                'review'=>$review,
                'ride'=>$ride,
                'driver'=>$driver,
                'passanger'=>$passanger];
        }

        // On compte le total d'avis en attente.
        $countReviewPending = count($listReviewPending);

        $this->render('employeeSpace', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'employeeSpace',
            'scriptJs'=>'employeeSpace',
            'reviewsInfo'=>$reviewsInfo,
            'countReviewPending' => $countReviewPending
        ]);
    }
}