<?php

namespace App\controller;

use app\repository\RidesharingRepo;

/**
 * Gère la logique de la page d'acceuil
 */
class HomeController extends BaseController
{

    public function index():void
    {
        $this->render('home', [
            'title'=>'Accueil - Ecoride',
            'pageCss'=>'home'
        ]);
    }
}