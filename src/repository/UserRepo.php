<?php

namespace App\repository;
use App\model\UserModel;
use App\model\Role;


class UserRepo extends Repository
{
    //1.Méthode pour récuperer un utilisateur par son pseudo
    public function getUserByPseudo(string $pseudo): ?UserModel
    {
        $query = "SELECT * FROM {$this->tableName} WHERE pseudo = :pseudo";
        $stmt = $this->pdo->prepare($query);
        $stmt->bindValue(':pseudo', $pseudo);
        $stmt->execute();
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);
        if ($data) {
            return $this->convertToUser($data);
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
            return $this->convertToUser($data);
        } else {
            throw new \Exception("L'utilisateur avec l'addresse : {$email} n'as pas été trouver.");
        }
    }

    
    //3.Méthode pour convertir un tableau de données en un objet User
    protected function convertToUser(array $data): UserModel
    {
        return new UserModel([
            'idUser' => $data['idUser'] ?? null,
            'lastName' => $data['lastName'] ?? '',
            'firstName' => $data['firstName'] ?? '',
            'pseudo' => $data['pseudo'] ?? '',
            'email' => $data['email'] ?? '',
            'password' => $data['password'] ?? '',
            'createdAt' => new \DateTimeImmutable($data['createdAt'] ?? 'now'),
            'creditBalance' => $data['creditBalance'] ?? 0,
            'photo' => $data['photo'] ?? '',
            'grade' => (float) ($data['grade'] ?? 0.0),
            'isActive' => (bool) ($data['isActive'] ?? true),
            'role' => Role::from($data['role'] ?? Role::User->value),
        ]);
    }

}