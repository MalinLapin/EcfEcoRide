<?php

namespace App\controller;


use App\model\UserModel;
use DateTimeImmutable;

class AuthController extends BaseController
{
    /**
     * Constructeur de la classe AuthController
     * Il initialise les modèles nécessaires pour l'authentification.
     */
    public function __construct()
    {
        // Appel du constructeur de la classe parente
        parent::__construct();
    }

    /**
    *Verifies les identifiants de l'utilisateur
    *@return UserModel|null l'objet user si l'authentification réussi sinon null
    */
    public function authenticate (string $email, string $password): ?UserModel
    {
        $user = $this->userRepo->getUserByEmail($email);

        // On vérifie que l'utilisateur existe et que le MDP fourni correspond au MDP haché stocké
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
        $this->render('login', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'login'
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
            $this->render('login',[
                'pageCss'=>'login',
                'error' => 'Erreur lors de l\'envoie du formulaire'   
            ]);
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
            $this->render('login', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'message'=>'Trop de tentatives échoué veuillez attendre 1 min. avant de réessayer.',
            'pageCss'=>'login']);
            return;
        }

        if(!$this->validator->validateEmail($data['email'])){
            $errors['email']='email Invalide';
        }

        // Validation des données de connexion 
        $user = $this->authenticate($data['email'], $data['password']);

        if($user->getIsActive() === false){
            $this->render('login', [
                'message'=>'Votre compte à été suspendu.',
                'csrf_token'=>$this->tokenManager->generateCsrfToken(),
                'pageCss'=>'login'
            ]);
            return;
        }

        if($user)
        {
            // Réinitialisation du compteur de tentatives de connexion réussie
            unset($_SESSION['login_attempts']);
            unset($_SESSION['login_attempts_time']);

            // Régénération de l'ID de session pour prévenir les attaques de fixation de session
            session_regenerate_id(true);

            // Si l'authentification réussit, on stocke les informations en session
            $_SESSION['idUser']=$user->getIdUser();
            $_SESSION['role']=$user->getRole()->value;
            $_SESSION['pseudo']=$user->getPseudo();

            // On vérifie ensuite le Role de la personne qui tente la connexion
            if ($_SESSION['role'] == 'admin'){
                //Si c'est un admin il est rediriger directement sur son espace de travail.
                $this->redirect('/adminSpace');
                return;
            }elseif ($_SESSION['role'] == 'employee'){
                //Si c'est un employer il est rediriger directement sur son espace de travail.
                $this->redirect('/employeeSpace');
                return;
            }else{
                $this->redirect('/');
                return;
            }

            
        }else{
            // Si l'authentification échoue, on ré-affiche le formulaire avec un message d'erreur
            $this->render('login', [
                'message'=>'Email ou mot de passe incorrect.',
                'csrf_token'=>$this->tokenManager->generateCsrfToken(),
                'pageCss'=>'login'
            ]);
            return;
        }
    }

    //Méthode qui affiche la page avec le formulaire d'inscription    
    public function showRegister():void
    {
        $this->render('register', [
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
            'pageCss'=>'register',
            'scriptJs'=>'register'
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
            $this->render('register',[
                'pageCss'=>'register'
            ]);
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

        if (empty($data['password']) || !$this->validator->validatePasswordStrength($data['password'])) 
        {
            $errors['password'] = 'Min. 12 caractères avec majuscules, minuscules, chiffres et caractères spéciaux.';
        }
        
        if (($data['password'] ?? '') !== ($data['confirmPassword'] ?? '')) {
            $errors['confirmPassword'] = 'Les mots de passe ne correspondent pas.';
        }

        if(!$this->validator->validateEmail($data['email'])){
            $errors['email']='email Invalide';
        }

        if (!empty($errors)) 
        {
            $this->render('register', [
                'errors' => $errors,
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss'=>'register'
            ]);
            return;
        }

        $email=$data['email'];        

        // Vérification si l'email est déjà utilisé
        if ($this->userRepo->getUserByEmail($email)) {
            // Si l'email existe déjà, on affiche une erreur
            $this->render('register', [
                'error' => 'L\'email est déjà utilisé.',
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss'=>'register'
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
                    ->setPassword(($data['password']))
                    ->setCreatedAt(new DateTimeImmutable())
                    ->setCreditBalance(20);
        

            $newIdUser = $this->userRepo->create($newUser);
            
            if ($newIdUser) {

                // On récupère notre nouvel user
                $newUser = $this->userRepo->findById($newIdUser);

                session_regenerate_id(true);
                // Si l'utilisateur est créé avec succès, on enregistre les informations en session
                $_SESSION['idUser'] = $newUser->getIdUser();
                $_SESSION['role'] = $newUser->getRole()->value;
                $_SESSION['pseudo'] = $newUser->getPseudo();
                
                // On redirige vers la page des trajets
                $this->redirect('/');
                return;// Succès...

            } else {
                $this->render('register', [
                    'error' => 'Erreur lors de la création du compte.',
                    'csrf_token' => $this->tokenManager->generateCsrfToken(),
                    'pageCss' => 'register'
                ]);
                return;
            }
        }catch (\Exception $e) {
            // En cas d'erreur lors de la création de l'utilisateur, on affiche un message d'erreur
            $this->logger->log('ERROR','Erreur lors de l\'inscription : ' . $e->getMessage());
            // On ré-affiche le formulaire d'inscription avec un message d'erreur
            $this->render('register', [
                'error' => 'Une erreur est survenue lors de l\'inscription. Veuillez réessayer plus tard.',
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
                'pageCss'=>'register'
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
            $this->render('home');
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
        $this->redirect('/');        
    }

    // Méthode pour vérifier la limitation du nombre de tentatives de connexion
    private function checkRateLimit(): bool
    {
        $key = 'login_attempts_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');
        $attempts = $_SESSION[$key] ?? 0;
        $lastAttempt = $_SESSION[$key . '_time'] ?? 0;
        
        // Reset après 1 minutes
        if (time() - $lastAttempt > 60) {
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