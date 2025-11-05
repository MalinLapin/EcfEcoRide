<?php

namespace App\repository;

use App\model\ParticipateModel;
use App\model\UserModel;
use App\model\RidesharingModel;

class ParticipateRepo extends BaseRepoSql
{
    protected string $tableName = 'participate';
    protected string $className = ParticipateModel::class; 
    
    /**
     * Trouve les participation auxquels un utilisateur est inscrit.
     * 
     * @param int $idUser L'identifiant de l'utilisateur.
     * @return array[]|null Un tableau d'instances de ParticipateModel et RidesharingModel ou null si aucun trajet n'est trouvé.
     */
    public function findListParticipationByUser(int $idUser): ?array
    {
        $query = "SELECT p.*,
                    r.status AS ridesharing_status,
                    r.departure_date AS ridesharing_departure_date,
                    r.departure_city AS ridesharing_departure_city,
                    r.arrival_city AS ridesharing_arrival_city,
                    r.price_per_seat AS ridesharing_price_per_seat,
                    r.id_driver AS ridesharing_id_driver
                    FROM {$this->tableName} p
                    JOIN  ridesharing r
                    ON r.id_ridesharing = p.id_ridesharing
                    AND p.confirmed = true         -- Pour ne pas compter des participations qui ne seraient pas encore validée.
                    WHERE p.id_participant = :id_participant
                    GROUP BY
                    r.status,
                    r.departure_date,
                    r.departure_city,
                    r.arrival_city,
                    r.price_per_seat
                    ORDER BY
                    CASE
                        WHEN r.status = 'ongoing' THEN 1
                        WHEN r.status = 'pending' THEN 2
                        ELSE 3
                    END,
                    r.departure_date ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt -> bindValue(':id_participant', $idUser);
        
        $stmt -> execute();
        
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result)
        {    
            
            foreach($result as $row)
            {   
                $paricipateData = [];
                $ridesharingData = [];

                foreach ($row as $key => $value)
                {
                    if (str_starts_with($key, 'ridesharing_')) {
                        $ridesharingData[substr($key, 12)] = $value;
                    } else {
                        $paricipateData[$key] = $value;
                    }
                }

                $participation = ParticipateModel::createAndHydrate($paricipateData);
                $ride = RidesharingModel::createAndHydrate($ridesharingData);
                
                $participationList [] = [
                    'participation'=>$participation,
                    'ride'=>$ride
                ];
            }
            return $participationList;
        }
        return null;
    }

    /**
     * Trouve les participation auxquels un utilisateur est inscrit.
     * 
     * @param int $idParticipant L'identifiant du participant.
     * @return ParticipateModel|null Un objet ParticipateModel ou null si aucune participation n'est trouvé.
     */
    public function findParticipationByUser(int $idParticipant): ?ParticipateModel
    {
        $query = "SELECT *
                    FROM {$this->tableName} p
                    WHERE p.id_participant = :id_participant";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':id_participant', $idParticipant);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if($result){
            $participation = ParticipateModel::createAndHydrate($result);

            return $participation;
        }

        return null;
    }

    /**
     * Confirme la participation d'un utilisateur à un trajet de covoiturage.
     * @param int $idParticipate L'ID de la participation à confirmer.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function confirmParticipation(int $userId, int $rideId): bool
    {
        $query = "UPDATE {$this->tableName} SET confirmed = TRUE WHERE id_participant = :id_participate AND id_ridesharing = :id_ridesharing AND confirmed = 0";
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
                WHERE id_ridesharing = :id_ridesharing ";

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
    public function findParticipantsByRide (int $rideId): array
    {
        $query = "SELECT u.* 
                    FROM user u
                    JOIN {$this->tableName} p ON u.id_user = p.id_participant
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