<?php


use PHPUnit\Framework\TestCase;
use App\config\Database;

final class TestConnexionMongo extends TestCase
{
    public function testPing(): void
    {
        $db = Database::getInstanceMongo();
        $res = $db->command(['ping' => 1])->toArray()[0] ?? null;

        $this->assertNotNull($res, 'Réponse ping nulle');
        // Selon la version du driver, $res peut être un stdClass/ArrayObject
        $ok = (float)($res->ok ?? $res['ok'] ?? 0);
        $this->assertSame(1.0, $ok, 'Ping MongoDB a échoué');
    }
}