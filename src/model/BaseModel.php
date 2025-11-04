<?php

namespace App\model;

use DateTimeImmutable;

/**
* BaseModel est une classe abstraite qui sert de base pour les modèles de données.
* Elle fournit des méthodes pour hydrater les entités à partir de données brutes.
*/
abstract class BaseModel
{
    
    //Champs de type dateTime pour hydratation
    private array $dateFields = ['created_at', 'updated_at', 'departure_date', 'arrival_date', 'first_registration', 'completed_at'];

    //Champs de type enum pour hydratation
    private array $enumFields = ['role', 'status', 'status_review', 'energy_type'];

    //Champs de type int pour hydratation
    private array $intFields =['id_brand', 'id_car', 'id_user', 'id_preference', 'id_review', 'id_redactor', 'id_target', 'id_ridesharing', 'available_seats', 'price_per_seat', 'id_driver', 'credit_balance'];

    private array $floatFields = ['grade'];

    private array $boolFields = ['is_accepted', 'is_active'];


    
    /**
    * Crée une instance de l'entité et hydrate ses propriétés avec les données fournies.
    * @param array $data Les données à utiliser pour hydrater l'entité.
    * @return static Une instance de l'entité hydratée.
    */
    public static function createAndHydrate(array $data):static
    {
        $entity = new static();
        $entity->hydrate($data);
        
        return $entity;
    }
    
    /**
    * Hydrate l'entité avec les données fournies.
    * @param array $data Les données à utiliser pour hydrater l'entité.
    * @return void
    */
    public function hydrate(array $data):void
    {
        foreach ($data as $key => $value)
        {
            // Pour les données reçue lors de l'hydratation par la bdd les $key seront en snake_case sinon le reste en camelCase nous devons donc gérer les deux conventions de nommage.
            $normalizedKey = $this->toSnakeCase($key);
            
            // Problème avec les noms de colonnes comme first_name.
            $methodName = str_replace(array('-','_'), ' ', $key); // first name
            $methodName = ucwords($methodName);// First Name
            $methodName = str_replace(' ','', $methodName); // FirstName
            $methodName = 'set'.$methodName; // setFirstName
            
            //Si la key est password on passe par setHashedPassword pour évité de re-hasher le mot de passe via le Setter de UserModel.
            if ($key === 'password') {
                $this->setHashedPassword($value);
                continue;
            }
            
            // On vérifie si la méthode existe avant de l'appeler
            if(method_exists($this,$methodName))
                {
                    
                // Champs date convertie en intance de DateTimeImmutable
                if(in_array($normalizedKey, $this->dateFields))
                    {
                    // On s'assure que la valeur est une chaîne de caractères avant de la convertir
                    if(is_string($value))
                        {
                            $value = new DateTimeImmutable($value);
                        }
                    }             
                
                // Champs enum convertie en instance d'enum
                if (in_array($normalizedKey, $this->enumFields))
                {
                        // On s'assure que la valeur est une chaîne de caractères avant de la convertir
                    if(is_string($value))
                    {
                        $value = match ($key)
                        {
                            'role' => Role::from($value),
                            'status' => Status::from($value),
                            'status_review' => StatusReview::from($value),
                            'energy_type'=> EnergyType::from($value)
                        };
                    }
                }   

                // Champs int
                if (in_array($normalizedKey, $this->intFields))
                {
                    $value = (int)$value;
                }

                // Champs float
                if (in_array($normalizedKey, $this->floatFields))
                {
                    $value = (float)$value;
                }

                // Champs bool
                if (in_array($normalizedKey, $this->boolFields)) 
                {
                    // Gère les formats "1"/"0", true/false, int, string
                    $tmp = filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    $value = $tmp === null ? (bool)$value : $tmp;
                }
                
                // On renvoie la méthode set correspondante avec sa valeur.                
                $this->{$methodName}($value);
                
            }            
        }
    }

    /**
     * Convertit une clé camelCase en snake_case pour la comparaison avec les tableaux de configuration.
     * 
     * @param string $key Clé en camelCase ou snake_case
     * @return string Clé en snake_case
     */
    private function toSnakeCase(string $key): string
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $key));
    }

    public function __debugInfo(): array
    {
        $reflect = new \ReflectionClass($this);
        $props = $reflect->getProperties(\ReflectionProperty::IS_PRIVATE | \ReflectionProperty::IS_PROTECTED);
        
        $data = [];
        foreach ($props as $prop) {
            // Ignore les métadonnées
            if (in_array($prop->getName(), ['dateFields','enumFields','intFields','floatFields','boolFields'])) {
                continue;
            }
            
            $prop->setAccessible(true);
            
            // Vérifie si la propriété est initialisée (PHP 7.4+)
            if (!$prop->isInitialized($this)) {
                $data[$prop->getName()] = '<non défini>';
                continue;
            }
            
            $data[$prop->getName()] = $prop->getValue($this);
        }
        return $data;
    }
}