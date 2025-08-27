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
    
    
    public function showSearchRidesharing(): void // Afficher le formulaire de recherche de covoiturage
    {
        $this->render('ridesharing/search-ridesharing', [
            'title' => 'Rechercher un covoiturage',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);
    }

    public function searchRidesharing(): void // Rechercher des covoiturages
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
        } else {
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

    public function showRidesharingDetail(int $idRidesharing): void  // Afficher les détails d'un covoiturage
    {

    }

    public function createRidesharing(): void // Créer un nouveau covoiturage
    {

    }

    public function myRidesharing(): void // Afficher les covoiturages de l'utilisateur connecté
    {

    }

    public function manageRidesharing(int $idRidesharing): void // Gérer un covoiturage spécifique (pour le conducteur)
    {

    }
}