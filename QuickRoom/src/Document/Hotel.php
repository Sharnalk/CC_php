<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ODM\Document]
#[ODM\Index(keys: ['nom' => 1])]
#[ODM\Index(keys: ['categorie' => 1])]
class Hotel
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $nom;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $categorie = null; // "*", "**", "***", ...

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $ville = null;

    /** @var Collection<int, Chambre> */
    #[ODM\ReferenceMany(targetDocument: Chambre::class, mappedBy: 'hotel')]
    private Collection $chambres;

    public function __construct(string $nom, ?string $categorie = null, ?string $ville = null)
    {
        $this->nom = $nom;
        $this->categorie = $categorie;
        $this->ville = $ville;
        $this->chambres = new ArrayCollection();
    }

    public function getId(): ?string { return $this->id; }
    public function getNom(): string { return $this->nom; }
    public function setNom(string $nom): self { $this->nom = $nom; return $this; }
    public function getCategorie(): ?string { return $this->categorie; }
    public function setCategorie(?string $categorie): self { $this->categorie = $categorie; return $this; }
    public function getVille(): ?string { return $this->ville; }
    public function setVille(?string $ville): self { $this->ville = $ville; return $this; }
    /** @return Collection<int, Chambre> */
    public function getChambres(): Collection
    {
        return $this->chambres;
    }
}
