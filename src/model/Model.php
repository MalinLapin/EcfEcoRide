<?php

namespace App\Model;

use DateTimeImmutable;

class Entity
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
                // Si la clé est created_at, on convertit la valeur en DateTimeImmutable
                if($key === 'created_at')
                {
                    $value = new DateTimeImmutable($value);
                }
                $this->{$methodName}($value);
            }
            
        }
    }
}