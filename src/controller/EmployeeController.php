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

        // on doit associer le trajet a chaque avis afin d'avoir les infos telle que ville de départ/arrivé et autre.
        foreach($listReviewPending as $review){
            $participate = $this->participateRepo->findById($review->getIdParticipation());
            $ride = $this ->ridesharingRepo->findById($participate->getIdRidesharing());

            $review[] = $ride;
        }

        // On compte le total d'avis en attente.
        $countReview = count($listReviewPending);

        $this->render('employeeSpace', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'employeeSpace',
            'scriptJs'=>'employeeSpace',
            'listReviewPending'=>$listReviewPending,
            'countReview' => $countReview
        ]);
    }
}