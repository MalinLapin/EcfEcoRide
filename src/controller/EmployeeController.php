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
        // on commence par récupérer ce qui est envoyer en JSON depuis le front
        $data = json_decode(file_get_contents('php://input'), true);
        // on récupère les données de notre tableau
        $idReview = $data['reviewId'];

        // on retrouve notre avis en bdd
        $review = $this->reviewRepo->findById($idReview);

        // on affecte nos modification
        $review ->setStatusReview(StatusReview::approved)
                ->setReviewedBy($_SESSION['idUser'])
                ->setReviewedAt(new DateTimeImmutable());

        // On essaie de mettre à jour
        try{
            $this->reviewRepo->update($idReview, $review);
        }catch(\Exception $e){
            $this->logger->log('ERROR', 'Erreur lors de la mise à jour du status de l\'avis : ' . $e->getMessage());
        }

        //Si la mise à jour est effectuée, il faut mettre à jour le rang du chauffeur :
        $newRating = $this->reviewRepo->getAverageRatingByIdUser($review->getIdTarget());

        $driver = $this->userRepo->findById($review->getIdTarget());

        $driver->setGrade($newRating);

        try{
            $this->userRepo->update($driver);
        }catch(\Exception $e){
            $this->logger->log('ERROR', 'Erreur lors de la mise à jour du rang du chauffeur : ' . $e->getMessage());
        }


        // Si on réussi on renvoie une réponse positive en JSON.
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Avis approuver avec succès'
        ]);
        exit;
    }

    public function rejectReview():void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $idReview = $data['reviewId'];
        $reasonOfReject = $data['reasonOfReject'];

        $review = $this->reviewRepo->findById($idReview);

        $review ->setStatusReview(StatusReview::rejected)
                ->setReviewedBy($_SESSION['idUser'])
                ->setReviewedAt(new DateTimeImmutable())
                ->setReason($reasonOfReject);

        try{
            $this->reviewRepo->update($idReview, $review);
        }catch(\Exception $e){
            $this->logger->log('ERROR', 'Erreur lors de la mise à jour des places disponibles : ' . $e->getMessage());
        }

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Avis rejeter avec succès'
        ]);
        exit;
        
    }
}