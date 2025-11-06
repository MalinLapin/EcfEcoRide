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

    public function showDetailReview(string $id):void
    {
        
    }
}