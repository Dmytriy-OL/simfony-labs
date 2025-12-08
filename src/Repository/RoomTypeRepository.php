<?php

namespace App\Repository;

use App\Entity\RoomType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class RoomTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoomType::class);
    }

    /**
     * Повертає типи кімнат з фільтрами + пагінацією
     */
    public function getAllRoomTypesByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('rt');

        // Фільтр: назва типу
        if (!empty($data['type_name'])) {
            $qb->andWhere('rt.typeName LIKE :type_name')
               ->setParameter('type_name', '%' . $data['type_name'] . '%');
        }

        // Фільтр: мінімальна ціна
        if (!empty($data['min_price'])) {
            $qb->andWhere('rt.pricePerNight >= :min_price')
               ->setParameter('min_price', (float)$data['min_price']);
        }

        // Фільтр: максимальна ціна
        if (!empty($data['max_price'])) {
            $qb->andWhere('rt.pricePerNight <= :max_price')
               ->setParameter('max_price', (float)$data['max_price']);
        }

        // Пагінація
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
           ->setMaxResults($itemsPerPage);

        return [
            'room_types' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages
        ];
    }
}
