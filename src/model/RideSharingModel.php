<?php

namespace App\model;

use DateTimeImmutable;
use App\Attribute\NotMapped;

/**
 * Enumération pour représenter les différents statuts d'un covoiturage.
 * Cette énumération permet de définir clairement les états possibles d'un covoiturage,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum Status: string
{
        case pending = 'pending'; // le covoiturage est en attente.
        case ongoing = 'ongoing'; // le covoiturage est en cours
        case completed = 'completed'; // le covoiturage est fini
        case cancelled = 'canceled'; // le covoiturage annulé par le chauffeur ou un employer/admin
}

// Fichier contenant notre classe RideSharingModel qui étend la classe BaseModel.
class RidesharingModel extends BaseModel
{
    private ?int $idRidesharing = null; // Identifiant du covoiturage.
    private int $idDriver; // Identifiant de l'utilisateur qui sera le conducteur.
    private int $idCar; // Identifiant de la voiture utiliser pour le covoiturage.
    private DateTimeImmutable $departureDate; //Date de départ
    private string $departureCity; //Ville de départ
    private string $departureAddress; //Adresse de départ
    private string $arrivalCity; //Ville d'arriver
    private string $arrivalAddress; //Adresse d'arriver
    private ?DateTimeImmutable $arrivalDate = null; //Date d'arriver
    private int $availableSeats; //Places disponibles
    private int $pricePerSeat; //Prix par place
    private Status $status = Status::pending; //Est défini par notre énum pour éviter des erreur de typo ou de type.
    private DateTimeImmutable $createdAt; //Date de création du covoiturage

    #[NotMapped]
    private ?int $nbParticipant = 0; // N'est pas stocker en base de données.
    

    public function __construct(array $data = [])
    {
        $this->hydrate($data);    
    }

    /**
     * Get the value of idRidesharing
     */
    public function getIdRidesharing(): ?int
    {
        return $this->idRidesharing;
    }

    /**
     * Set the value of idRidesharing
     */
    public function setIdRidesharing(?int $idRidesharing): self
    {
        $this->idRidesharing = $idRidesharing;

        return $this;
    }

    /**
     * Get the value of idDriver
     */
    public function getIdDriver(): int
    {
        return $this->idDriver;
    }

    /**
     * Set the value of idDriver
     */
    public function setIdDriver(int $idDriver): self
    {
        $this->idDriver = $idDriver;

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

    /**
     * Get the value of departureDate
     */
    public function getDepartureDate(): DateTimeImmutable
    {
        return $this->departureDate;
    }

    /**
     * Set the value of departureDate
     */
    public function setDepartureDate(DateTimeImmutable $departureDate): self
    {
        $this->departureDate = $departureDate;

        return $this;
    }

    /**
     * Get the value of departureCity
     */
    public function getDepartureCity(): string
    {
        return $this->departureCity;
    }

    /**
     * Set the value of departureCity
     */
    public function setDepartureCity(string $departureCity): self
    {
        $this->departureCity = $departureCity;

        return $this;
    }

    /**
     * Get the value of departureAddress
     */
    public function getDepartureAddress(): string
    {
        return $this->departureAddress;
    }

    /**
     * Set the value of departureAddress
     */
    public function setDepartureAddress(string $departureAddress): self
    {
        $this->departureAddress = $departureAddress;

        return $this;
    }

    /**
     * Get the value of arrivalCity
     */
    public function getArrivalCity(): string
    {
        return $this->arrivalCity;
    }

    /**
     * Set the value of arrivalCity
     */
    public function setArrivalCity(string $arrivalCity): self
    {
        $this->arrivalCity = $arrivalCity;

        return $this;
    }

    /**
     * Get the value of arrivalAddress
     */
    public function getArrivalAddress(): string
    {
        return $this->arrivalAddress;
    }

    /**
     * Set the value of arrivalAddress
     */
    public function setArrivalAddress(string $arrivalAddress): self
    {
        $this->arrivalAddress = $arrivalAddress;

        return $this;
    }

    /**
     * Get the value of arrivalDate
     */
    public function getArrivalDate(): ?DateTimeImmutable
    {
        return $this->arrivalDate;
    }

    /**
     * Set the value of arrivalDate
     */
    public function setArrivalDate(?DateTimeImmutable $arrivalDate): self
    {
        $this->arrivalDate = $arrivalDate;

        return $this;
    }

    /**
     * Get the value of availableSeats
     */
    public function getAvailableSeats(): int
    {
        return $this->availableSeats;
    }

    /**
     * Set the value of availableSeats
     */
    public function setAvailableSeats(int $availableSeats): self
    {
        $this->availableSeats = $availableSeats;

        return $this;
    }

    /**
     * Get the value of pricePerSeat
     */
    public function getPricePerSeat(): int
    {
        return $this->pricePerSeat;
    }

    /**
     * Set the value of pricePerSeat
     */
    public function setPricePerSeat(int $pricePerSeat): self
    {
        $this->pricePerSeat = $pricePerSeat;

        return $this;
    }

    /**
     * Get the value of status
     */
    public function getStatus(): Status
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of nbParticipant
     */
    public function getNbParticipant(): ?int
    {
        return $this->nbParticipant;
    }

    /**
     * Set the value of nbParticipant
     */
    public function setNbParticipant(?int $nbParticipant): self
    {
        $this->nbParticipant = $nbParticipant;

        return $this;
    }
}

    