<?php

namespace App\Service;

use App\Domain\AvailabilityServiceInterface;
use App\Domain\DateRange;
use App\Document\Chambre;
use App\Document\Hotel;
use App\Repository\ChambreRepository;
use App\Repository\ReservationRepository;
use Doctrine\ODM\MongoDB\DocumentManager;

final class AvailabilityService implements AvailabilityServiceInterface
{
    public function __construct(
        private readonly DocumentManager $dm,
        private readonly ChambreRepository $chambreRepo,
        private readonly ReservationRepository $reservationRepo
    ) {}

    public function getUnavailabilityMap(Hotel $hotel, DateRange $range): array
    {
        $map = [];
        foreach ($this->reservationRepo->findOverlapping($hotel, $range->start, $range->end) as $resa) {
            foreach ($resa->getChambres() as $c) {
                $map[$c->getId()] = true;
            }
        }
        return $map;
    }

    /** @return list<Chambre> */
    public function findAvailableRooms(Hotel $hotel, DateRange $range, ?string $type = null): array
    {
        $candidates = $type
            ? $this->chambreRepo->findByHotelAndType($hotel, $type)
            : $this->chambreRepo->findByHotel($hotel);

        $indispo = $this->getUnavailabilityMap($hotel, $range);

        return array_values(array_filter(
            $candidates,
            fn(Chambre $c) => !isset($indispo[$c->getId()])
        ));
    }
}
