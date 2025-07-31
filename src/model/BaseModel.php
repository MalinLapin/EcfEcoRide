<?php

namespace App\Model;

use DateTimeImmutable;

/**
* BaseModel est une classe abstraite qui sert de base pour les modèles de données.
* Elle fournit des méthodes pour hydrater les entités à partir de données brutes.
*/
abstract class BaseModel
{
    /**
     * Champs de type date à transformer en DateTimeImmutable lors de l'hydratation.
     * Ces champs sont définis pour être automatiquement convertis lors de l'hydratation de l'entité.
     */
    protected array $dateFields = ['created_at', 'updated_at', 'departure_date', 'arrival_date'];

    /**
     * Champs d'énumération à hydrater.
     * Ces champs sont définis pour être automatiquement convertis en instances d'énumération lors de l'hydratation de l'entité.
     */
    protected array $enumFields = ['role', 'status', 'statusReview'];
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
            // Problème avec les noms de colonnes comme first_name.
            $methodName = str_replace(array('-','_'), ' ', $key); // first name
            $methodName = ucwords($methodName);// First Name
            $methodName = str_replace(' ','', $methodName); // FirstName
            $methodName = 'set'.$methodName; // setFirstName
            
            
            // On vérifie si la méthode existe avant de l'appeler
            if(method_exists($this,$methodName))
                {
                    
                // Si la clé est une date, on la convertit en DateTimeImmutable
                // On vérifie si la clé est dans les champs de date définis
                if(in_array($key, $this->dateFields))
                    {
                    // On s'assure que la valeur est une chaîne de caractères avant de la convertir
                    if(is_string($value))
                        {
                        $value = new DateTimeImmutable($value);
                    }
                }             
                
                // Si la clé est un enum, on le convertit en instance de l'enum
                if (in_array($key, $this->enumFields)){
                    // On s'assure que la valeur est une chaîne de caractères avant de la convertir
                    if(is_string($value)){
                        $value = match ($key) {
                            'role' => Role::from($value),
                            'status' => Status::from($value),
                            'statusReview' => StatusReview::from($value),
                        };
                    }
                }  
                
                $this->{$methodName}($value);
                
            }            
        }
    }
}