<?php

namespace App\Model;

use DateTimeImmutable;

/**
 * Enumération pour représenter les différents statuts d'un covoiturage.
 * Cette énumération permet de définir clairement les états possibles d'un covoiturage,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum Status: string
{
        case Pending = 'pending'; // le covoiturage est crée mais pas lancé
        case Ongoing = "ongoing"; // le covoiturage est en cours
        case Completed = "completed"; // le covoiturage est fini
        case Cancelled = "cancelled"; // le covoiturage à été annuler par le chauffeur ou un employer/admin
}

// Fichier contenant notre classe RideSharingModel qui étend la classe BaseModel.
class RideSharingModel extends BaseModel
{
    private int $idRideSharing; // Identifiant du covoiturage.
    private DateTimeImmutable $departureDate; //Date de départ
    private string $departureCity; //Ville de départ
    private string $departureAdress; //Adresse de départ
    private string $arrivalCity; //Ville d'arriver
    private string $arrivalAdress; //Adresse d'arriver
    private DateTimeImmutable $arrivalDate; //Date d'arriver
    private int $availableSeats; //Place disponnible
    private int $priceParSeat; //Prix par place
    private Status $status; //Est défini par notre énum pour éviter des erreur de tipo ou de type.
    private DateTimeImmutable $createdAt; //Date de création du covoiturage
    private int $idDriver; // Identifiant du conducteur
    private int $idCar; // Identifiant de la voiture utilisée pour le covoiturage

    /**
     * Get the value of idRideSharing
     */
    public function getIdRideSharing(): int
    {
        return $this->idRideSharing;
    }

    /**
     * Set the value of idRideSharing
     */
    public function setIdRideSharing(int $idRideSharing): self
    {
        $this->idRideSharing = $idRideSharing;

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
     * Get the value of departureAdress
     */
    public function getDepartureAdress(): string
    {
        return $this->departureAdress;
    }

    /**
     * Set the value of departureAdress
     */
    public function setDepartureAdress(string $departureAdress): self
    {
        $this->departureAdress = $departureAdress;

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
     * Get the value of arrivalAdress
     */
    public function getArrivalAdress(): string
    {
        return $this->arrivalAdress;
    }

    /**
     * Set the value of arrivalAdress
     */
    public function setArrivalAdress(string $arrivalAdress): self
    {
        $this->arrivalAdress = $arrivalAdress;

        return $this;
    }

    /**
     * Get the value of arrivalDate
     */
    public function getArrivalDate(): DateTimeImmutable
    {
        return $this->arrivalDate;
    }

    /**
     * Set the value of arrivalDate
     */
    public function setArrivalDate(DateTimeImmutable $arrivalDate): self
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
     * Get the value of priceParSeat
     */
    public function getPriceParSeat(): int
    {
        return $this->priceParSeat;
    }

    /**
     * Set the value of priceParSeat
     */
    public function setPriceParSeat(int $priceParSeat): self
    {
        $this->priceParSeat = $priceParSeat;

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
}