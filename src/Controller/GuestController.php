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

    public function __construct(
        GuestManager $guestManager,
        RequestCheckerService $requestChecker,
        EntityManagerInterface $em
    ) {
        $this->guestManager = $guestManager;
        $this->requestChecker = $requestChecker;
        $this->em = $em;
    }

    const REQUIRED_FIELDS = ['full_name', 'email', 'phone'];

    /**
     * CREATE Guest
     * @throws Exception
     */
    #[Route('/guests', name: 'create_guest', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Перевірка наявності обов'язкових полів
        $this->requestChecker->check($data, self::REQUIRED_FIELDS);

        // Створення гостя через сервіс
        $guest = $this->guestManager->createGuest(
            $data['full_name'],
            $data['email'],
            $data['phone']
        );

        $this->em->flush();

        return new JsonResponse($guest, Response::HTTP_CREATED);
    }

    /**
     * GET all guests
     */
    #[Route('/guests', name: 'get_guests', methods: ['GET'])]
    public function getAll(): JsonResponse
    {
        $guests = $this->em->getRepository(Guest::class)->findAll();
        return new JsonResponse($guests);
    }

    /**
     * GET guest by id
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
     * UPDATE guest
     * @throws Exception
     */
    #[Route('/guests/{id}', name: 'update_guest', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $guest = $this->em->getRepository(Guest::class)->find($id);

        if (!$guest) {
            return new JsonResponse(['error' => 'Guest not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        // Оновлення через сервіс
        $this->guestManager->updateGuest($guest, $data);
        $this->em->flush();

        return new JsonResponse($guest);
    }

    /**
     * DELETE guest
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
