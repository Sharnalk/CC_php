<?php

namespace App\Document;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Uid\Uuid;

#[ODM\Document]
class Reservation
{

    public function __construct()
    {
        $this->chambres = new ArrayCollection();
        $this->numReservation = Uuid::v4();
    }
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
    private Collection $chambres;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $commentaire = null;

    #[ODM\Field(type: 'string')]
    private string $numReservation;

    public function getNumReservation(): string
    {
        return $this->numReservation;
    }

    public function getCommentaire(): ?string
    {
        return $this->commentaire;
    }

    public function setCommentaire(?string $commentaire): void
    {
        $this->commentaire = $commentaire;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getDateDebut(): \DateTime
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTime $dateDebut): void
    {
        $this->dateDebut = $dateDebut;
    }

    public function getDateFin(): \DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): void
    {
        $this->dateFin = $dateFin;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    public function getHotel(): Hotel
    {
        return $this->hotel;
    }

    public function setHotel(Hotel $hotel): void
    {
        $this->hotel = $hotel;
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