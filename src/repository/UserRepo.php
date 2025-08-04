<?php

namespace App\repository;
use App\model\UserModel;




class UserRepo extends Repository
{
    protected string $tableName = 'user';
    protected string $className = UserModel::class;
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
            return null;
        }        
    }
    
    /**
     * Récupère un utilisateur par son adresse e-mail.
     * @param string $email L'adresse e-mail de l'utilisateur à récupérer.
     * @return UserModel|null L'instance de UserModel correspondant à l'utilisateur, ou null si l'utilisateur n'existe pas.
     */
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

