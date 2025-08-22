<?php

namespace App\Service;

use App\Document\Client;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserRegistrationService
{
    public function __construct(
        private readonly DocumentManager $dm,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    public function registerClient(string $email, string $plainPassword, ?string $nom = null, ?string $prenom = null): Client
    {
        // email normalisé
        $email = mb_strtolower($email);

        // Unicité
        $existing = $this->dm->getRepository(Client::class)->findOneBy(['email' => $email]);
        if ($existing) {
            throw new \DomainException('Un compte existe déjà avec cet email.');
        }

        // Hash
        $tmpUser = new Client($email, ''); // user nécessaire pour le hasher (selon algo)
        $hash = $this->passwordHasher->hashPassword($tmpUser, $plainPassword);

        // Création
        $user = new Client($email, $hash, $nom, $prenom);
        $user->setRoles(['ROLE_CLIENT']); // par défaut

        $this->dm->persist($user);
        $this->dm->flush();

        return $user;
    }

    public function promoteToAdmin(Client $user): void
    {
        $roles = $user->getRoles();
        if (!in_array('ROLE_ADMIN', $roles, true)) {
            $roles[] = 'ROLE_ADMIN';
            $user->setRoles($roles);
            $this->dm->flush();
        }
    }
}
