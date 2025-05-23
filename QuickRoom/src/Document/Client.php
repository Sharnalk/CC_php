<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Client
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'string')]
    private string $nom;

    #[ODM\Field(type: 'string')]
    private string $adresse;

    #[ODM\ReferenceMany(targetDocument: Reservation::class, mappedBy: 'client')]
    private $reservations;
}