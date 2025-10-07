<?php

namespace App\model;


use DateTimeImmutable;
use App\security\Validator;

/**
 * Enumération pour représenter les différents rôles d'un utilisateur.
 * Cette énumération permet de définir clairement les rôles possibles d'un utilisateur,
 * ce qui aide à éviter les erreurs de typage et de logique dans le code.
 */
enum Role: string
{
        case user = 'user'; // Rôle par défaut pour les utilisateurs normaux.
        case admin = 'admin'; // Rôle pour les administrateurs du système.
        case employe = 'employe';// Rôle pour les employés du service client ou de la gestion des covoiturages.
}

/**
 * Fichier contenant notre classe UserModel qui étend la classe BaseModel.
 * Cette classe représente un utilisateur du système.
 */
class UserModel extends BaseModel
{
    private ?int $idUser = null; // Identifiant de l'utilisateur, initialisé à null.
    private string $pseudo; // Pseudo de l'utilisateur.
    private string $email; // Adresse e-mail de l'utilisateur.
    private string $password; // Mot de passe de l'utilisateur.
    private DateTimeImmutable $createdAt; // Date de création du compte utilisateur.
    private int $creditBalance; //Solde de crédit
    private ?string $photo; //String car chemin pour la photo et non la photo elle meme.
    private ?float $grade; //Note générale de l'utilisateur
    private bool $isActive = true; //Etat de l'utilisateur (actif ou suspendu).
    private Role $role = Role::user; // Rôle de l'utilisateur


    public function __construct(array $data = [])
    {
        $this->hydrate($data);    
    }

    
    /**
     * Get the value of idUser
     */
    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    /**
     * Set the value of idUser
     */
    public function setIdUser(?int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

    /**
     * Get the value of pseudo
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * Set the value of pseudo
     */
    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get the value of email
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Set the value of email
     * On vérifie le format de l'email via le service validator.
     */
    public function setEmail(string $email): self
    {
        if (Validator::validateEmail($email)){
            $this->email = $email;            
        }
        return $this;
    }

    /**
     * Get the value of password
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Set the value of password
     * On Vérifie la complexiter du mot de passe en passant par le service de validation
     * Puis on hash le mot de passe en séléctionnant le PASSWORD_DEFAULT afin d'avoir le dernier procéder de hashage fournie par PHP.
     */
    public function setPassword(string $password): self
    {
        if (Validator::validatePasswordStrength($password))
        {
            $this->password = password_hash($password,PASSWORD_DEFAULT);
        }

        return $this;
    }

    /**
     * Setter du Password une fois hasher.
     */
    public function setHashedPassword(string $hashedPassword): self
    {
        // Méthode explicite pour hash depuis BDD
        $this->password = $hashedPassword;
        return $this;
    }

    /**
     * Get the value of createdAt
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * Set the value of createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get the value of creditBalance
     */
    public function getCreditBalance(): int
    {
        return $this->creditBalance;
    }

    /**
     * Set the value of creditBalance
     */
    public function setCreditBalance(int $creditBalance): self
    {
        $this->creditBalance = $creditBalance;

        return $this;
    }

    /**
     * Get the value of photo
     */
    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    /**
     * Set the value of photo
     */
    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get the value of grade
     */
    public function getGrade(): ?float
    {
        return $this->grade;
    }

    /**
     * Set the value of grade
     */
    public function setGrade(?float $grade): self
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get the value of isActive
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * Set the value of isActive
     */
    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the value of role
     */
    public function getRole(): Role
    {
        return $this->role;
    }

    /**
     * Set the value of role
     */
    public function setRole(Role $role): self
    {
        $this->role = $role;

        return $this;
    }
}