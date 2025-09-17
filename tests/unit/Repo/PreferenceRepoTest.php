<?php

use PHPUnit\Framework\TestCase;
use App\config\Database;
use App\repository\PreferenceRepo;
use App\model\PreferenceModel;
use MongoDB\Driver\Exception\BulkWriteException;

class PreferenceRepoTest extends TestCase
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
     * Test de la création et de la récupération d'une préférence par son ID de voiture
     * 
     * @return void
     * Cette méthode teste la création d'une préférence, sa récupération par ID de voiture,
     * et vérifie que les données sont correctement hydratées dans le modèle PreferenceModel.
     */
    public function testCreateAndFindByCar(): void
    {
        $repo = new PreferenceRepo();
        $id = $repo->create([
            'id_ridesharing' => 123,
            'label' => 'Préférence de test',
            'is_accepted' => true,
        ]);
        $this->assertIsString($id, 'L\'ID de la préférence doit être une chaîne de caractères');

        $preference = $repo->findById($id);
        $this->assertInstanceOf(PreferenceModel::class, $preference, 'Le modèle retourné doit être une instance de PreferenceModel');
        $this->assertEquals(123, $preference->getIdRidesharing(), 'L\'ID de la voiture doit correspondre à celui utilisé lors de la création');
        $this->assertEquals('Préférence de test', $preference->getLabel(), 'Le label doit correspondre à celui utilisé lors de la création');
        $this->assertTrue($preference->getIsAccepted(), 'La préférence doit être acceptée');
    }

    /**
     * Test de la récupération des préférences par leur statut d'acceptation
     * 
     * @return void
     * Cette méthode teste la récupération des préférences acceptées et non acceptées,
     * et vérifie que les données sont correctement hydratées dans le modèle PreferenceModel.
     */
    public function testFindByAccept(): void
    {
        $repo = new PreferenceRepo();
        $acceptedPreferences = $repo->findByAccept(true);
        $this->assertIsArray($acceptedPreferences, 'La méthode findByAccept doit retourner un tableau');
        foreach ($acceptedPreferences as $preference) {
            $this->assertInstanceOf(PreferenceModel::class, $preference, 'Chaque élément du tableau doit être une instance de PreferenceModel');
            $this->assertTrue($preference->getIsAccepted(), 'Chaque préférence retournée doit être acceptée');
        }
        $nonAcceptedPreferences = $repo->findByAccept(false);
        $this->assertIsArray($nonAcceptedPreferences, 'La méthode findByAccept doit retourner un tableau');
        foreach ($nonAcceptedPreferences as $preference) {
            $this->assertInstanceOf(PreferenceModel::class, $preference, 'Chaque élément du tableau doit être une instance de PreferenceModel');
            $this->assertFalse($preference->getIsAccepted(), 'Chaque préférence retournée doit être non acceptée');
        }
    }
}