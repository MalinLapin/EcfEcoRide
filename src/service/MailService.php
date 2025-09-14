<?php

namespace App\service;

use App\config\Config;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../../vendor/autoload.php';

class MailService
{

    private function createMailer() : PHPMailer
    {
        // Instanciation de PHPMailer
        $mail = new PHPMailer(true);

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
        $mail->isSMTP();
        $mail->Host = $smtpHost;
        $mail->SMTPAuth = true;
        $mail->Username = $smtpUser;
        $mail->Password = $smtpPass;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $smtpPort;
        $mail->CharSet = 'UTF-8';
        $mail->setFrom($fromEmail, $fromName);


        // Si la connexion au serveur SMTP est ok, on retourne l'objet mail
        if($mail->smtpConnect())
        {
            return $mail;
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
            error_log("Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo);
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
            error_log("Erreur lors de l'envoi de l'email : " . $mail->ErrorInfo);
        }
    }
}