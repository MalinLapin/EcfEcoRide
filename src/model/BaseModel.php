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
            $methodName = str_replace(array('-','_'), ' ',$key); // first name
            $methodName = ucwords($methodName);// First Name
            $methodName = str_replace(' ','', $methodName); // FirstName
            $methodName = "set".ucfirst($key); // setFirstName
            
            
            // On vérifie si la méthode existe avant de l'appeler
            if(method_exists($this,$methodName))
                {
                    
                // Si la clé est une date, on la convertit en DateTimeImmutable
                // On vérifie si la clé est 'created_at' ou 'updated_at'
                if($key === 'created_at'|| $key === 'updated_at')
                    {
                    // On s'assure que la valeur est une chaîne de caractères avant de la convertir
                    if(is_string($value))
                        {
                        $value = new DateTimeImmutable($value);
                    }
                }             
                
                // Si la clé est un enum, on le convertit en instance de l'enum
                if ($key === 'role'){
                    $value = Role::from($value);
                }
                
                if ($key === 'status'){
                    $value = Status::from($value);
                }
                
                if($key === 'statusReview'){
                    $value = StatusReview::from($value);
                }
                
                // Si la clé est is_active ou is_verified, on convertit la valeur en booléen
                if ($key === 'is_active' || $key === 'is_verified'){
                    $value = (bool)$value;
                }
                
                $this->{$methodName}($value);
                
            }
            
        }
    }
}