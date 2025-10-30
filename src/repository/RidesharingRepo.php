<?php

namespace App\repository;

use App\model\RidesharingModel;
use App\model\UserModel;
use App\model\CarModel;
use App\model\ParticipateModel;
use Exception;

class RidesharingRepo extends BaseRepoSql
{
    protected string $tableName = 'ridesharing';
    protected string $className = RidesharingModel::class;

    /**
     * Recherche des trajets de covoiturage en fonction de divers paramètres.
     * 
     * @param array $data Un tableau associatif contenant les paramètres de recherche.
     * @return RidesharingModel[]|null Un tableau d'instances de RidesharingModel correspondant aux critères de recherche, ou null si aucun trajet n'est trouvé.
     */
    public function getRidesharingByParams(array $data): ?array
    {
        $Sqlparams = [];

        /**
         * On utilise une jointure pour récupérer les informations de l'utilisateur et du trajet 
         * On sélectionne l'ensemble des données du trajet ainsi que l'id, le pseudo, le grade et la photo de l'utilisateur.
         * Mais aussi les données utiles de la voiture telles que le carburant.
         */
        $query = "SELECT r.*, 
                        u.id_user AS user_idUser, 
                        u.pseudo AS user_pseudo, 
                        u.grade AS user_grade, 
                        u.photo AS user_photo, 
                        c.energy_type AS car_energy_type
            FROM {$this->tableName} r
            JOIN user u ON r.id_driver = u.id_user
            JOIN car c ON r.id_car = c.id_car
            WHERE 1=1 ";
        
        // Je vais utiliser ce tableau pour construire la requête SQL dynamiquement.
        $criteria = [
            'departureCity' => ['r.departure_city', '=', 'departure_city'],
            'departureAddress' => ['r.departure_address', '=', 'departure_address'],
            'departureDate' => ['r.departure_date', '>=', 'departure_date'],// On recherchera les trajets dont la date de départ ne sera pas encore passée.
            'arrivalCity' => ['r.arrival_city', '=', 'arrival_city'],
            'arrivalAddress' => ['r.arrival_address', '=', 'arrival_address'],
            'pricePerSeat' => ['r.price_per_seat', '<=', 'price_per_seat'], // Pour une recherche de tarif inférieur ou égal à la recherche utilisateur.
            'nbSeats' => ['r.available_seats', '>=', 'nbSeats'], // On recherchera uniquement les trajet avec autant ou plus de place que demandé.
            'status' => ['r.status', '=', 'status'],
            'pseudoDriver' => ['u.pseudo', '=', 'pseudo_driver'],
            'gradeDriver' => ['u.grade', '=', 'grade_driver'],
            'energyType' => ['c.energy_type', '=', 'energy_type'] // Recherche de voiture avec type d'énergie spécifique (ex: électrique).
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
        if ($result) 
        {
            foreach ($result as $row) 
            {                
                $userData = [];
                $carData = [];
                $ridesharingData = [];
                
                foreach ($row as $key => $value)
                {
                    if (str_starts_with($key, 'user_')) {
                        $userData[substr($key, 5)] = $value;
                    } elseif (str_starts_with($key, 'car_')) {
                        $carData[substr($key, 4)] = $value;
                    } else {
                        $ridesharingData[$key] = $value;
                    }
                }
                
                // Créer et assembler les objets
                $ridesharingInfo = RidesharingModel::createAndHydrate($ridesharingData);

                $driverInfo = UserModel::createAndHydrate($userData);                    
                
                $carEnergyType = $carData['energy_type'];          

                $rides[] = [
                    'ridesharingInfo'=>$ridesharingInfo,
                    'driverInfo'=>$driverInfo,
                    'carEnergyType'=>$carEnergyType
                ];
            }
            return $rides;
        }
        return null;
    }

    /**
     * Trouve un trajet de covoiturage par son ID avec les détails du conducteur et de la voiture.
     * 
     * @param int $id L'identifiant du trajet de covoiturage.
     * @return RidesharingModel|null Une instance de RidesharingModel avec les détails, ou null si aucun trajet n'est trouvé.
     * 
     * Cette méthode utilise une jointure pour récupérer les informations de l'utilisateur (conducteur) et de la voiture associée au trajet.
     */
    public function findByIdWithDetails(int $idRidesharing): ?array
    {
        $query = "SELECT r.*, 
                u.id_user AS user_idUser, 
                u.pseudo AS user_pseudo, 
                u.grade AS user_grade, 
                u.photo AS user_photo, 
                c.energy_type AS car_energy_type, 
                c.model AS car_model, 
                c.color AS car_color,
                b.label AS brand_label
            FROM {$this->tableName} r
            JOIN user u ON r.id_driver = u.id_user
            JOIN car c ON r.id_car = c.id_car
            JOIN brand b ON c.id_brand = b.id_brand
            WHERE r.id_ridesharing = :id_ridesharing";

        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(":id_ridesharing", $idRidesharing, \PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);


        if ($result) 
        {
            $userData = [];
            $carData = [];
            $ridesharingData = [];
            $brandData=[];

            foreach ($result as $key => $value) 
            {
                if (str_starts_with($key, 'user_')) {
                    $userData[substr($key, 5)] = $value; // Enlève "user_"
                }elseif (str_starts_with($key, 'car_')) {
                    $carData[substr($key, 4)] = $value; // Enlève "car_"
                }elseif (str_starts_with($key, 'brand_')) {
                    $brandData[substr($key, 6)] = $value; // Enlève "car_"
                }else {
                    $ridesharingData[$key] = $value;
                }
            }
            
            $ridesharingInfo = RidesharingModel::createAndHydrate($ridesharingData);
            $driverInfo = UserModel::createAndHydrate($userData);
            $brandCar = $brandData['label'];
            $carInfo = CarModel::createAndHydrate($carData);
                
            return $ridesharing[] = [
                'ridesharing'=>$ridesharingInfo,
                'driver'=>$driverInfo,
                'car'=>$carInfo,
                'brand'=>$brandCar
            ];
        }

        return null; // Retourne null si aucun résultat n'est trouvé
    }

    /**
     * Trouve les covoiturages d'un conducteur.
     * 
     * @param int $idDriver
     * @return RidesharingModel[]|null
     * On utilise LEFT JOIN pour afficher même les trajets sans participant.
     */
    public function findRidesharingByDriver(int $idDriver): ?array
    {
        $query = "SELECT r.status,
                    r.id_ridesharing,
                    r.departure_date,
                    r.departure_city,
                    r.arrival_city,
                    r.price_per_seat,
                    COALESCE(COUNT(DISTINCT p.id_participant), 0) AS nb_participants
                    FROM {$this->tableName} r
                    LEFT JOIN participate p
                    ON p.id_ridesharing = r.id_ridesharing
                    AND p.confirmed = true         -- Pour ne pas compter des participations qui ne seraient pas encore validée.
                    WHERE r.id_driver = :id_driver
                    GROUP BY
                    r.id_ridesharing,
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
        $stmt -> bindValue(':id_driver', $idDriver);
        $stmt -> execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result)
        {
            $ridesharingList = [];
            foreach ($result as $row)
            {
                $ridesharing = RidesharingModel::createAndHydrate($row);
                $ridesharingList[] = $ridesharing;
            }
            return $ridesharingList;
        }
        return null;
    }

