<?php

namespace App\Repository;

use App\Document\Reservation;
use App\Document\Hotel;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class ReservationRepository extends DocumentRepository
{
    /**
     * Renvoie les réservations qui chevauchent l'intervalle [debut, fin) pour un hôtel donné.
     * Overlap si (debut < res.fin) && (fin > res.debut)
     * @return iterable<Reservation>
     */
    public function findOverlapping(Hotel $hotel, \DateTimeImmutable $debut, \DateTimeImmutable $fin): iterable
    {
        return $this->createQueryBuilder()
            ->field('hotel')->references($hotel)
            ->field('dateDebut')->lt($fin)
            ->field('dateFin')->gt($debut)
            ->getQuery()->execute();
    }

    public function existsNumReservation(string $num): bool
    {
        return $this->createQueryBuilder()
                ->field('numReservation')->equals($num)
                ->limit(1)
                ->getQuery()->execute()->count() > 0;
    }
}
