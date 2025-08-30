<?php

namespace App\controller;

use App\repository\RidesharingRepo;
use App\model\RidesharingModel;
use App\security\Validator;
use App\security\TokenManager;
use App\utils\Logger;
use App\utils\Response;

class RidesharingController extends BaseController
{
    public function __construct(
        private TokenManager $tokenManager,
        private Logger $logger,
        private RidesharingRepo $ridesharingRepo,
        private Validator $validator) 
    {
        parent::__construct();
    }
    
    /**
     * Afficher le formulaire de recherche de covoiturage
     * @return void
     */
    public function showSearchRidesharing(): void
    {
        $this->render('ridesharing/search-ridesharing', [
            'title' => 'Rechercher un covoiturage',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
    }

    /**
     * Gérer la soumission du formulaire de recherche de covoiturage
     * @return void
     */
    public function searchRidesharing(): void 
    {
        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('ridesharing/search-ridesharing');
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

        // Validation des données de recherche
        $errors = [];

        if (empty($data['departure_city'])) {
            $errors['departure_city'] = "La ville de départ est requise.";
        }

        if (empty($data['departure_address'])) {
            $errors['departure_address'] = "L'addresse de départ est requise.";
        }

        if (empty($data['departure_date']) || !strtotime($data['departure_date'])) {
            $errors['departure_date'] = "La date de départ est requise et doit être au format valide.";
        } else { // On vérifie que la date de départ est bien dans le futur.
            $departureDateTime = new \DateTime($data['departure_date']);
            $now = new \DateTime();
            if ($departureDateTime < $now) {
                $errors['departure_date'] = "La date de départ doit être dans le futur.";
            }
        }

        if (empty($data['arrival_city'])) {
            $errors['arrival_city'] = "La ville d'arriver est requise.";
        }

        if (empty($data['arrival_address'])) {
            $errors['arrival_address'] = "L'addresse d'arriver' est requise.";
        }

        if (empty($data['nb_seats']) || !is_numeric($data['nb_seats']) || $data['nb_seats'] < 1 || $data['nb_seats'] > 6) {
            $errors['nb_seats'] = "Le nombre de places rechercher doit être un nombre entre 1 et 6.";
        }

        if (!empty($errors)) 
        {
            $this->render('ridesharing/search-ridesharing', [
                'title' => 'Inscription',
                'errors' => $errors,
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        $listRidesharing = $this->ridesharingRepo->getRidesharingByParams($data);

        // Recherche des covoiturages en fonction des critères fournis
        if($listRidesharing)
        {
            $this->render('ridesharing/list-ridesharing', [
                'title' => 'Résultats de la recherche',
                'ridesharings' => $listRidesharing
            ]);
            return;
        } else
        {
            $this->render('ridesharing/search-ridesharing', [
                'title' => 'Résultats de la recherche',
                'error' => 'Aucun covoiturage ne correspond à votre recherche.',
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }
    }
    
    /**
     * Afficher les détails d'un covoiturage spécifique
     * @param int $idRidesharing
     */
    public function showRidesharingDetail(int $idRidesharing): void
    {
        // On vérifie que l'ID est un entier valide
        $idRidesharing = filter_var($_GET['id'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
        if (!$idRidesharing) {
            $this->response->error('ID de covoiturage invalide.', 400);
            return;
        }
        
        $ridesharingDetails = $this->ridesharingRepo->findByIdWithDetails($idRidesharing);
        
        // On vérifie que le covoiturage existe
        if (!$ridesharingDetails) {
            $this->response->error('Covoiturage non trouvé.', 404);
            return;
        }

        // Affichage des détails du covoiturage 
        $this->render('ridesharing/ridesharing-detail', [
            'title' => 'Détails du covoiturage',
            'ridesharing' => $ridesharingDetails
        ]);
    }

    public function myRidesharing(): void // Afficher les covoiturages de l'utilisateur connecté
    {

    }

    public function createRidesharing(): void // Créer un nouveau covoiturage
    {
        
    }

    public function manageRidesharing(int $idRidesharing): void // Gérer un covoiturage spécifique (changer son statue selon le besoin)
    {

    }

}