<?php

use PHPUnit\Framework\TestCase;
use App\config\Database;
use App\repository\ReviewRepo;
use App\Model\ReviewModel;
use App\Model\StatusReview;
use MongoDB\Driver\Exception\BulkWriteException;

class ReviewRepoTest extends TestCase
{
    /**
     * Initialisation de la base de données et de la collection review
     * 
     * @return void
     * Cette méthode est appelée automatiquement par PHPUnit avant chaque test.
     * Elle exécute un script MongoDB pour initialiser la collection review.
     */
    public static function setUpBeforeClass(): void
    {

        $mongoUrl = $_ENV['MONGO_URL'];
        $dbName   = $_ENV['MONGO_DB'];
        $script   = __DIR__ . '/../../fixtures/initTest.mongodb';

        if (!is_file($script)) {
            self::fail("Script Mongo introuvable: $script");
        }

        // Exécute le script d'initialisation de la base de données
        $cmd = 'mongosh ' . escapeshellarg($mongoUrl)
            . ' --quiet --eval ' . escapeshellarg("use $dbName")
            . ' ' . escapeshellarg($script);

        exec($cmd, $output, $code);
        if ($code !== 0) {
            self::fail("mongosh init failed ($code):\n" . implode("\n", $output));
        }
    }

    /**
     * Test de la création et de la récupération d'un avis par son ID
     * 
     * @return void
     * Cette méthode teste la création d'un avis, sa récupération par ID,
     * et vérifie que les données sont correctement hydratées dans le modèle ReviewModel.
     */
    public function testCreateAndFindById(): void
    {
        $repo = new ReviewRepo();
        
        $id = $repo->create([
            'id_target'   => 101,
            'id_redactor' => 202,
            'status_review'     => 'pending',
            'rating'     => 5,
            'comment'    => 'Trajet nickel',
            // createdAt est ajouté dans BaseRepoMongo si absent
        ]);
        

        $this->assertNotEmpty($id);
        $doc = $repo->findById($id);
        $this->assertNotNull($doc);
        $this->assertSame(101, $doc->getIdTarget());
        $this->assertSame(202, $doc->getIdRedactor());
        $this->assertSame(StatusReview::pending, $doc->getStatusReview());
        $this->assertSame(5, $doc->getRating());
    }

    /**
     * Test des méthodes de recherche par cible, rédacteur et statut
     * 
     * @return void
     * Cette méthode teste les méthodes findByTarget, findByRedactor et findByStatus
     * pour s'assurer qu'elles retournent les avis correspondants.
     */
    public function testFinders(): void
    {
        $repo = new ReviewRepo();
        $repo->create(['id_target'=>1,'id_redactor'=>2,'status_review'=>'approved','rating'=>4,'comment'=>'Bien']);
        $repo->create(['id_target'=>1,'id_redactor'=>3,'status_review'=>'rejected','rating'=>2,'comment'=>'Bof']);
        $repo->create(['id_target'=>2,'id_redactor'=>2,'status_review'=>'approved','rating'=>5,'comment'=>'Top']);

        $byTarget = $repo->findByTarget(1);
        $this->assertCount(2, $byTarget);

        $byRedactor = $repo->findByRedactor(2);
        $this->assertCount(2, $byRedactor);

        $byStatus = $repo->findByStatus(StatusReview::approved->value);
        $this->assertGreaterThanOrEqual(2, count($byStatus));
    }

    /**
     * Test de la mise à jour et de la suppression d'un avis
     * 
     * @return void
     * Cette méthode teste la mise à jour d'un avis existant et sa suppression.
     * Elle vérifie que les modifications sont bien appliquées et que l'avis est supprimé.
     */
    public function testUpdateAndDelete(): void
    {
        $repo = new ReviewRepo();
        $id = $repo->create(['id_target'=>9,'id_redactor'=>9,'status_review'=>'pending','rating'=>3,'comment'=>'OK']);

        $updated = $repo->update($id, ['status_review' => 'approved', 'rating' => 4]);
        $this->assertTrue($updated);

        $doc = $repo->findById($id);
        $this->assertSame(StatusReview::approved, $doc->getStatusReview());
        $this->assertSame(4, $doc->getRating());

        $deleted = $repo->delete($id);
        $this->assertTrue($deleted);
        $this->assertNull($repo->findById($id));
    }

    /**
     * Nettoyage de la collection review après chaque test
     * 
     * @throws CommandException
     * @throws BulkWriteException
     * @return void
     * Cette méthode est appelée automatiquement par PHPUnit après chaque test.
     * Elle vide la collection review pour éviter les interférences entre les tests.
     */
    protected function tearDown(): void
    {
        $db = Database::getInstanceMongo();
        $db->selectCollection('review')->deleteMany([]);
    }

    /**
     * Test de la validation du schéma pour un avis invalide
     * 
     * @return void
     * Cette méthode teste que la création d'un avis avec une note invalide
     * (par exemple, une note supérieure à 5) lève une exception de validation.
     */
    public function testSchemaValidationRejectsBadRating(): void
    {
        $this->expectException(BulkWriteException::class);
        $repo = new ReviewRepo();
        $repo->create([
            'id_target'=>1,'id_redactor'=>1,'status_review'=>'pending','rating'=>10,'comment'=>'Invalid'
        ]);
    }

    
}