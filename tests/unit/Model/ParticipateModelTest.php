<?php
use PHPUnit\Framework\TestCase;
use App\Model\ParticipateModel;

class ParticipateModelTest extends TestCase
{
    public function testHydrateSetsProperties()
    {
        $data = [
            'id_participate' => 1,
            'id_participant' => 2,
            'id_ridesharing' => 3,
            'confirmed' => 0,
            'created_at' => '2025-12-01 12:00:00'
        ];

        $participate = ParticipateModel::createAndHydrate($data);

        $this->assertEquals(1, $participate->getIdParticipate());
        $this->assertEquals(2, $participate->getIdParticipant());
        $this->assertEquals(3, $participate->getIdRidesharing());
        $this->assertFalse($participate->isConfirmed());
        $this->assertInstanceOf(DateTimeImmutable::class, $participate->getCreatedAt());
    }
}