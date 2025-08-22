<?php

namespace App\Security;

use App\Document\Client;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

final class MongoUserProvider implements UserProviderInterface, PasswordUpgraderInterface
{
    public function __construct(private readonly DocumentManager $dm) {}

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        $user = $this->dm->getRepository(Client::class)
            ->findOneBy(['email' => mb_strtolower($identifier)]);

        if (!$user) {
            $ex = new UserNotFoundException(sprintf('User "%s" not found.', $identifier));
            $ex->setUserIdentifier($identifier);
            throw $ex;
        }
        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof Client) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }
        return $this->loadUserByIdentifier($user->getUserIdentifier());
    }

    public function supportsClass(string $class): bool
    {
        // is_a permet de supporter d’éventuels proxies
        return is_a($class, Client::class, true);
    }


    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof Client) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', $user::class));
        }
        $user->setPasswordHash($newHashedPassword);
        $this->dm->flush();
    }
}
