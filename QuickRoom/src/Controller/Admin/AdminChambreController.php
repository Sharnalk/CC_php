<?php

namespace App\Controller\Admin;

use App\Document\Chambre;
use App\Document\Hotel;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/chambres', name: 'app_admin_chambres_')]
final class AdminChambreController extends AbstractController
{
    public function __construct(private readonly DocumentManager $dm) {}

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request): JsonResponse
    {
        $hotelId = $request->query->get('hotelId');
        $type    = $request->query->get('type');

        $qb = $this->dm->createQueryBuilder(Chambre::class);
        if ($hotelId) {
            $hotel = $this->dm->getRepository(Hotel::class)->find($hotelId);
            if ($hotel) { $qb->field('hotel')->references($hotel); }
        }
        if ($type) {
            $qb->field('type')->equals($type);
        }

        $items = $qb->sort('numero','asc')->getQuery()->execute()->toArray();
        return new JsonResponse(array_map(fn(Chambre $c) => [
            'id'        => $c->getId(),
            'hotelId'   => $c->getHotel()->getId(),
            'numero'    => $c->getNumero(),
            'etage'     => $c->getEtage(),
            'type'      => $c->getType(),
            'nombreLit' => $c->getNombreLit(),
        ], $items));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $hotelId = $request->request->get('hotelId');
        $hotel = $this->dm->getRepository(Hotel::class)->find($hotelId);
        if (!$hotel) return new JsonResponse(['error' => 'hotel not found'], 404);

        $numero = (int)$request->request->get('numero');
        $etage  = (int)$request->request->get('etage', 0);
        $type   = (string)$request->request->get('type', 'Standard');
        $nbLit  = (int)$request->request->get('nombreLit', 1);

        $c = new Chambre($hotel, $numero, $etage, $type, $nbLit);
        $this->dm->persist($c);
        $this->dm->flush();

        return new JsonResponse(['id' => $c->getId()], 201);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT','PATCH'])]
    public function update(string $id, Request $request): JsonResponse
    {
        $c = $this->dm->getRepository(Chambre::class)->find($id);
        if (!$c) return new JsonResponse(['error' => 'not found'], 404);

        if ($request->request->has('numero'))    $c->setNumero((int)$request->request->get('numero'));
        if ($request->request->has('etage'))     $c->setEtage((int)$request->request->get('etage'));
        if ($request->request->has('type'))      $c->setType((string)$request->request->get('type'));
        if ($request->request->has('nombreLit')) $c->setNombreLit((int)$request->request->get('nombreLit'));

        $this->dm->flush();
        return new JsonResponse(['ok' => true]);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(string $id): JsonResponse
    {
        $c = $this->dm->getRepository(Chambre::class)->find($id);
        if (!$c) return new JsonResponse(['error' => 'not found'], 404);

        $this->dm->remove($c);
        $this->dm->flush();
        return new JsonResponse(['ok' => true]);
    }
}
