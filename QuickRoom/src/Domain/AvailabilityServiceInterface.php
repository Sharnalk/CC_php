<?php

namespace App\Domain;

use App\Document\Chambre;
use App\Document\Hotel;

interface AvailabilityServiceInterface
{
    /** @return list<Chambre> */
    public function findAvailableRooms(Hotel $hotel, DateRange $range, ?string $type = null): array;

    /** @return array<string,bool> map chambreId => indisponible */
    public function getUnavailabilityMap(Hotel $hotel, DateRange $range): array;
}
