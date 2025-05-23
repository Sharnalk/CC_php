<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Chambre
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'int')]
    private int $etage;

    #[ODM\Field(type: 'string')]
    private string $type;

    #[ODM\Field(type: 'int')]
    private int $nombreLit;

    #[ODM\ReferenceOne(targetDocument: Hotel::class, inversedBy: 'chambres')]
    private Hotel $hotel;
}