<?php

namespace App\Controller\Admin;

use App\Document\Client;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/clients', name: 'app_admin_clients_')]
final class AdminClientController extends AbstractController
{
    public function __construct(private readonly DocumentManager $dm) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $q = $request->query->get('q'); // nom ou email
        $qb = $this->dm->createQueryBuilder(Client::class)->limit(50);

        if ($q) {
            $qb->addAnd(
                $qb->expr()->addOr([
                    $qb->expr()->field('email')->equals(mb_strtolower($q)),
                    $qb->expr()->field('nom')->equals($q),
                ])
            );
        }

        $items = $qb->getQuery()->execute()->toArray();

        return new JsonResponse(array_map(fn(Client $c) => [
            'id'     => $c->getId(),
            'email'  => $c->getEmail(),
            'nom'    => $c->getNom(),
            'prenom' => $c->getPrenom(),
            'roles'  => $c->getRoles(),
        ], $items));
    }
}
