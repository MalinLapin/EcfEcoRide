<?php

namespace App\controller;


use App\model\StatusReview;
use DateTimeImmutable;

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
            'countReviewPending'=>$countReviewPending
        ]);
    }

    public function approvedReview ():void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $idReview = $data['reviewId'];

        $review = $this->reviewRepo->findById($idReview);
        $review ->setStatusReview(StatusReview::approved)
                ->setReviewedBy($_SESSION['idUser'])
                ->setReviewedAt(new DateTimeImmutable());

        try{
            $this->reviewRepo->update($idReview, $review);
        }catch(\Exception $e){
            $this->logger->log('ERROR', 'Erreur lors de la mise à jour des places disponibles : ' . $e->getMessage());
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Avis approuver avec succès'
        ]);
        exit;
    }

    public function rejectReview():void
    {

    }
}