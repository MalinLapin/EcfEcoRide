<?php

namespace App\model;

/**
 * Fichier contenant notre classe Preference qui étend la classe BaseModel.
 * Cette classe représente une préférence d'un utilisateur pour une voiture.
 */
class PreferenceModel extends BaseModel
{
    private ?string $idPreference = null; // Identifiant des preferences. En string car l'id en MongoDb est un ObjectId
    private string $label; // Label de la préférence.    
    private int $idRidesharing; // Identifiant du trajet associé.


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
     * Get the value of idRidesharing
     */
    public function getIdRidesharing(): int
    {
        return $this->idRidesharing;
    }

    /**
     * Set the value of idRidesharing
     */
    public function setIdRidesharing(int $idRidesharing): self
    {
        $this->idRidesharing = $idRidesharing;

        return $this;
    }
}