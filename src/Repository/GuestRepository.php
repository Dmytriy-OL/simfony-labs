<?php

namespace App\Repository;

use App\Entity\Guest;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class GuestRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Guest::class);
    }

    /**
     * Фільтрація + пагінація
     */
    public function getAllGuestsByFilter(array $filters, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('g');

        // ------- ФІЛЬТРИ --------
        if (!empty($filters['full_name'])) {
            $qb->andWhere('g.fullName LIKE :name')
               ->setParameter('name', '%'.$filters['full_name'].'%');
        }

        if (!empty($filters['email'])) {
            $qb->andWhere('g.email LIKE :email')
               ->setParameter('email', '%'.$filters['email'].'%');
        }

        // ------- ПАГІНАЦІЯ --------
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
           ->setMaxResults($itemsPerPage);

        return [
            'items' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'itemsPerPage' => $itemsPerPage,
        ];
    }
}
