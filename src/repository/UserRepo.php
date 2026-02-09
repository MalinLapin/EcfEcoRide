<?php

namespace App\repository;

use App\model\Role;
use App\model\Status;
use App\model\UserModel;

class UserRepo extends BaseRepoSql
{
    protected string $tableName = 'user';
    protected string $className = UserModel::class;
    
    /**
     * Récupère un utilisateur par son pseudo.
     * @param string $pseudo Le pseudo de l'utilisateur à récupérer.
     * @return UserModel|null L'instance de UserModel correspondant à l'utilisateur, ou null si l'utilisateur n'existe pas.
     * 
     * Cette méthode prépare une requête SQL pour récupérer un utilisateur en fonction de son pseudo.
     */
    public function getUserByPseudo(string $pseudo): ?UserModel
    {
        $query = "SELECT * FROM {$this->tableName} WHERE pseudo = :pseudo";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':pseudo', $pseudo , \PDO::PARAM_STR);
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
     *
     * Cette méthode prépare une requête SQL pour récupérer un utilisateur en fonction de son adresse e-mail.
     */
    public function getUserByEmail(string $email): ?UserModel
    {
        $query = "SELECT * FROM {$this->tableName} WHERE email = :email";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            return UserModel::createAndHydrate($data);
        } else {
            return null; // Retourne null si l'utilisateur n'existe pas
        }
    }

    public function findUserByRole(Role $role): array
    {
        $query ="SELECT * FROM {$this->tableName} WHERE role = :role";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':role', $role->value , \PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($result) {

            $users = [];

            foreach ($result as $row){

                $users[] = UserModel::createAndHydrate($row);
            }
        } 
        return $users;
    }

}

