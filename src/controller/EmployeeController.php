<?php

namespace App\controller;

use App\model\ReviewModel;
use App\model\StatusReview;

class EmployeeController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showEmployeeSpace():void
    {

        $listReviewPending = $this->reviewRepo->findByStatus(StatusReview::pending);        

        $this->render('employeeSpace', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'employeeSpace',
            'scriptJs'=>'employeeSpace',
            'listReviewPending'=>$listReviewPending
        ]);
    }
}