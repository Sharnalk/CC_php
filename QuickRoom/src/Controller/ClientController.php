<?php

namespace App\Controller;

use App\Document\Reservation;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_CLIENT')]
#[Route('/client', name: 'app_client_')]
final class ClientController extends AbstractController
{
    public function __construct(private readonly DocumentManager $dm) {}

    #[Route('/reservations', name: 'reservations', methods: ['GET'])]
    public function reservations(Request $request): JsonResponse
    {
        $page = max(1, (int)($request->query->get('page', 1)));
        $size = min(100, max(1, (int)($request->query->get('size', 20))));
        $skip = ($page - 1) * $size;

        $qb = $this->dm->createQueryBuilder(Reservation::class)
            ->field('client')->references($this->getUser())
            ->sort('dateDebut', 'desc')
            ->limit($size)
            ->skip($skip);

        $items = $qb->getQuery()->execute()->toArray();

        return new JsonResponse(array_map(fn(Reservation $r) => [
            'id'        => $r->getId(),
            'num'       => $r->getNumReservation(),
            'debut'     => $r->getDateDebut()->format(DATE_ATOM),
            'fin'       => $r->getDateFin()->format(DATE_ATOM),
            'hotelId'   => $r->getHotel()->getId(),
            'rooms'     => array_map(fn($c) => $c->getId(), $r->getChambres()->toArray()),
            'comment'   => $r->getCommentaireClient(),
        ], $items));
    }

    #[Route('/reservations/{id}/comment', name: 'reservation_comment', methods: ['POST'])]
    public function comment(string $id, Request $request): JsonResponse
    {
        /** @var ?Reservation $r */
        $r = $this->dm->getRepository(Reservation::class)->find($id);
        if (!$r) {
            return new JsonResponse(['error' => 'reservation not found'], 404);
        }
        // ownership
        if ($r->getClient()->getId() !== $this->getUser()->getId()) {
            return new JsonResponse(['error' => 'forbidden'], 403);
        }

        $comment = (string)$request->request->get('comment', '');
        $r->setCommentaireClient($comment);
        $this->dm->flush();

        return new JsonResponse(['ok' => true]);
    }
}
