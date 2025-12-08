<?php

namespace App\Repository;

use App\Entity\Booking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

class BookingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Booking::class);
    }

    /**
     * Повертає всі бронювання з фільтрами та пагінацією
     */
    public function getAllBookingsByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('b')
            ->leftJoin('b.guest', 'guest')
            ->leftJoin('b.room', 'room')
            ->addSelect('guest', 'room');

        // Фільтр по гостю
        if (!empty($data['guest_id'])) {
            $qb->andWhere('guest.id = :guest_id')
               ->setParameter('guest_id', $data['guest_id']);
        }

        // Фільтр по кімнаті
        if (!empty($data['room_id'])) {
            $qb->andWhere('room.id = :room_id')
               ->setParameter('room_id', $data['room_id']);
        }

        // Фільтр по статусу
        if (!empty($data['status'])) {
            $qb->andWhere('b.status = :status')
               ->setParameter('status', $data['status']);
        }

        // Фільтр по даті check_in від
        if (!empty($data['date_from'])) {
            $qb->andWhere('b.check_in >= :date_from')
               ->setParameter('date_from', $data['date_from']);
        }

        // Фільтр по даті check_in до
        if (!empty($data['date_to'])) {
            $qb->andWhere('b.check_in <= :date_to')
               ->setParameter('date_to', $data['date_to']);
        }

        // ПАГІНАЦІЯ
        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $itemsPerPage);

        $qb->setFirstResult($itemsPerPage * ($page - 1))
           ->setMaxResults($itemsPerPage);

        return [
            'bookings' => $paginator->getQuery()->getResult(),
            'totalItems' => $totalItems,
            'totalPages' => $totalPages
        ];
    }
}
