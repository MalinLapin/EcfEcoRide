<?php

namespace App\controller;

use App\security\TokenManager;
use App\security\Validator;
use App\utils\Response;

/**
 * Controler de base
 * Toutes les autres classe de controlleru hériteront de celle ci
 * 
 */
abstract class BaseController
{
    protected Response $response;
    protected Validator $validator;
    protected TokenManager $tokenManager;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new Validator();
        $this->tokenManager = new TokenManager();
    }

    /**
     * Affiche une vue en l'injectant dans le layout principale
     * @param string $view le nom de fichier de vue
     * @param array $data les données à rendres accéssible dans la vue
     */
    protected function render(string $view, array $data = []):void
    {
        // On construit le chemin complet vers le chemin de vue
        $viewPath = dirname(__DIR__).'/view/template/'.$view.'.php';
        // On vérifie que le fichier vue existe bien.
        if(!file_exists($viewPath))
        {
            $this->response->error("Vue non trouvée : $viewPath", 500);
            return;
        }

        // extract() Transforme les clefs d'un tableau en variables
        // Exemple: $data = ['title'=>'Accueil']  devient $title = 'Accueil'
        extract($data);

        // On utilise la mise en tampon de sortie (outpout buffering) pour capturer le HTML de la vue.
        ob_start();
        include $viewPath;

        // Ici on vide le cache, la variable $content contient la vue
        $content = ob_get_clean();

        // Finalement, on inclut le layout principal, qui peut maintenant utiliser la variable $content.
        include dirname(__DIR__).'/view/layout.php';
    }

    /**
     * Permet la redirection d'url
     * @param string $url l'adresse HTTP de destination
     */
    protected function redirect(string $url):void
    {
        header("Location: $url");
        exit;
    }

    /**
     * Récupère et nettoie les données envoyées via une requête POST
     */
    protected function getPostData():array
    {
        return $this->validator->sanitize($_POST);
    }

    /**
     * Vérifie si l'utilisateur est connecter sinon le redirige vers la page login.
     */
    protected function requireAuth():void
    {
        if(!isset($_SESSION['id_user']))
        {
            $this->render('login', [
            'title'=> 'Connexion',
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'login.css'
        ]);
        }
    }
}