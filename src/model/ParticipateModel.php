<?php

namespace App\Model;

use DateTimeImmutable;

class ParticipateModel extends BaseModel
{
    private ?int $idParticipate = null; // Identifiant unique de la participation, initialisé à null.
    private int $idParticipant; // Identifiant de l'utilisateur participant.
    private int $idRidesharing; // Identifiant du trajet de covoiturage.
    private int $nbSeats = 1; // Nombre de places réservées, initialisé à 1.
    private DateTimeImmutable $createdAt; // Date de création de la participation.
    private ?DateTimeImmutable $completedAt = null; // Date de complétion de la participation, initialisé à null.
    private bool $confirmed = false; // Indique si la participation a été confirmée, initialisé à false.

    public function __construct(array $data = [])
    {
        $this->hydrate($data);
    }    

    /**
     * Get the value of idParticipate
     */
    public function getIdParticipate(): ?int
    {
        return $this->idParticipate;
    }

    /**
     * Set the value of idParticipate
     */
    public function setIdParticipate(?int $idParticipate): self
    {
        $this->idParticipate = $idParticipate;

        return $this;
    }

    /**
     * Get the value of idParticipant
     */
    public function getIdParticipant(): int
    {
        return $this->idParticipant;
    }

    /**
     * Set the value of idParticipant
     */
    public function setIdParticipant(int $idParticipant): self
    {
        $this->idParticipant = $idParticipant;

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

    /**
     * Get the value of nbSeats
     */
    public function getNbSeats(): int
    {
        return $this->nbSeats;
    }

    /**
     * Set the value of nbSeats
     */
    public function setNbSeats(int $nbSeats): self
    {
        $this->nbSeats = $nbSeats;

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
     * Get the value of completedAt
     */
    public function getCompletedAt(): ?DateTimeImmutable
    {
        return $this->completedAt;
    }

    /**
     * Set the value of completedAt
     */
    public function setCompletedAt(?DateTimeImmutable $completedAt): self
    {
        $this->completedAt = $completedAt;

        return $this;
    }

    /**
     * Get the value of confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * Set the value of confirmed
     */
    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;

        return $this;
    }
}