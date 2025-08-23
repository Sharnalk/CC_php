<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

#[ODM\Document(repositoryClass: \App\Repository\ReservationRepository::class)]
#[ODM\UniqueIndex(keys: ['numReservation' => 1])]
#[ODM\Index(keys: ['hotel' => 1, 'dateDebut' => 1, 'dateFin' => 1])]
class Reservation
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $numReservation; // lisible + unique

    #[ODM\ReferenceOne(targetDocument: Client::class, storeAs: 'dbRefWithDb')]
    private Client $client;

    #[ODM\ReferenceOne(targetDocument: Hotel::class, storeAs: 'dbRefWithDb')]
    private Hotel $hotel;

    /** @var Collection<int, Chambre> */
    #[ODM\ReferenceMany(targetDocument: Chambre::class, storeAs: 'dbRefWithDb')]
    private Collection $chambres;
    #[ODM\Field(type: 'date_immutable')]
    private \DateTimeImmutable $dateDebut;

    #[ODM\Field(type: 'date_immutable')]
    private \DateTimeImmutable $dateFin;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $commentaireClient = null;

    #[ODM\Field(type: 'string')]
    private string $statut = 'CONFIRMED'; // PENDING|CONFIRMED|CANCELLED ...

    public function __construct(
        string $numReservation,
        Client $client,
        Hotel $hotel,
        array $chambres,
        \DateTimeImmutable $dateDebut,
        \DateTimeImmutable $dateFin,
        ?string $commentaireClient = null
    ) {
        $this->numReservation = $numReservation;
        $this->client = $client;
        $this->hotel = $hotel;
        $this->chambres = new ArrayCollection($chambres);
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
        $this->commentaireClient = $commentaireClient;
    }

    public function getId(): ?string { return $this->id; }
    public function getNumReservation(): string { return $this->numReservation; }
    public function getClient(): Client { return $this->client; }
    public function getHotel(): Hotel { return $this->hotel; }
    /** @return Collection<int, Chambre> */
    public function getChambres(): Collection { return $this->chambres; }
    public function addChambre(Chambre $c): self
    {
        if (!$this->chambres->contains($c)) {
            $this->chambres->add($c);
        }
        return $this;
    }

    public function removeChambre(Chambre $c): self
    {
        $this->chambres->removeElement($c);
        return $this;
    }
    public function getDateDebut(): \DateTimeImmutable { return $this->dateDebut; }
    public function getDateFin(): \DateTimeImmutable { return $this->dateFin; }
    public function getCommentaireClient(): ?string { return $this->commentaireClient; }
    public function setCommentaireClient(?string $c): self { $this->commentaireClient = $c; return $this; }
    public function getStatut(): string { return $this->statut; }
    public function setStatut(string $s): self { $this->statut = $s; return $this; }
}
