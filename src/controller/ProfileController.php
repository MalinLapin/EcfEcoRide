<?php

namespace App\controller;

use App\model\CarModel;
use App\model\EnergyType;
use DateTimeImmutable;

class ProfileController extends BaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function profile():void
    {
        $user = $this->userRepo->findById($_SESSION['idUser']);
        $listCar = $this->carRepo->findListCarByUserId($_SESSION['idUser']);
        $listBrand = $this->brandRepo->findAll();

        $flashMessage = $this->getFlashMessage();

        $this->render('profile',[
            'pageCss'=> 'profile',
            'scriptJs'=> 'profile',
            'flash'=>$flashMessage,
            'user'=>$user,
            'listCar'=>$listCar,
            'listBrand'=>$listBrand,
            'csrf_token' => $this->tokenManager->generateCsrfToken(),

        ]);
    }

    public function addCar():void
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
        
        // Récupération edes données du formulaire
        $data = json_decode(file_get_contents('php://input'), true);

        $data = $this->validator->sanitize($data);

        $car = new CarModel();

        $car->setModel($data['model'])
            ->setRegistrationNumber($data['registrationNumber'])
            ->setFirstRegistration(new DateTimeImmutable($data['firstRegistration']))
            ->setEnergyType(EnergyType::tryFrom($data['energyType']))
            ->setColor($data['color'])
            ->setIdBrand($data['brandId'])
            ->setIdUser($_SESSION['idUser']);

        try{
            $this->carRepo->create($car);
        }catch(\Exception){
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'La création de la voiture n\'a pu avoir lieu.'
            ]);
        exit;
        }

        http_response_code(200);
            echo json_encode([
                'success' => true,
                'message' => 'La création de la voiture a réussie.'
            ]);
        exit;
        
        
    }
    
}