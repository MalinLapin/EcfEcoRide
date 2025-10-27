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
use DateTimeImmutable;

class ParticipateController extends BaseController
{
    private Logger $logger;
    private ParticipateRepo $participateRepo;
    private UserRepo $userRepo;
    private RidesharingRepo $ridesharingRepo;

    public function __construct()
    {
        $this->logger = new Logger();
        $this->participateRepo = new ParticipateRepo();
        $this->userRepo = new UserRepo();
        $this->ridesharingRepo = new RidesharingRepo();
        parent::__construct();
    }

    /**
     * Permet à un utilisateur de participer à un covoiturage.
     * Gère la validation des données, la création de la participation, et les erreurs potentielles.
     * Redirige l'utilisateur en fonction du succès ou de l'échec de l'opération.
     */
    public function participateToRidesharing() :void
    {
        var_dump("=== TEST 1 : Entrée dans la méthode ===");

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('/');
            return;
        }
        var_dump("=== TEST 2 : Méthode POST ok ===");

        // Récupération et nettoyage des données du formulaire
        $data = $this->getPostData();
        var_dump("=== TEST 3 : Récupération des données du formulaire ===");

        var_dump($data['csrf_token']);

        // Validation du token CSRF
        if (!$this->tokenManager->validateCsrfToken($data['csrf_token']??''))
        {
            $this->response->error('Token de sécurité invalide.', 403);
            return;
        }
        var_dump("=== TEST 4 : le token est bon ===");

        // On vérifie que l'utilisateur est bien connecter.
        $this->requireAuth();
        var_dump("=== TEST 5 : test authentification ok ===");

        // On récupère toutes les infos de notre utilisateur
        $user = $this->userRepo->findById($_SESSION['idUser']);
        var_dump("=== TEST 6 : Récupération des info User ===");        
        
        $idRidesharing = $data['idRidesharing'];
        var_dump("=== TEST 7 : Récupération de l'id du trajet ===");
        // On récupérer le covoiturage.
        $ridesharing = $this->ridesharingRepo->findById($idRidesharing);
        var_dump("=== TEST 8 : Récupération du trajet ===");

        if(!$ridesharing)
        {
            $this->response->redirect("ridesharingDetail", [
                'errors' => 'Le covoiturage sélectionné est invalide.',
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss' => 'ridesharingDetail'
            ]);
            return;
        }

        
        var_dump("=== TEST 9 : vérification des place reservé ===");
        // On vérifie que le nombre de siège reserve ne dépasse pas le nombre de siège encore disponible et qu'il soit bien suppérieur à 0.
        if ($data['nbSeats'] == 0 || $data['nbSeats'] < 0) 
        {
            $this->render("ridesharingDetail/". $idRidesharing, [
                'errors' => 'Le nombre de place réservée doit être supérieur à 0.',
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss' => 'ridesharingDetail'
            ]);
            return;
        }else if ($data['nbSeats'] > $ridesharing->getAvailableSeats())
        {
            $this->render("ridesharingDetail/". $idRidesharing, [
                'errors' => 'Le nombre de place réservée dépasse le nombre de place disponible.',
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss' => 'ridesharingDetail'
            ]);
            return;
        }

        var_dump("=== TEST 10 : vérification éffectuer ===");
        // On créer la demande de participation en fonction des donées transmisent.
        $newparticipate = new ParticipateModel();

        $newparticipate->setIdParticipant($_SESSION['idUser'])
                        ->setIdRidesharing($ridesharing->getIdRidesharing())
                        ->setNbSeats($data['nbSeats'])
                        ->setCreatedAt(new DateTimeImmutable());

        var_dump("=== TEST 11 : Création objet participation ===");
        
        // On vérifie que le solde de crédit de l'utilisateur permette la participation
        try{
            var_dump("=== TEST 12 : vérification du crédit de l'user ===");
            if ($user->getCreditBalance() < $ridesharing->getPricePerSeat() * $data['nbSeats']) 
            {
                throw new \Exception("Crédit insuffisant");
            }
            var_dump("=== TEST 12 : Création objet participation ===");

        }catch (\Exception $e){
            $this->logger->log('ERROR','Votre solde de crédit ne permet pas votre participation : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->render("ridesharingDetail/". $idRidesharing, [
            'errors' => 'Votre solde de crédit ne permet pas votre participation',
            'csrf_token' => $this->tokenManager->generateCsrfToken(),
            'pageCss' => 'ridesharingDetail'
        ]);
            return;
        }
        var_dump("=== TEST 13 : Solde de crédit ok ===");
        try{
            var_dump("=== TEST 14 : Création de la participation en Bdd ===");
            $this->participateRepo->create($newparticipate);

        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de l\'inscritpion au covoiturage : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->render("ridesharingDetail/". $idRidesharing, [
                'errors' => 'Une erreur est survenue lors de votre inscription, veuillez réessayer plus tard.',
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss' => 'ridesharingDetail'
            ]);
            return;
        }
        var_dump("=== TEST 15 : Création objet participation OK ===");
        // Si tout ce passe bien on met à jour le solde de crédit de l'utilisateur
        var_dump("=== TEST 16 : Mise à jour du porte-feuille client ===");
        var_dump("Avant update". $user->getCreditBalance());
        $user->setCreditBalance($user->getCreditBalance() - ($ridesharing->getPricePerSeat() * $data['nbSeats']));
        try{
            
            $this->userRepo->update($user);
            var_dump("Après update". $user->getCreditBalance());
        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de la mise à jour du solde de crédit : ' . $e->getMessage());
            // Même si la mise à jour du crédit échoue, on ne bloque pas la participation.
        }

        var_dump("=== TEST 17 : Mise à jour du nombre de siège disponible ===");
        var_dump("avant update". $ridesharing->getAvailableSeats());
        // Maintenant on met a jour le nombre de place disponible du covoiturage
        $ridesharing->setAvailableSeats($ridesharing->getAvailableSeats() - $data['nbSeats']);
        var_dump("avant update l'objet ridesharing = ". $ridesharing);
        try{
            $this->ridesharingRepo->update($ridesharing);
            var_dump("après update". $ridesharing->getAvailableSeats());
        }catch (\Exception $e){
            $this->logger->log('ERROR','Erreur lors de la mise à jour des places disponibles : ' . $e->getMessage());
            // Même si la mise à jour des places disponibles échoue, on ne bloque pas la participation.
        }
        var_dump("=== TEST 18 : Tout est ok avant redirection ===");
        $this->render('/',[
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
            $this->response->redirect('page/myRidesharing');
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
            $this->response->redirect('page/myRidesharing', [
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
            $this->response->redirect('page/myRidesharing', [
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
            $this->response->redirect('page/myRidesharing', [
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
        $this->response->redirect('page/myRidesharing', [
            'title' => 'Mes covoiturages',
            'validation' => 'Votre participation a bien été annulée.'
        ]);
    }
}