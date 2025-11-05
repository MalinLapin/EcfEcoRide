<?php

namespace App\model;

use DateTime;
use DateTimeImmutable;

/**
 * Enumération pour représenter les différents statuts d'une revue.
 * Cette énumération permet de définir clairement les états possibles d'une revue,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum StatusReview: string
{
    case pending = 'pending'; // La revue est en attente de traitement.
    case approved = 'approved'; // La revue a été approuvée.
    case rejected = 'rejected'; // La revue a été rejetée.
}

/**
 * Fichier contenant notre classe ReviewModel qui étend la classe BaseModel.
 * Cette classe représente une revue faite par un utilisateur sur un autre utilisateur.
 */
class ReviewModel extends BaseModel
{
    private ?string $idReview = null; // Identifiant de l'avis. En string car l'id en MongoDb est un ObjectId
    private string $comment; // Contenu de la revue.
    private int $rating; // Note attribuée dans la revue, généralement entre 1 et 5.
    private DateTimeImmutable $createdAt; // Date de création de la revue.
    private StatusReview $statusReview; // Statut de la revue, défini par l'énumération StatusReview.
    private ?int $reviewedBy; // Id de l'employer qui a validé ou rejeté la review
    private ?DateTimeImmutable $reviewedAt; // Date de la validation ou du rejet
    private ?string $reason = null; // La raison du refus, peut être null si avis accepté
    private int $idRedactor; // Identifiant de l'utilisateur qui a créé la revue.
    private int $idTarget; // Identifiant de l'utilisateur qui est la cible de la revue.

    public function __construct(array $data = [])
    {
        $this->hydrate($data);    
    }

    /**
     * Get the value of idReview
     */
    public function getIdReview(): string
    {
        return $this->idReview;
    }

    /**
     * Set the value of idReview
     */
    public function setIdReview(string $idReview): self
    {
        $this->idReview = $idReview;

        return $this;
    }

    /**
     * Get the value of content
     */
    public function getComment(): string
    {
        return $this->comment;
    }

    /**
     * Set the value of content
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

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
    public function getStatusReview(): StatusReview
    {
        return $this->statusReview;
    }

    /**
     * Set the value of status
     */
    public function setStatusReview(StatusReview $statusReview): self
    {
        $this->statusReview = $statusReview;

        return $this;
    }

    public function getReviewedBy(): ?int
    {
        return $this->reviewedBy;
    }

    public function setReviewedBy(?int $reviewedBy): self
    {
        $this->reviewedBy = $reviewedBy;

        return $this;
    }

    public function getReviewedAt(): ?DateTimeImmutable
    {
        return $this->reviewedAt;
    }

    public function setReviewedAt(?DateTimeImmutable $reviewedAt): self
    {
        $this->reviewedAt = $reviewedAt;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    /**
     * Set the value of idReview
     */
    public function setReason(?string $reason): self
    {
        $this->reason = $reason;
        return $this;
    }

    /**
     * Get the value of idRedacteur
     */
    public function getIdRedactor(): int
    {
        return $this->idRedactor;
    }

    /**
     * Set the value of idRedacteur
     */
    public function setIdRedactor(int $idRedactor): self
    {
        $this->idRedactor = $idRedactor;

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