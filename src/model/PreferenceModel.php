<?php

namespace App\Model;

/**
 * Fichier contenant notre classe Preference qui étend la classe BaseModel.
 * Cette classe représente une préférence d'un utilisateur pour une voiture.
 */
class Preference extends BaseModel
{
    private ?string $idPreference = null; // Identifiant des preferences. En string car l'id en MongoDb est un ObjectId
    private bool $label; // Label de la préférence.
    private bool $isAccepted; // Indique si la préférence est acceptée ou non.    
    private int $idCar; // Identifiant de la voiture associée à cette préférence.


    public function __construct(array $data = [])
    {
        $this->hydrate($data);    
    }
    
    /**
     * Get the value of idPreference
     */
    public function getIdPreference(): string
    {
        return $this->idPreference;
    }

    /**
     * Set the value of idPreference
     */
    public function setIdPreference(string $idPreference): self
    {
        $this->idPreference = $idPreference;

        return $this;
    }

    /**
     * Get the value of label
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Set the value of label
     */
    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get the value of isAccepted
     */
    public function isIsAccepted(): bool
    {
        return $this->isAccepted;
    }

    /**
     * Set the value of isAccepted
     */
    public function setIsAccepted(bool $isAccepted): self
    {
        $this->isAccepted = $isAccepted;

        return $this;
    }

    /**
     * Get the value of idCar
     */
    public function getIdCar(): int
    {
        return $this->idCar;
    }

    /**
     * Set the value of idCar
     */
    public function setIdCar(int $idCar): self
    {
        $this->idCar = $idCar;

        return $this;
    }
}