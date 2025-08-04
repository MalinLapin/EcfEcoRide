<?php

namespace App\repository;

use App\Model\RidesharingModel;

class RidesharingRepo extends BaseRepo
{
    protected string $tableName = 'ridesharing';
    protected string $className = RidesharingModel::class;

    /**
     * Récupère les trajets de covoiturage en fonction des paramètres fournis.
     * 
     * @param array $data Tableau associatif contenant les critères de recherche.
     * @return array Un tableau d'instances de RidesharingModel correspondant aux critères de recherche.
     * @return null Si aucun trajet n'est trouver.
     * 
     * Cette méthode construit dynamiquement une requête SQL en fonction des paramètres fournis.
     * Elle récupère les trajets dont le statut est 'pending', la date de départ est supérieure ou égale à la date et l'heure actuelles,
     * et le nombre de places disponibles est supérieur à 0. Les résultats sont retournés sous forme d'instances de RidesharingModel.
     */
    public function getRidesharingByParams(array $data): array
    {
        
        $Sqlparams = [];
        //On définit la date et l'heure actuelle pour les opérations de comparaison
        $currentDateTime = (new \DateTime())->format('Y-m-d H:i:s');

        /**
         * On récupère les trajets en attente de validation, dont la date de départ est supérieure ou égale à la date et l'heure actuelle,
         * et dont le nombre de places disponibles est supérieur à 0.
         * On utilise une jointure pour récupérer les informations de l'utilisateur associé au trajet.
         * On sélectionne les colonnes du trajet ainsi que l'id, le pseudo, le grade et la photo de l'utilisateur.
         * Mais aussi les données utils de la voiture telle que le carburant, le modèle et la couleur.
         */
        $query = "SELECT r.*, u.id_user AS user_id, u.pseudo, u.grade, u.photo, c.model, c.energy_type, c.color
            FROM {$this->tableName} r
            JOIN user u ON r.id_rider = u.id_user
            JOIN car c ON r.id_car = c.id_car
            WHERE r.status = 'pending'
            AND r.departure_date >= :currentDateTime
            AND r.available_seats > 0";

        $Sqlparams = ['currentDateTime' => $currentDateTime];
        
        //Je vais utiliser ce tableau pour construire la requête SQL dynamiquement.
        $criteria = [ 
            'departure_city'=>['r.departure_city', '=', 'departure_city'],
            'departure_adress'=>['r.departure_adress', '=', 'departure_adress'],
            'arrival_city'=>['r.arrival_city', '=', 'arrival_city'],
            'arrival_adress'=>['r.arrival_adress', '=', 'arrival_adress'],
            'price_par_seat'=>['r.price_par_seat', '<=', 'price_par_seat'],//Pour une recherche de tarif inférieur ou égale à la recherche utilisateur.
            'pseudo_driver'=>['u.pseudo', '=', 'pseudo_driver'],
            'grade_driver' =>['u.grade', '=', 'grade_driver'],
            'energy_type' =>['c.energy_type', '=', 'energy_type'], //Pour la recherche de voiture avec un carburant spécifique (Ex: electrique).
        ];

        
        // On parcourt les critères pour ajouter les conditions à la requête SQL
        foreach ($criteria as $key =>[$param, $operator, $placeholder]) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $query .= " AND $param $operator :$placeholder";
                $Sqlparams[$placeholder] = $data[$key];     
            }
        }

        $stmt = $this->pdo->prepare($query);        
        $stmt->execute($Sqlparams);
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        // Si un trajet est trouvé, on crée une instance du modèle RideSharingModel avec les données récupérées
        if($result){ 
            $rides = [];
            foreach ($result as $row){ 
                $rides[] = RidesharingModel::createAndHydrate($row);
            }
            
            return $rides;
        }
        return [];
    }


    /**
     * Modifie le trajet lorsque ce dernier est terminé.
     * 
     * @param int $id Identifiant du trajet fini
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function endRide(int $rideId):bool
    {

        $sql = "UPDATE {$this->tableName} SET status = 'completed', arrival_date = :arrival_date WHERE id_ridesharing = :id_ridesharing AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'arrival_date' => (new \DateTime())->format('Y-m-d H:i:s'),
            'id_ridesharing' => $rideId
        ]);
    }

    /**
     * Modifie le trajet si ce dernier est annulé par le chauffeur ou un employé.
     * 
     * @param int $rideId L'identifiant du trajet
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function cancelRide(int $rideId): bool
    {
        $sql = "UPDATE {$this->tableName} SET status = 'cancelled' WHERE id_ridesharing = :id_ridesharing AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id_ridesharing' => $rideId]);
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
                WHERE id_ridesharing = :id_ridesharing AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id_ridesharing'  => $rideId]);
    }

    /**
     * Décrémente le nombre de places disponibles pour un trajet.
     *
     * @param int $rideId L'identifiant du trajet.
     * @param int $nbSeats Nombre de places à retirer (par défaut 1).
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function decrementSeats(int $rideId, int $nbSeats = 1): bool
    {
        $sql = "UPDATE {$this->tableName} 
                SET available_seats = available_seats - :nbSeats 
                WHERE id_ridesharing = :id_ridesharing AND available_seats >= :nbSeats"; // On ne peut désendre en dessous du nombre de place total disponible.
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nbSeats' => $nbSeats,
            'id_ridesharing'      => $rideId
        ]);
    }

    /**
     * Incrémente le nombre de places disponibles pour un trajet.
     *
     * @param int $rideId L'identifiant du trajet.
     * @param int $nbSeats Nombre de places à rajouter (par défaut 1).
     * @return bool Vrai si la mise à jour a réussi, faux sinon.
     */
    public function incrementSeats(int $rideId, int $nbSeats = 1): bool
    {
        $sql = "UPDATE {$this->tableName} 
                SET available_seats = available_seats + :nbSeats 
                WHERE id_ridesharing = :id_ridesharing";

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            'nbSeats' => $nbSeats,
            'id_ridesharing' => $rideId
        ]);
    }
}

