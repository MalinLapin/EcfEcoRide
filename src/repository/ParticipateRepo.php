<?php

namespace App\repository;

use App\Model\ParticipateModel;
use App\Model\UserModel;

class ParticipateRepo extends BaseRepoSql
{
    protected string $tableName = 'participate';
    protected string $className = ParticipateModel::class; 
    
    /**
     * Confirme la participation d'un utilisateur à un trajet de covoiturage.
     * @param int $idParticipate L'ID de la participation à confirmer.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function confirmParticipation(int $userId, int $rideId): bool
    {
        $query = "UPDATE {$this->tableName} SET confirmed = TRUE WHERE id_participant = :uid AND id_ridesharing = :rid AND confirmed = 0";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id_participate', $userId);
        $stmt->bindValue(':id_ridesharing', $rideId);
        $stmt->execute();
        return $stmt->rowCount() === 1; // Retourne true uniquement si une ligne a été affectée
    }

    /**
     * Décrémente le solde de crédits d'un utilisateur.
     * @param int $idParticipant L'ID de l'utilisateur dont on veut décrémenter le solde de crédits.
     * @param int $amount Le montant à décrémenter.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function decrementCreditBalance(int $idUser, int $amount): bool
    {
        $query = "UPDATE user SET credit_balance = credit_balance - :amount WHERE id_user = :id_user AND credit_balance >= :amount";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':amount', $amount);
        $stmt->bindValue(':id_user', $idUser);
        $stmt->execute();
        return $stmt->rowCount() === 1; // Retourne true uniquement si une ligne a été affectée
    }

    /**
     * Incrémente le nombre de places disponibles pour un trajet de covoiturage.
     * @param int $rideId L'ID du trajet de covoiturage.
     * @param int $nbSeats Le nombre de places à incrémenter (par défaut 1).
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function incrementSeats(int $rideId, int $nbSeats = 1): bool
    {
        $sql = "UPDATE ridesharing
                SET available_seats = available_seats + :nbSeats
                WHERE id_ridesharing = :id_ridesharing AND available_seats + :nbSeats <= total_seats;"; // On ne peut pas dépasser le nombre total de places.

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nbSeats' => $nbSeats,
            'id_ridesharing' => $rideId
        ]);
        return $stmt->rowCount() === 1;
    }

    /**
     * Décrémente le nombre de places disponibles pour un trajet de covoiturage.
     * @param int $rideId L'ID du trajet de covoiturage.
     * @param int $nbSeats Le nombre de places à décrémenter (par défaut 1).
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function decrementSeats(int $rideId, int $nbSeats = 1): bool
    {
        $sql = "UPDATE ridesharing
                SET available_seats = available_seats - :nbSeats
                WHERE id_ridesharing = :id_ridesharing 
                AND available_seats >= :nbSeats"; // On ne peut pas descendre en dessous de zéro place disponible.
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'nbSeats' => $nbSeats,
            'id_ridesharing' => $rideId
        ]);
        return $stmt->rowCount() === 1;
    }

    /**
     * Récupère la liste des participants confirmés pour un trajet de covoiturage donné.
     * @param int $rideId L'ID du trajet de covoiturage.
     * @return array Un tableau d'instances de ParticipateModel représentant les participants.
     */
    public function getParticipantsByRide (int $rideId): array
    {
        $query = "SELECT u.* FROM user u
                    JOIN participate p ON u.id_user = p.id_participant
                    WHERE p.id_ridesharing = :id_ridesharing ";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id_ridesharing', $rideId);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if ($result) {
            $participants = [];
            foreach ($result as $row) {
                $participants[] = UserModel::createAndHydrate($row);
            }
            return $participants;
        }
        return [];
    }
} 