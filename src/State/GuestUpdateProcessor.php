<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\GuestManager;
use Doctrine\ORM\EntityManagerInterface;

class GuestUpdateProcessor implements ProcessorInterface
{
    public function __construct(
        private GuestManager $guestManager,
        private EntityManagerInterface $em
    ) {}

    public function process($guest, Operation $operation, array $context = [])
    {
        $arr = json_decode($context['request']->getContent(), true);

        $this->guestManager->updateGuest($guest, $arr);

        $this->em->flush();

        return $guest;
    }
}
