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
        $flashMessage = $this->getFlashMessage();

        $this->render('home', [
            'pageCss'=>'home',
            'flash'=>$flashMessage
        ]);
    }

    public function mentionLegal():void
    {
        $this->render('mentionLegal',[
            'pageCss'=>'mentionLegal'
        ]);
    }

    public function profil():void
    {
        $flashMessage = $this->getFlashMessage();

        $this->render('profil',[
            'pageCss'=> 'profil',
            'flash'=>$flashMessage
        ]);
    }

    public function contact():void
    {
        $flashMessage = $this->getFlashMessage();

        $this->render('contact',[
            'pageCss'=> 'contact',
            'flash'=>$flashMessage
        ]);
    }

    public function myRidesharing():void
    {
        $flashMessage = $this->getFlashMessage();

        $this->render('myRidesharing',[
            'pageCss'=> 'myRidesharing',
            'flash'=>$flashMessage
        ]);
    }
}