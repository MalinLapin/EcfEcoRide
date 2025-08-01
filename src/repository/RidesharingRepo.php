<?php

namespace App\repository;

use App\Model\RidesharingModel;



class RidesharingRepo extends Repository
{
    protected string $tableName = 'ridesharing';

    /**
     * Récupère les trajets de covoiturage en fonction des paramètres fournis.
     * 
     * @param array $data Tableau associatif contenant les critères de recherche.
     * @return array Un tableau d'instances de RidesharingModel correspondant aux critères de recherche.
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
         */
        $query = "SELECT r.*, u.id_user, u.pseudo, u.grade, u.photo
                FROM {$this->tableName} r
                JOIN user u ON r.id_rider = u.id_user
                WHERE r.status = 'pending'
                AND r.departure_date >= '$currentDateTime'
                AND r.available_seats > 0";

        
        //Je vais utiliser ce tableau pour construire la requête SQL dynamiquement.
        $criteria = [ 
            'departure_city'=>['r.departure_city', '=', ':departure_city'],
            'departure_adress'=>['r.departure_adress', '=', ':departure_adress'],
            'arrival_city'=>['r.arrival_city', '=', ':arrival_city'],
            'arrival_adress'=>['r.arrival_adress', '=', ':arrival_adress'],
            'price_par_seat'=>['r.price_par_seat', '<=', ':price_par_seat'],//Pour une recherche de tarif inférieur ou égale à la recherche utilisateur.
            'pseudo_driver'=>['u.pseudo', '=', ':pseudo'],
            'grade_driver' =>['u.grade', '=', ':grade'],
            'energy_type' =>['c.energy_type', '=', ':energy_type'], //Pour la recherche de voiture electrique.
        ];

        
        // On parcourt les critères pour ajouter les conditions à la requête SQL
        foreach ($criteria as $key =>[$param, $operator, $placeholder]) {
            if (isset($data[$key]) && !empty($data[$key])) {
                $query .= " AND $param $operator $placeholder";
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
        }else{
            return [];
        }; // Si aucun trajet n'est trouvé, on retourne un tableau vide.
        
        
    }
}

