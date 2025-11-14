<?php

namespace App\controller;

use App\model\ReviewModel;
use App\model\Status;
use App\model\StatusReview;
use DateTimeImmutable;

class ReviewController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function letReview():void
    {
        // On vérifie que l'utilisateur est bien connecter.
        $this->requireAuth();
        

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
            exit;
        }
        
        
        // Validation du token CSRF
        if (!$this->tokenManager->validateCsrfToken($_SERVER['HTTP_CSRFTOKEN']??''))
        {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Token de sécurité invalide']);
            exit;
        }
        
        // Récupération des données du formulaire
        $data = json_decode(file_get_contents('php://input'), true);

        $data = $this->validator->sanitize($data);

        $review = new ReviewModel();
        
        $review ->setIdParticipation($data['idParticipation'])
                ->setComment($data['comment'])
                ->setRating($data['rating'])
                ->setCreatedAt(new DateTimeImmutable())
                ->setStatusReview(StatusReview::pending)
                ->setIdRedactor($_SESSION['idUser'])
                ->setIdTarget($data['idDriver']);
        try{
            $this->reviewRepo->create($review);
        }catch(\Exception){
            http_response_code(500);
            echo json_encode([
                'success' => true,
                'message' => "Une erreur lors de la création de votre avis, veuillez réessayer plus tard."
            ]);
            exit; 
        }
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Votre avis à bien été transmis, il sera visible après confirmation de nos équipes.'
        ]);
        exit;
        
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

    public function countReviewValidateByDay(DateTimeImmutable $date):int
    {
        // On va rechercher le nombre d'avis valider en fonction d'une date.
        $listeReviewValidate = $this->reviewRepo->countReviewApprovedByDay($date);
        return (int)$listeReviewValidate;
    }
}