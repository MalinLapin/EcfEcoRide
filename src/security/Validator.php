<?php

namespace App\security;

/**
* Fichier qui fournit des méthodes pour valider et nettoyer les données
*/
class Validator
{
    /**
     * @var array $errors Contient les erreurs de validation
     */
    private array $errors =[];
    
    /**
     * @var array $data Contient les données à valider
     */
    private array $data = [];
    
    /**
     * Valide les données selon les règles fournies
     *
     * @param array $data Les données à valider
     * @param array $rules Les règles de validation
     * @return array Les erreurs de validation
     */
    public function validate(array $data, array $rules):array
    {
        // Réinitialiser les erreurs et les données
        $this->errors = [];
        // Assigner les données à valider
        $this->data = $data;        
        
        // Parcourir les règles de validation
        foreach ($rules as $field => $ruleString) 
        {
            
                $value = $data[$field] ?? null; // Valeur du champ, ou null si non défini
                $rulesArray = explode('|', $ruleString); // Séparer les règles par le caractère '|'
                
                // Appliquer chaque règle de validation
                foreach ($rulesArray as $rule) 
                {
                    $this->applyRule($field, $value, $rule);
                }
        }
        return $this->errors;
    }
    
    /**
     * Applique une règle de validation à un champ
     *
     * @param string $field Le nom du champ
     * @param mixed $value La valeur du champ
     * @param string $rule La règle de validation
     */
    private function applyRule(string $field, $value, string $rule): void
    {
        // Initialiser le paramètre à null
        $param = null;
        
        // Vérifier si la règle contient un paramètre
        if(strpos($rule, ':') !== false){
            [$rule, $param] = explode(':', $rule, 2);
        }
        
        // Appliquer la règle de validation
        switch ($rule){
            
            case 'required':
                if (empty($value))
                {
                    $this->addError($field, "Le champ {$field} est requis.");
                }
                break;
            case 'email':
                if (!filter_var($value, FILTER_VALIDATE_EMAIL))
                {
                    $this->addError($field, "Le champ {$field} doit être une adresse email valide.");
                }
                break;
            case 'min':
                if (strlen($value) < (int)$param)
                {
                    $this->addError($field, "Le champ {$field} doit contenir au moins {$param} caractères.");
                }
                break;
            case 'max':
                if (strlen($value) > (int)$param)
                {
                    $this->addError($field, "Le champ {$field} doit contenir au plus {$param} caractères.");
                }
                break;
            case 'same':
                if ($value !== $this->data[$param] ?? null)
                {
                    $this->addError($field, "Le champ {$field} doit être identique au champ {$param}.");
                }
                break;
        }
    }

    /**
     * Ajoute une erreur de validation pour un champ spécifique
     *
     * @param string $field Le nom du champ
     * @param string $message Le message d'erreur
     */
    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Nettoie les espaces inutiles dans les données. Les requêtes SQL sont protégées par PDO->prepare->bindValue->execute.
     *
     * @param array $data Les données à nettoyer
     * @return array Les données nettoyées
     */
    public function sanitize(array $data):array
    {
        $sanitized = [];
    
        foreach ($data as $key => $value) {
            // Si c'est une chaîne : trim uniquement
            if (is_string($value)) {
                $sanitized[$key] = trim($value);
            }
            // Si c'est un tableau : récursif
            elseif (is_array($value)) {
                $sanitized[$key] = $this->sanitize($value);
            }
            // Autres types : conserver tel quel
            else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }

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
        if(strlen($password) >= 12 && // Depuis 2025 l'ANSSI recommande un mot de passe entre 12 et 16 caractères.
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[a-z]/', $password) &&
            preg_match('/[0-9]/', $password) &&
            preg_match('/[\W]/', $password))// caractère spécial
        {
            return true;
        }
        return false; 
    }
    
}