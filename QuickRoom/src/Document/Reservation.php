<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Reservation
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'date')]
    private \DateTime $dateDebut;

    #[ODM\Field(type: 'date')]
    private \DateTime $dateFin;

    #[ODM\ReferenceOne(targetDocument: Client::class, inversedBy: 'reservations')]
    private Client $client;

    #[ODM\ReferenceOne(targetDocument: Hotel::class)]
    private Hotel $hotel;

    #[ODM\ReferenceMany(targetDocument: Chambre::class)]
    private $chambres;
}