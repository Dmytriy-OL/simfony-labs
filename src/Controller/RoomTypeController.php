<?php

namespace App\Controller;

use App\Entity\RoomType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class RoomTypeController extends AbstractController
{
    const ITEMS_PER_PAGE = 10;

    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * GET Room Types with filters + pagination
     */
    #[Route('/room-types', name: 'get_room_types_collection', methods: ['GET'])]
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
            ->getRepository(RoomType::class)
            ->getAllRoomTypesByFilter($query, $itemsPerPage, $page);

        return new JsonResponse($result);
    }
}
