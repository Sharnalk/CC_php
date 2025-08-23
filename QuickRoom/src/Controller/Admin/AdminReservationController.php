<?php

namespace App\Controller\Admin;

use App\Document\Reservation;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/reservations', name: 'app_admin_reservations_')]
final class AdminReservationController extends AbstractController
{
    public function __construct(private readonly DocumentManager $dm) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $q    = $request->query->get('q');   // numReservation
        $page = max(1, (int)$request->query->get('page', 1));
        $size = min(100, max(1, (int)$request->query->get('size', 20)));
        $skip = ($page - 1) * $size;

        $qb = $this->dm->createQueryBuilder(Reservation::class)->sort('dateDebut','desc');
        if ($q) $qb->field('numReservation')->equals($q);
        $items = $qb->limit($size)->skip($skip)->getQuery()->execute()->toArray();

        return new JsonResponse(array_map(fn(Reservation $r) => [
            'id'   => $r->getId(),
            'num'  => $r->getNumReservation(),
            'from' => $r->getDateDebut()->format(DATE_ATOM),
            'to'   => $r->getDateFin()->format(DATE_ATOM),
        ], $items));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(string $id): JsonResponse
    {
        $r = $this->dm->getRepository(Reservation::class)->find($id);
        if (!$r) return new JsonResponse(['error' => 'not found'], 404);

        return new JsonResponse([
            'id'   => $r->getId(),
            'num'  => $r->getNumReservation(),
            'from' => $r->getDateDebut()->format(DATE_ATOM),
            'to'   => $r->getDateFin()->format(DATE_ATOM),
            'hotelId' => $r->getHotel()->getId(),
            'rooms'   => array_map(fn($c) => [
                'id'     => $c->getId(),
                'numero' => $c->getNumero(),
                'type'   => $c->getType(),
            ], $r->getChambres()->toArray()),
            'clientId' => $r->getClient()->getId(),
            'comment'  => $r->getCommentaireClient(),
        ]);
    }
}
