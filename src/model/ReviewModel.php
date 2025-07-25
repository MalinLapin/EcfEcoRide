<?php

namespace App\Model;

use DateTimeImmutable;

enum StatusReview: string
{
    case Pending = 'pending'; // La revue est en attente de traitement.
    case Approved = 'approved'; // La revue a été approuvée.
    case Rejected = 'rejected'; // La revue a été rejetée.
}

class ReviewModel extends BaseModel
{
    private int $idReview; // Identifiant de la revue.
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
}