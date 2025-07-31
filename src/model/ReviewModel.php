<?php

namespace App\Model;

use DateTimeImmutable;

/**
 * Enumération pour représenter les différents statuts d'une revue.
 * Cette énumération permet de définir clairement les états possibles d'une revue,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum StatusReview: string
{
    case Pending = 'pending'; // La revue est en attente de traitement.
    case Approved = 'approved'; // La revue a été approuvée.
    case Rejected = 'rejected'; // La revue a été rejetée.
}

/**
 * Fichier contenant notre classe ReviewModel qui étend la classe BaseModel.
 * Cette classe représente une revue faite par un utilisateur sur un autre utilisateur.
 */
class ReviewModel extends BaseModel
{
    private ?int $idReview = null; // Identifiant de la revue.
    private string $content; // Contenu de la revue.
    private int $rating; // Note attribuée dans la revue, généralement entre 1 et 5.
    private DateTimeImmutable $createdAt; // Date de création de la revue.
    private StatusReview $status; // Statut de la revue, défini par l'énumération StatusReview.
    private int $idRedacteur; // Identifiant de l'utilisateur qui a créé la revue.
    private int $idTarget; // Identifiant de l'utilisateur qui est la cible de la revue.

    

    /**
     * Get the value of idReview
     */
    public function getIdReview(): int
    {
        return $this->idReview;
    }

    /**
     * Set the value of idReview
     */
    public function setIdReview(int $idReview): self
    {
        $this->idReview = $idReview;

        return $this;
    }

    /**
     * Get the value of content
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Set the value of content
     */
    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get the value of rating
     */
    public function getRating(): int
    {
        return $this->rating;
    }

    /**
     * Set the value of rating
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

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
     * Get the value of status
     */
    public function getStatus(): StatusReview
    {
        return $this->status;
    }

    /**
     * Set the value of status
     */
    public function setStatus(StatusReview $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the value of idRedacteur
     */
    public function getIdRedacteur(): int
    {
        return $this->idRedacteur;
    }

    /**
     * Set the value of idRedacteur
     */
    public function setIdRedacteur(int $idRedacteur): self
    {
        $this->idRedacteur = $idRedacteur;

        return $this;
    }

    /**
     * Get the value of idTarget
     */
    public function getIdTarget(): int
    {
        return $this->idTarget;
    }

    /**
     * Set the value of idTarget
     */
    public function setIdTarget(int $idTarget): self
    {
        $this->idTarget = $idTarget;

        return $this;
    }
}