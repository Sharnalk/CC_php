<?php

namespace App\Repository;

use App\Document\Chambre;
use App\Document\Hotel;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class ChambreRepository extends DocumentRepository
{
    /** @return list<Chambre> */
    public function findByHotel(Hotel $hotel): array
    {
        return $this->createQueryBuilder()
            ->field('hotel')->references($hotel)
            ->sort('numero', 'asc')
            ->getQuery()->execute()->toArray();
    }

    /** @return list<Chambre> */
    public function findByHotelAndType(Hotel $hotel, string $type): array
    {
        return $this->createQueryBuilder()
            ->field('hotel')->references($hotel)
            ->field('type')->equals($type)
            ->sort('numero', 'asc')
            ->getQuery()->execute()->toArray();
    }
}
