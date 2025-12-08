<?php

namespace App\Service;

use App\Entity\Guest;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\RequestCheckerService;

class GuestEntityService
{
    private EntityManagerInterface $entityManager;
    private RequestCheckerService $requestCheckerService;

    public function __construct(
        EntityManagerInterface $entityManager,
        RequestCheckerService $requestCheckerService
    ) {
        $this->entityManager = $entityManager;
        $this->requestCheckerService = $requestCheckerService;
    }

    public function createGuest(string $fullName, string $email, ?string $phone): Guest
    {
        $guest = new Guest();
        $guest->setFullName($fullName);
        $guest->setEmail($email);
        $guest->setPhone($phone);

        // валідація через Constraints
        $this->requestCheckerService->validateRequestDataByConstraints($guest);

        $this->entityManager->persist($guest);
        return $guest;
    }

    public function updateGuest(Guest $guest, array $data): Guest
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            if (method_exists($guest, $method)) {
                $guest->$method($value);
            }
        }

        // повторна валідація
        $this->requestCheckerService->validateRequestDataByConstraints($guest);

        return $guest;
    }
}
