<?php

use App\model\RidesharingModel;
use App\model\Status;
use App\repository\RidesharingRepo;
use PHPunit\Framework\TestCase;

class RidesharingRepoTest extends TestCase
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
     * Méthode de test pour vérifier la création d'un trajet.
     * @return void
     * Cette méthode teste la création d'un nouveau trajet dans la base de données en utilisant le RidesharingRepo.
     * Elle crée un objet RidesharingRepo avec des données fictives, appelle la méthode create du BaseRepo,
     * puis vérifie que le trajet a bien été créé en le recherchant par son adress de départ.
     * Elle s'assure également que les propriétés du trajet créé correspondent aux données fournies.
     */
    public function testCreateRideSharing() : void
    {
        $repo = new RidesharingRepo(self::$pdo);
        $newRide = new RidesharingModel();

        $newRide->setDepartureDate(new DateTimeImmutable('2026-10-02 09:00:00'));
        $newRide->setDepartureCity('Digna');
        $newRide->setDepartureAddress('11 Rue du prélion');
        $newRide->setArrivalCity('Beaufort-Orbagna');
        $newRide->setArrivalAddress('8 avenue Augustin');
        $newRide->setAvailableSeats(2);
        $newRide->setPricePerSeat(3);
        $newRide->setCreatedAt(new DateTimeImmutable());
        $newRide->setIdDriver(1);
        $newRide->setIdCar(4);

        $repo->create($newRide);

        //Vérifie le covoiturage en le recherchant par sont adresse d'arriver
        $creatRidesharing = $repo->getRidesharingByParams(['arrival_address'=>'8 avenue Augustin']);
        $this->assertNotNull($creatRidesharing);
        //On recherche dans le tableau de trajet si le premier élément a comme adresse d'arriver ce qu'on lui demande.
        $this->assertSame('8 avenue Augustin', $creatRidesharing[0]->getArrivalAddress());
        //On souhaite vérifer que le status soit bien "pending" lors de la création meme si celui ci n'est pas spécifier.
        $this->assertSame(Status::pending, $creatRidesharing[0]->getStatus());
        //On verifie le nombre de place
        $this->assertEquals(2, $creatRidesharing[0]->getAvailableSeats());
        
    }

    /**
     * Méthode de test pour la modification d'un covoiturage.
     * @return void
     * Cette méthode test la mise à jour (modification) dans la base de données en utilisant le RidesharingRepo.
     * Elle récupère un Ridesharing via sa ville d'arriver, lui modifie des données et appelle la méthode update du BaseRepo,
     * puis vérifie que le trajet à bien été modifier.
     * Elle s'assure également que les propriétés correspondent aux données modifiées.
     */
    public function testUpdateRidesharing() : void
    {
        // On récupère par l'email un utilisateur présent dans la Bdd
        $repo = new RidesharingRepo(self::$pdo);
        $rideSharing = $repo->getRidesharingByParams(['arrival_address'=>'456 Avenue de Lyon']);
        $this->assertNotNull($rideSharing);

        // On change différente données
        $rideSharing[0]->setAvailableSeats(4);
        $rideSharing[0]->setPricePerSeat(60);
        $rideSharing[0]->setArrivalCity('Besançon');

        // On lance la requête via la méthode update()
        $success = $repo ->update($rideSharing[0]);
        $this->assertTrue($success);

        // Relire depuis la BDD pour vérifier
        $updatedUser = $repo->getRidesharingByParams(['arrival_city'=>'Besançon']);
        $this->assertSame(4, $updatedUser[0]->getAvailableSeats());
        $this->assertSame(60, $updatedUser[0]->getPricePerSeat());

    }

    /**
     * Méthode de test pour la modification d'un trajet qui n'existe pas en bdd
     * @return void
     * On créer un trajet fictif que nous cherchons à modifier.
     * On verifie si la modification est un succes ou non.
     */
    public function testErrorUpdateRidesharging() :void
    {
        $repo = new RidesharingRepo(self::$pdo);
        $newRide = new RidesharingModel();

        $newRide->setIdRidesharing(9);
        $newRide->setDepartureDate(new DateTimeImmutable('2025-10-02 09:00:00'));
        $newRide->setDepartureCity('Strasbourg');
        $newRide->setDepartureAddress('11 Rue du prélion');
        $newRide->setArrivalCity('Strasbourg');
        $newRide->setArrivalAddress('8 avenue Augustin');
        $newRide->setAvailableSeats(2);
        $newRide->setPricePerSeat(4);
        $newRide->setCreatedAt(new DateTimeImmutable());
        $newRide->setIdDriver(4);
        $newRide->setIdCar(2);

        $success = $repo->update($newRide);
        $this->assertFalse($success);
    }



    //--------------------- test méthode spécifique a UserRepo ----------------------


    /**
     * Méthode de test pour la recherche de trajet
     * @return void
     * On recherche un trajet dans notre Bdd
     * On verifie si la recherche est un succes ou non.
     */
    public function testGetRidesaringByParams() :void
    {
        $repo = new RidesharingRepo(self::$pdo);
        //On va tester une recherche en fonction du prix de la place
        $rideSharingPerPrice = $repo->getRidesharingByParams(['price_per_seat'=>'20']);
        $this->assertCount(5, $rideSharingPerPrice); // J'ai 5 fixtures qui ont un prix inférieur ou égale à 20.

        $rideSharing = $repo->getRidesharingByParams([
            'departure_city'=>'Paris',  // 3 trajet en base de donnée
            'departure_address'=>'123 Rue de Paris', // Idem
            'arrival_city'=>'Lyon', // Idem
            'arrival_address'=>'456 Avenue de Lyon', // Plus que 2 trajet.
            'available_seats'=>1 // Je n'est plus qu'un trajet avec au moins une place restante.
        ]);
        $this->assertCount(1, $rideSharing);
    }

    /**
     * Méthode de test la fin d'un trajet
     * @return void
     * On recherche un trajet dans notre Bdd
     * On verifie si la recherche est un succes ou non.
     */
    public function testEndRide() :void
    {
        $repo = new RidesharingRepo(self::$pdo);
        $newRide = new RidesharingModel();

        $newRide->setDepartureDate(new DateTimeImmutable('2026-10-02 09:00:00'));
        $newRide->setDepartureCity('Digna');
        $newRide->setDepartureAddress('11 Rue du prélion');
        $newRide->setArrivalCity('Beaufort-Orbagna');
        $newRide->setArrivalAddress('8 avenue Augustin');
        $newRide->setAvailableSeats(2);
        $newRide->setPricePerSeat(3);
        $newRide->setCreatedAt(new DateTimeImmutable());
        $newRide->setIdDriver(1);
        $newRide->setIdCar(4);

        $idNewRide = $repo->create($newRide);     

        $repo->endRide($idNewRide);
        

        $updatedRide = $repo->findById($idNewRide); // Recharge depuis la BDD

        $this->assertInstanceOf(RidesharingModel::class, $updatedRide);

        $this->assertSame(Status::completed, $updatedRide->getStatus()); 
        
    }

    /**
     * Méthode de test pour l'annulation d'un trajet
     * @return void
     * 
     * On recherche un trajet ou une liste de trajet dans notre Bdd
     * On applique la méthode cancelRide()
     * On récupere à nouveau notre trajet mis a jour depuis la Bdd puis on test son changement de status.
     */
    public function testCancelRide():void
    {
        $repo = new RidesharingRepo(self::$pdo);
        $listRide = $repo->getRidesharingByParams(['id_driver'=>3]); // On recherche les trajet d'un conducteur par son Id
        
        $rideToCancelled = $repo -> findById($listRide[0]->getIdRidesharing()); // On recherche l'id du trajet dans la liste trouver précédement.

        $repo->cancelRide($rideToCancelled->getIdRidesharing());

        $updatedRide = $repo->findById($rideToCancelled->getIdRidesharing());

        $this->assertSame(Status::cancelled, $updatedRide->getStatus());

    }

    /**
     * Méthode de test pour le lancement d'un trajet
     *
     * @return void 
     * On recherche le trajet par son id
     * On applique la méthode setOngoing()
     * On récupere à nouveau notre trajet mis a jour depuis la Bdd puis on test son changement de status.
     */
    public function testSetRideOngoing():void
    {
        $repo = new RidesharingRepo(self::$pdo);
        $ride = $repo->findById(3); // On recherche un trajet par son Id

        $repo->setRideOngoing($ride->getIdRidesharing());

        $updatedRide = $repo->findById($ride->getIdRidesharing());
        
        $this->assertSame(Status::ongoing, $updatedRide->getStatus());
    }   
    
}