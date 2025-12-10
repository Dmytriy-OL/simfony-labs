<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\BookingManager;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

class BookingCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private BookingManager $bookingManager,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    public function process($data, Operation $operation, array $context = [])
    {
        $arr = json_decode($context['request']->getContent(), true);

        $this->checker->check($arr, ['guest_id', 'room_id', 'check_in', 'check_out']);

        $booking = $this->bookingManager->createBooking($arr);

        $this->em->flush();

        return $booking;
    }
}
