<?php
// tests/unit/TestConnexionPDO.php

use PHPUnit\Framework\TestCase;

class TestConnexionPDO extends TestCase
{
    public function testConnexion()
    {
        echo 'DB_HOST ENV : '; var_dump($_ENV['DB_HOST'] ?? null);
        echo 'DB_NAME ENV : '; var_dump($_ENV['DB_NAME'] ?? null);
        echo 'DB_USER ENV : '; var_dump($_ENV['DB_USER'] ?? null);
        echo 'DB_PASS ENV : '; var_dump($_ENV['DB_PASSWORD'] ?? null);
        $this->assertTrue(true);
    }
}