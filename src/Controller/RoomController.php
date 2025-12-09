<?php

namespace App\Controller;

use App\Entity\Room;
use App\Service\RoomManager;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class RoomController extends AbstractController
{
    private RoomManager $roomManager;
    private RequestCheckerService $requestChecker;
    private EntityManagerInterface $em;

    const ITEMS_PER_PAGE = 10;
    const REQUIRED_FIELDS = ['room_number', 'hotel_id', 'room_type_id', 'capacity'];

    public function __construct(
        RoomManager $roomManager,
        RequestCheckerService $requestChecker,
        EntityManagerInterface $em
    ) {
        $this->roomManager = $roomManager;
        $this->requestChecker = $requestChecker;
        $this->em = $em;
    }

    /**
     * LIST + FILTER + PAGINATION
     */
    #[Route('/rooms', name: 'get_rooms_collection', methods: ['GET'])]
    public function getCollection(Request $request): JsonResponse
    {
        $query = $request->query->all();

        $itemsPerPage = isset($query['itemsPerPage']) ? (int)$query['itemsPerPage'] : self::ITEMS_PER_PAGE;
        $page = isset($query['page']) ? (int)$query['page'] : 1;

        $result = $this->em
            ->getRepository(Room::class)
            ->getAllRoomsByFilter($query, $itemsPerPage, $page);

        return new JsonResponse($result);
    }

    /**
     * CREATE
     */
    #[Route('/rooms', name: 'create_room', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $this->requestChecker->check($data, self::REQUIRED_FIELDS);

        $room = $this->roomManager->createRoom($data);
        $this->em->flush();

        return new JsonResponse($room, Response::HTTP_CREATED);
    }

    /**
     * GET ONE
     */
    #[Route('/rooms/{id}', name: 'get_room', methods: ['GET'])]
    public function getOne(int $id): JsonResponse
    {
        $room = $this->em->getRepository(Room::class)->find($id);

        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($room);
    }

    /**
     * UPDATE
     */
    #[Route('/rooms/{id}', name: 'update_room', methods: ['PUT'])]
    public function update(int $id, Request $request): JsonResponse
    {
        $room = $this->em->getRepository(Room::class)->find($id);

        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $this->roomManager->updateRoom($room, $data);
        $this->em->flush();

        return new JsonResponse($room);
    }

    /**
     * DELETE
     */
    #[Route('/rooms/{id}', name: 'delete_room', methods: ['DELETE'])]
    public function delete(int $id): JsonResponse
    {
        $room = $this->em->getRepository(Room::class)->find($id);

        if (!$room) {
            return new JsonResponse(['error' => 'Room not found'], Response::HTTP_NOT_FOUND);
        }

        $this->em->remove($room);
        $this->em->flush();

        return new JsonResponse(['message' => 'Room deleted']);
    }
}
