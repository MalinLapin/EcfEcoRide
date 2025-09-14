<?php

namespace App\controller;

use App\model\ParticipateModel;
use App\utils\Logger;
use App\utils\Response;
use App\security\Validator;
use App\security\TokenManager;
use App\repository\ParticipateRepo;
use App\repository\RidesharingRepo;
use App\repository\UserRepo;
use App\service\MailService;

class ParticipateController extends BaseController
{
    public function __construct(
        private TokenManager $tokenManager,
        private Logger $logger,
        private ParticipateRepo $participateRepo,
        private UserRepo $userRepo,      
        private Validator $validator,
        private RidesharingRepo $ridesharingRepo) 
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
        // On vérifie que l'utilisateur est bien connecter.
        $this->requireAuth();

        // On récupère toutes les infos de notre utilisateur
        $user = $this->userRepo->findById($_SESSION['id_user']);

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('ridesharing/ridesharing-detail');
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

        
        $idRidesharing = $data['id_ridesharing'];
        // On récupérer les détails du covoiturage.
        $ridesharing = $this->ridesharingRepo->findByIdWithDetails($idRidesharing);

        if(!$ridesharing)
        {
            $this->response->redirect("ridesharing/ridesharing-detail?id=$idRidesharing", [
                'title' => 'Détails du covoiturage',
                'old' => $data,
                'errors' => 'Le covoiturage sélectionné est invalide.',
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        // On créer la demande de participation en fonction des donées transmisent.
        $newparticipate = new ParticipateModel();

        $newparticipate->setIdParticipant($data['id_user'])
                        ->setIdRidesharing($data['id_ridesharing'])
                        ->setNbSeats($data['nb_seat']);

        
        // On vérifie que le nombre de siège reserve ne dépasse pas le nombre de siège encore disponible et qu'il soit bien suppérieur à 0.
        if ($data['nb_seat'] == 0 || $data['nb_seat'] < 0) 
        {
            $this->response->redirect("ridesharing/ridesharing-detail?id=$idRidesharing", [
                'title' => 'Détails du covoiturage',
                'old' => $data,
                'errors' => 'Le nombre de place réservée doit être supérieur à 0.',
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }else if ($data['nb_seat'] > $ridesharing->getAvailableSeats())
        {
            $this->response->redirect("ridesharing/ridesharing-detail?id=$idRidesharing", [
                'title' => 'Détails du covoiturage',
                'old' => $data,
                'errors' => 'Le nombre de place réservée dépasse le nombre de place disponible.',
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        
        
        // On vérifie que le solde de crédit de l'utilisateur permette la participation
        try{

            if ($user->getCreditBalance() < $ridesharing->getPricePerSeat() * $data['nb_seat']) 
            {
                throw new \Exception("Crédit insuffisant");
            }

        }catch (\Exception $e){
            $this->logger->log('ERROR','Votre solde de crédit ne permet pas votre participation : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->response->redirect("ridesharing/ridesharing-detail?id=$idRidesharing", [
            'title' => 'Détails du covoiturage',
            'old' => $data,
            'errors' => 'Votre solde de crédit ne permet pas votre participation',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
            return;
        }
        
        try{
            $this->participateRepo->create($newparticipate);

        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de l\'inscritpion au covoiturage : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->response->redirect("ridesharing/ridesharing-detail?id=$idRidesharing", [
                'title' => 'Détails du covoiturage',
                'old' => $data,
                'errors' => 'Une erreur est survenue lors de votre inscription, veuillez réessayer plus tard.',
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        // Si tout ce passe bien on met à jour le solde de crédit de l'utilisateur
        $user->setCreditBalance($user->getCreditBalance() - ($ridesharing->getPricePerSeat() * $data['nb_seat']));
        try{
            $this->userRepo->update($user);
        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de la mise à jour du solde de crédit : ' . $e->getMessage());
            // Même si la mise à jour du crédit échoue, on ne bloque pas la participation.
        }

        // Maintenant on met a jour le nombre de place disponible du covoiturage
        $ridesharing->setAvailableSeats($ridesharing->getAvailableSeats() - $data['nb_seat']);
        try{
            $this->ridesharingRepo->update($ridesharing);
        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de la mise à jour des places disponibles : ' . $e->getMessage());
            // Même si la mise à jour des places disponibles échoue, on ne bloque pas la participation.
        }

        $this->response->redirect('home/index',[
                'title'=>'Accueil - Ecoride',
                'validation'=>'La participation à bien été enregistré.'
            ]);
    }


    public function cancelParticipation(): void
    {
        // On vérifie que l'utilisateur est bien connecter.
        $this->requireAuth();

        // On récupère toutes les infos de notre utilisateur
        $user = $this->userRepo->findById($_SESSION['id_user']);

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('profile/my-ridesharing');
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

        // On vérifie que l'ID de la participation est bien présent et valide.
        $idParticipate = $data['id_participate'];

        // Si l'ID de la participation n'est pas valide, on redirige avec un message d'erreur.
        if (!$idParticipate) {
            $this->response->redirect('profile/my-ridesharing', [
                'title' => 'Mes covoiturages',
                'errors' => 'Participation invalide.',
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        // On récupère la participation
        $participate = $this->participateRepo->findById($idParticipate);

        // On vérifie que la participation existe et appartient à l'utilisateur connecté.
        if (!$participate || $participate->getIdParticipant() !== $user->getIdUser()) {
            $this->response->redirect('profile/my-ridesharing', [
                'title' => 'Mes covoiturages',
                'errors' => 'Participation invalide ou non autorisée.',
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }
        // On tente de supprimer la participation.
        try {
            $this->participateRepo->delete($idParticipate);
        } catch (\Exception $e) {
            $this->logger->log('ERROR', 'Erreur lors de l\'annulation de la participation : ' . $e->getMessage());
            $this->response->redirect('profile/my-ridesharing', [
                'title' => 'Mes covoiturages',
                'errors' => 'Une erreur est survenue lors de l\'annulation de votre participation, veuillez réessayer plus tard.',
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        // Si tout ce passe bien on remet à jour le solde de crédit de l'utilisateur
        $refundAmount = $participate->getNbSeats() * $participate->getPricePerSeat();
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
        $this->response->redirect('profile/my-ridesharing', [
            'title' => 'Mes covoiturages',
            'validation' => 'Votre participation a bien été annulée.'
        ]);
    }
}