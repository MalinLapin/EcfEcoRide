<?php

namespace App\repository;
use App\model\UserModel;




class UserRepo extends BaseRepo
{
    protected string $tableName = 'user'; // Nom de la table dans la base de données
    
    //1.Méthode pour récuperer un utilisateur par son pseudo
    public function getUserByPseudo(string $pseudo): ?UserModel
    {
        $query = "SELECT * FROM {$this->tableName} WHERE pseudo = :pseudo";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':pseudo', $pseudo);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            return UserModel::createAndHydrate($data);
        } else {
            throw new \Exception("L'utilisateur nommé: {$pseudo} n'as pas été trouver.");
        }        
    }
    
    //2.Méthode pour récuperer un utilisateur par son email
    public function getUserByEmail(string $email): ?UserModel
    {
        $query = "SELECT * FROM {$this->tableName} WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            return UserModel::createAndHydrate($data);
        } else {
            return null; // Retourne null si l'utilisateur n'existe pas
        }
    }

}

