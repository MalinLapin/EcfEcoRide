<?php

namespace App\repository;

use App\model\CarModel;
use App\model\BrandModel;


class CarRepo extends BaseRepoSql
{
    protected string $tableName = 'car';
    protected string $className = CarModel::class;

    public function findListCarByUserId(int $idUser): ?array
    {
        $query = "SELECT c.*,
                        b.label AS brand_label
                    FROM {$this->tableName} c
                    JOIN brand b ON c.id_brand = b.id_brand
                    JOIN user u ON c.id_user = u.id_user
                    WHERE u.id_user = :id_user";

        $stmt = $this->pdo->prepare($query);
        $stmt -> bindValue(':id_user', $idUser,\PDO::PARAM_INT);
        $stmt -> execute();

        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if($result)
        {    
            foreach ($result as $row) 
            {
                $carData = [];
                $brandData = [];
                
                foreach ($row as $key => $value)
                {
                    if (str_starts_with($key, 'brand_')) {
                        $brandData[substr($key, 6)] = $value;
                    } else {
                        $carData[$key] = $value;
                    }
                }
                
                // Créer et assembler les objets
                $brandInfo = BrandModel::createAndHydrate($brandData);

                $carInfo = CarModel::createAndHydrate($carData);       

                $carList[] = [
                    'carInfo'=>$carInfo,
                    'brandInfo'=>$brandInfo,
                ];
            }
            return $carList;
        }
        return null;
    }
}