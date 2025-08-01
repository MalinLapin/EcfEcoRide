<?php

use App\repository\UserRepo;
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
    public static function setUpBeforeClass(): void
    {
        // Connexion à la BDD test (garde ça cohérent avec ta config)
        $dbHost = $_ENV['DB_HOST'];
        $dbName = $_ENV['DB_NAME'];
        $dbUser = $_ENV['DB_USER'];
        $dbPass = $_ENV['DB_PASSWORD'];

        
        $firstPdo = new PDO("mysql:host=$dbHost", $dbUser, $dbPass);
        var_dump(__DIR__ . '/../../fixtures/DataBaseTest.sql');

        // Lis tout le contenu de ton script de fixtures
        $sql = file_get_contents(__DIR__ . '/../../fixtures/DataBaseTest.sql');
        // Exécute le script (DROP + CREATE + INSERT)
        $firstPdo->exec($sql);

        // Nouvelle connexion à la BDD fraichement créée !
        self::$pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    }

    /**
     * Méthode de test pour vérifier que la méthode getUserByEmail retourne l'utilisateur attendu.
     * @return void
     * Cette méthode teste le comportement de la méthode getUserByEmail lorsqu'elle est appelée avec un email
     * qui existe dans la base de données. Elle s'assure que la méthode retourne un objet UserModel
     * avec les propriétés correctes, notamment le nom et le prénom de l'utilisateur.
     */
    public function testFindByEmailReturnsExpectedUser()
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
    public function testFindByEmailReturnsNullIfNotExist()
    {
        $repo = new UserRepo(self::$pdo);
        $user = $repo->getUserByEmail('inexistant@ecoride.fr');
        $this->assertNull($user);
    }
}