<?php

namespace App\controller;

use App\model\ParticipateModel;
use App\service\MailService;
use DateTimeImmutable;

class ParticipateController extends BaseController
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Permet à un utilisateur de participer à un covoiturage.
     * Gère la validation des données, la création de la participation, et les erreurs potentielles.
     * Redirige l'utilisateur en fonction du succès ou de l'échec de l'opération.
     */
    public function participateToRidesharing() :void
    {       

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('/');
            return;
        }

        // Récupération et nettoyage des données du formulaire
        $data = $this->getPostData();

        // Validation du token CSRF
        if (!$this->tokenManager->validateCsrfToken($data['csrf_token']??''))
        {
            $this->response->error('Token de sécurité invalide.', 403);
            return;
        }

        // On vérifie que l'utilisateur est bien connecter.
        $this->requireAuth();

        // On récupère toutes les infos de notre utilisateur
        $user = $this->userRepo->findById($_SESSION['idUser']);      
        
        $idRidesharing = $data['idRidesharing'];
        // On récupérer le covoiturage.
        $ridesharing = $this->ridesharingRepo->findById($idRidesharing);
        
        if(!$ridesharing)
        {
            $this->response->redirect("ridesharingDetail", [
                'message' => 'Une erreur est survenue veuillez éssayer plus tard',
                'type'=>'error'
            ]);
            return;
        }

        // On vérifie que le nombre de siège reserve ne dépasse pas le nombre de siège encore disponible et qu'il soit bien suppérieur à 0.
        if ($data['nbSeats'] == 0 || $data['nbSeats'] < 0) 
        {
            $this->redirect("ridesharingDetail/{$idRidesharing}", [
                'message' => 'Le nombre de place réservée doit être supérieur à 0.',
                'type'=>'warning'
            ]);
            return;
        }else if ($data['nbSeats'] > $ridesharing->getAvailableSeats())
        {
            $this->redirect("ridesharingDetail/{$idRidesharing}", [
                'message' => 'Le nombre de place réservée dépasse le nombre de place disponible.',
                'type'=> 'warning'
            ]);
            return;
        }

        // On créer la demande de participation en fonction des donées transmisent.
        $newparticipate = new ParticipateModel();

        $newparticipate->setIdParticipant($_SESSION['idUser'])
                        ->setIdRidesharing($ridesharing->getIdRidesharing())
                        ->setNbSeats($data['nbSeats'])
                        ->setCreatedAt(new DateTimeImmutable());
                        
        // On vérifie que le solde de crédit de l'utilisateur permette la participation
        try{
            if ($user->getCreditBalance() < $ridesharing->getPricePerSeat() * $data['nbSeats']) 
            {
                throw new \Exception("Crédit insuffisant");
            }
            
        }catch (\Exception $e){
            $this->logger->log('ERROR','Votre solde de crédit ne permet pas votre participation : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->redirect("ridesharingDetail/{$idRidesharing}", [
            'message' => 'Votre solde de crédit ne permet pas votre participation',
            'type' => 'warning'
        ]);
            return;
        }
        
        try{
            $this->participateRepo->create($newparticipate);

        }catch (\Exception $e){
            if($e->getCode()==23000){
                $this->logger->log('ERROR','Inscription déjà effectuée : ' . $e->getMessage());
                // On ré-affiche le formulaire d'inscription avec un message d'erreur
                $this->redirect("ridesharingDetail/{$idRidesharing}", [
                    'message' => 'Vous etes déjà inscrit pour ce covoiturage veuillez vérifier sur votre profil.',
                    'type'=>'error',
                ]);
            return; 
            }
            
            $this->logger->log('ERROR','Erreur lors de l\'inscritpion au covoiturage : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->redirect("ridesharingDetail/{$idRidesharing}", [
                'message' => 'Une erreur est survenue lors de votre inscription, veuillez réessayer plus tard.',
                'type'=>'error',
            ]);
            return;
        }

        // Si tout ce passe bien on met à jour le solde de crédit de l'utilisateur
        $user->setCreditBalance($user->getCreditBalance() - ($ridesharing->getPricePerSeat() * $data['nbSeats']));
        try{
            
            $this->userRepo->update($user);
        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de la mise à jour du solde de crédit : ' . $e->getMessage());
            // Même si la mise à jour du crédit échoue, on ne bloque pas la participation.
        }

        // Maintenant on met a jour le nombre de place disponible du covoiturage
        $ridesharing->setAvailableSeats($ridesharing->getAvailableSeats() - $data['nbSeats']);
        
        try{
            $this->ridesharingRepo->update($ridesharing);
            
        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de la mise à jour des places disponibles : ' . $e->getMessage());
            // Même si la mise à jour des places disponibles échoue, on ne bloque pas la participation.
        }

        $this->redirect('/',[
                'message'=>'La participation à bien été enregistré.',
                'type'=>'success'
            ]);
    }


    public function cancelParticipation(int $idParticipation): void
    {
        
        // On vérifie si la requête du front est bien en AJAX
        $isAjax = isset($_SERVER['HTTP_TYPEREQUETE']) && strtolower($_SERVER['HTTP_TYPEREQUETE']) === 'ajax';
        

        // Si c'est le cas on définit le header JSON
        if($isAjax){
            header('Content-Type: application/json; charset=utf-8');
        }
        
        
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
        
        // On récupère la participation
        $participate = $this->participateRepo->findById($idParticipation);
        

        // On vérifie que la participation existe et appartient à l'utilisateur connecté.
        if (!$participate || $participate->getIdParticipant() !== $_SESSION['idUser']) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Participation invalide']);
            exit;
        }
        // On tente de supprimer la participation.
        try {
            $this->participateRepo->delete($idParticipation);
        } catch (\Exception $e) {
            $this->logger->log('ERROR', 'Erreur lors de l\'annulation de la participation : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue, veuillez réessayer.'
            ]);
            exit;
        }

        // on récupere l'objet ridesharing
        $ride = $this->ridesharingRepo->findById($participate->getIdRidesharing());

        // Si tout ce passe bien on remet à jour le solde de crédit de l'utilisateur
        $refundAmount = $participate->getNbSeats() * $ride->getPricePerSeat();

        $user = $this->userRepo->findById($_SESSION['idUser']);

        $user->setCreditBalance($user->getCreditBalance() + $refundAmount);
        try {
            $this->userRepo->update($user);
        } catch (\Exception $e) {
            $this->logger->log('ERROR', 'Erreur lors de la mise à jour du solde de crédit : ' . $e->getMessage());
            // Même si la mise à jour du crédit échoue, on ne bloque pas l'annulation de la participation.
        }

        // Maintenant on remet a jour le nombre de place disponible du covoiturage
        $ridesharing = $this->ridesharingRepo->findById($participate->getIdRidesharing());
        if ($ridesharing) {
            $ridesharing->setAvailableSeats($ridesharing->getAvailableSeats() + $participate->getNbSeats());
            try {
                $this->ridesharingRepo->update($ridesharing);
            } catch (\Exception $e) {
                $this->logger->log('ERROR', 'Erreur lors de la mise à jour des places disponibles : ' . $e->getMessage());
                // Même si la mise à jour des places disponibles échoue, on ne bloque pas l'annulation de la participation.
            }
        }

        // Redirection avec message de succès.
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Votre participation à ce trajet a été annulée avec succès.'
        ]);
        exit;
    }
}