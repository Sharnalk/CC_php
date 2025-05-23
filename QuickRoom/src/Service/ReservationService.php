<?php

namespace App\Service;

use App\Document\Reservation;
use App\Document\Chambre;
use App\Document\Hotel;
use App\Document\Client;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ReservationService
{
    private DocumentManager $dm;

    public function __construct(DocumentManager $dm)
    {
        $this->dm = $dm;
    }


    public function findAvailableRooms(Hotel $hotel, \DateTime $dateDebut, \DateTime $dateFin): array
    {
        $chambreRepo = $this->dm->getRepository(Chambre::class);
        $allRooms = $chambreRepo->findBy(['hotel.$id' => $hotel->getId()]);

        $reservations = $this->dm->getRepository(Reservation::class)->createQueryBuilder()
            ->field('hotel.id')->equals($hotel->getId())
            ->field('dateFin')->gt($dateDebut)
            ->field('dateDebut')->lt($dateFin)
            ->getQuery()
            ->execute();

        $occupiedRoomIds = [];
        foreach ($reservations as $res) {
            foreach ($res->getChambres() as $chambre) {
                $occupiedRoomIds[] = (string) $chambre->getId();
            }
        }

        return array_filter($allRooms, fn(Chambre $c) => !in_array((string) $c->getId(), $occupiedRoomIds));
    }

    public function createReservation(Client $client, Hotel $hotel, \DateTime $dateDebut, \DateTime $dateFin, array $chambres, ?string $commentaire = null): Reservation
    {
        if (count($chambres) < 1) {
            throw new BadRequestHttpException("Le client doit réserver au moins une chambre.");
        }

        $reservation = new Reservation();
        $reservation->setClient($client);
        $reservation->setHotel($hotel);
        $reservation->setDateDebut($dateDebut);
        $reservation->setDateFin($dateFin);
        $reservation->setChambres(new ArrayCollection($chambres));
        $reservation->setCommentaire($commentaire);

        $this->dm->persist($reservation);
        $this->dm->flush();

        return $reservation;
    }

    public function findByNumReservation(string $numReservation): ?Reservation
    {
        return $this->dm->getRepository(Reservation::class)
            ->findOneBy(['numReservation' => $numReservation]);
    }

    public function getClientReservations(Client $client): array
    {
        return $this->dm->getRepository(Reservation::class)
            ->findBy(['client.$id' => $client->getId()]);
    }
}