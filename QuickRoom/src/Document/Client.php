<?php

namespace App\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ODM\Document]
#[ODM\UniqueIndex(keys: ['email' => 1])]
class Client implements UserInterface, PasswordAuthenticatedUserInterface
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

    #[ODM\Field(type: 'collection')]
    private array $roles = ['ROLE_CLIENT'];

    public function __construct(string $email, string $passwordHash, ?string $nom = null, ?string $prenom = null)
    {
        $this->email = mb_strtolower($email);
        $this->passwordHash = $passwordHash;
        $this->nom = $nom;
        $this->prenom = $prenom;
    }

    public function getId(): ?string { return $this->id; }

    // === UserInterface ===
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return array_values(array_unique($this->roles));
    }

    public function setRoles(array $roles): self
    {
        $this->roles = array_values(array_unique($roles));
        return $this;
    }

    // === PasswordAuthenticatedUserInterface ===
    public function getPassword(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $hash): self
    {
        $this->passwordHash = $hash;
        return $this;
    }

    // Supprime d’éventuelles données sensibles en mémoire
    public function eraseCredentials(): void
    {
        // si tu stockes un plainPassword temporaire, vide-le ici
    }

    // === Autres champs métiers ===
    public function getEmail(): string { return $this->email; }
    public function setEmail(string $email): self { $this->email = mb_strtolower($email); return $this; }
    public function getNom(): ?string { return $this->nom; }
    public function setNom(?string $nom): self { $this->nom = $nom; return $this; }
    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(?string $prenom): self { $this->prenom = $prenom; return $this; }
}
