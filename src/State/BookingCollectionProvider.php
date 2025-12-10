<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class BookingCollectionProvider implements ProviderInterface
{
    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack
    ) {}

    public function provide(Operation $operation, array $context = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        $query = $request->query->all();

        $itemsPerPage = $query['itemsPerPage'] ?? 10;
        $page = $query['page'] ?? 1;

        return $this->em
            ->getRepository(\App\Entity\Booking::class)
            ->getAllBookingsByFilter($query, $itemsPerPage, $page);
    }
}
