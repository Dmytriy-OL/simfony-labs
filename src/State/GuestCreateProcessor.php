<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Service\GuestManager;
use App\Service\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

class GuestCreateProcessor implements ProcessorInterface
{
    public function __construct(
        private GuestManager $guestManager,
        private RequestCheckerService $checker,
        private EntityManagerInterface $em
    ) {}

    public function process($data, Operation $operation, array $context = [])
    {
        $arr = json_decode($context['request']->getContent(), true);

        $this->checker->check($arr, ['full_name', 'email']);

        $guest = $this->guestManager->createGuest($arr);

        $this->em->flush();

        return $guest;
    }
}
