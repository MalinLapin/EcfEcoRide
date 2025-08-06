<?php

use App\Model\Role;
use App\repository\UserRepo;
use App\Model\UserModel;
use PHPUnit\Framework\TestCase;

class UserRepoTest extends TestCase
{
    
    private  static PDO $pdo;

    /**
     * Méthode statique pour configurer la connexion à la base de données avant l'exécution des tests.
     * @return void
     * Cette méthode est appelée une seule fois avant l'exécution de tous les tests de cette classe.
     * Elle initialise la connexion à la base de données en utilisant les variables d'environnement définies dans le fichier .
     */
    protected function setUp(): void
    {
        // Connexion à la BDD test (garde ça cohérent avec ta config)
        $dbHost = $_ENV['DB_HOST'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASSWORD'];

        
        $firstPdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);

        // Lis tout le contenu de ton script de fixtures
        $sql = file_get_contents(__DIR__ . '/../../fixtures/DataBaseTest.sql');
        // Exécute le script (DROP + CREATE + INSERT)
        $firstPdo->exec($sql);

        // Nouvelle connexion à la BDD fraichement créée !
        self::$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    }


    //---------------------test CRUD générique----------------------


    /**
     * Méthode de test pour vérifier la création d'un utilisateur.
     * @return void
     * Cette méthode teste la création d'un nouvel utilisateur dans la base de données en utilisant le UserRepo.
     * Elle crée un objet UserModel avec des données fictives, appelle la méthode create du BaseRepo,
     * puis vérifie que l'utilisateur a bien été créé en le recherchant par son email.
     * Elle s'assure également que les propriétés de l'utilisateur créé correspondent aux données fournies.
     */
    public function testCreateUser() : void
    {
        $repo = new UserRepo(self::$pdo);
        $newUser = new UserModel();
        $newUser->setLastName('Durand');
        $newUser->setFirstName('Julie');
        $newUser->setPseudo('julie33');
        $newUser->setEmail('julie@test.fr');
        $newUser->setPassword('password123');
        $newUser->setRole(Role::user);
        $newUser->setIsActive(true);
        $newUser->setCreditBalance(100);
        $newUser->setPhoto('path/to/photo.jpg');
        $newUser->setGrade(4.5);
        $newUser->setCreatedAt(new DateTimeImmutable());
        $repo->create($newUser);

        // Vérifie que l'utilisateur a été créé en le recherchant par email
        $createdUser = $repo->getUserByEmail('julie@test.fr');
        $this->assertNotNull($createdUser);
        $this->assertSame('Durand', $createdUser->getLastName());
        $this->assertSame('Julie', $createdUser->getFirstName());
        $this->assertSame('julie33', $createdUser->getPseudo());
        $this->assertSame('path/to/photo.jpg', $createdUser->getPhoto());
        $this->assertEquals(4.5, $createdUser->getGrade(), '', 0.01);
        $this->assertTrue($createdUser->getIsActive());
        $this->assertSame(Role::user, $createdUser->getRole());
    }


    /**
     * Méthode de test pour la modification d'un utilisateur.
     * @return void
     * Cette méthode test la mise à jour (modification) dans la base de données en utilisant le UserRepo.
     * Elle récupère un User via son email, lui modifie de données et appelle la méthode update du BaseRepo,
     * puis vérifie que l'utilisateur a bien été modifier.
     * Elle s'assur également que les propriété correspondent aux données modifiées.
     */
    public function testUpdateUser() : void
    {
        // On récupère par l'email un utilisateur présent dans la Bdd
        $repo = new UserRepo(self::$pdo);
        $user = $repo->getUserByEmail('elina.uny@test.com');
        $this->assertNotNull($user);

        // On change différente données
        $user->setFirstName('elina');
        $user->setCreditBalance(120);

        // On lance la requête via la méthode update()
        $success = $repo ->update($user);
        $this->assertTrue($success);

        // Relire depuis la BDD pour vérifier
        $updatedUser = $repo->getUserByEmail('elina.uny@test.com');
        $this->assertSame('elina', $updatedUser->getFirstName());
        $this->assertSame(120, $updatedUser->getCreditBalance());

    }

    /**
     * Méthode de test pour la modification d'un utilisateur qui n'existe pas en bdd
     * @return void
     * On créer un User fictif que nous cherchons à modifier.
     * On verifie si la modification est un succes ou non.
     */
    public function testErrorUpdateUser() :void
    {
        $repo = new UserRepo(self::$pdo);
        $user = new UserModel();
        $user->setIdUser(8);
        $user->setLastName('User');
        $user->setFirstName('test');
        $user->setPseudo('testUser');
        $user->setEmail('test@user.fr');
        $user->setPassword('nopass');
        $user->setRole(Role::user);
        $user->setIsActive(true);
        $user->setCreditBalance(14);
        $user->setPhoto('none.jpg');
        $user->setGrade(0.0);
        $user->setCreatedAt(new DateTimeImmutable());
                
        $success = $repo->update($user);
        $this->assertFalse($success);
    }


