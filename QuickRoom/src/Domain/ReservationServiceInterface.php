<?php

namespace App\Domain;

use App\Document\Client;
use App\Document\Hotel;
use App\Document\Reservation;
use App\Document\Chambre;

interface ReservationServiceInterface
{
    /**
     * @param list<Chambre> $rooms
     */
    public function createReservation(
        Client $client,
        Hotel $hotel,
        array $rooms,
        DateRange $dates,
        ?string $commentaireClient = null
    ): Reservation;
}
