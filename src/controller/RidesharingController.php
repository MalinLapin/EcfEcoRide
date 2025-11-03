<?php

namespace App\controller;


use App\model\RidesharingModel;
use App\model\PreferenceModel;


class RidesharingController extends BaseController
{

    public function __construct() 
    {        
        parent::__construct();
    }
    
    /**
     * Afficher le formulaire de recherche de covoiturage
     * @return void
     */
    public function showSearchRidesharing(): void
    {
        $departureCity = isset($_GET['departureBar']) && $_GET['departureBar'] !== '' ? htmlspecialchars($_GET['departureBar']) : null;

        $arrivalCity = isset($_GET['arrivalBar']) && $_GET['arrivalBar'] !== '' ? htmlspecialchars($_GET['arrivalBar']) : null;
        
        $dateSearch = isset($_GET['dateSearch']) && $_GET['dateSearch'] !== '' ? htmlspecialchars($_GET['dateSearch']) : null;


        $nbSeats = isset($_GET['seatsBar']) ? (int)$_GET['seatsBar'] : null;

        $this->render('searchRidesharing', [
            'csrf_token' => $this->tokenManager->generateCsrfToken(),
            'pageCss' => 'searchRidesharing',
            'departureCity'=>$departureCity,
            'arrivalCity'=>$arrivalCity,
            'dateSearch'=>$dateSearch,
            'nbSeats'=>$nbSeats
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
            $this->response->redirect('/search');
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

        if (empty($data['departureDate']) || !strtotime($data['departureDate'])) {
            $errors['departureDate'] = "La date de départ est requise et doit être au format valide.";
        } else { // On vérifie que la date de départ est bien dans le futur.
            $departureDateTime = new \DateTime($data['departureDate']);
            $now = new \DateTime();
            $now =$now->format('Y-m-d H:i');
            if ($departureDateTime < $now) {
                $errors['departureDate'] = "La date de départ doit être dans le futur.";
            }
        }

        if (empty($data['arrivalCity'])) {
            $errors['arrivalCity'] = "La ville d'arriver est requise.";
        }

        if (empty($data['nbSeats']) || !is_numeric($data['nbSeats']) || $data['nbSeats'] < 1 || $data['nbSeats'] > 6) {
            $errors['nbSeats'] = "Le nombre de places rechercher doit être un nombre entre 1 et 6.";
        }

        if (!empty($errors)) 
        {
            $this->render('searchRidesharing', [
                'errors' => $errors,
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss' => 'searchRidesharing'
            ]);
            return;
        }

        $listRidesharing = $this->ridesharingRepo->getRidesharingByParams($data);
        // Recherche des covoiturages en fonction des critères fournis
        if($listRidesharing === null)
        {
            $errors['ridesharing'] = "aucun covoiturage trouvés";
            
            $this->render('searchRidesharing', [
                'errors' => $errors,
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss' => 'searchRidesharing'
            ]);
            return;
            
        } else {
            $this->render('listRidesharing', [
                'listRidesharing' => $listRidesharing,
                'pageCss' => 'listRidesharing'
            ]);
            return;
        }
    }
    
    /**
     * Afficher les détails d'un covoiturage spécifique
     * @param int $idRidesharing
     */
    public function showRidesharingDetail(int $idRide): void
    {    
        if ($idRide <= 0) {
            $this->response->error('ID manquant', 400);
            return;
        }
        
        $ridesharingDetails = $this->ridesharingRepo->findByIdWithDetails($idRide);
        // On vérifie que le covoiturage existe
        if (!$ridesharingDetails) {
            $this->response->error('Covoiturage non trouvé.', 404);
            return;
        }

        // On récupère l'id du driver dans l'objet ridesharing présent dans le tableau ridesharingDetails.
        $idDriver = $ridesharingDetails['ridesharing']->getIdDriver();
        
        // Récuperer les avis du conducteur, uniquement les avis approuver par les employers.
        $listReview = $this->reviewRepo->findByTarget($idDriver, true);

        $listReviewForView = [];
        
        foreach($listReview as $review){

            $idRedactor = $review->getIdRedactor();
            $redactor = $this->userRepo->findById($idRedactor);

            $listReviewForView[]=[
                'review'=> $review,
                'pseudoRedactor'=> $redactor -> getPseudo()
            ];
        }

        // Récuperer les préférences du conducteur pour le trajet.
        $listPreference = $this->preferenceRepo->findByRidesharing($idRide);

        $flashMessage = $this->getFlashMessage();
        // Affichage des détails du covoiturage 
        $this->render("ridesharingDetail", [
            'ridesharingDetails' => $ridesharingDetails,
            'listReview' => $listReviewForView,
            'listPreference'=>$listPreference,
            'flash'=>$flashMessage,
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss' => 'ridesharingDetail'
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
        $userId = $_SESSION['idUser'];


        // Récupération des covoiturages où l'utilisateur est passager 
        $participations = $this->participateRepo->findListParticipationByUser($userId);

        // Récupération des covoiturages où l'utilisateur est conducteur
        $offeredRides = $this->ridesharingRepo->findRidesharingByDriver($userId);

        $flashMessage = $this->getFlashMessage();

        $this->render('myRidesharing', [
            'pageCss'=>'myRidesharing',
            'flash'=>$flashMessage,
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'scriptJs'=>'myRidesharing',
            'participations' => $participations,
            'offeredRides' => $offeredRides            
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
    public function cancelRidesharing(int $idRidesharing): void
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


        $ridesharing = $this->ridesharingRepo->findById($idRidesharing);
        $driver = $this->userRepo->findById($ridesharing->getIdDriver());
        
        // On vérifie que le covoiturage existe et que l'utilisateur connecté est bien le conducteur.
        if (!$ridesharing || $driver->getIdUser() !== $_SESSION['idUser'])
        {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => "Vous n'avez pas le droit d'annuler ce trajet."
            ]);
            exit;
        }

        // On récupère la liste des participants prévu.
        $listParticipants = $this->participateRepo->findParticipantsByRide($idRidesharing);

        foreach ($listParticipants as $participant){

            // On recherche sa participation.
            $participation = $this->participateRepo->findParticipationByUser($participant->getIdUser());

            // Envoie du mail d'annulation de covoiturage.
            try{
                $this->mailService->sendRideCancelledEmail($ridesharing, $participant);
            }catch (\Exception $e){
                $this->logger->log('ERROR', "Erreur lors de l'envoie du mail a l'utilisateurs: ".$participant->getPseudo() . " suite à l'annulation d'un covoiturage : " . $e->getMessage());
            }

            $amountCredit = ($participation->getNbSeats() * $ridesharing->getPricePerSeat());
            $participant->setCreditBalance($participant->getCreditBalance() + $amountCredit);
            
            // Modifcation du solde de crédit des participants.
            try{

                $this->userRepo->update($participant);

            }catch (\Exception $e){
                $this->logger->log('ERROR', "Erreur lors de la mise à jour du solde de crédit de l'utilisateur: ".$participant->getPseudo()." suite à l'annulation d'un covoiturage : " . $e->getMessage());
            }
            
            $this->participateRepo->delete($participation->getIdParticipate());   
        }

        // Enfin nous annulons le trajet
        try {
            $this->ridesharingRepo->cancelRide($idRidesharing);

        } catch (\Exception $e) {

            $this->logger->log('ERROR', 'Erreur lors de l\'annulation du trajet : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue, veuillez réessayer.'
            ]);
            exit;
        }
        
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Votre proposition de trajet a été annulée.'
        ]);
        exit;
    }

}