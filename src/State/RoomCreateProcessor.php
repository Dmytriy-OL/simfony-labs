<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\RoomManager;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

class RoomCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private RoomManager $roomManager,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    public function process($data, Operation $operation, array $context = [])
    {
        $arr = json_decode($context['request']->getContent(), true);

        $this->checker->check($arr, ['room_number', 'hotel_id', 'room_type_id', 'capacity']);

        $room = $this->roomManager->createRoom($arr);

        $this->em->flush();

        return $room;
    }
}
