<?php

namespace App\Repository;

use App\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    /**
     * Повертає кімнати з фільтрами + пагінацією
     */
    public function getAllRoomsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('room');

        // Фільтр по номеру
        if (!empty($data['room_number'])) {
            $qb->andWhere('room.roomNumber LIKE :rn')
               ->setParameter('rn', '%' . $data['room_number'] . '%');
        }

        // Фільтр по місткості
        if (!empty($data['capacity'])) {
            $qb->andWhere('room.capacity = :cap')
               ->setParameter('cap', (int)$data['capacity']);
        }

        // Фільтр по статусу
        if (!empty($data['status'])) {
            $qb->andWhere('room.status = :st')
               ->setParameter('st', $data['status']);
        }

        // ПАГІНАЦІЯ
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
           ->setMaxResults($itemsPerPage);

        return [
            'rooms' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages
        ];
    }
}
