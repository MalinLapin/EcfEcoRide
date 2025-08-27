<?php

namespace App\controller;

use App\utils\Logger;
use App\model\UserModel;
use App\repository\UserRepo;
use App\security\TokenManager;
use App\security\Validator;

class AuthController extends BaseController
{
    /**
     * Constructeur de la classe AuthController
     * Il initialise les modèles nécessaires pour l'authentification.
     */
    public function __construct(
        private TokenManager $tokenManager,
        private Logger $logger,
        private UserRepo $userRepo,
        private Validator $validator)
    {
        // Appel du constructeur de la classe parente
        parent::__construct();
    }

    /**
    *Verifie les identifiants de l'utilisateur
    *@return UserModel|null l'objet user si l'authentification réussi sinon null
    */
    public function authenticate (string $email, string $password): ?UserModel
    {
        $user = $this->userRepo->getUserByEmail($email);

        // On vérifie que l'utilisateur existe et que le MDP fourni correspond au MDP hashé stocké
        if ($user instanceof UserModel && password_verify($password, $user->getPassword())){
            return $user;
        }
        return null;
    }

    /**
     * Méthode qui affiche la page avec le formulaire de connexion
     * Elle génère un token CSRF pour sécuriser le formulaire.
     */
    public function showLogin():void
    {
        $this->render('auth/login', [
            'title'=> 'Connexion',
            'csrf_token'=>$this->tokenManager->generateCsrfToken()
        ]);
    }

    /**
     * Méthode qui traite la soumission du formulaire de connexion
     * Elle vérifie les données, authentifie l'utilisateur et redirige en conséquence.
     */
    public function login():void
    {
        // On s'assure que la requête est de type POST.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('/login');
            return;
        }

        $data = $this->getPostData();

        // Validation du token CSRF
        if (!$this->tokenManager->validateCsrfToken($data['csrf_token']??''))
        {
            $this->response->error('Token de sécurité invalide.', 403);
            return;
        }

        // Limitation du nombre de tentatives de connexion pour prévenir les attaques par force brute
        if (!$this->checkRateLimit()) 
        {
            $this->response->error('Trop de tentatives, réessayez dans 15 minutes.', 429);
            return;
        }

        $email = Validator::validateEmail($data['email']);

        // Validation des données de connexion 
        $user = $this->authenticate($email, $data['password']);

