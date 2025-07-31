<?php

use App\Model\RideSharingModel;
use PHPUnit\Framework\TestCase;


class RidesharingModelTest extends TestCase
{
    public function testHydrateSetsProperties()
    {
        $data = [
            'id_ridesharing' => 1,
            'departure_date' => '2023-06-01 12:00:00',
            'departure_city' => 'Paris',
            'departure_adress' => '18 Impasse de la Défense',
            'arrival_city' => 'Lyon',
            'arrival_adress' => '18 Impasse des fleurs',
            'available_seats' => '3',
            'price_par_seat' => '40',
            'status' => 'pending',
            'created_at' => '2023-06-01 12:00:00',
            'id_driver' => 9,
            'id_car' => 427
        ];

        $ridesharing = RideSharingModel::createAndHydrate($data);

        $this->assertEquals(1, $ridesharing->getIdRideSharing());
        $this->assertInstanceOf(DateTimeImmutable::class, $ridesharing->getDepartureDate());
        $this->assertEquals('Paris', $ridesharing->getDepartureCity());
        $this->assertEquals('18 Impasse de la Défense', $ridesharing->getDepartureAdress());
        $this->assertEquals('Lyon', $ridesharing->getArrivalCity());
        $this->assertEquals('18 Impasse des fleurs', $ridesharing->getArrivalAdress());
        $this->assertEquals(3, $ridesharing->getAvailableSeats());
        $this->assertEquals(40, $ridesharing->getPriceParSeat());
        $this->assertEquals('pending', $ridesharing->getStatus()->value);
        $this->assertInstanceOf(DateTimeImmutable::class, $ridesharing->getCreatedAt());
        $this->assertEquals(9, $ridesharing->getIdDriver());
        $this->assertEquals(427, $ridesharing->getIdCar());
    }
}