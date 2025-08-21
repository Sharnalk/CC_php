<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document]
#[ODM\UniqueIndex(keys: ['email' => 1])]
class Client
{
    #[ODM\Id]
    private ?string $id = null;

    #[ODM\Field(type: 'string')]
    private string $email;

    #[ODM\Field(type: 'string')]
    private string $passwordHash;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $nom = null;

    #[ODM\Field(type: 'string', nullable: true)]
    private ?string $prenom = null;

    public function __construct(string $email, string $passwordHash, ?string $nom = null, ?string $prenom = null)
    {
        $this->email = $email;
        $this->passwordHash = $passwordHash;
        $this->nom = $nom;
        $this->prenom = $prenom;
    }

    public function getId(): ?string { return $this->id; }
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getPasswordHash(): string { return $this->passwordHash; }
    public function setPasswordHash(string $hash): self { $this->passwordHash = $hash; return $this; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): self { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): self { $this->prenom = $prenom; return $this; }
}
