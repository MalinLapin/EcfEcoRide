<?php

namespace App\services;

class UserValidator
{
    /**
     * Méthode de vérification d'email
     * 
     * @param string $email Est l'email à vérifier
     * @return bool Si l'email est dans le bon format return true sinon false     * 
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }


    /**
     * Méthode de vérification de mot de passe
     * 
     * @param string @password Est le mot de passe à tester
     * @return bool Si le mot de passe contient toutes les recommandations de sécurité renvoie true.
     */
    public static function validatePasswordStrength(string $password): bool
    {
        return
            strlen($password) >= 12 && // Depuis 2025 l'ANSSI recommande un mot de passe entre 12 et 16 caractères.
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[\W]/', $password); // caractère spécial
    }
}