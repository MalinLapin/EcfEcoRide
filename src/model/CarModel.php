<?php

namespace App\model;

use DateTimeImmutable;


enum EnergyType: string
{
        case electric = 'electric';
        case hybrid = 'hybrid';
        case gasoline = 'gasoline';
        case diesel = 'diesel';
        case gpl = 'gpl';
}
// Fichier contenant notre classe CarModel qui étend la classe BaseModel.
class CarModel extends BaseModel
{
    private ?int $idCar = null; // Identifiant de la voiture, initialisé à null.
    private string $model; // Modèle de la voiture.
    private string $registrationNumber; // Numéro d'immatriculation de la voiture.
    private DateTimeImmutable $firstRegistration; // Date de première immatriculation.
    private EnergyType $energyType; // Type d'énergie (électrique, essence, diesel).
    private string $color; // Couleur de la voiture.
    private int $idBrand; // Identifiant de la marque associée à la voiture.
    private int $idUser; // Identifiant de l'utilisateur propriétaire de la voiture.   

    public function __construct(array $data = [])
    {
        $this->hydrate($data);    
    }
    
    /**
     * Get the value of idCar
     */
    public function getIdCar(): ?int
    {
        return $this->idCar;
    }

    /**
     * Set the value of idCar
     */
    public function setIdCar(?int $idCar): self
    {
        $this->idCar = $idCar;

        return $this;
    }

    /**
     * Get the value of model
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Set the value of model
     */
    public function setModel(string $model): self
    {
        $this->model = $model;

        return $this;
    }

    /**
     * Get the value of registrationNumber
     */
    public function getRegistrationNumber(): string
    {
        return $this->registrationNumber;
    }

    /**
     * Set the value of registrationNumber
     */
    public function setRegistrationNumber(string $registrationNumber): self
    {
        $this->registrationNumber = $registrationNumber;

        return $this;
    }

    /**
     * Get the value of firstRegistration
     */
    public function getFirstRegistration(): DateTimeImmutable
    {
        return $this->firstRegistration;
    }

    /**
     * Set the value of firstRegistration
     */
    public function setFirstRegistration(DateTimeImmutable $firstRegistration): self
    {
        $this->firstRegistration = $firstRegistration;

        return $this;
    }

    /**
     * Get the value of energyType
     */
    public function getEnergyType(): EnergyType
    {
        return $this->energyType;
    }

    /**
     * Set the value of energyType
     */
    public function setEnergyType(EnergyType $energyType): self
    {
        $this->energyType = $energyType;

        return $this;
    }

    /**
     * Get the value of color
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * Set the value of color
     */
    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get the value of idBrand
     */
    public function getIdBrand(): int
    {
        return $this->idBrand;
    }

    /**
     * Set the value of idBrand
     */
    public function setIdBrand(int $idBrand): self
    {
        $this->idBrand = $idBrand;

        return $this;
    }

    /**
     * Get the value of idUser
     */
    public function getIdUser(): int
    {
        return $this->idUser;
    }

    /**
     * Set the value of idUser
     */
    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }
}