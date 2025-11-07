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

    public function findDetailReview(int $idParticipation, int $idTarget, int $idRedactor):array
    {
        $data = [];

        try{
            $participate = $this->participateRepo->findById($idParticipation);
        }catch(\Exception $e){
            $this->logger->log('ERROR', "Erreur lors de la recherche de participation : " . $e->getMessage());
        }

        $data [] = $participate;

        try{
            $ride = $this->ridesharingRepo->findById($participate->getIdRidesharing());
        }catch(\Exception $e){
            $this->logger->log('ERROR', "Erreur lors de la recherche du trajet : " . $e->getMessage());
        }

        $data[] = $ride;

        try{
            $driver = $this->userRepo->findById($idTarget);
        }catch(\Exception $e){
            $this->logger->log('ERROR', "Erreur lors de la recherche du chauffeur : " . $e->getMessage());
        }

        $data[] = $driver;

        try{
            $passanger = $this->userRepo->findById($idRedactor);
        }catch(\Exception $e){
            $this->logger->log('ERROR', "Erreur lors de la recherche du chauffeur : " . $e->getMessage());
        }

        $data[]= $passanger;

        return $data;       
    }
}