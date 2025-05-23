<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Hotel
{
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'string')]
    private string $nom;

    #[ODM\Field(type: 'string')]
    private string $adresse;

    #[ODM\Field(type: 'string')]
    private string $categorie;

    #[ODM\ReferenceMany(targetDocument: Chambre::class, mappedBy: 'hotel')]
    private $chambres;
}