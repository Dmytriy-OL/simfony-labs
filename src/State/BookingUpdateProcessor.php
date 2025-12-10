<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\BookingManager;
use Doctrine\ORM\EntityManagerInterface;

class BookingUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private BookingManager $manager,
        private EntityManagerInterface $em
    ) {}

    public function process($booking, Operation $operation, array $context = [])
    {
        $data = json_decode($context['request']->getContent(), true);

        $this->manager->updateBooking($booking, $data);

        $this->em->flush();

        return $booking;
    }
}
