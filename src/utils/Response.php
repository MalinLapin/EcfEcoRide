<?php

namespace App\utils;

/**
 * Classe Response : fournit des méthodes standard pour envoyer des réponses HTTP.
 * Centraliser ces actions (redirection, erreur) rend le code des controleurs plus propre et cohérent.
 */

class Response
{
    /**
     * Redirige vers une URL spécifique.
     *
     * @param string $url L'URL vers laquelle rediriger.
     */
    public static function redirect(string $url): void
    {
        // header() envoie un en-tête HTTP brut. 'Location' indique au navigateur de rediriger vers une nouvelle URL.
        // Il est important de ne pas avoir d'espace ou de sortie avant cet appel, sinon
        // l'en-tête ne sera pas envoyé correctement.
        header("Location: $url");
        exit; // Toujours appeler exit après une redirection pour arrêter l'exécution du script.
    }

    /**
     * Affiche une page d'erreur standard et arrête le script.
     *
     * @param string $message Le message d'erreur à afficher.
     * @param int $code Le code de statut HTTP (ex: 404 pour non trouvé, 500 pour erreur serveur).
     */
    public function error(string $message, int $code): void
    {
        // `http_response_code()` définit le code de statut de la réponse HTTP.
        http_response_code($code);
        // `die()` affiche le message et termine immédiatement l'exécution du script.
        die("Erreur {$code}: " . htmlspecialchars($message));
    }
}