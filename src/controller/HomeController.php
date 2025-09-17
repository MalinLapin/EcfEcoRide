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
        $ridesharing = new RidesharingRepo();
        $this->render('index', [
            'title'=>'Accueil - Ecoride',
        ]);
    }
}