        if($user)
        {
            // Réinitialisation du compteur de tentatives de connexion réussie
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_attempts_time']);

            // Régénération de l'ID de session pour prévenir les attaques de fixation de session
            session_regenerate_id(true);

            // Si l'authentification réussit, on stocke les informations en session
            $_SESSION['id_user']=$user->getIdUser();
            $_SESSION['role']=$user->getRole();
            $_SESSION['pseudo']=$user->getPseudo();
            $_SESSION['photo']=$user->getPhoto();


            // Redirection vers la page d'acceuil
            $this->response->redirect('home/index',[
                'title'=>'Accueil - Ecoride',
                'pseudo'=>$_SESSION['pseudo'],
                'role'=>$_SESSION['role'],
                'photo'=>$_SESSION['photo'],
                'id_user'=>$_SESSION['id_user']
            ]);
            return;
        }else{
            // Si l'authentification échoue, on ré-affiche le formulaire avec un message d'erreur
            $this->render('auth/login', [
                'title'=>'Connexion',
                'error'=>'Email ou mot de passe incorrect.',
                'old'=>['email'=>$data['email']],
                'csrf_token'=>$this->tokenManager->generateCsrfToken()
            ]);
            return;
        }
    }

    //Méthode qui affiche la page avec le formulaire d'inscription    
    public function showRegister():void
    {
        $this->render('auth/register', [
            'title'=> 'Inscription',
            'csrf_token'=>$this->tokenManager->generateCsrfToken()
        ]);
    }

    /**
     * Méthode qui traite la soumission du formulaire d'inscription
     * Elle valide les données, crée l'utilisateur et redirige en conséquence.
     */
    public function register():void
    {
        // On s'assure que la requête est de type POST. Sinon on redirige vers la page d'inscription.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('/register');
            return;
        }

        $data = $this->getPostData();

        // Validation du token CSRF
        if (!$this->tokenManager->validateCsrfToken($data['csrf_token']??''))
        {
            $this->response->error('Token de sécurité invalide.', 403);
            return;
        }

        // Validation des données d'inscription
        
        $errors = [];

        if (empty($data['pseudo']) || strlen($data['pseudo']) < 3 || strlen($data['pseudo']) > 50) 
        {
            $errors['pseudo'] = 'Le pseudo doit faire entre 3 et 50 caractères.';
        }

        if (empty($data['password']) || !Validator::validatePasswordStrength($data['password'])) 
        {
            $errors['password'] = 'Le mot de passe doit contenir au moins 12 caractères avec majuscules, minuscules, chiffres et caractères spéciaux.';
        }
        
        if (($data['password'] ?? '') !== ($data['confirm_password'] ?? '')) {
            $errors['confirm_password'] = 'Les mots de passe ne correspondent pas.';
        }

        if (!empty($errors)) 
        {
            $this->render('auth/register', [
                'title' => 'Inscription',
                'errors' => $errors,
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        $email = Validator::validateEmail($data['email']);

        // Vérification si l'email est déjà utilisé
        if ($this->userRepo->getUserByEmail($email)) {
            // Si l'email existe déjà, on affiche une erreur
            $this->render('auth/register', [
                'title' => 'Inscription',
                'error' => 'L\'email est déjà utilisé.',
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }

        
        //Si tout est correct, on crée un nouvel utilisateur
        try{
            
            $newUser = new UserModel();

            /**
             * Hydratation de l'objet User avec les données du formulaire
             * On utilise les setters pour définir les propriétés de l'utilisateur
             * Inclut la validation et le hachage du mot de passe
             */           
            $newUser->setPseudo($data['pseudo'])
                    ->setEmail($email)
                    ->setPassword(($data['password']));
            
            
            if($this->userRepo->create($newUser))
            {
                session_regenerate_id(true);
                // Si l'utilisateur est créé avec succès, on enregistre les informations en session
                $_SESSION['id_user'] = $newUser->getIdUser();
                $_SESSION['role'] = $newUser->getRole();
                $_SESSION['pseudo'] = $newUser->getPseudo();
                
                // On redirige vers la page des trajets
                $this->response->redirect('/ridesharing');
                return;
            }
        }catch (\Exception $e) {
            // En cas d'erreur lors de la création de l'utilisateur, on affiche un message d'erreur
            $this->logger->log('ERROR','Erreur lors de l\'inscription : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->render('auth/register', [
                'title' => 'Inscription',
                'error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer plus tard.',
                'old' => $data,
                'csrf_token' => $this->tokenManager->generateCsrfToken()
            ]);
            return;
        }
    }

    /**
     * Méthode pour déconnecter l'utilisateur
     * Elle détruit la session et redirige vers la page de connexion
     */
    public function logout():void
    {
        // On s'assure que la requête est de type POST. Sinon on redirige vers la page d'accueil.
        if ($_SERVER['REQUEST_METHOD'] != 'POST')
        {
            $this->response->redirect('/');
            return;
        }

        $data = $this->getPostData();
        if (!$this->tokenManager->validateCsrfToken($data['csrf_token'] ?? '')) {
            $this->response->error('Token de sécurité invalide.', 403);
            return;
        }

        // On détruit la session pour déconnecter l'utilisateur
        session_destroy();
        session_start(); // nouvelle session vide
        session_regenerate_id(true);
        // On redirige vers la page de connexion
        $this->response->redirect('/login');        
    }

    // Méthode pour vérifier la limitation du nombre de tentatives de connexion
    private function checkRateLimit(): bool
    {
        $key = 'login_attempts_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $attempts = $_SESSION[$key] ?? 0;
        $lastAttempt = $_SESSION[$key . '_time'] ?? 0;
        
        // Reset après 15 minutes
        if (time() - $lastAttempt > 900) {
            $_SESSION[$key] = 0;
            $attempts = 0;
        }
        
        if ($attempts >= 5) {
            return false;
        }
        
        $_SESSION[$key] = $attempts + 1;
        $_SESSION[$key . '_time'] = time();
        return true;
    }
}