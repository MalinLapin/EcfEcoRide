<?php

namespace App\controller;

use App\utils\Logger;
use App\utils\Response;
use App\security\Validator;
use App\repository\UserRepo;
use App\service\MailService;
use App\repository\ReviewRepo;
use App\security\TokenManager;
use App\repository\PreferenceRepo;
use App\repository\ParticipateRepo;
use App\repository\RidesharingRepo;
use App\repository\CarRepo;
use App\repository\BrandRepo;

/**
 * Controller de base
 * Toutes les autres classe de controller hériteront de celle ci
 * 
 */
abstract class BaseController
{
    protected Response $response;
    protected Validator $validator;
    protected TokenManager $tokenManager;
    protected Logger $logger;
    protected RidesharingRepo $ridesharingRepo;
    protected PreferenceRepo $preferenceRepo;
    protected UserRepo $userRepo;
    protected ReviewRepo $reviewRepo;
    protected ParticipateRepo $participateRepo;
    protected MailService $mailService;
    protected CarRepo $carRepo;
    protected BrandRepo $brandRepo;

    public function __construct()
    {
        $this->response = new Response();
        $this->validator = new Validator();
        $this->tokenManager = new TokenManager();
        $this->logger = new Logger();
        $this->ridesharingRepo = new RidesharingRepo();
        $this->preferenceRepo = new PreferenceRepo();
        $this->userRepo = new UserRepo();
        $this->reviewRepo = new ReviewRepo();
        $this->participateRepo = new ParticipateRepo();
        $this->mailService = new MailService(); 
        $this->carRepo = new CarRepo(); 
        $this->brandRepo = new BrandRepo(); 
    }

    /**
     * Affiche une vue en l'injectant dans le layout principale
     * @param string $view le nom de fichier de vue
     * @param array $data les données à rendre accessible dans la vue
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
    protected function redirect(string $url, array $flashData = []):void
    {
        if (!empty($flashData))
        {
            $message= $flashData['message'] ?? 'Une erreur est survenue';
            $type = $flashData['type'] ?? 'error';

            $this->setFlashMessage($message, $type); 
        }
        
        header("Location: $url");
        exit;
    }

    protected function setFlashMessage(string $message, string $type = 'error'):void
    {
        $_SESSION['flashMessage'] = [
            'message'=> $message,
            'type'=> $type
        ];
    }

    protected function getFlashMessage():?array
    {
        if(empty($_SESSION['flashMessage']))
        {
            return null;
        }
        $flashMessage = $_SESSION['flashMessage'];
        unset($_SESSION['flashMessage']);

        return $flashMessage;
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
        if(!isset($_SESSION['idUser']))
        {
            $this->render('login', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'message'=>'Il faut être connecter pour avoir accès à cette fonctionnalité',
            'pageCss'=>'login']);

            exit;
        }
    }
}