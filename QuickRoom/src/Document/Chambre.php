<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(repositoryClass: \App\Repository\ChambreRepository::class)]
#[ODM\UniqueIndex(keys: ['hotel' => 1, 'numero' => 1])] // n° unique par hôtel
#[ODM\Index(keys: ['hotel' => 1, 'type' => 1])]
#[ODM\Index(keys: ['hotel' => 1, 'etage' => 1])]
class Chambre
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'int')]
    private int $etage;

    #[ODM\Field(type: 'string')]
    private string $type; // ex: "Single", "Double", "Deluxe", ...

    #[ODM\Field(type: 'int')]
    private int $nombreLit;

    #[ODM\Field(type: 'int')]
    private int $numero; // ex: 204

    #[ODM\ReferenceOne(targetDocument: Hotel::class, inversedBy: 'chambres', storeAs: 'dbRefWithDb')]
    private Hotel $hotel;

    public function __construct(Hotel $hotel, int $numero, int $etage, string $type, int $nombreLit)
    {
        $this->hotel = $hotel;
        $this->numero = $numero;
        $this->etage = $etage;
        $this->type = $type;
        $this->nombreLit = $nombreLit;
    }

    public function getId(): ?string { return $this->id; }
    public function getHotel(): Hotel { return $this->hotel; }
    public function setHotel(Hotel $hotel): self { $this->hotel = $hotel; return $this; }
    public function getNumero(): int { return $this->numero; }
    public function setNumero(int $numero): self { $this->numero = $numero; return $this; }
    public function getEtage(): int { return $this->etage; }
    public function setEtage(int $etage): self { $this->etage = $etage; return $this; }
    public function getType(): string { return $this->type; }
    public function setType(string $type): self { $this->type = $type; return $this; }
    public function getNombreLit(): int { return $this->nombreLit; }
    public function setNombreLit(int $nombreLit): self { $this->nombreLit = $nombreLit; return $this; }
}
