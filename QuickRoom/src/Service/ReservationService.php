<?php

namespace App\Service;

use App\Domain\DateRange;
use App\Domain\ReservationServiceInterface;
use App\Domain\ReservationNumberGeneratorInterface;
use App\Domain\AvailabilityServiceInterface;
use App\Domain\Exception\RoomNotAvailableException;
use App\Document\Client;
use App\Document\Hotel;
use App\Document\Reservation;
use Doctrine\ODM\MongoDB\DocumentManager;

final class ReservationService implements ReservationServiceInterface
{
    public function __construct(
        private readonly DocumentManager $dm,
        private readonly AvailabilityServiceInterface $availability,
        private readonly ReservationNumberGeneratorInterface $numberGen
    ) {}

    public function createReservation(
        Client $client,
        Hotel $hotel,
        array $rooms,
        DateRange $dates,
        ?string $commentaireClient = null
    ): Reservation {

        if (count($rooms) < 1) {
            throw new \InvalidArgumentException('Au moins une chambre doit être réservée.');
        }

        // Vérifier que toutes les chambres sont dans le bon hôtel
        foreach ($rooms as $room) {
            if ($room->getHotel()->getId() !== $hotel->getId()) {
                throw new \InvalidArgumentException('Toutes les chambres doivent appartenir au même hôtel.');
            }
        }

        // Vérifier la disponibilité
        $unavail = $this->availability->getUnavailabilityMap($hotel, $dates);
        foreach ($rooms as $room) {
            if (isset($unavail[$room->getId()])) {
                throw new RoomNotAvailableException(sprintf('Chambre %s indisponible sur la période.', $room->getNumero()));
            }
        }

        $num = $this->numberGen->generate();

        $reservation = new Reservation(
            numReservation: $num,
            client: $client,
            hotel: $hotel,
            chambres: $rooms,
            dateDebut: $dates->start,
            dateFin: $dates->end,
            commentaireClient: $commentaireClient
        );

        $this->dm->persist($reservation);
        $this->dm->flush();

        return $reservation;
    }

}
