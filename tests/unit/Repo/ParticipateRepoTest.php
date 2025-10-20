<?php

use App\model\ParticipateModel;
use App\repository\ParticipateRepo;
use App\repository\BaseRepoSql;
use App\model\UserModel;
use App\model\RidesharingModel;
use PHPUnit\Framework\TestCase;

class ParticipateRepoTest extends TestCase
{
    private static PDO $pdo;

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

    /**
     * Méthode de test pour vérifier la création d'une participation.
     * @return void
     * Cette méthode teste la création d'une nouvelle participation dans la base de données en utilisant le ParticipateRepo.
     * Elle crée un objet ParticipateRepo avec des données fictives, appelle la méthode create du BaseRepo,
     * puis vérifie que la participation a bien été créée en la recherchant par son ID.
     * Elle s'assure également que les propriétés de la participation créée correspondent aux données fournies.
     */
    public function testCreateParticipate(): void
    {
        $repo = new ParticipateRepo(self::$pdo);
        $newParticipate = new ParticipateModel();

        $newParticipate->setIdParticipant(1);
        $newParticipate->setIdRidesharing(1);
        $newParticipate->setCreatedAt(new DateTimeImmutable());
        $newParticipate->setConfirmed(false);

        // Crée la participation dans la BDD
        $createdParticipate = $repo->create($newParticipate);

        // Récupère la participation créée depuis la BDD
        $fetchedParticipate = $repo->findById($createdParticipate);

        // Vérifie que la participation a été créée et que les propriétés correspondent
        $this->assertNotNull($fetchedParticipate);
        $this->assertEquals(1, $fetchedParticipate->getIdParticipant());
        $this->assertEquals(1, $fetchedParticipate->getIdRidesharing());
        $this->assertFalse($fetchedParticipate->isConfirmed());
    }

    /**
     * Méthode de test pour vérifier la confirmation d'une participation.
     * @return void
     * Cette méthode teste la confirmation d'une participation dans la base de données en utilisant le ParticipateRepo.
     * Elle crée un objet ParticipateRepo, appelle la méthode confirmParticipation,
     * puis vérifie que la participation a bien été confirmée en la recherchant par son ID.
     * Elle s'assure également que le champ "confirmed" de la participation est passé à true.
     */
    public function testConfirmParticipation(): void
    {
        $repo = new ParticipateRepo(self::$pdo);
        $userId = 2; // ID de l'utilisateur participant
        $rideId = 1; // ID du trajet de covoiturage

        // Confirme la participation
        $result = $repo->confirmParticipation($userId, $rideId);
        $this->assertTrue($result, "La confirmation de la participation a échoué.");

        // Récupère la participation confirmée depuis la BDD
        $query = "SELECT * FROM participate WHERE id_participant = :id_participant AND id_ridesharing = :id_ridesharing";
        $stmt = self::$pdo->prepare($query);
        $stmt->bindValue(':id_participant', $userId);
        $stmt->bindValue(':id_ridesharing', $rideId);
        $stmt->execute();
        $fetchedParticipateData = $stmt->fetch(PDO::FETCH_ASSOC);

        // Vérifie que la participation a été confirmée
        $this->assertNotFalse($fetchedParticipateData, "Aucune participation trouvée pour l'utilisateur et le trajet spécifiés.");
        $this->assertEquals(1, (int)$fetchedParticipateData['confirmed'], "La participation n'a pas été confirmée.");
    }

    /**
     * Méthode de test pour vérifier la décrémentation du solde de crédit d'un utilisateur.
     * @return void
     * Cette méthode teste la décrémentation du solde de crédit d'un utilisateur dans la base de données en utilisant le ParticipateRepo.
     * Elle crée un objet ParticipateRepo, appelle la méthode decrementCreditBalance,
     * puis vérifie que le solde de crédit de l'utilisateur a bien été décrémenté en le recherchant par son ID.
     * Elle s'assure également que le solde de crédit est correct après la décrémentation.
     */
    public function testDecrementCreditBalance(): void
    {
        $repo = new ParticipateRepo(self::$pdo);
        $userId = 1; // ID de l'utilisateur
        $initialBalance = 100; // Solde initial pour l'utilisateur dans les fixtures
        $decrementAmount = 30; // Montant à décrémenter

        // Décrémente le solde de crédit
        $result = $repo->decrementCreditBalance($userId, $decrementAmount);
        $this->assertTrue($result, "La décrémentation du solde de crédit a échoué.");

        // Récupère l'utilisateur depuis la BDD pour vérifier le solde de crédit
        $userRepo = new class(self::$pdo) extends BaseRepoSql {
            protected string $tableName = 'user';
            protected string $className = UserModel::class;
        };
        $fetchedUser = $userRepo->findById($userId);

        // Vérifie que le solde de crédit a été décrémenté correctement
        $this->assertNotNull($fetchedUser, "Utilisateur non trouvé.");
        $expectedBalance = $initialBalance - $decrementAmount;
        $this->assertEquals($expectedBalance, $fetchedUser->getCreditBalance(), "Le solde de crédit n'a pas été décrémenté correctement.");
    }

    /**
     * Méthode de test pour vérifier l'incrémentation des places disponibles d'un trajet de covoiturage.
     * @return void
     * Cette méthode teste l'incrémentation des places disponibles d'un trajet de covoiturage dans la base de données en utilisant le ParticipateRepo.
     * Elle crée un objet ParticipateRepo, appelle la méthode incrementSeats,
     * puis vérifie que le nombre de places disponibles a bien été incrémenté en recherchant le trajet par son ID.
     * Elle s'assure également que le nombre de places disponibles est correct après l'incrémentation.
     */
    public function testIncrementSeats(): void
    {
        $repo = new ParticipateRepo(self::$pdo);
        $rideId = 1; // ID du trajet de covoiturage
        $initialSeats = 3; // Nombre initial de places disponibles pour le trajet dans les fixtures
        $incrementAmount = 2; // Nombre de places à incrémenter

        // Incrémente les places disponibles
        $result = $repo->incrementSeats($rideId, $incrementAmount);
        $this->assertTrue($result, "L'incrémentation des places disponibles a échoué.");

        // Récupère le trajet depuis la BDD pour vérifier le nombre de places disponibles
        $rideRepo = new class(self::$pdo) extends BaseRepoSql {
            protected string $tableName = 'ridesharing';
            protected string $className = RidesharingModel::class;
        };
        $fetchedRide = $rideRepo->findById($rideId);

        // Vérifie que le nombre de places disponibles a été incrémenté correctement
        $this->assertNotNull($fetchedRide, "Trajet non trouvé.");
        $expectedSeats = $initialSeats + $incrementAmount;
        $this->assertEquals($expectedSeats, $fetchedRide->getAvailableSeats(), "Le nombre de places disponibles n'a pas été incrémenté correctement.");
    }


}
