<?php

use App\model\CarModel;
use App\model\RideSharingModel;
use App\model\UserModel;
use PHPUnit\Framework\TestCase;


class RidesharingModelTest extends TestCase
{
    public function testHydrateSetsProperties()
    {
        $data = [
            'id_ridesharing' => 1,
            'departure_date' => '2023-06-01 12:00:00',
            'departure_city' => 'Paris',
            'departure_address' => '18 Impasse de la Défense',
            'arrival_city' => 'Lyon',
            'arrival_address' => '18 Impasse des fleurs',
            'available_seats' => '3',
            'price_per_seat' => '40',
            'status' => 'pending',
            'created_at' => '2023-06-01 12:00:00',
            'driver' => new UserModel( ['id_user' => 9, 'pseudo' => 'Chauffeur1'] ),
            'id_car' => new CarModel(['id_car' => 427, 'brand' => 'Peugeot', 'model' => '208'])
        ];

        $ridesharing = RideSharingModel::createAndHydrate($data);

        $this->assertEquals(1, $ridesharing->getIdRideSharing());
        $this->assertInstanceOf(DateTimeImmutable::class, $ridesharing->getDepartureDate());
        $this->assertEquals('Paris', $ridesharing->getDepartureCity());
        $this->assertEquals('18 Impasse de la Défense', $ridesharing->getDepartureAddress());
        $this->assertEquals('Lyon', $ridesharing->getArrivalCity());
        $this->assertEquals('18 Impasse des fleurs', $ridesharing->getArrivalAddress());
        $this->assertEquals(3, $ridesharing->getAvailableSeats());
        $this->assertEquals(40, $ridesharing->getPricePerSeat());
        $this->assertEquals('pending', $ridesharing->getStatus()->value);
        $this->assertInstanceOf(DateTimeImmutable::class, $ridesharing->getCreatedAt());
        $this->assertEquals(9, $ridesharing->getDriver()->getIdUser());
        $this->assertEquals(427, $ridesharing->getCar() ->getIdCar());
    }
}