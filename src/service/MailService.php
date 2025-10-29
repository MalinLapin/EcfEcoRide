<?php

namespace App\service;

use App\config\Config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../../vendor/autoload.php';

class MailService
{

    private static function createMailer() : PHPMailer
    {
        // Instanciation de PHPMailer
        $mailer = new PHPMailer(true);

        // On va chercher les variables d'environnement pour la configuration SMTP dans le fichier .env
        Config::load(__DIR__ . '/../../');        

        // Hôte SMTP (ex: smtp.gmail.com)
        $smtpHost = Config::get('MAIL_HOST');
        
        // Port SMTP (par défaut 587 pour TLS)
        $smtpPort = Config::get('MAIL_PORT');
        
        // Identifiants SMTP
        $smtpUser = Config::get('MAIL_USERNAME');
        
        // Mot de passe SMTP
        $smtpPass = Config::get('MAIL_PASSWORD');
        
        // Adresse d'expédition par défaut (si non spécifiée dans l'email)
        $fromEmail = Config::get('MAIL_FROM', $smtpUser);
        
        // Nom de l'expéditeur par défaut
        $fromName = Config::get('MAIL_FROM_NAME', 'EcoRide');
        

        // Configuration SMTP
        $mailer->isSMTP();
        $mailer->Host = $smtpHost;
        $mailer->SMTPAuth = true;
        $mailer->Username = $smtpUser;
        $mailer->Password = $smtpPass;
        $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mailer->Port = $smtpPort;
        $mailer->CharSet = 'UTF-8';
        $mailer->setFrom($fromEmail, $fromName);


        // Si la connexion au serveur SMTP est ok, on retourne l'objet mail
        if($mailer->smtpConnect())
        {
            return $mailer;
        }
        // Sinon on lance une exception
        else
        {
            throw new Exception("Erreur de connexion au serveur SMTP");
        }

    }
    

    public static function sendRideCompletionEmail($ridesharing, $participant) : void
    {

        $mail = self::createMailer();
        
        $templateEmail = file_get_contents(__DIR__ . '/../../templates/emails/rideCompletion.txt');

        $placeholder= ['{{participant_pseudo}}',
                        '{{departurecity}}',
                        '{{departureAddress}}',
                        '{{arrivalCity}}', 
                        '{{arrivalAddress}}',
                        '{{departureDate}}'];

        $value = [$participant->getPseudo(),
                    $ridesharing->getDepartureCity(), 
                    $ridesharing->getDepartureAddress(), 
                    $ridesharing->getArrivalCity(), 
                    $ridesharing->getArrivalAddress(), 
                    $ridesharing->getDepartureDate()->format('d/m/Y H:i') ];
        
        
        try {
            // Configuration de l'email
            $mail->addAddress($participant->getEmail(), $participant->getPseudo());
            $mail->Subject = "Fin de participation covoiturage Ecoride.";
            
            $mail->Body = str_replace($placeholder, $value, $templateEmail);
            $mail->send();
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
        }
    }

    public static function sendRideCancelledEmail($ridesharing, $participant) : void
    {
        $mail = self::createMailer();
        
        $templateEmail = file_get_contents(__DIR__ . '/../../templates/emails/rideCancelled.txt');

        $placeholder= ['{{participant_pseudo}}',
                        '{{departurecity}}',
                        '{{departureAddress}}',
                        '{{arrivalCity}}', 
                        '{{arrivalAddress}}',
                        '{{departureDate}}'];

        $value = [$participant->getPseudo(),
                    $ridesharing->getDepartureCity(), 
                    $ridesharing->getDepartureAddress(), 
                    $ridesharing->getArrivalCity(), 
                    $ridesharing->getArrivalAddress(), 
                    $ridesharing->getDepartureDate()->format('d/m/Y H:i') ];

        try {
            // Configuration de l'email
            $mail->addAddress($participant->getEmail(), $participant->getPseudo());
            $mail->Subject = "Annulation covoiturage Ecoride.";
            
            $mail->Body = str_replace($placeholder, $value, $templateEmail);
            $mail->send();
        } catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
        }
    }

    public static function sendContactEmail(string $sendEmail, string $subject, string $content):void
    {
        $mail= self::createMailer();

        try{
            $mail->addAddress('marc.uny39@gmail.com');
            $mail->Subject = $subject;
            $mail->Body = $content;
            $mail->addReplyTo($sendEmail);

            $mail->send();
        }catch (Exception $e) {
            error_log("Erreur lors de l'envoi de l'email : " . $e->getMessage());
        }

    }
}