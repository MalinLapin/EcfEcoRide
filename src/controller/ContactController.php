<?php

namespace App\controller;

use App\utils\Logger;
use App\security\TokenManager;
use DateTimeImmutable;
use App\service\MailService;
use Exception;

class ContactController extends BaseController
{
    private Logger $logger;
    private MailService $mailService;


    public function __construct()
    {
        $this->logger = new Logger();
        $this->mailService = new MailService();

        parent::__construct();
    }

    public function showContact():void
    {
        $flashMessage = $this->getFlashMessage();

        $this->render('contact',[
            'pageCss'=> 'contact',
            'flash'=>$flashMessage,
            'csrf_token'=>$this->tokenManager->generateCsrfToken(),
        ]);
    }

    public function handleSubmitEmail():void
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

        $errors = [];

        
        if (empty($data['emailSender'])) {
            $errors['emailSender'] = 'L\'email est requis.';
        } else {
            if ($this->validator->validateEmail($data['emailSender'])){
                $emailSender = $data['emailSender'];
            };
            if(!$emailSender){
                $errors['emailSender'] = 'Veuillez verifier votre adresse mail.';
            }
        }
        
        if (empty($data['subject'])) {
            $errors['subject'] = 'Le sujet est requis.';
        } elseif (strlen($data['subject']) < 5) {
            $errors['subject'] = 'Le sujet doit contenir au moins 5 caractères.';
        } elseif (strlen($data['subject']) > 200) {
            $errors['subject'] = 'Le sujet ne peut dépasser 200 caractères.';
        }
        
        // On néttoie les données.
        $subject = htmlspecialchars(trim($data['subject']));

        if (empty($data['content'])) {
            $errors['content'] = 'Le message est requis.';
        } elseif (strlen($data['content']) < 10) {
            $errors['content'] = 'Le message doit contenir au moins 10 caractères.';
        } elseif (strlen($data['content']) > 2000) {
            $errors['content'] = 'Le message ne peut dépasser 2000 caractères.';
        }

        $content = htmlspecialchars(trim($data['content']));

        if (!empty($errors)) {
            $this->setFlashMessage('error', 'Veuillez corriger les erreurs du formulaire.');
            $this->render('contact', [
                'pageCss' => 'contact',
                'errors' => $errors,
                'csrf_token' => $this->tokenManager->generateCsrfToken(),
            ]);
            return;
        }

        try{
            MailService::sendContactEmail($emailSender, $subject, $content);
            $this->setFlashMessage('Votre message a été envoyé avec succès !', 'success');
        }catch(Exception $e){
            error_log("erreur lors de la verification de l'email:". $e->getMessage());
            $this->setFlashMessage('error', 'Une erreur est survenue. Veuillez réessayer plus tard.');
        }

        $this->redirect('contact');
    }
    
}