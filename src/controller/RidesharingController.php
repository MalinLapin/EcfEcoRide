<?php

namespace App\controller;


use DateTimeImmutable;
use App\model\PreferenceModel;
use App\model\RidesharingModel;
use MongoDB\Operation\Update;

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

        // On parcour le tableau $data pour supprimer les données vides.
        foreach($data as $key =>$value){
            if ($value === ''){
                unset($data[$key]);
            }
            $data[$key] = strtolower($value);
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
        $listReview = $this->reviewRepo->findByTarget($idDriver);

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
            'pageCss' => 'ridesharingDetail',
            'scriptJs' => 'ridesharingDetail'
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

        // On recupere la liste des voitures de l'utilisateur.
        $listCar = $this->carRepo->findListCarByUserId($_SESSION['idUser']);

        $flashMessage = $this->getFlashMessage();
        
        $this->render('createRidesharing', [
            'csrf_token' => $this->tokenManager->generateCsrfToken(),
            'pageCss' => 'createRidesharing',
            'scriptJs'=> 'createRidesharing',
            'flashMessage' => $flashMessage,
            'listCar' => $listCar
        ]);       
    }

    /**
     * Gérer la soumission du formulaire de création de covoiturage
     * 
     * @return void
     */
    public function createRidesharing(): void
    {
        error_log("=== createRidesharing START ===");
        error_log("POST data: " . json_encode($_POST));
        error_log("SESSION user: " . ($_SESSION['idUser'] ?? 'NOT SET'));
        
        $this->requireAuth();
        
        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
            {
            $this->redirect('createRidesharing');
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
        
        if (empty($data['arrivalCity'])) {
            $errors['pricePerSeat'] = "Veuillez indiquer un prix par participant";
        }
        
        if (empty($data['availableSeats']) || !is_numeric($data['availableSeats']) || $data['availableSeats'] < 1 || $data['availableSeats'] > 6) {
            $errors['nbSeats'] = "Le nombre de places doit être un nombre entre 1 et 6.";
        }
        
        if (empty($data['idCar'])) {
            $errors['idCar'] = "Veuillez selectionner un véhicule.";
        }
        
        if (!empty($data['arrivalDate']) < $data['departureDate']){
            $errors ['arrivalDate'] = "La date d'arrivée ne peut être antérieur à la date de départ.";
        }
        
        if (!empty($errors)) 
            {
            $this->render('createRidesharing', [
                'pageCss' => 'createRidesharing',
                'errors' => $errors,
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }
        
        if (!empty($data['preferenceChoice']) && is_array($data['preferenceChoice'])) {
            // Filtre les valeurs vides
            $preferenceList = array_filter(
                $data['preferenceChoice'], 
                fn($p) => !empty(trim($p))
            );
            
            error_log("✅ Preferences submitted: " . count($preferenceList));
            error_log("Preferences raw: " . json_encode($preferenceList));
            
            unset($data['preferenceChoice']);
        } else {
            error_log("ℹ️ No preferences submitted (field empty or missing)");
        }
        
        // On retire le token CSRF maintenant qu'il a été vérifié
        unset($data['csrf_token']);
        $data['idDriver'] = $_SESSION['idUser'];
        
        // Création de l'objet Ridesharing
        $ridesharing = RidesharingModel::createAndHydrate($data);
        $ridesharing->setIdDriver($_SESSION['idUser'])
        ->setCreatedAt(new DateTimeImmutable());
        
        // ========== CRÉATION DU TRAJET ==========
        error_log("=== Creating ridesharing in MySQL ===");
        $newIdRide = $this->ridesharingRepo->create($ridesharing);
        error_log("Ridesharing created with id: " . var_export($newIdRide, true));
        
        if (!$newIdRide) {
            error_log("❌ ERROR: Ridesharing creation failed");
            $this->response->error('Une erreur est survenue lors de la création du covoiturage.', 500);
            return;
        }
        
        // ========== CRÉATION DES PRÉFÉRENCES (SI PRÉSENTES) ==========
        if (!empty($preferenceList)) {
            error_log("=== Starting preferences insertion ===");
            error_log("Preferences to insert: " . count($preferenceList));
            
            $failedPreferences = [];
            
            foreach ($preferenceList as $index => $pref) {
                error_log("--- Processing preference #$index: '$pref' ---");
                
                try {
                    $preferenceData = [
                        'label' => trim($pref),
                        'idRidesharing' => $newIdRide
                    ];
                    
                    error_log("Creating PreferenceModel with data: " . json_encode($preferenceData));
                    $preferenceModel = PreferenceModel::createAndHydrate($preferenceData);
                    
                    error_log("Calling preferenceRepo->create()");
                    $isCreated = $this->preferenceRepo->create($preferenceModel);
                    
                    if (!$isCreated) {
                        error_log("❌ FAILED to insert preference #$index");
                        $failedPreferences[] = $pref;
                    } else {
                        error_log("✅ SUCCESS: Preference #$index inserted");
                    }
                    
                } catch (\Exception $e) {
                    error_log("🔥 EXCEPTION while saving preference #$index");
                    error_log("Message: " . $e->getMessage());
                    error_log("File: " . $e->getFile() . ":" . $e->getLine());
                    error_log("Trace: " . $e->getTraceAsString());
                    
                    $failedPreferences[] = $pref;
                }
            }
            
            // Résumé final
            $successCount = count($preferenceList) - count($failedPreferences);
            error_log("=== Preferences insertion complete ===");
            error_log("Success: $successCount / " . count($preferenceList));
            
            if (!empty($failedPreferences)) {
                error_log("⚠️ Failed preferences: " . json_encode($failedPreferences));
                
                $this->ridesharingRepo->delete($newIdRide);
                $this->response->error('Erreur lors de l\'enregistrement des préférences.', 500);
                return;
            }
        } else {
            error_log("ℹ️ No preferences to insert (user didn't fill any)");
        }
        
        // ========== REDIRECTION ==========
        error_log("=== createRidesharing END - Redirecting to /myRidesharing ===");
        header('Location: /myRidesharing');
        exit;
    }

    /**
     * Permet au conducteur de démarrer un covoiturage.
     * 
     * @return void
     */
    public function startRidesharing(int $idRidesharing): void 
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

        $ridesharing = $this->ridesharingRepo->findById($idRidesharing);
        $driver = $this->userRepo->findById($ridesharing->getIdDriver());
        
        // On vérifie que le covoiturage existe et que l'utilisateur connecté est bien le conducteur.
        if (!$ridesharing || $driver->getIdUser() !== $_SESSION['idUser'])
        {
            $this->response->error('Covoiturage non trouvé ou accès refusé.', 403);
            return;
        }
        
        $this->ridesharingRepo->startRide($idRidesharing);
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Votre trajet a commencé.'
        ]);
        exit;
    }
    
    /**
     * Permet au conducteur de terminer un covoiturage.
     * 
     * @return void
     */
    public function completeRide(int $idRide): void 
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

        $ridesharing = $this->ridesharingRepo->findById($idRide);
        $driver = $this->userRepo->findById($ridesharing->getIdDriver());
        
        // On vérifie que le covoiturage existe et que l'utilisateur connecté est bien le conducteur.
        if (!$ridesharing || $driver->getIdUser() !== $_SESSION['idUser'])
        {
            $this->response->error('Covoiturage non trouvé ou accès refusé.', 403);
            return;
        }        
        
        // Vérification de la fin de participation.
        $listParticipants = $this->participateRepo->findParticipantsByRide($idRide);

        foreach ($listParticipants as $participant){

            // On recherche sa participation.
            $participation = $this->participateRepo->findParticipationByUser($participant->getIdUser());

            // Envoie du mail de fin de covoiturage.
            try{
                $this->mailService->sendRideCompletionEmail($ridesharing, $participant);
            }catch (\Exception $e){
                $this->logger->log('ERROR', "Erreur lors de l'envoie du mail a l'utilisateurs: ".$participant->getPseudo() . " suite à l'annulation d'un covoiturage : " . $e->getMessage());
            }

            $amountCredit = ($participation->getNbSeats() * ($ridesharing->getPricePerSeat()-2));
            $driver->setCreditBalance($driver->getCreditBalance() + $amountCredit);
            
            // Modification du solde de crédit des participants.
            try{

                $this->userRepo->update($participant);

            }catch (\Exception $e){
                $this->logger->log('ERROR', "Erreur lors de la mise à jour du solde de crédit de l'utilisateur: ".$participant->getPseudo()." suite à l'annulation d'un covoiturage : " . $e->getMessage());
            }
            
            $participation->setCompletedAt(new DateTimeImmutable());

            $this->participateRepo->update($participation);
        }

        try{
            $this->ridesharingRepo->endRide($idRide);
        }catch(\Exception $e){
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'le changement de status du trajet n\'est pas effectuer.'
            ]);
        exit;
        }
        

        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Votre trajet est terminé.'
        ]);
        exit;
    }

    /**
     * Permet au conducteur d'annuler un covoiturage.
     * 
     * @return void
     */
    public function cancelRidesharing(int $idRidesharing): void
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
            
            // Modification du solde de crédit des participants.
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