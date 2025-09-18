<?php

namespace App\controller;

use App\utils\Logger;
use App\utils\Response;
use App\security\Validator;
use App\security\TokenManager;
use App\model\RidesharingModel;
use App\repository\RidesharingRepo;
use App\model\PreferenceModel;
use App\repository\PreferenceRepo;
use App\repository\ReviewRepo;
use App\repository\UserRepo;
use App\service\MailService;

class RidesharingController extends BaseController
{
    public function __construct(
        private TokenManager $tokenManager,
        private Logger $logger,
        private RidesharingRepo $ridesharingRepo,
        private PreferenceRepo $preferenceRepo,
        private UserRepo $userRepo,        
        private Validator $validator,
        private ReviewRepo $reviewRepo) 
    {
        parent::__construct();
    }
    
    /**
     * Afficher le formulaire de recherche de covoiturage
     * @return void
     */
    public function showSearchRidesharing(): void
    {
        $this->render('searchRidesharing', [
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
            $this->response->redirect('page/searchRidesharing');
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

        if (empty($data['departureCity'])) {
            $errors['departureCity'] = "La ville de départ est requise.";
        }

        if (empty($data['departureAddress'])) {
            $errors['departureAddress'] = "L'addresse de départ est requise.";
        }

        if (empty($data['departureDate']) || !strtotime($data['departureDate'])) {
            $errors['departureDate'] = "La date de départ est requise et doit être au format valide.";
        } else { // On vérifie que la date de départ est bien dans le futur.
            $departureDateTime = new \DateTime($data['departureDate']);
            $now = new \DateTime();
            if ($departureDateTime < $now) {
                $errors['departureDate'] = "La date de départ doit être dans le futur.";
            }
        }

        if (empty($data['arrivalCity'])) {
            $errors['arrivalCity'] = "La ville d'arriver est requise.";
        }

        if (empty($data['arrivalAddress'])) {
            $errors['arrivalAddress'] = "L'addresse d'arriver' est requise.";
        }

        if (empty($data['nbSeats']) || !is_numeric($data['nbSeats']) || $data['nbSeats'] < 1 || $data['nbSeats'] > 6) {
            $errors['nbSeats'] = "Le nombre de places rechercher doit être un nombre entre 1 et 6.";
        }

        if (!empty($errors)) 
        {
            $this->render('search-ridesharing', [
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
            $this->render('list-ridesharing', [
                'title' => 'Résultats de la recherche',
                'ridesharings' => $listRidesharing
            ]);
            return;
        } else
        {
            $this->render('search-ridesharing', [
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
        $idRidesharing = filter_var($_GET['idRidesharing'], FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
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

        // Récuperer les avis du conducteur.
        $listReview = $this->reviewRepo->findByTarget($ridesharingDetails->getIdRidesharing());

        // Affichage des détails du covoiturage 
        $this->render("ridesharing-detail?id=$idRidesharing", [
            'title' => 'Détails du covoiturage',
            'ridesharing' => $ridesharingDetails,
            'listReview' => $listReview
        ]);
    }


    /**
     * Afficher les covoiturages où l'utilisateur connecté est conducteur ou passager
     * 
     * @return void
     */
    public function myRidesharing(): void 
    {
        // On s'assure que l'utilisateur est connecté
        $this->requireAuth();

        // Récupération de l'ID de l'utilisateur connecté
        $userId = $_SESSION['id_user'];


        // Récupération des covoiturages où l'utilisateur est conducteur ou passager
        $listParticipate = $this->ridesharingRepo->findRidesharingByParticipant($userId);
        $listRidesharing = $this->ridesharingRepo->findRidesharingByDriver($userId);        
        
        $this->render('my-ridesharing', [
            'title' => 'Mes covoiturages',
            'participates' => $listParticipate,
            'ridesharings' => $listRidesharing
        ]);
        
    }

    /**
     * Afficher le formulaire de création de covoiturage
     * 
     * @return void
     */
    public function showCreateRidesharing(): void
    {
        $this->requireAuth();
        
        $this->render('createRidesharing', [
            'title' => 'Créer un covoiturage',
            'csrf_token' => $this->tokenManager->generateCsrfToken()
        ]);       
    }

    /**
     * Gérer la soumission du formulaire de création de covoiturage
     * 
     * @return void
     */
    public function createRidesharing(): void
    {
        $this->requireAuth();

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('ridesharing/createRidesharing');
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

        // Validation des données de création de covoiturage
        $errors = [];

        if (empty($data['departureCity'])) {
            $errors['departureCity'] = "La ville de départ est requise.";
        }

        if (empty($data['departureAddress'])) {
            $errors['departureAddress'] = "L'adresse de départ est requise.";
        }

        if (empty($data['departureDate']) || !strtotime($data['departureDate'])) {
            $errors['departureDate'] = "La date de départ est requise et doit être au format valide.";
        } else { // On vérifie que la date de départ est bien dans le futur.
            $departureDateTime = new \DateTime($data['departureDate']);
            $now = new \DateTime();
            if ($departureDateTime < $now) {
                $errors['departureDate'] = "La date de départ doit être dans le futur.";
            }
        }

        if (empty($data['arrivalCity'])) {
            $errors['arrivalCity'] = "La ville d'arrivée est requise.";
        }

        if (empty($data['arrivalAddress'])) {
            $errors['arrivalAddress'] = "L'adresse d'arrivée est requise.";
        }

        if (empty($data['nbSeats']) || !is_numeric($data['nbSeats']) || $data['nbSeats'] < 1 || $data['nbSeats'] > 6) {
            $errors['nbSeats'] = "Le nombre de places doit être un nombre entre 1 et 6.";
        }

        if (empty($data['id_car'])) {
            $errors['id_car'] = "Veuillez selectionner un véhicule.";
        }

        if (!empty($errors)) 
        {
            $this->render('createRidesharing', [
                'title' => 'Créer un covoiturage',
                'errors' => $errors,
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        // Préparation des données pour la création du covoiturage
        $ridesharingModel = new RidesharingModel(
            $data['departureCity'],
            $data['departureAddress'],
            new \DateTimeImmutable($data['departureDate']),
            $data['arrivalCity'],
            $data['arrivalAddress'],
            $data['nbSeats'],
            $data['id_car'],
            $_SESSION['id_user'],
            'pending'
        );
        // Création du covoiturage + recuperation de son ID
        $ridesharingId = $this->ridesharingRepo->create($ridesharingModel);

        if(!$ridesharingId)
        {
            $this->response->error('Une erreur est survenue lors de la création du covoiturage.', 500);
            return;
        }

        // Recupération des preferences défini par le conducteur.
        foreach ($data['preferences'] as $pref) 
        {
            if (!empty($pref['label'])) 
            {
                $preferenceData = [
                    'label' => $pref['label'],
                    'isAccepted' => $pref['isAccepted'],
                    'idRidesharing' => $ridesharingId
                ];
                $preferenceModel = new PreferenceModel($preferenceData);
                $preference = $this->preferenceRepo->create($preferenceModel);

                if(!$preference) 
                {
                    $this->ridesharingRepo->delete($ridesharingId); // Suppression du covoiturage créé précédemment en cas d'erreur.
                    $errors[] = "Une erreur est survenue lors de l'enregistrement des préférences.";
                    $this->render('createRidesharing', [
                        'title' => 'Créer un covoiturage',
                        'errors' => $errors,
                        'old' => $data,
                        'csrf_token' => $this->tokenManager->generateCsrfToken()
                    ]);
                    
                    return;
                }
            }
        }

        // Redirection vers la page de détails du covoiturage nouvellement créé
        $this->response->redirect("/page/ridesharing-detail?id=$ridesharingId");
    }

    /**
     * Permet au conducteur de démarrer un covoiturage.
     * 
     * @return void
     */
    public function startRidesharing(): void 
    {
        $this->requireAuth();

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('page/createRidesharing');
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

        $ridesharing = $this->ridesharingRepo->findById($data['id_ridesharing']??0);
        $driver = $this->userRepo->findById($ridesharing->getIdDriver());
        
        // On vérifie que le covoiturage existe et que l'utilisateur connecté est bien le conducteur.
        if (!$ridesharing || $driver->getIdUser() !== $_SESSION['id_user'])
        {
            $this->response->error('Covoiturage non trouvé ou accès refusé.', 403);
            return;
        }
        
        $this->ridesharingRepo->startRide($data['id_ridesharing']);
        $this->response->redirect("page/myRidesharing");
    }
    
    /**
     * Permet au conducteur de terminer un covoiturage.
     * 
     * @return void
     */
    public function endRidesharing(): void 
    {
        $this->requireAuth();

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('page/createRidesharing');
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

        $ridesharing = $this->ridesharingRepo->findById($data['id_ridesharing']??0);
        $driver = $this->userRepo->findById($ridesharing->getIdDriver());
        
        // On vérifie que le covoiturage existe et que l'utilisateur connecté est bien le conducteur.
        if (!$ridesharing || $driver->getIdUser() !== $_SESSION['id_user'])
        {
            $this->response->error('Covoiturage non trouvé ou accès refusé.', 403);
            return;
        }

        $this->ridesharingRepo->endRide($data['id_ridesharing']);
        
        // Vérification de la fin de participation.

        // Envoie du mail de fin de covoiturage.

        $this->response->redirect("page/myRidesharing");
    }

    /**
     * Permet au conducteur d'annuler un covoiturage.
     * 
     * @return void
     */
    public function cancelRidesharing(): void
    {
        $this->requireAuth();

        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('page/createRidesharing');
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

        $ridesharing = $this->ridesharingRepo->findById($data['id_ridesharing']??0);
        $driver = $this->userRepo->findById($ridesharing->getIdDriver());
        
        // On vérifie que le covoiturage existe et que l'utilisateur connecté est bien le conducteur.
        if (!$ridesharing || $driver->getIdUser() !== $_SESSION['id_user'])
        {
            $this->response->error('Covoiturage non trouvé ou accès refusé.', 403);
            return;
        }
        
        $this->ridesharingRepo->cancelRide($data['id_ridesharing']);

        // Envoie du mail d'annulation de covoiturage.

        // Modification du solde de crédit de l'utilisateur.

        // Modifcation du solde de crédit des participants.

        $this->response->redirect("page/myRidesharing");
    }

}