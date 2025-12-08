<?php

namespace App\Repository;

use App\Entity\Hotel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class HotelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hotel::class);
    }

    /**
     * Повертає список готелів з фільтрами та пагінацією
     */
    public function getAllHotelsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('hotel');

        // Фільтр по назві
        if (!empty($data['name'])) {
            $qb->andWhere('hotel.name LIKE :name')
               ->setParameter('name', '%' . $data['name'] . '%');
        }

        // Фільтр по місту
        if (!empty($data['city'])) {
            $qb->andWhere('hotel.city LIKE :city')
               ->setParameter('city', '%' . $data['city'] . '%');
        }

        // Пагінація
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
           ->setMaxResults($itemsPerPage);

        return [
            'hotels' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages
        ];
    }
}
