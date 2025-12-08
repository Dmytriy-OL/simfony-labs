<?php

namespace App\Controller;

use App\Entity\Guest;
use App\Services\GuestManager;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuestController extends AbstractController
{
    private GuestManager $guestManager;
    private RequestCheckerService $requestChecker;
    private EntityManagerInterface $em;

    const ITEMS_PER_PAGE = 10;
    const REQUIRED_FIELDS = ['full_name', 'email'];

    public function __construct(
        GuestManager $guestManager,
        RequestCheckerService $requestChecker,
        EntityManagerInterface $em
    ) {
        $this->guestManager = $guestManager;
        $this->requestChecker = $requestChecker;
        $this->em = $em;
    }

    /**
     * =============================
     * PAGINATION + FILTERS
     * GET /guests
     * =============================
     */
    #[Route('/guests', name: 'get_guests_collection', methods: ['GET'])]
    public function getCollection(Request $request): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = isset($query['itemsPerPage'])
            ? (int)$query['itemsPerPage']
            : self::ITEMS_PER_PAGE;

        $page = isset($query['page'])
            ? (int)$query['page']
            : 1;

        $result = $this->em
            ->getRepository(Guest::class)
            ->getAllGuestsByFilter($query, $itemsPerPage, $page);

        return new JsonResponse($result);
    }

    /**
     * =============================
     * CREATE Guest
     * POST /guests
     * =============================
     */
    #[Route('/guests', name: 'create_guest', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->requestChecker->check($data, self::REQUIRED_FIELDS);

        $guest = $this->guestManager->createGuest($data);

        $this->em->flush();

        return new JsonResponse($guest, Response::HTTP_CREATED);
    }

    /**
     * =============================
     * GET ONE Guest
     * GET /guests/{id}
     * =============================
     */
    #[Route('/guests/{id}', name: 'get_guest', methods: ['GET'])]
    public function getOne(int $id): JsonResponse
    {
        $guest = $this->em->getRepository(Guest::class)->find($id);

        if (!$guest) {
            return new JsonResponse(['error' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($guest);
    }

    /**
     * =============================
     * UPDATE Guest
     * PUT /guests/{id}
     * =============================
     */
    #[Route('/guests/{id}', name: 'update_guest', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $guest = $this->em->getRepository(Guest::class)->find($id);

        if (!$guest) {
            return new JsonResponse(['error' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $this->guestManager->updateGuest($guest, $data);
        $this->em->flush();

        return new JsonResponse($guest);
    }

    /**
     * =============================
     * DELETE Guest
     * DELETE /guests/{id}
     * =============================
     */
    #[Route('/guests/{id}', name: 'delete_guest', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $guest = $this->em->getRepository(Guest::class)->find($id);

        if (!$guest) {
            return new JsonResponse(['error' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($guest);
        $this->em->flush();

        return new JsonResponse(['message' => 'Guest deleted']);
    }
}
