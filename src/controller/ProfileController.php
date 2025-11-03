<?php

namespace App\controller;

use App\repository\UserRepo;
use App\repository\CarRepo;

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

        $flashMessage = $this->getFlashMessage();

        $this->render('profile',[
            'pageCss'=> 'profile',
            'flash'=>$flashMessage,
            'user'=>$user,
            'listCar'=>$listCar
        ]);
    }
    
}