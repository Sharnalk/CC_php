<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
class Hotel
{
    public function __construct()
    {
        $this->chambres = new ArrayCollection();
    }
    #[ODM\Id]
    private string $id;

    #[ODM\Field(type: 'string')]
    private string $nom;

    #[ODM\Field(type: 'string')]
    private string $adresse;

    #[ODM\Field(type: 'string')]
    private string $categorie;

    #[ODM\ReferenceMany(targetDocument: Chambre::class, mappedBy: 'hotel')]
    private Collection $chambres;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getCategorie(): string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): void
    {
        $this->categorie = $categorie;
    }

    public function getChambres(): Collection
    {
        return $this->chambres;
    }

    public function setChambres(Collection $chambres): void
    {
        $this->chambres = $chambres;
    }




}