    /**
     * Trouve les covoiturages auxquels un participant est inscrit.
     * 
     * @param int $idParticipant L'identifiant du participant.
     * @return ParticipateModel[]|null Un tableau d'instances de ParticipateModel avec les détails des trajets, ou null si aucun trajet n'est trouvé.
     */
    public function findRidesharingByParticipant(int $idParticipant): ?array
    {
        $query = "SELECT p.nb_seats AS participate_nb_seats,
                    r.status,
                    r.departure_date,
                    r.departure_city,
                    r.arrival_city,
                    r.price_per_seat,
                    FROM participate p
                    LEFT JOIN {$this->tableName} r
                    ON r.id_ridesharing = p.id_ridesharing
                    AND p.confirmed = true         -- Pour ne pas compter des participations qui ne seraient pas encore validée.
                    WHERE p.id_participant = :id_participant
                    GROUP BY
                    p.nb_seats,
                    p.created_at,
                    p.nb_seats,
                    r.status,
                    r.departure_date,
                    r.departure_city,
                    r.arrival_city,
                    r.price_per_seat,
                    ORDER BY
                    CASE
                        WHEN r.status = 'ongoing' THEN 1
                        WHEN r.status = 'pending' THEN 2
                        ELSE 3
                    END,
                    r.departure_date ASC";

        $stmt = $this->pdo->prepare($query);
        $stmt -> bindValue(':id_participant', $idParticipant);
        $stmt -> execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result)
        {    
            $paricipateData = [];
            $ridesharingData = [];
                
                foreach ($result as $key => $value)
                {
                    if (str_starts_with($key, 'participant_')) {
                        $paricipateData[substr($key, 12)] = $value;
                    } else {
                        $ridesharingData[$key] = $value;
                    }
                }
                $participation = ParticipateModel::createAndHydrate($paricipateData);
                $ridesharing = RidesharingModel::createAndHydrate($ridesharingData);
                
                return $participationList[] = [
                    'participant'=>$participation,
                    'ridesharing'=>$ridesharing
                ];
        }
        return null;
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
    public function startRide(int $rideId): bool
    {
        $sql = "UPDATE {$this->tableName}
                SET status = 'ongoing'
                WHERE id_ridesharing = :id_ridesharing 
                AND status = 'pending'";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute(['id_ridesharing' => $rideId]);
    }
}