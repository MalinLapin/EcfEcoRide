<?php

namespace App\repository;

use App\Model\RidesharingModel;
use Exception;

class RidesharingRepo extends BaseRepoSql
{
    protected string $tableName = 'ridesharing';
    protected string $className = RidesharingModel::class;

    /**
     * Récupère les trajets de covoiturage en fonction des paramètres fournis.
     * 
     * @param array $data Tableau associatif contenant les critères de recherche.
     * @return array Un tableau d'instances de RidesharingModel correspondant aux critères de recherche, ou un tableau vide si aucun trajet n'est trouvé.
     * 
     * Cette méthode construit dynamiquement une requête SQL en fonction des paramètres fournis.
     */
    public function getRidesharingByParams(array $data): array
    {
        $Sqlparams = [];
        // On définit la date et l'heure actuelle pour les opérations de comparaison
        $currentDateTime = (new \DateTime())->format('Y-m-d H:i:s');

        /**
         * On utilise une jointure pour récupérer les informations de l'utilisateur et la voiture associé au trajet 
         * On sélectionne l'ensemble des données du trajet ainsi que l'id, le pseudo, le grade et la photo de l'utilisateur.
         * Mais aussi les données utiles de la voiture telles que le carburant, le modèle et la couleur.
         */
        $query = "SELECT r.*, u.id_user AS user_id, u.pseudo, u.grade, u.photo, c.model, c.energy_type, c.color
            FROM {$this->tableName} r
            JOIN user u ON r.id_driver = u.id_user
            JOIN car c ON r.id_car = c.id_car
            WHERE 1=1 ";
        
        // Je vais utiliser ce tableau pour construire la requête SQL dynamiquement.
        $criteria = [
            'departure_city' => ['r.departure_city', '=', 'departure_city'],
            'departure_address' => ['r.departure_address', '=', 'departure_address'],
            'departure_date' => ['r.departure_date', '>=', 'departure_date'],// On recherchera les trajets dont la date de départ ne sera pas encore passée.
            'arrival_city' => ['r.arrival_city', '=', 'arrival_city'],
            'arrival_address' => ['r.arrival_address', '=', 'arrival_address'],
            'price_per_seat' => ['r.price_per_seat', '<=', 'price_per_seat'], // Pour une recherche de tarif inférieur ou égal à la recherche utilisateur.
            'available_seats' => ['r.available_seats', '>=', 'available_seats'], // On recherchera uniquement les trajet avec autant ou plus de place que demandé.
            'status' => ['r.status', '=', 'status'],
            'pseudo_driver' => ['u.pseudo', '=', 'pseudo_driver'],
            'grade_driver' => ['u.grade', '=', 'grade_driver'],
            'energy_type' => ['c.energy_type', '=', 'energy_type'], // Recherche de voiture avec type d'énergie spécifique (ex: électrique).
        ];

        // On parcourt les critères pour ajouter les conditions à la requête SQL
        foreach ($criteria as $key => [$param, $operator, $placeholder]) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $query .= " AND $param $operator :$placeholder";
                $Sqlparams[$placeholder] = $data[$key];
            }
        }

        $stmt = $this->pdo->prepare($query);
        $stmt->execute($Sqlparams);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Si au moins un trajet est trouvé, on crée une instance du modèle RidesharingModel avec les données récupérées
        if ($result) {
            $rides = [];
            foreach ($result as $row) {
                $rides[] = RidesharingModel::createAndHydrate($row);
            }

            return $rides;
        }
        return [];
    }

    /**
     * Marque un trajet comme terminé.
     *
     * @param int $rideId Identifiant du trajet fini.
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function endRide(int $rideId): bool
    {
        $sql = "UPDATE {$this->tableName} 
        SET status = 'completed' 
        WHERE id_ridesharing = :id_ridesharing 
        AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute(['id_ridesharing' => $rideId ]);
    }

    /**
     * Annule le trajet (par le chauffeur ou un employé).
     *
     * @param int $rideId L'identifiant du trajet.
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     * On en pourra pas supprimer un trajet completment de la bdd on changera seulement sont status en 'cancelled'.
     */
    public function cancelRide(int $rideId): bool
    {
        $sql = "UPDATE {$this->tableName} 
        SET status = 'cancelled' 
        WHERE id_ridesharing = :id_ridesharing 
        AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id_ridesharing' => $rideId ]);
    }

    /**
     * Passe le statut d'un trajet de 'pending' à 'ongoing'.
     *
     * @param int $rideId L'identifiant du trajet.
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function setRideOngoing(int $rideId): bool
    {
        $sql = "UPDATE {$this->tableName}
                SET status = 'ongoing'
                WHERE id_ridesharing = :id_ridesharing 
                AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id_ridesharing' => $rideId]);
    }
}