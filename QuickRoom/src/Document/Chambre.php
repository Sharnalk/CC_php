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

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getEtage(): int
    {
        return $this->etage;
    }

    public function setEtage(int $etage): void
    {
        $this->etage = $etage;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getNombreLit(): int
    {
        return $this->nombreLit;
    }

    public function setNombreLit(int $nombreLit): void
    {
        $this->nombreLit = $nombreLit;
    }

    public function getHotel(): Hotel
    {
        return $this->hotel;
    }

    public function setHotel(Hotel $hotel): void
    {
        $this->hotel = $hotel;
    }


}