<?php

namespace App\Controller;

use App\Domain\DateRange;
use App\Domain\ReservationServiceInterface;
use App\Document\Chambre;
use App\Document\Hotel;
use App\Service\AvailabilityService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class PublicController extends AbstractController
{
    public function __construct(
        private readonly DocumentManager $dm,
        private readonly AvailabilityService $availability,
        private readonly ReservationServiceInterface $reservations
    ) {}

    #[Route('/availability', name: 'app_availability', methods: ['GET'])]
    public function availability(Request $request): JsonResponse
    {
        $hotelId = $request->query->get('hotelId');
        $start   = $request->query->get('start'); // ISO 8601 recommandé
        $end     = $request->query->get('end');
        $type    = $request->query->get('type'); // optionnel

        if (!$hotelId || !$start || !$end) {
            return new JsonResponse(['error' => 'hotelId, start, end are required'], 400);
        }

        /** @var ?Hotel $hotel */
        $hotel = $this->dm->getRepository(Hotel::class)->find($hotelId);
        if (!$hotel) {
            return new JsonResponse(['error' => 'hotel not found'], 404);
        }

        $range = new DateRange(new \DateTimeImmutable($start), new \DateTimeImmutable($end));
        $rooms = $this->availability->findAvailableRooms($hotel, $range, $type);

        return new JsonResponse(array_map(fn(Chambre $c) => [
            'id'        => $c->getId(),
            'numero'    => $c->getNumero(),
            'etage'     => $c->getEtage(),
            'type'      => $c->getType(),
            'nombreLit' => $c->getNombreLit(),
        ], $rooms));
    }

    // Création de réservation : requiert d'être connecté (ROLE_CLIENT)
    #[Route('/reservation', name: 'app_reservation_create', methods: ['POST'])]
    #[IsGranted('ROLE_CLIENT')]
    public function createReservation(Request $request): JsonResponse
    {
        $hotelId = $request->request->get('hotelId');
        $roomIds = $request->request->all('roomIds'); // array
        $start   = $request->request->get('start');
        $end     = $request->request->get('end');
        $comment = $request->request->get('comment');

        if (!$hotelId || !is_array($roomIds) || !$start || !$end) {
            return new JsonResponse(['error' => 'hotelId, roomIds[], start, end are required'], 400);
        }

        $hotel = $this->dm->getRepository(Hotel::class)->find($hotelId);
        if (!$hotel) {
            return new JsonResponse(['error' => 'hotel not found'], 404);
        }

        $rooms = [];
        foreach ($roomIds as $rid) {
            $r = $this->dm->getRepository(Chambre::class)->find($rid);
            if ($r) { $rooms[] = $r; }
        }

        try {
            $range = new DateRange(new \DateTimeImmutable($start), new \DateTimeImmutable($end));
            $reservation = $this->reservations->createReservation(
                client: $this->getUser(), // Client implémente UserInterface
                hotel: $hotel,
                rooms: $rooms,
                dates: $range,
                commentaireClient: $comment
            );
        } catch (\Throwable $e) {
            return new JsonResponse(['error' => $e->getMessage()], 400);
        }

        return new JsonResponse([
            'id'             => $reservation->getId(),
            'numReservation' => $reservation->getNumReservation(),
            'dateDebut'      => $reservation->getDateDebut()->format(DATE_ATOM),
            'dateFin'        => $reservation->getDateFin()->format(DATE_ATOM),
            'rooms'          => array_map(fn(Chambre $c) => $c->getId(), $reservation->getChambres()->toArray()),
        ], 201);
    }
}