    /**
     * Méthode de test pour la suppréssion d'un utilisateur.
     * @return void
     * On récupère un User via son email, puis on récupère son id.
     * On utilise la méthode delete() en indiquant l'id de l'utilisateur.
     * On test si on retrouve l'id de l'utilisateur après l'avoir supprimer. 
     */
    public function testDeleteUser():void
    {
        $repo = new UserRepo(self::$pdo);

        // Récupère un utilisateur par son email pour effectuer la suppression
        $created = $repo->getUserByEmail('milka.uny@test.com');
        $this->assertNotNull($created);
        $userId = $created->getIdUser();

        // On utilise la méthode du suppréssion.
        $deleted = $repo->delete($userId);
        $this->assertTrue($deleted);

        // Vérifie que l'utilisateur a bien disparu
        $shouldBeGone = $repo->findById($userId);
        $this->assertNull($shouldBeGone);
    }

    /**
     * Méthode de test de suppréssion d'un utilisateur qui n'existe pas
     * @return void
     * On récupère un Id qui ne correspond a aucune entré en Bdd
     * On test si on a bien une erreur.
     */
    public function testDeleteFakeUser():void
    {
        $repo = new UserRepo(self::$pdo);

        // On demande la supprésion d'un id qui n'est pas encore existant donc d'une entré en Bdd qui n'existe pas ou n'existe plus.
        $deleted = $repo->delete(8); 
        $this->assertFalse($deleted);
    }

    /**
     * Récupération de l'utilisateur par son identifiant numérique.
     */
    public function testFindById():void
    {
        $repo = new UserRepo(self::$pdo);

        $user = $repo->findById(1);
        $this->assertInstanceOf(UserModel::class, $user, "Devrait retourner un objet UserModel.");
    }


    /**
     * Test de récupération d'un utilisateur inexistant (par ID)
     */
    public function testFindByIdReturnsNullIfNotExist()
    {
        $repo = new UserRepo(self::$pdo);
        $user = $repo->findById(-42); // ID négatif censé ne rien renvoyer
        $this->assertNull($user);
    }

    /**
     * Récupère la liste de tous les utilisateurs (find all).
     */
    public function testFindAllReturnsArrayOfUsers()
    {
        $repo = new UserRepo(self::$pdo);
        $users = $repo->findAll();
        $this->assertIsArray($users);
        $this->assertNotEmpty($users);
        $this->assertInstanceOf(UserModel::class, $users[0]);
    }


    //--------------------- test méthode spécifique a UserRepo ----------------------



    /**
     * Méthode de test pour vérifier que la méthode getUserByEmail retourne l'utilisateur attendu.
     * @return void
     * Cette méthode teste le comportement de la méthode getUserByEmail lorsqu'elle est appelée avec un email
     * qui existe dans la base de données. Elle s'assure que la méthode retourne un objet UserModel
     * avec les propriétés correctes, notamment le nom et le prénom de l'utilisateur.
     */
    public function testGetByEmail(): void
    {
        $repo = new UserRepo(self::$pdo);
        $user = $repo->getUserByEmail('axel.uny@test.com');
        $this->assertNotNull($user);
        $this->assertSame('Uny', $user->getLastName());
        $this->assertSame('Axel', $user->getFirstName());
    }

    /**
     * Méthode de test pour vérifier que la méthode getUserByEmail retourne null si l'utilisateur n'existe pas.
     * @return void
     * Cette méthode teste le comportement de la méthode getUserByEmail lorsqu'elle est appelée avec un email
     * qui n'existe pas dans la base de données. Elle s'assure que la méthode retourne null
     * au lieu de lancer une exception, ce qui est le comportement attendu dans ce cas.
     */
    public function testGetByEmailIfNotExist(): void
    {
        $repo = new UserRepo(self::$pdo);
        $user = $repo->getUserByEmail('inexistant@ecoride.fr');
        $this->assertNull($user);
    }

    /**
     * Méthode de test pour vérifier que la méthode getUserByPseudo retourne l'utilisateur attendu.
     * @return void
     * Cette méthode teste le comportement de la méthode getUserByPseudo lorsqu'elle est appelée avec un pseudo
     * qui existe dans la base de données. Elle s'assure que la méthode retourne un objet UserModel
     * avec les propriétés correctes, notamment le nom et le prénom de l'utilisateur.
     */
    public function testGetByPseudo(): void
    {
        $repo = new UserRepo(self::$pdo);
        $user = $repo->getUserByPseudo('marcuny');
        $this->assertNotNull($user);
        $this->assertSame('Uny', $user->getLastName());
        $this->assertSame('Marc', $user->getFirstName());
    }

    /**
     * Méthode de test pour vérifier que la méthode getUserBypseudo retourne null si l'utilisateur n'existe pas.
     * @return void
     * Cette méthode teste le comportement de la méthode getUserByPseudo lorsqu'elle est appelée avec un pseudo
     * qui n'existe pas dans la base de données. Elle s'assure que la méthode retourne null
     * au lieu de lancer une exception, ce qui est le comportement attendu dans ce cas.
     */
    public function testGetByPSeudoIfNotExist(): void
    {
        $repo = new UserRepo(self::$pdo);
        $user = $repo->getUserByPseudo('fakePseudo');
        $this->assertNull($user);
    }

}