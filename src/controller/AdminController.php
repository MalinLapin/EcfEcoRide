<?php

namespace App\controller;

use App\model\Role;
use DateTimeImmutable;
use App\model\UserModel; 

class AdminController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function showAdminSpace():void
    {
        $participates = $this->participateRepo->findAll();
        $totalCredit = 0;
        foreach($participates as $participate){
            $totalCredit += $participate->getNbSeats()*2;
        }

        $users = $this->userRepo->findUserByRole(Role::user);

        $employees = $this->userRepo->findUserByRole(Role::employe);

        $countUsers = count($users);
        $countEmployees = count($employees);


        $this->render('adminSpace', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'adminSpace',
            'scriptJs'=>'adminSpace',
            'totalCredit'=>$totalCredit,
            'users'=>$users,
            'countUsers'=>$countUsers,
            'employees'=> $employees,
            'countEmployees'=>$countEmployees,
        ]);
    }

    public function getCreditInfoPerDay():int
    {
        $itsOk = $this->verifAllBeforFunction();

        if($itsOk == false){
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'MON RACOURCI NE FONCTIONNE PAS']);
            exit;
        }

        // Récupération des données du formulaire
        $data = json_decode(file_get_contents('php://input'), true);

        $data = $this->validator->sanitize($data);

        try{
            $nbSeatReserved = $this->participateRepo->findParticipationByDay($data['day']);
        }catch(\Exception $e){
            $this->logger->log('ERROR', 'Erreur lors de la recherche de participation : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue, veuillez réessayer.'
            ]);
            exit;
        }

        // Pour avoir le nombre de crédit il faut multiplier le nombre de siège par 2 car la plateforme prend 2 crédit par siège reserver.
        return $nbSeatReserved * 2;
    }

    public function getParticipationInfoPerWeek():void
    {
        $itsOk = $this->verifAllBeforFunction();

        if($itsOk == false){
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'MON RACOURCI NE FONCTIONNE PAS']);
            exit;
        }

        // Récupération des données du formulaire
        $data = json_decode(file_get_contents('php://input'), true);

        $data = $this->validator->sanitize($data);

        // On convertie notre date en objet DateTime pour notre requête SQL
        $day = new \DateTimeImmutable($data['date']);


        try{
            $nbSeatReservedByDay = $this->participateRepo->findParticipationByWeek($day);

            $nbSeatReservedByDayFormatChart = $this->formatChartData($nbSeatReservedByDay);

        }catch(\Exception $e){
            $this->logger->log('ERROR', 'Erreur lors de la recherche de participation : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue, veuillez réessayer.'
            ]);
            exit;
        }

        echo json_encode([
            'success' => true,
            'labels' => $nbSeatReservedByDayFormatChart['labels'],
            'values' => $nbSeatReservedByDayFormatChart['values']           
        ]);
        exit;
    }

    public function getCreditInfoPerWeek():void
    {
        $itsOk = $this->verifAllBeforFunction();

        if($itsOk == false){
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'MON RACOURCI NE FONCTIONNE PAS']);
            exit;
        }

        // Récupération des données du formulaire
        $data = json_decode(file_get_contents('php://input'), true);

        $data = $this->validator->sanitize($data);

        // On convertie notre date en objet DateTime pour notre requête SQL
        $day = new \DateTimeImmutable($data['date']);


        try{
            $nbSeatReservedByDay = $this->participateRepo->findParticipationByWeek($day);

            $nbSeatReservedByDayFormatChart = $this->formatChartData($nbSeatReservedByDay);            

        }catch(\Exception $e){
            $this->logger->log('ERROR', 'Erreur lors de la recherche de participation : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Une erreur est survenue, veuillez réessayer.'
            ]);
            exit;
        }

        // On recherche la valeur "values" dans notre tableau et nous retournons sont double pour avoir les crédits générés par siège.
        // La plateforme prend 2 crédits par siège réserver.
        $nbSeatReservedByDayFormatChart['values'] = array_map(function($v) { return $v * 2; },$nbSeatReservedByDayFormatChart['values']);
        

        echo json_encode([
            'success' => true,
            'labels' => $nbSeatReservedByDayFormatChart['labels'],
            'values' => $nbSeatReservedByDayFormatChart['values']           
        ]);
        exit; 
    }

    private function formatChartData(array $data): array
    {
        $daysOrder = ['lundi','mardi','mercredi','jeudi','vendredi','samedi','dimanche'];
        $labels = [];
        $values = [];
        
        // Crée un tableau associatif pour accès rapide
        $dataByDay = [];
        foreach ($data as $row) {
            $dataByDay[strtolower($row['day'])] = intval($row['total_seats']);
        }
        
        // Construit les tableaux dans le bon ordre
        foreach ($daysOrder as $day) {
            $labels[] = ucfirst(substr($day, 0, 3));  // "Lun", "Mar", etc.
            $values[] = $dataByDay[$day] ?? 0;  // 0 si pas de données
        }
        
        return ['labels' => $labels, 'values' => $values];
    }

    public function createEmployee():void
    {

        $itsOk = $this->verifAllBeforFunction();

        if($itsOk == false){
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'MON RACOURCI NE FONCTIONNE PAS']);
            exit;
        }

        // Récupération des données du formulaire
        $data = json_decode(file_get_contents('php://input'), true);

        $data = $this->validator->sanitize($data);

        // Validation des données d'inscription

        if (empty($data['name']) || strlen($data['name']) < 3 || strlen($data['name']) > 50) 
        {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Le pseudo doit faire entre 3 et 50 caractères.']);
            exit;
        }

        if (empty($data['password']) || !$this->validator->validatePasswordStrength($data['password'])) 
        {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Min. 12 caractères avec majuscules, minuscules, chiffres et caractères spéciaux.']);
            exit;
        }

        if(!$this->validator->validateEmail($data['email']))
        {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'email Invalide.']);
            exit;
        }

        $email=$data['email'];        

        // Vérification si l'email est déjà utilisé
        if ($this->userRepo->getUserByEmail($email)) {
            // Si l'email existe déjà, on affiche une erreur
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'email déjà utilisé.']);
            exit;
        }

        try{
            
            $newUser = new UserModel();
            
            
            $newUser->setPseudo($data['name'])
                    ->setEmail($email)
                    ->setPassword(($data['password']))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setCreditBalance(0) // un employer n'as pas besoin de crédit
                    ->setRole(Role::employe);
        

            $this->userRepo->create($newUser);
            
        }catch (\Exception $e) {
            // En cas d'erreur lors de la création de l'utilisateur, on affiche un message d'erreur
            $this->logger->log('ERROR','Erreur lors de l\'inscription : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la création du compte.']);
            exit;
        }

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Le nouvel employé a été ajouté.']);
        exit;
    }

    private function verifAllBeforFunction():bool
    {
        try{        
            
            // On vérifie que l'utilisateur est bien connecter.
            $this->requireAuth();

            // On verifie aussi que l'utilisateur soit bien l'admin.
            if($_SESSION['role'] != 'admin'){
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Vous devez etre admin pour procéder a cette action.']);
                exit; 
            }

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
        }catch (\Exception $e) {
            // En cas d'erreur lors de la création de l'utilisateur, on affiche un message d'erreur
            $this->logger->log('ERROR','Erreur lors de la vérification : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Une erreur survenue surveillé']);
            exit;
        }
        return true;
    }

    public function suspendUser():void
    {
        $itsOk = $this->verifAllBeforFunction();

        if($itsOk == false){
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => "erreur lors de l'envoie de la requête."]);
            exit;
        }

        // Récupération des données du formulaire
        $data = json_decode(file_get_contents('php://input'), true);

        $data = $this->validator->sanitize($data);

        // on convertie la string reçu par le Json en int.
        $isInt = intval($data['id']);

        // Si l'id est 0 ce n'est pas un id valide.
        if ($isInt === 0){
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => "l'identifiant envoyer n'est pas un nombre entier."]);
            exit;
        }

        // on récupère l'user ou l'employé
        $user = $this->userRepo->findById($data['id']);
        
        // on met a jour son status en fonction de ce que nous envoie le front.
        if ($data['suspend'] === true)
        {
            $user->setIsActive(false);
        }elseif ($data['suspend'] === false){
            $user->setIsActive(true);
        }else{
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => "Le nouvel état du compte n'est pas correct."]);
            exit;
        }

        try{
            $this->userRepo->update($user);
        }catch(\Exception $e) {
            // En cas d'erreur lors de la création de l'utilisateur, on affiche un message d'erreur
            $this->logger->log('ERROR','Erreur lors de la mise a jour du status : ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification du statusActif.']);
            exit;
        }

        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Le nouvel employé a été ajouté.']);
        exit;
    }
}