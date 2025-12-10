<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\RoomManager;
use Doctrine\ORM\EntityManagerInterface;

class RoomUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private RoomManager $roomManager,
        private EntityManagerInterface $em
    ) {}

    public function process($room, Operation $operation, array $context = [])
    {
        $arr = json_decode($context['request']->getContent(), true);

        $this->roomManager->updateRoom($room, $arr);

        $this->em->flush();

        return $room;
    }
}
