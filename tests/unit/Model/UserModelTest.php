<?php
use PHPUnit\Framework\TestCase;
use App\Model\UserModel;

class UserModelTest extends TestCase
{
    public function testHydrateSetsProperties()
    {
        $data = [
            'id_user' => 1,
            'last_name' => 'Uny',
            'first_name' => 'Marc',
            'created_at' => '2023-06-01 12:00:00',
            'role' => 'user'
        ];

        $user = UserModel::createAndHydrate($data);

        $this->assertEquals(1, $user->getIdUser());
        $this->assertEquals('Uny', $user->getLastName());
        $this->assertInstanceOf(DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertEquals('user', $user->getRole()->value);
    }
}